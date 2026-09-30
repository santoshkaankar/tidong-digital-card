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
     * Pending -> Accepted -> Cooking -> Ready -> Pickedup -> Served -> Delivered -> Completed -> Cancelled
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,cooking,preparing,ready,pickedup,served,delivered,cancelled,completed'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($order) {
            $order->status = $request->status;
            
            $itemKitchenStatus = match($request->status) {
                'pending' => 'pending',
                'accepted', 'cooking', 'preparing' => 'cooking',
                'ready' => 'ready',
                'pickedup', 'served', 'delivered', 'completed' => 'served',
                'cancelled' => 'cancelled',
                default => 'cooking'
            };

            RestaurantOrderItem::where('order_id', $order->id)
                ->update(['kitchen_status' => $itemKitchenStatus]);

            $order->save();

            // Release Table if Order Completed or Cancelled or Delivered
            if (in_array($request->status, ['completed', 'cancelled', 'delivered']) && $order->table_id) {
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