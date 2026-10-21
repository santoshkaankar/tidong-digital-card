<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class GlobalMenuCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $items = DB::table('global_menu_items')
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.addcslashes($search, '%_\\').'%')
                ->orWhere('category', 'like', '%'.addcslashes($search, '%_\\').'%')))
            ->orderBy('category')->orderBy('name')->paginate(24)->withQueryString();
        return view('global-menu.index', compact('items','search'));
    }

    public function store(Request $request, PosOrderService $pos): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','min:2','max:150'],
            'category' => ['required','string','max:80'],
            'description' => ['nullable','string','max:1500'],
        ]);
        $this->ensureAtomic($data['name']);
        $name = trim($data['name']);
        $slug = substr(Str::slug($name), 0, 120);
        if ($slug === '') throw ValidationException::withMessages(['name'=>'Please enter a valid item name.']);
        DB::transaction(function () use ($request,$pos,$data,$name,$slug): void {
            if (DB::table('global_menu_items')->where('slug',$slug)->exists()) {
                throw ValidationException::withMessages(['name'=>'A Global Item with this name already exists.']);
            }
            $id = DB::table('global_menu_items')->insertGetId([
                'slug'=>$slug, 'name'=>$name, 'category'=>trim($data['category']),
                'description'=>$data['description'] ?? null, 'is_active'=>true,
                'created_by_user_id'=>$request->user()->id,
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            $pos->audit($request,'global.menu.created','global_menu_item',$id,['name'=>$name,'slug'=>$slug]);
        });
        return back()->with('success','Global item added. Each branch must opt in and set its own price.');
    }

    public function availability(Request $request, int $item, PosOrderService $pos): RedirectResponse
    {
        $data = $request->validate(['active'=>['required','boolean']]);
        DB::transaction(function () use ($request,$item,$pos,$data): void {
            $row = DB::table('global_menu_items')->where('id',$item)->lockForUpdate()->firstOrFail();
            DB::table('global_menu_items')->where('id',$item)->update([
                'is_active'=>(bool)$data['active'],'updated_at'=>now(),
            ]);
            $pos->audit($request,'global.menu.availability_changed','global_menu_item',$item,
                ['is_active'=>(bool)$data['active']],['is_active'=>(bool)$row->is_active]);
        });
        return back()->with('success','Global catalog status updated. Existing branch menus and historical orders are not rewritten.');
    }

    private function ensureAtomic(string $name): void
    {
        if (preg_match('/\b(thali|combo|platter|meal\s*deal)\b/i', $name)) {
            throw ValidationException::withMessages(['name'=>'Thali/Combo will use the separate merge system, not a Global single item.']);
        }
    }
}
