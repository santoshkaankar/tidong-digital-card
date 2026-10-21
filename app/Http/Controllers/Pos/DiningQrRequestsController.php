<?php
namespace App\Http\Controllers\Pos;
use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
class DiningQrRequestsController extends Controller
{
    public function __construct(private readonly PosOrderService $pos) {}
    public function index(Request $request,int $branch): View
    {
        $branchRecord=$this->pos->ownedBranch($request,$branch);
        abort_unless(in_array($branchRecord->business_type,PosOrderService::BUSINESS_TYPES,true),404);
        $requests=DB::table('restaurant_qr_requests as qr')
            ->join('restaurant_dining_tables as t','t.id','=','qr.dining_table_id')
            ->where('qr.branch_id',$branch)->orderByRaw("CASE qr.status WHEN 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('qr.id')->limit(60)->select('qr.*','t.label as table_label')->get();
        $items=DB::table('restaurant_qr_request_items')->whereIn('request_id',$requests->pluck('id'))
            ->orderBy('id')->get()->groupBy('request_id');
        return view('pos.qr-requests',compact('branchRecord','requests','items'));
    }
    public function accept(Request $request,int $qrRequest): RedirectResponse
    {
        DB::transaction(function () use($request,$qrRequest):void {
            $r=DB::table('restaurant_qr_requests')->where('id',$qrRequest)->lockForUpdate()->first();
            abort_unless($r,404);
            $branch=$this->pos->ownedBranch($request,(int)$r->branch_id,true);
            $this->pos->ensureCanOrder($branch);
            if ($r->status!=='pending') throw ValidationException::withMessages(['request'=>'This request was already decided.']);
            $table=DB::table('restaurant_dining_tables')->where('id',$r->dining_table_id)
                ->where('branch_id',$r->branch_id)->where('is_active',true)->first();
            if (!$table) throw ValidationException::withMessages(['request'=>'Table is disabled. Reject this request or re-enable the Table.']);
            $items=DB::table('restaurant_qr_request_items')->where('request_id',$qrRequest)->orderBy('id')->get();
            if ($items->isEmpty()) throw ValidationException::withMessages(['request'=>'Request has no items.']);
            $current=DB::table('pos_menu_items')->where('branch_id',$r->branch_id)
                ->where('is_active',true)->whereIn('id',$items->pluck('menu_item_id'))->lockForUpdate()->get()->keyBy('id');
            foreach($items as $item) {
                $menu=$current->get($item->menu_item_id);
                if (!$menu || (int)$menu->price_paise!==(int)$item->unit_price_paise) {
                    throw ValidationException::withMessages(['request'=>'Menu item or price changed. Reject this request and ask the guest to submit again.']);
                }
            }
            $data=['items'=>$items->map(fn($item)=>['menu_item_id'=>$item->menu_item_id,'quantity'=>$item->quantity])->all(),
                'customer_note'=>$r->customer_note,'dining_table_id'=>$r->dining_table_id,'order_origin'=>'qr'];
            $orderId=$this->pos->createOrder($request,(int)$r->branch_id,$data);
            DB::table('restaurant_qr_requests')->where('id',$qrRequest)->update([
                'status'=>'accepted','accepted_order_id'=>$orderId,'decided_by_user_id'=>$request->user()->id,
                'decided_at'=>now(),'updated_at'=>now(),
            ]);
            $this->pos->audit($request,'restaurant.qr_request.accepted','restaurant_qr_request',$qrRequest,
                ['status'=>'accepted','accepted_order_id'=>$orderId],['status'=>'pending']);
        },3);
        return back()->with('success','Request accepted and sent to Kitchen. Payment remains unpaid.');
    }
    public function reject(Request $request,int $qrRequest): RedirectResponse
    {
        $data=$request->validate(['reason'=>['required','string','min:5','max:500']]);
        DB::transaction(function () use($request,$qrRequest,$data):void {
            $r=DB::table('restaurant_qr_requests')->where('id',$qrRequest)->lockForUpdate()->first();
            abort_unless($r,404);
            $this->pos->ownedBranch($request,(int)$r->branch_id,true);
            if ($r->status!=='pending') throw ValidationException::withMessages(['request'=>'Already decided.']);
            DB::table('restaurant_qr_requests')->where('id',$qrRequest)->update([
                'status'=>'rejected','rejection_reason'=>$data['reason'],
                'decided_by_user_id'=>$request->user()->id,'decided_at'=>now(),'updated_at'=>now(),
            ]);
            $this->pos->audit($request,'restaurant.qr_request.rejected','restaurant_qr_request',$qrRequest,
                ['status'=>'rejected'],['status'=>'pending'],$data['reason']);
        },3);
        return back()->with('success','Request rejected and kept for audit.');
    }
}
