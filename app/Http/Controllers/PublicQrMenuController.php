<?php
namespace App\Http\Controllers;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
class PublicQrMenuController extends Controller
{
    public function show(Request $request, string $token): View
    {
        abort_unless((bool)preg_match('/^[a-zA-Z0-9]{48}$/D',$token),404);
        $table = DB::table('restaurant_dining_tables as t')
            ->join('branches as br','br.id','=','t.branch_id')
            ->join('businesses as bu','bu.id','=','br.business_id')
            ->join('vendors as ve','ve.id','=','bu.vendor_id')
            ->where('t.qr_token',$token)->where('t.is_active',true)
            ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
            ->whereIn('bu.business_type', PosOrderService::BUSINESS_TYPES)
            ->select('t.id','t.qr_token','t.label','t.branch_id','br.name as branch_name','bu.name as business_name',
                'br.status as branch_status','br.is_accepting_orders as branch_accepting',
                'bu.status as business_status','bu.is_accepting_orders as business_accepting','ve.status as vendor_status')->first();
        abort_unless($table && $table->vendor_status === 'approved' && $table->business_status === 'approved',404);
        $canOrder = $table->branch_status === 'active' && $table->branch_accepting && $table->business_accepting;
        $items = DB::table('pos_menu_items')->where('branch_id',$table->branch_id)->where('is_active',true)->orderBy('name')->get();
        return view('qr.menu',compact('table','items','canOrder'));
    }
}
