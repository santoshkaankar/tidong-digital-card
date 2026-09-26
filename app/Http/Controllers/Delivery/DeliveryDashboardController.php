<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;

class DeliveryDashboardController extends Controller
{
    public function index()
    {
        $deliveryBoyId = auth()->id();

        // Stats Counters
        $todayDeliveries = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();

        $activeDeliveries = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'out_for_delivery')
            ->count();

        $totalEarnings = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        $availablePickups = RestaurantOrder::where('status', 'ready_for_pickup')
            ->whereNull('delivery_boy_id')
            ->count();

        // Recent Orders for Dashboard Table
        $recentOrders = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->latest()
            ->take(5)
            ->get();

        return view('delivery.dashboard', compact(
            'todayDeliveries',
            'activeDeliveries',
            'totalEarnings',
            'availablePickups',
            'recentOrders'
        ));
    }
}