<?php
namespace App\Http\Controllers;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class RestaurantQrOrderController extends Controller
{
    public function submit(Request $request, string $token): RedirectResponse
    {
        abort_unless((bool) preg_match('/^[a-zA-Z0-9]{48}$/D',$token),404);
        $data=$request->validate([
            'submission_key'=>['required','uuid'],
            'customer_name'=>['required','string','min:2','max:80'],
            'customer_note'=>['nullable','string','max:500'],
            'quantities'=>['required','array','min:1','max:200'],
            'quantities.*'=>['required','integer','min:0','max:20'],
            'confirmed'=>['required','accepted'],
        ]);
        if (DB::table('restaurant_qr_requests')->where('submission_key',$data['submission_key'])->exists()) {
            return back()->with('success','This request was already received. Please do not submit twice.');
        }
        $selected=collect($data['quantities'])->filter(fn($qty)=>(int)$qty>0);
        if ($selected->isEmpty() || $selected->count()>25) {
            throw ValidationException::withMessages(['quantities'=>'Select 1 to 25 available menu items.']);
        }
        foreach ($selected->keys() as $id) {
            if (! ctype_digit((string)$id) || (int)$id<1) {
                throw ValidationException::withMessages(['quantities'=>'Invalid menu item.']);
            }
        }
        DB::transaction(function () use ($request,$token,$data,$selected): void {
            $table=DB::table('restaurant_dining_tables as t')
                ->join('branches as br','br.id','=','t.branch_id')
                ->join('businesses as bu','bu.id','=','br.business_id')
                ->join('vendors as ve','ve.id','=','bu.vendor_id')
                ->where('t.qr_token',$token)->where('t.is_active',true)
                ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
                ->whereIn('bu.business_type',PosOrderService::BUSINESS_TYPES)
                ->select('t.id','t.branch_id','br.status as branch_status','br.is_accepting_orders as branch_accepting',
                    'bu.status as business_status','bu.is_accepting_orders as business_accepting','ve.status as vendor_status')
                ->lockForUpdate()->first();
            abort_unless($table,404);
            if ($table->vendor_status!=='approved'||$table->business_status!=='approved'||$table->branch_status!=='active'
                ||!$table->business_accepting||!$table->branch_accepting) {
                throw ValidationException::withMessages(['order'=>'This restaurant is not accepting orders now.']);
            }
            $menu=DB::table('pos_menu_items')->where('branch_id',$table->branch_id)->where('is_active',true)
                ->whereIn('id',$selected->keys()->all())->lockForUpdate()->get()->keyBy('id');
            if ($menu->count()!==$selected->count()) {
                throw ValidationException::withMessages(['quantities'=>'An item is now unavailable. Refresh the menu.']);
            }
            $total=0; $rows=[];
            foreach ($selected as $id=>$qty) {
                $item=$menu->get((int)$id);
                $line=(int)$item->price_paise*(int)$qty;
                $total+=$line;
                $rows[]=['menu_item_id'=>$item->id,'name_snapshot'=>$item->name,'quantity'=>(int)$qty,
                    'unit_price_paise'=>$item->price_paise,'line_total_paise'=>$line,
                    'created_at'=>now(),'updated_at'=>now()];
            }
            if ($total<1 || $total>5000000) {
                throw ValidationException::withMessages(['quantities'=>'Order exceeds the allowed amount. Ask the waiter.']);
            }
            $id=DB::table('restaurant_qr_requests')->insertGetId([
                'dining_table_id'=>$table->id,'branch_id'=>$table->branch_id,'submission_key'=>$data['submission_key'],
                'customer_name'=>trim($data['customer_name']),'customer_note'=>$data['customer_note']??null,
                'status'=>'pending','quoted_total_paise'=>$total,'created_at'=>now(),'updated_at'=>now(),
            ]);
            foreach ($rows as &$row) $row['request_id']=$id;
            unset($row);
            DB::table('restaurant_qr_request_items')->insert($rows);
            DB::table('audit_logs')->insert(['actor_user_id'=>null,'action'=>'restaurant.qr_request.submitted',
                'entity_type'=>'restaurant_qr_request','entity_id'=>$id,
                'new_values'=>json_encode(['branch_id'=>$table->branch_id,'dining_table_id'=>$table->id,'status'=>'pending']),
                'ip_address'=>$request->ip(),'created_at'=>now()]);
        },3);
        return back()->with('success','Order request sent for restaurant confirmation. This is NOT yet a Kitchen order or a payment.');
    }
}
