<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class OrderController extends Controller
{
    private function formatOrderDetails($order)
    {
        if (!$order) return $order;

        $subTotal = floatval($order->sub_total ?? 0);
        $totalAmount = floatval($order->total_amount ?? 0);
        
        $order->delivery_charge = property_exists($order, 'delivery_charge') && $order->delivery_charge !== null 
            ? floatval($order->delivery_charge) 
            : max(0, $totalAmount - $subTotal);

        if (empty($order->delivery_address)) {
            if (!empty($order->notes) && str_contains(strtolower($order->notes), 'address')) {
                $order->delivery_address = str_replace(['Delivery Address: ', 'Delivery Address:'], '', $order->notes);
            } elseif (!empty($order->notes)) {
                $order->delivery_address = $order->notes;
            } elseif (!empty($order->address)) {
                $order->delivery_address = $order->address;
            } else {
                $order->delivery_address = (isset($order->order_type) && $order->order_type === 'delivery') 
                    ? 'Address details saved in notes' 
                    : 'N/A (Takeaway / Dine-in)';
            }
        }

        return $order;
    }

    public function index()
    {
        $userId = Auth::id();
        $restaurantOrders = collect([]);

        if (Schema::hasTable('restaurant_orders')) {
            try {
                $query = DB::table('restaurant_orders');
                
                $query->where(function($q) use ($userId) {
                    if (Schema::hasColumn('restaurant_orders', 'customer_id')) {
                        $q->where('customer_id', $userId);
                    }
                    if (Schema::hasColumn('restaurant_orders', 'user_id')) {
                        $q->orWhere('user_id', $userId);
                    }
                    if (Auth::user() && !empty(Auth::user()->mobile) && Schema::hasColumn('restaurant_orders', 'customer_phone')) {
                        $q->orWhere('customer_phone', Auth::user()->mobile);
                    }
                });

                if (Schema::hasColumn('restaurant_orders', 'created_at')) {
                    $query->orderBy('created_at', 'desc');
                } else {
                    $query->orderBy('id', 'desc');
                }
                
                $restaurantOrders = $query->get()->map(function($order) {
                    return $this->formatOrderDetails($order);
                });
            } catch (\Exception $e) {
                $restaurantOrders = collect([]);
            }
        }

        $cardOrders = collect([]);
        $cardTable = Schema::hasTable('orders') ? 'orders' : (Schema::hasTable('card_orders') ? 'card_orders' : null);

        if ($cardTable) {
            try {
                $query = DB::table($cardTable);

                $query->where(function($q) use ($cardTable, $userId) {
                    if (Schema::hasColumn($cardTable, 'customer_id')) {
                        $q->where('customer_id', $userId);
                    }
                    if (Schema::hasColumn($cardTable, 'user_id')) {
                        $q->orWhere('user_id', $userId);
                    }
                    if (Schema::hasColumn($cardTable, 'member_id')) {
                        $q->orWhere('member_id', $userId);
                    }
                });

                if (Schema::hasColumn($cardTable, 'created_at')) {
                    $query->orderBy('created_at', 'desc');
                } else {
                    $query->orderBy('id', 'desc');
                }

                $cardOrders = $query->get();
            } catch (\Exception $e) {
                $cardOrders = collect([]);
            }
        }

        $orders = $restaurantOrders->concat($cardOrders);

        return view('member.orders.index', compact('restaurantOrders', 'cardOrders', 'orders'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $order = null;
        $orderItems = collect([]);

        if (Schema::hasTable('restaurant_orders')) {
            try {
                $query = DB::table('restaurant_orders')->where('id', $id);
                $query->where(function($q) use ($userId) {
                    if (Schema::hasColumn('restaurant_orders', 'customer_id')) {
                        $q->where('customer_id', $userId);
                    }
                    if (Schema::hasColumn('restaurant_orders', 'user_id')) {
                        $q->orWhere('user_id', $userId);
                    }
                });
                $order = $query->first();
            } catch (\Exception $e) {
                $order = null;
            }
        }

        if ($order) {
            $order = $this->formatOrderDetails($order);
        }

        if ($order && Schema::hasTable('restaurant_order_items')) {
            try {
                $queryItems = DB::table('restaurant_order_items');
                $queryItems->where(function($q) use ($id) {
                    if (Schema::hasColumn('restaurant_order_items', 'order_id')) {
                        $q->where('order_id', $id);
                    }
                    if (Schema::hasColumn('restaurant_order_items', 'restaurant_order_id')) {
                        $q->orWhere('restaurant_order_id', $id);
                    }
                });
                $orderItems = $queryItems->get();
            } catch (\Exception $e) {
                $orderItems = collect([]);
            }
        }

        if (!$order) {
            $cardTable = Schema::hasTable('orders') ? 'orders' : (Schema::hasTable('card_orders') ? 'card_orders' : null);
            if ($cardTable) {
                try {
                    $query = DB::table($cardTable)->where('id', $id);
                    $query->where(function($q) use ($cardTable, $userId) {
                        if (Schema::hasColumn($cardTable, 'customer_id')) {
                            $q->where('customer_id', $userId);
                        }
                        if (Schema::hasColumn($cardTable, 'user_id')) {
                            $q->orWhere('user_id', $userId);
                        }
                    });
                    $order = $query->first();
                } catch (\Exception $e) {
                    $order = null;
                }
            }
        }

        if (!$order) {
            abort(404, 'Order nahi mila ya aapke account se linked nahi hai.');
        }

        // Fetch Restaurant details for safe fallback
        $restaurant = null;
        if (!empty($order->user_id)) {
            $restaurant = User::find($order->user_id);
        }
        if (!$restaurant) {
            $restaurant = (object)[
                'name' => $order->customer_name ?? 'Restaurant',
                'address' => $order->delivery_address ?? 'N/A',
                'phone' => $order->customer_phone ?? ''
            ];
        }

        return view('member.orders.show', compact('order', 'orderItems', 'restaurant'));
    }
}