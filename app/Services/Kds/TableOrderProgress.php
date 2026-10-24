<?php
namespace App\Services\Kds;

use App\Services\Pos\PosOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Every change is attached to the existing POS order, never creates another order.
 * No language-model output can call this service without an authenticated action.
 */
final class TableOrderProgress
{
    public function __construct(private readonly PosOrderService $pos) {}

    public function change(Request $request, int $itemId, string $action, int $quantity): array
    {
        if (! in_array($action, ['add','ready','served'], true) || $quantity < 1 || $quantity > 100) {
            throw ValidationException::withMessages(['quantity'=>'Choose a valid action and quantity.']);
        }
        return DB::transaction(function () use ($request,$itemId,$action,$quantity): array {
            $ref = DB::table('pos_order_items')->where('id',$itemId)->first();
            abort_unless($ref,404);
            $order = DB::table('pos_orders')->where('id',$ref->order_id)->lockForUpdate()->first();
            abort_unless($order,404);
            $branch = $this->pos->ownedBranch($request,(int)$order->branch_id,true);
            $this->pos->ensureApproved($branch);
            if ($action === 'add') {
                $this->pos->ensureCanOrder($branch);
            }
            // Closed/settled bills cannot gain charges via an item-counter action.
            // Preparation/serving never changes paid amounts.
            if ($order->status === 'cancelled'
                || ($action === 'add' && ($order->status === 'served' || $order->payment_status !== 'unpaid'))) {
                throw ValidationException::withMessages(['order'=>'This order is already closed.']);
            }
            $item = DB::table('pos_order_items')->where('id',$itemId)
                ->where('order_id',$order->id)->lockForUpdate()->firstOrFail();
            $ordered=(int)$item->quantity; $ready=(int)$item->ready_quantity;
            $served=(int)$item->served_quantity; $added=(int)$item->added_quantity;
            if ($action === 'add') {
                $menu = DB::table('pos_menu_items')->where('id',$item->menu_item_id)
                    ->where('branch_id',$order->branch_id)->where('is_active',true)->first();
                if (! $menu || (int)$menu->price_paise !== (int)$item->unit_price_paise) {
                    throw ValidationException::withMessages(['item'=>'Item or price changed. Get a fresh quote.']);
                }
                $delta = (int)$menu->price_paise*$quantity;
                if ($ordered+$quantity > 1000 || (int)$order->total_paise+$delta > 5000000) {
                    throw ValidationException::withMessages(['quantity'=>'Order limit reached.']);
                }
                $ordered+=$quantity; $added+=$quantity;
                DB::table('pos_orders')->where('id',$order->id)->update([
                    'subtotal_paise'=>(int)$order->subtotal_paise+$delta,
                    'total_paise'=>(int)$order->total_paise+$delta, 'updated_at'=>now(),
                ]);
            } elseif ($action === 'ready') {
                if ($ready+$quantity > $ordered) {
                    throw ValidationException::withMessages(['quantity'=>'More ready than ordered.']);
                }
                $ready+=$quantity;
            } else {
                if ($served+$quantity > $ready) {
                    throw ValidationException::withMessages(['quantity'=>'Waiter cannot serve more than ready.']);
                }
                $served+=$quantity;
            }
            DB::table('pos_order_items')->where('id',$itemId)->update([
                'quantity'=>$ordered,'added_quantity'=>$added,
                'ready_quantity'=>$ready,'served_quantity'=>$served,
                'line_total_paise'=>(int)$item->unit_price_paise*$ordered,
                'updated_at'=>now(),
            ]);
            DB::table('pos_item_progress_events')->insert([
                'order_id'=>$order->id,'item_id'=>$itemId,'actor_user_id'=>$request->user()->id,
                'action'=>$action,'quantity'=>$quantity,'created_at'=>now(),
            ]);
            $rows=DB::table('pos_order_items')->where('order_id',$order->id)->get();
            $fullyServed=$rows->every(fn($r)=>(int)$r->served_quantity===(int)$r->quantity);
            $fullyReady=$rows->every(fn($r)=>(int)$r->ready_quantity===(int)$r->quantity);
            $status=$fullyServed?'served':($fullyReady?'ready':'preparing');
            DB::table('pos_orders')->where('id',$order->id)->update([
                'status'=>$status,'updated_at'=>now(),
            ]);
            $this->pos->audit($request,'kds.item.'.$action,'pos_order_item',$itemId,[
                'order_id'=>$order->id,'table_id'=>$order->dining_table_id,
                'added'=>$added,'ordered'=>$ordered,'ready'=>$ready,'served'=>$served,
                'delta_quantity'=>$quantity,
            ]);
            return ['order_id'=>(int)$order->id,'item_id'=>$itemId,
                'added'=>$added,'ordered'=>$ordered,'ready'=>$ready,'served'=>$served,
                'remaining_to_prepare'=>$ordered-$ready,'remaining_to_serve'=>$ready-$served,
                'status'=>$status];
        },3);
    }

    public function table(Request $request, int $branchId, int $tableId): array
    {
        $branch=$this->pos->ownedBranch($request,$branchId);
        $this->pos->ensureApproved($branch);
        $table=DB::table('restaurant_dining_tables')->where('branch_id',$branchId)->where('id',$tableId)->first();
        abort_unless($table,404);
        $rows=DB::table('pos_orders as o')->join('pos_order_items as i','i.order_id','=','o.id')
            ->where('o.branch_id',$branchId)->where('o.dining_table_id',$tableId)
            ->whereNotIn('o.status',['cancelled'])
            ->select('o.order_number','i.name_snapshot','i.quantity','i.added_quantity','i.ready_quantity','i.served_quantity')->get();
        $items=$rows->groupBy('name_snapshot')->map(function($group,$name){
            $ordered=$group->sum('quantity');$ready=$group->sum('ready_quantity');$served=$group->sum('served_quantity');
            return ['name'=>$name,'total_ordered'=>$ordered,'added'=>$group->sum('added_quantity'),
                'ready'=>$ready,'served'=>$served,'remaining_to_prepare'=>$ordered-$ready,
                'remaining_to_serve'=>$ready-$served];
        })->values()->all();
        return ['table'=>$table->label,'table_id'=>$tableId,'items'=>$items];
    }
}
