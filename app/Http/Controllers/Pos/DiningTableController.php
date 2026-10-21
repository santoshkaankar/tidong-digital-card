<?php
namespace App\Http\Controllers\Pos;
use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use App\Services\Qr\OfflineQrSvg;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
class DiningTableController extends Controller
{
    public function __construct(private readonly PosOrderService $pos) {}
    public function index(Request $request, int $branch): View
    {
        $branchRecord = $this->pos->ownedBranch($request, $branch);
        abort_unless(in_array($branchRecord->business_type, PosOrderService::BUSINESS_TYPES, true), 404);
        $tables = DB::table('restaurant_dining_tables')->where('branch_id',$branch)->orderBy('label')->get();
        $qrs = [];
        foreach ($tables as $table) $qrs[$table->id] = OfflineQrSvg::make(route('qr.menu.show',$table->qr_token));
        return view('pos.tables', compact('tables','branchRecord','qrs'));
    }
    public function store(Request $request, int $branch): RedirectResponse
    {
        $data = $request->validate(['label'=>['required','string','min:1','max:60','regex:/^[\\pL\\pN _-]+$/u']]);
        DB::transaction(function () use ($request,$branch,$data): void {
            $record = $this->pos->ownedBranch($request,$branch,true);
            $this->pos->ensureApproved($record);
            $label = trim($data['label']);
            if (DB::table('restaurant_dining_tables')->where('branch_id',$branch)->where('label',$label)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['label'=>'This table name already exists in this Branch.']);
            }
            $id = DB::table('restaurant_dining_tables')->insertGetId([
                'branch_id'=>$branch,'label'=>$label,'qr_token'=>Str::random(48),
                'is_active'=>true,'created_at'=>now(),'updated_at'=>now(),
            ]);
            $this->pos->audit($request,'restaurant.table.created','restaurant_dining_table',$id,['branch_id'=>$branch,'label'=>$label]);
        });
        return back()->with('success','Table created. Print its unique QR code.');
    }
    public function toggle(Request $request,int $table): RedirectResponse
    {
        $data = $request->validate(['active'=>['required','boolean']]);
        DB::transaction(function () use ($request,$table,$data): void {
            $ref = DB::table('restaurant_dining_tables')->where('id',$table)->first();
            abort_unless($ref,404);
            $this->pos->ownedBranch($request,(int)$ref->branch_id,true);
            $record = DB::table('restaurant_dining_tables')->where('id',$table)->lockForUpdate()->firstOrFail();
            DB::table('restaurant_dining_tables')->where('id',$table)->update(['is_active'=>(bool)$data['active'],'updated_at'=>now()]);
            $this->pos->audit($request,'restaurant.table.availability_changed','restaurant_dining_table',$table,
                ['is_active'=>(bool)$data['active']],['is_active'=>(bool)$record->is_active]);
        });
        return back()->with('success','Table QR availability updated.');
    }
}
