<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    // Live Pickups & Active Orders List
    public function index()
    {
        $deliveryBoyId = auth()->id();

        // 1. Available restaurant orders for pickup
        $availableOrders = RestaurantOrder::where('status', 'ready_for_pickup')
            ->whereNull('delivery_boy_id')
            ->latest()
            ->get();

        // 2. Orders currently being delivered by this delivery boy
        $activeOrders = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'out_for_delivery')
            ->latest()
            ->get();

        return view('delivery.orders.index', compact('availableOrders', 'activeOrders'));
    }

    // Accept Order (Pickup)
    public function accept($id)
    {
        $order = RestaurantOrder::findOrFail($id);

        if ($order->status !== 'ready_for_pickup') {
            return back()->with('error', 'Order is no longer available for pickup.');
        }

        $order->update([
            'delivery_boy_id' => auth()->id(),
            'status' => 'out_for_delivery',
        ]);

        return back()->with('success', 'Order accepted! Proceed to delivery.');
    }

    // Mark Order as Delivered
    public function complete($id)
    {
        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', auth()->id())
            ->firstOrFail();

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return redirect()->route('delivery.orders.history')
            ->with('success', 'Order delivered successfully!');
    }

    // Delivery History
    public function history()
    {
        $orders = RestaurantOrder::where('delivery_boy_id', auth()->id())
            ->where('status', 'delivered')
            ->latest()
            ->get();

        return view('delivery.orders.history', compact('orders'));
    }
}