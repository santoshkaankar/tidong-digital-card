<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BranchMenuCatalogController extends Controller
{
    public function __construct(private readonly PosOrderService $pos) {}

    public function index(Request $request, int $branch): View
    {
        $branchRecord = $this->pos->ownedBranch($request, $branch);
        abort_unless(in_array($branchRecord->business_type, PosOrderService::BUSINESS_TYPES, true),404);
        $catalog = DB::table('global_menu_items')->where('is_active',true)
            ->orderBy('category')->orderBy('name')->get();
        $branchItems = DB::table('pos_menu_items')->where('branch_id',$branch)
            ->orderBy('name')->get();
        $attached = $branchItems->pluck('global_menu_item_id')->filter()->map(fn($id)=>(int)$id)->all();
        $navBusiness = (object) [
            'id' => $branchRecord->business_id,
            'name' => $branchRecord->business_name,
            'business_type' => $branchRecord->business_type,
            'status' => $branchRecord->business_status,
        ];
        $navBranch = (object) ['id' => $branchRecord->id, 'name' => $branchRecord->name];
        $navBusinesses = DB::table('businesses')->where('vendor_id', $branchRecord->vendor_id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        return view('pos.branch-menu-catalog', compact('branchRecord','catalog','branchItems','attached',
            'navBusiness','navBranch','navBusinesses'));
    }

    public function attachGlobal(Request $request, int $branch): RedirectResponse
    {
        $data = $request->validate([
            'global_menu_item_id'=>['required','integer','min:1'],
            'price'=>['required','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
        ]);
        $paise=$this->pos->rupeesToPaise($data['price']);
        DB::transaction(function () use ($request,$branch,$data,$paise): void {
            $owned=$this->pos->ownedBranch($request,$branch,true);
            $this->pos->ensureApproved($owned);
            $global=DB::table('global_menu_items')->where('id',$data['global_menu_item_id'])
                ->where('is_active',true)->first();
            if (!$global) throw ValidationException::withMessages(['global_menu_item_id'=>'This Global Item is not available.']);
            if (DB::table('pos_menu_items')->where('branch_id',$branch)
                ->where('global_menu_item_id',$global->id)->exists()) {
                throw ValidationException::withMessages(['global_menu_item_id'=>'This Global Item is already in this Branch menu.']);
            }
            $id=DB::table('pos_menu_items')->insertGetId([
                'branch_id'=>$branch,'global_menu_item_id'=>$global->id,'source_kind'=>'global',
                'name'=>$global->name,'category'=>$global->category,
                'photo_path'=>$global->image_path,'price_paise'=>$paise,'is_active'=>true,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $this->pos->audit($request,'pos.menu.global_attached','pos_menu_item',$id,
                ['branch_id'=>$branch,'global_menu_item_id'=>$global->id,'price_paise'=>$paise]);
        });
        return back()->with('success','Global Item added to this Branch at its own price.');
    }

    public function createCustom(Request $request, int $branch): RedirectResponse
    {
        $data=$request->validate([
            'name'=>['required','string','min:2','max:150'],
            'category'=>['nullable','string','max:80'],
            'price'=>['required','string','regex:/^\d{1,7}(\.\d{1,2})?$/'],
            'photo'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:3072'],
        ]);
        if (preg_match('/\b(thali|combo|platter|meal\s*deal)\b/i', $data['name'])) {
            throw ValidationException::withMessages(['name'=>'Thali/Combo will be built using a separate merge system.']);
        }
        $paise=$this->pos->rupeesToPaise($data['price']);
        $uploadedPath=null;
        try {
            DB::transaction(function () use ($request,$branch,$data,$paise,&$uploadedPath): void {
                $owned=$this->pos->ownedBranch($request,$branch,true);
                $this->pos->ensureApproved($owned);
                $name=trim($data['name']);
                $duplicate=DB::table('pos_menu_items')->where('branch_id',$branch)
                    ->where('name',$name)->exists();
                if ($duplicate) throw ValidationException::withMessages(['name'=>'This name is already used in this Branch menu.']);
                if ($request->hasFile('photo')) {
                    $uploadedPath=$request->file('photo')->store('branch-menu/'.$branch,'public');
                    if (!$uploadedPath) throw ValidationException::withMessages(['photo'=>'The photo could not be stored.']);
                }
                $id=DB::table('pos_menu_items')->insertGetId([
                    'branch_id'=>$branch,'global_menu_item_id'=>null,'source_kind'=>'custom',
                    'name'=>$name,'category'=>trim((string)($data['category']??''))?:null,
                    'photo_path'=>$uploadedPath,'price_paise'=>$paise,'is_active'=>true,
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
                $this->pos->audit($request,'pos.menu.branch_custom_created','pos_menu_item',$id,
                    ['branch_id'=>$branch,'name'=>$name,'price_paise'=>$paise,'has_photo'=>$uploadedPath!==null]);
            });
        } catch (\Throwable $e) {
            if ($uploadedPath) Storage::disk('public')->delete($uploadedPath);
            throw $e;
        }
        return back()->with('success','Private item saved only for this Branch.');
    }

    public function updateCustomPhoto(Request $request, int $item): RedirectResponse
    {
        $data=$request->validate(['photo'=>['required','image','mimes:jpg,jpeg,png,webp','max:3072']]);
        $reference=DB::table('pos_menu_items')->where('id',$item)->firstOrFail();
        // Always authorize ownership by Branch BEFORE touching an item.
        $branch=$this->pos->ownedBranch($request,(int)$reference->branch_id);
        $this->pos->ensureApproved($branch);
        abort_unless($reference->source_kind==='custom',403,'Only private items allow custom photos.');
        $path=$request->file('photo')->store('branch-menu/'.$branch->id,'public');
        if (!$path) throw ValidationException::withMessages(['photo'=>'The photo could not be stored.']);
        try {
            DB::transaction(function () use ($request,$item,$branch,$path): void {
                $this->pos->ownedBranch($request,(int)$branch->id,true);
                $row=DB::table('pos_menu_items')->where('id',$item)
                    ->where('branch_id',$branch->id)->lockForUpdate()->firstOrFail();
                abort_unless($row->source_kind==='custom',403);
                DB::table('pos_menu_items')->where('id',$item)->update(['photo_path'=>$path,'updated_at'=>now()]);
                $this->pos->audit($request,'pos.menu.branch_custom_photo_updated','pos_menu_item',$item,
                    ['photo_path'=>$path],['photo_path'=>$row->photo_path]);
            });
        } catch (\Throwable $e) { Storage::disk('public')->delete($path); throw $e; }
        return back()->with('success','Branch item photo updated.');
    }
}
