<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenOrderController extends Controller
{
    /**
     * Update Running Order Status (Pending -> Cooking -> Ready -> Served)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,cooking,preparing,ready,served,cancelled,completed'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($order) {
            $order->status = $request->status;
            $order->save();

            // Release Table if Order Completed or Cancelled
            if (in_array($request->status, ['completed', 'cancelled']) && $order->table_id) {
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