<?php

namespace App\Http\Controllers\Customer\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantTable;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\WaiterCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerRestaurantController extends Controller
{
    // 1. Customer QR Menu Display
    public function showMenu($token)
    {
        $table = RestaurantTable::where('qr_code_token', $token)->firstOrFail();
        
        $selectedItemIds = is_array($table->selected_items) 
            ? $table->selected_items 
            : json_decode($table->selected_items ?? '[]', true);

        $categories = RestaurantCategory::where('user_id', $table->user_id)
            ->where('status', true)
            ->whereHas('items', function($q) use ($table, $selectedItemIds) {
                $q->where('user_id', $table->user_id)
                  ->where('is_available', true)
                  ->where('status', true);
                
                if (!empty($selectedItemIds)) {
                    $q->whereIn('id', $selectedItemIds);
                }
            })
            ->with(['items' => function($q) use ($table, $selectedItemIds) {
                $q->where('user_id', $table->user_id)
                  ->where('is_available', true)
                  ->where('status', true);

                if (!empty($selectedItemIds)) {
                    $q->whereIn('id', $selectedItemIds);
                }

                $q->with('globalItem');
            }])->get();

        $activeOrder = RestaurantOrder::where('table_id', $table->id)
            ->whereIn('status', ['pending', 'cooking', 'served'])
            ->where('payment_status', 'unpaid')
            ->with(['items.restaurantItem.globalItem'])
            ->latest()
            ->first();

        return view('customer.restaurant.menu', compact('table', 'categories', 'activeOrder'));
    }

    // 2. Customer Order Placement / Running Order Append
    public function placeOrder(Request $request, $token)
    {
        try {
            $table = RestaurantTable::where('qr_code_token', $token)->firstOrFail();
            
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:restaurant_items,id',
                'items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string'
            ]);

            $order = DB::transaction(function () use ($request, $table) {
                $order = RestaurantOrder::where('table_id', $table->id)
                    ->whereIn('status', ['pending', 'cooking', 'served'])
                    ->where('payment_status', 'unpaid')
                    ->first();

                $batchNumber = 1;

                if ($order) {
                    $lastBatch = $order->items()->max('batch_number');
                    $batchNumber = $lastBatch ? $lastBatch + 1 : 1;
                    $order->status = 'cooking';
                } else {
                    $order = RestaurantOrder::create([
                        'user_id' => $table->user_id,
                        'table_id' => $table->id,
                        'order_number' => 'ORD-' . strtoupper(Str::random(6)),
                        'order_type' => 'dine_in',
                        'sub_total' => 0,
                        'total_amount' => 0,
                        'status' => 'pending',
                        'payment_status' => 'unpaid',
                        'notes' => $request->notes
                    ]);

                    $table->update(['status' => 'occupied']);
                }

                $subTotal = 0;
                foreach ($request->items as $itemData) {
                    $item = RestaurantItem::with('globalItem')->findOrFail($itemData['id']);
                    $itemSubtotal = $item->price * $itemData['quantity'];
                    $itemName = $item->globalItem->item_name ?? $item->name ?? 'Food Item';
                    
                    $existingItem = RestaurantOrderItem::where('order_id', $order->id)
                        ->where('item_id', $item->id)
                        ->first();

                    if ($existingItem) {
                        $existingItem->quantity += $itemData['quantity'];
                        $existingItem->subtotal += $itemSubtotal;
                        $existingItem->batch_number = $batchNumber;
                        $existingItem->kitchen_status = 'cooking';
                        $existingItem->save();
                    } else {
                        RestaurantOrderItem::create([
                            'order_id' => $order->id,
                            'item_id' => $item->id,
                            'item_name' => $itemName,
                            'quantity' => $itemData['quantity'],
                            'price' => $item->price,
                            'subtotal' => $itemSubtotal,
                            'batch_number' => $batchNumber,
                            'kitchen_status' => 'cooking'
                        ]);
                    }

                    $subTotal += $itemSubtotal;
                }

                $order->sub_total = ($order->sub_total ?? 0) + $subTotal;
                $order->total_amount = $order->sub_total + ($order->tax_amount ?? 0) + ($order->tip_amount ?? 0) - ($order->discount_amount ?? 0);
                $order->save();

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order has been sent to the kitchen!',
                'order_id' => $order->id,
                'total_amount' => $order->total_amount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 3. Live Order Status Polling for Customer
    public function getOrderStatus($token, $order_id)
    {
        $table = RestaurantTable::where('qr_code_token', $token)->firstOrFail();
        
        $order = RestaurantOrder::where('id', $order_id)
            ->where('table_id', $table->id)
            ->with(['items.restaurantItem.globalItem'])
            ->firstOrFail();

        $statusText = match (strtolower($order->status)) {
            'pending' => 'Order Received',
            'cooking', 'preparing' => 'Preparing / Cooking',
            'served', 'ready' => 'Served / Ready',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Order in Kitchen',
        };

        $badgeClass = match (strtolower($order->status)) {
            'pending' => 'bg-warning text-dark',
            'cooking', 'preparing' => 'bg-primary text-white',
            'served', 'ready' => 'bg-info text-white',
            'completed' => 'bg-success text-white',
            'cancelled' => 'bg-danger text-white',
            default => 'bg-warning text-dark',
        };

        $groupedItems = $order->items->groupBy('item_id')->map(function($items) {
            return [
                'item_name' => $items->first()->item_name,
                'price' => (float)$items->first()->price,
                'quantity' => (int)$items->sum('quantity'),
                'subtotal' => (float)$items->sum('subtotal'),
                'kitchen_status' => ucfirst(str_replace('_', ' ', $items->last()->kitchen_status ?? 'sent_to_kitchen'))
            ];
        })->values();

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'status_text' => $statusText,
            'badge_class' => $badgeClass,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'total_amount' => (float)$order->total_amount,
            'items' => $groupedItems
        ]);
    }

    // 4. Customer Call Waiter Request
    public function callWaiter(Request $request, $token)
    {
        $table = RestaurantTable::where('qr_code_token', $token)->firstOrFail();

        WaiterCall::create([
            'user_id' => $table->user_id,
            'table_id' => $table->id,
            'call_type' => $request->type ?? 'call_waiter',
            'status' => 'pending'
        ]);

        return response()->json(['success' => true, 'message' => 'Waiter has been notified!']);
    }

    // 5. Customer Payment Request (Cash / UPI)
    // Customer Side Payment Request Method
public function requestPayment(Request $request, $token)
{
    $table = RestaurantTable::where('qr_code_token', $token)->firstOrFail();

    // Check if request already exists for this table
    $existingCall = WaiterCall::where('table_id', $table->id)
        ->where('call_type', 'pay_bill_cash')
        ->first();

    if ($existingCall) {
        return response()->json([
            'success' => true,
            'message' => 'Cash request already sent to counter!'
        ]);
    }

    // Insert only if NOT exists
    WaiterCall::create([
        'user_id'   => $table->user_id,
        'table_id'  => $table->id,
        'call_type' => 'pay_bill_cash',
        'status'    => 'pending'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Cash payment request sent successfully!'
    ]);
}
}