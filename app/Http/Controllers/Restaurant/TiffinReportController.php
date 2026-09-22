<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiffinReportController extends Controller
{
    public function getProductionReport(Request $request)
    {
        $userId = Auth::id();
        $targetDate = $request->input('date', date('Y-m-d'));

        // Tiffin orders fetch karein selected date ke anusaar
        $tiffinOrders = \App\Models\Restaurant\TiffinOrder::with(['items'])
            ->where('user_id', $userId)
            ->whereDate('order_date', $targetDate)
            ->get();

        $mealSummary = [
            'breakfast' => [],
            'lunch' => [],
            'snacks' => [],
            'dinner' => []
        ];

        $grandTotalItems = [];

        foreach ($tiffinOrders as $order) {
            $mealType = strtolower($order->meal_type ?? 'lunch'); // breakfast, lunch, snacks, dinner
            
            if (!isset($mealSummary[$mealType])) {
                $mealSummary[$mealType] = [];
            }

            foreach ($order->items as $item) {
                $itemName = $item->item_name ?? $item->name ?? $item->title ?? 'Tiffin Item';
                $qty = $item->quantity ?? 1;

                if (!isset($mealSummary[$mealType][$itemName])) {
                    $mealSummary[$mealType][$itemName] = 0;
                }
                $mealSummary[$mealType][$itemName] += $qty;

                if (!isset($grandTotalItems[$itemName])) {
                    $grandTotalItems[$itemName] = 0;
                }
                $grandTotalItems[$itemName] += $qty;
            }
        }

        return response()->json([
            'success' => true,
            'date' => $targetDate,
            'meal_summary' => $mealSummary,
            'grand_total_items' => $grandTotalItems,
            'total_orders' => $tiffinOrders->count()
        ]);
    }
}