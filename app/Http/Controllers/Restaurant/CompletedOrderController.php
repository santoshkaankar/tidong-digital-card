<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Support\Facades\Auth;

class CompletedOrderController extends Controller
{
    /**
     * Fetch Completed / Served / Delivered Orders
     */
    public function index()
    {
        $completedOrders = RestaurantOrder::with(['items.restaurantItem.globalItem', 'table'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['served', 'delivered', 'completed'])
            ->orderBy('updated_at', 'desc')
            ->take(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $completedOrders
        ]);
    }
}