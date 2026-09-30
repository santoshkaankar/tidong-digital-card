<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryDashboardController extends Controller
{
    public function index()
    {
        $deliveryBoyId = Auth::id();

        // 1. Today's Completed Deliveries (Supporting both 'delivered' and 'completed' status)
        $todayDeliveries = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->whereIn('status', ['delivered', 'completed'])
            ->whereDate('updated_at', today())
            ->count();

        // 2. Active Pickups / On-Going Orders Count Fix
        $activeDeliveries = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->whereIn('status', ['accepted', 'pickedup', 'picked_up', 'on_the_way', 'out_for_delivery'])
            ->count();

        // 3. Ready Pickups (Available for any online rider to accept)
        $availablePickups = RestaurantOrder::where('order_type', 'delivery')
            ->whereIn('status', ['ready', 'ready_for_pickup'])
            ->whereNull('delivery_boy_id')
            ->count();

        // 4. Total Earnings
        $totalEarnings = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('delivery_fee');

        // 5. Active Order for Live Map & Quick Actions
        $currentActiveOrder = RestaurantOrder::with(['restaurant'])
            ->where('delivery_boy_id', $deliveryBoyId)
            ->whereIn('status', ['accepted', 'pickedup', 'picked_up', 'on_the_way', 'out_for_delivery'])
            ->latest()
            ->first();

        // 6. Recent Orders History for Dashboard Table
        $recentOrders = RestaurantOrder::with(['restaurant', 'items'])
            ->where('delivery_boy_id', $deliveryBoyId)
            ->latest()
            ->take(5)
            ->get();

        return view('delivery.dashboard', compact(
            'todayDeliveries',
            'activeDeliveries',
            'availablePickups',
            'totalEarnings',
            'currentActiveOrder',
            'recentOrders'
        ));
    }
}