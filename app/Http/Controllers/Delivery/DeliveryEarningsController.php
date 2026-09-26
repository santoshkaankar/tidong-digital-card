<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Http\Request;

class DeliveryEarningsController extends Controller
{
    public function index()
    {
        $deliveryBoyId = auth()->id();

        // 1. Total Earnings
        $totalEarnings = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        // 2. Today's Earnings
        $todayEarnings = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->sum('delivery_fee');

        // 3. This Week's Earnings
        $weeklyEarnings = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('delivery_fee');

        // 4. This Month's Earnings
        $monthlyEarnings = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('delivery_fee');

        // 5. Recent Delivery Transactions / Completed Orders List
        $recentDeliveries = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->where('status', 'delivered')
            ->latest('delivered_at')
            ->paginate(10);

        return view('delivery.earnings.index', compact(
            'totalEarnings',
            'todayEarnings',
            'weeklyEarnings',
            'monthlyEarnings',
            'recentDeliveries'
        ));
    }
}