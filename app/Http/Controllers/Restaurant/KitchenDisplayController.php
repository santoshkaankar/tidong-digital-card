<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantTable;
use App\Models\Restaurant\WaiterCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KitchenDisplayController extends Controller
{
    /**
     * Display Main KDS View Screen
     */
    public function index()
    {
        $userId = Auth::id();

        $activeOrders = RestaurantOrder::with(['items.restaurantItem.globalItem', 'table'])
            ->where('user_id', $userId)
            ->where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhereNull('payment_status');
            })
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 1. Fetch Normal Waiter Calls (Read Only for Screen)
        $waiterCalls = WaiterCall::with('table')
            ->where('user_id', $userId)
            ->where(function($query) {
                $query->where('status', 'pending')
                      ->orWhereNull('status');
            })
            ->whereNotIn('call_type', ['pay_bill_cash', 'bill_cash', 'cash_payment', 'cash_requested'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Fetch Cash Payment Requests (Read Only for Screen)
        $cashRequests = WaiterCall::with('table')
            ->where('user_id', $userId)
            ->where(function($query) {
                $query->where('status', 'pending')
                      ->orWhereNull('status');
            })
            ->whereIn('call_type', ['pay_bill_cash', 'bill_cash', 'cash_payment', 'cash_requested'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeOrdersCount = $activeOrders->count();

        return view('vendor.restaurant.kitchen_screen', compact('activeOrders', 'waiterCalls', 'cashRequests', 'activeOrdersCount'));
    }

    /**
     * Fetch Detailed Order Data for Modal View
     */
    public function getOrderDetails($id)
    {
        $order = RestaurantOrder::with(['items.restaurantItem.globalItem', 'table'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $order->items->transform(function ($item) {
            $item->display_name = $item->item_name 
                ?? $item->name 
                ?? $item->restaurantItem->globalItem->name 
                ?? $item->restaurantItem->name 
                ?? (__('Item #') . $item->id);
                
            return $item;
        });

        $order->created_at_formatted = $order->created_at ? $order->created_at->format('h:i A, d M Y') : '';

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Live Polling Endpoint (Data Feed for UI Sync)
     */
    public function liveOrders(Request $request)
{
    $userId = Auth::id();

    // 1. Active Waiter Calls
    $waiterCalls = WaiterCall::with('table')
        ->where('user_id', $userId)
        ->whereIn('call_type', ['waiter', 'call_waiter'])
        ->where(function($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // 2. Cash Requests
    $cashRequests = WaiterCall::with('table')
        ->where('user_id', $userId)
        ->whereIn('call_type', ['pay_bill_cash', 'bill_cash', 'cash_payment', 'cash_requested'])
        ->where(function($q) {
            $q->where('status', 'pending')->orWhereNull('status');
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // 3. Running Orders (Sahi Model: RestaurantOrder)
    $runningOrders = \App\Models\Restaurant\RestaurantOrder::with(['items.item', 'table'])
        ->where('user_id', $userId)
        ->whereIn('status', ['pending', 'cooking', 'preparing', 'waiting', 'accepted'])
        ->orderBy('created_at', 'desc')
        ->get();

    $activeOrdersCount = $runningOrders->count();

    return response()->json([
        'success' => true,
        'activeOrdersCount' => $activeOrdersCount,
        'waiter_calls' => $waiterCalls,
        'cash_requests' => $cashRequests,
        'cash_requests_html' => view('vendor.restaurant.kitchen.cash_requests', compact('cashRequests'))->render(),
        'waiter_calls_html' => view('vendor.restaurant.kitchen.waiter_calls', compact('waiterCalls'))->render(),
        'running_orders_html' => view('vendor.restaurant.kitchen.running_orders', [
            'activeOrders' => $runningOrders, 
            'activeOrdersCount' => $activeOrdersCount
        ])->render(),
    ]);
}

    /**
     * Update Kitchen Order Status & Release Table on Completion
     */
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string',
                'payment_status' => 'nullable|string'
            ]);

            $userId = Auth::id();
            $order = RestaurantOrder::where('user_id', $userId)->where('id', $id)->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => __('Order not found!')
                ], 404);
            }

            $order->status = $request->status;

            if ($request->filled('payment_status')) {
                $order->payment_status = $request->payment_status;
            }

            $order->save();

            // Table Release Logic: Order Complete hote hi Table Free karein
            if ($request->status === 'completed' && $order->table_id) {
                RestaurantTable::where('id', $order->table_id)
                    ->where('user_id', $userId)
                    ->update([
                        'status' => 'available'
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Order status updated successfully.')
            ]);

        } catch (\Exception $e) {
            Log::error('KDS Update Order Status Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Server Error: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alias for updateOrderStatus
     */
    public function updateStatus(Request $request, $id)
    {
        return $this->updateOrderStatus($request, $id);
    }

    /**
     * Mark Payment Received and Release Table
     */
    public function markPaymentReceived($id)
    {
        $userId = Auth::id();
        $order = RestaurantOrder::where('user_id', $userId)->findOrFail($id);

        $order->payment_status = 'paid';
        $order->status = 'completed';
        $order->save();

        if ($order->table_id) {
            RestaurantTable::where('id', $order->table_id)
                ->where('user_id', $userId)
                ->update([
                    'status' => 'available'
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Payment received, order completed, and table released successfully.')
        ]);
    }
}