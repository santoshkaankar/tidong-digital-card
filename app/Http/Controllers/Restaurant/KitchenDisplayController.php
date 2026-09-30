<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use App\Models\Restaurant\RestaurantTable;
use App\Models\Restaurant\WaiterCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KitchenDisplayController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $activeOrders = RestaurantOrder::with(['items.restaurantItem.globalItem', 'table'])
            ->where('user_id', $userId)
            ->whereNotIn('status', ['completed', 'cancelled', 'delivered'])
            ->orderBy('created_at', 'desc')
            ->get();

        $waiterCalls = WaiterCall::with('table')
            ->where('user_id', $userId)
            ->where(function($query) {
                $query->where('status', 'pending')
                      ->orWhereNull('status');
            })
            ->whereNotIn('call_type', ['pay_bill_cash', 'bill_cash', 'cash_payment', 'cash_requested'])
            ->orderBy('created_at', 'desc')
            ->get();

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

    public function liveOrders(Request $request)
    {
        $userId = Auth::id();

        $waiterCalls = WaiterCall::with('table')
            ->where('user_id', $userId)
            ->whereIn('call_type', ['waiter', 'call_waiter'])
            ->where(function($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $cashRequests = WaiterCall::with('table')
            ->where('user_id', $userId)
            ->whereIn('call_type', ['pay_bill_cash', 'bill_cash', 'cash_payment', 'cash_requested'])
            ->where(function($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $runningOrders = RestaurantOrder::with(['items.restaurantItem.globalItem', 'table'])
            ->where('user_id', $userId)
            ->whereNotIn('status', ['completed', 'cancelled', 'delivered'])
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

    // Complete Flow Status Handler
    public function updateOrderStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,accepted,cooking,preparing,ready,pickedup,served,delivered,completed,cancelled',
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

            // Sync item kitchen_status based on main order status
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

            // Table Auto Release Logic on Complete / Cancel
            if (in_array($request->status, ['completed', 'cancelled', 'delivered']) && $order->table_id) {
                RestaurantTable::where('id', $order->table_id)
                    ->where('user_id', $userId)
                    ->update([
                        'status' => 'available',
                        'current_order_id' => null
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Order status updated successfully to ') . $order->status,
                'status'  => $order->status
            ]);

        } catch (\Exception $e) {
            Log::error('KDS Update Order Status Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Server Error: ') . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        return $this->updateOrderStatus($request, $id);
    }

    public function markPaymentReceived($id)
    {
        $userId = Auth::id();
        $order = RestaurantOrder::where('user_id', $userId)->findOrFail($id);

        $order->payment_status = 'paid';
        $order->status = ($order->order_type === 'delivery') ? 'delivered' : 'completed';
        $order->save();

        if ($order->table_id) {
            RestaurantTable::where('id', $order->table_id)
                ->where('user_id', $userId)
                ->update([
                    'status' => 'available',
                    'current_order_id' => null
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Payment received and order status updated successfully.')
        ]);
    }
}