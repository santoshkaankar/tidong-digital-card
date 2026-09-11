<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenOrderController extends Controller
{
    /**
     * Update Running Order Status (Pending -> Cooking -> Ready -> Served)
     */
    public function updateStatus(Request $request, $id)
    {
        // Added 'cooking' to validation list
        $request->validate([
            'status' => 'required|in:pending,cooking,preparing,ready,served,cancelled,completed'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($order) {
            $order->status = $request->status;
            $order->save();

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