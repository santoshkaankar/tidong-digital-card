<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use App\Models\Restaurant\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenOrderController extends Controller
{
    /**
     * Complete Order Flow Status Route Handler
     * 1. pending -> 2. cooking -> 3. ready -> 4. served/picked_up -> 5. on_the_way -> 6. delivered -> 7. completed
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,cooking,preparing,ready,pickedup,picked_up,served,on_the_way,out_for_delivery,delivered,cancelled,completed'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($order) {
            $status = $request->status;

            // Auto Split logic: Only Dine-in gets converted to 'served'
            if ($order->order_type === 'dine_in') {
                if (in_array($status, ['ready', 'pickedup', 'picked_up'])) {
                    $status = 'served';
                }
            }

            $order->status = $status;
            
            // Sync kitchen status accurately without breaking delivery flow
            $itemKitchenStatus = match($status) {
                'pending' => 'pending',
                'accepted', 'cooking', 'preparing' => 'cooking',
                'ready' => 'ready',
                'served' => 'served',
                'pickedup', 'picked_up', 'on_the_way', 'out_for_delivery', 'delivered', 'completed' => ($order->order_type === 'dine_in' ? 'served' : 'delivered'),
                'cancelled' => 'cancelled',
                default => 'cooking'
            };

            RestaurantOrderItem::where('order_id', $order->id)
                ->update(['kitchen_status' => $itemKitchenStatus]);

            $order->save();

            // Release Table when Order completed or cancelled
            if (in_array($order->status, ['completed', 'cancelled']) && $order->table_id) {
                RestaurantTable::where('id', $order->table_id)
                    ->where('user_id', Auth::id())
                    ->update([
                        'status' => 'available',
                        'current_order_id' => null
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'status'  => $order->status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Order not found'
        ], 404);
    }
}