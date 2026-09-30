<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use App\Models\Restaurant\RestaurantTable;
use App\Models\Payment\VendorWallet;
use App\Models\Payment\WalletTransaction;

class OrderController extends Controller
{
    private function formatOrderDetails($order)
    {
        if (!$order) return $order;

        $subTotal = floatval($order->sub_total ?? 0);
        $totalAmount = floatval($order->total_amount ?? 0);
        $taxAmount = floatval($order->tax_amount ?? 0);

        $order->cgst = property_exists($order, 'cgst') && $order->cgst !== null 
            ? floatval($order->cgst) 
            : ($taxAmount / 2);

        $order->sgst = property_exists($order, 'sgst') && $order->sgst !== null 
            ? floatval($order->sgst) 
            : ($taxAmount / 2);

        if (property_exists($order, 'delivery_charge') && $order->delivery_charge !== null) {
            $order->delivery_charge = floatval($order->delivery_charge);
        } elseif (property_exists($order, 'delivery_fee') && $order->delivery_fee !== null) {
            $order->delivery_charge = floatval($order->delivery_fee);
        } else {
            $order->delivery_charge = ($subTotal >= 999.00 || (isset($order->order_type) && $order->order_type !== 'delivery')) 
                ? 0.00 
                : max(0, $totalAmount - ($subTotal + $taxAmount));
        }

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

    /**
     * Store new order from Customer/Member Checkout
     * STRICT CHECK: Blocks DB insertion if payment method is Online/UPI and unpaid.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'       => 'required|exists:users,id',
            'order_type'      => 'required|in:dine_in,takeaway,delivery',
            'table_id'        => 'nullable|exists:restaurant_tables,id',
            'customer_phone'  => 'nullable|string|min:10|max:15',
            'customer_name'   => 'nullable|string|max:100',
            'payment_method'  => 'required|string',
            'payment_status'  => 'nullable|string|in:unpaid,paid',
            'cart'            => 'required|array|min:1',
            'cart.*.id'       => 'required',
            'cart.*.name'     => 'required|string',
            'cart.*.price'    => 'required|numeric',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $vendorId = $request->vendor_id;
        $paymentMethod = strtolower($request->payment_method ?? 'cash');
        $paymentStatus = strtolower($request->payment_status ?? (in_array($paymentMethod, ['cash', 'cod']) ? 'unpaid' : 'paid'));

        // STRICT RULE: Agar COD/Cash nahi hai aur Paid bhi nahi hua, to DB me entry nahi hogi
        if (!in_array($paymentMethod, ['cash', 'cod']) && $paymentStatus !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => __('Order cannot be created before successful payment. Please complete payment first.')
            ], 400);
        }

        DB::beginTransaction();

        try {
            $cart = $request->cart;
            $subTotal = 0;
            $totalTax = 0;
            $processedCart = [];

            foreach ($cart as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                
                $taxPercentage = 0;
                $restaurantItem = DB::table('restaurant_items')->where('id', $item['id'])->first();
                if (!$restaurantItem) {
                    $restaurantItem = DB::table('restaurant_custom_items')->where('id', $item['id'])->first();
                }
                
                if ($restaurantItem && isset($restaurantItem->tax_id) && $restaurantItem->tax_id) {
                    $taxData = DB::table('taxes')->where('id', $restaurantItem->tax_id)->first();
                    if ($taxData) {
                        $taxPercentage = $taxData->tax_percentage;
                    }
                }
                
                if ($taxPercentage > 0) {
                    $basePrice = $itemTotal / (1 + ($taxPercentage / 100));
                    $itemTax = $itemTotal - $basePrice;
                } else {
                    $basePrice = $itemTotal;
                    $itemTax = 0;
                }
                
                $subTotal += $basePrice;
                $totalTax += $itemTax;
                
                $processedCart[] = [
                    'id'         => $item['id'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'subtotal'   => $basePrice,
                    'tax_amount' => $itemTax
                ];
            }

            $totalAmount = $subTotal + $totalTax;
            $orderNumber = 'ORD-' . strtoupper(Str::random(6)) . '-' . time();

            $order = RestaurantOrder::create([
                'user_id'         => $vendorId,
                'customer_id'     => Auth::id(),
                'table_id'        => $request->order_type === 'dine_in' ? $request->table_id : null,
                'order_number'    => $orderNumber,
                'order_type'      => $request->order_type,
                'is_guest'        => Auth::check() ? false : true,
                'customer_name'   => $request->customer_name ?? (Auth::user() ? Auth::user()->name : __('Guest Customer')),
                'customer_phone'  => $request->customer_phone ?? (Auth::user() ? Auth::user()->mobile : null),
                'sub_total'       => round($subTotal, 2),
                'discount_amount' => 0.00,
                'tax_amount'      => round($totalTax, 2),
                'tip_amount'      => 0.00,
                'total_amount'    => round($totalAmount, 2),
                'currency_code'   => 'INR',
                'status'          => 'pending',
                'payment_status'  => $paymentStatus,
                'payment_method'  => $paymentMethod,
                'notes'           => $request->delivery_address ? "Delivery Address: {$request->delivery_address}" : null
            ]);

            if (class_exists(VendorWallet::class)) {
                $wallet = VendorWallet::firstOrCreate(
                    ['vendor_id' => $vendorId],
                    ['bonus_balance' => 200.00, 'sales_balance' => 0.00]
                );

                $commissionFee = max(1.00, round($totalAmount * 0.01, 2));

                if ($wallet->bonus_balance >= $commissionFee) {
                    $wallet->decrement('bonus_balance', $commissionFee);
                    $walletType = 'bonus';
                } else {
                    $wallet->decrement('sales_balance', $commissionFee);
                    $walletType = 'sales';
                }

                if (class_exists(WalletTransaction::class)) {
                    WalletTransaction::create([
                        'vendor_id'   => $vendorId,
                        'wallet_type' => $walletType,
                        'type'        => 'debit',
                        'amount'      => $commissionFee,
                        'description' => 'Order Commission Fee (1% / Min ₹1) - Order #' . $order->order_number,
                        'status'      => 'success'
                    ]);
                }
            }

            foreach ($processedCart as $item) {
                RestaurantOrderItem::create([
                    'order_id'       => $order->id,
                    'item_id'        => $item['id'],
                    'item_name'      => $item['name'],
                    'quantity'       => $item['quantity'],
                    'price'          => $item['price'],
                    'tax_amount'     => round($item['tax_amount'], 2),
                    'subtotal'       => round($item['subtotal'], 2),
                    'batch_number'   => 1,
                    'kitchen_status' => 'sent_to_kitchen',
                ]);
            }

            if ($request->order_type === 'dine_in' && $request->table_id && class_exists(RestaurantTable::class)) {
                RestaurantTable::where('id', $request->table_id)
                    ->update([
                        'status'           => 'occupied',
                        'current_order_id' => $order->id
                    ]);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => __('Order placed successfully!'),
                'order_id' => $order->id,
                'order'    => $order
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('Failed to place order: ') . $e->getMessage()
            ], 500);
        }
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

                // STRICT FIX: Hide all unpaid online/UPI payment orders from customer history
                $query->where(function($subQ) {
                    $subQ->whereIn('payment_method', ['cash', 'cod'])
                         ->orWhere(function($onlineQ) {
                             $onlineQ->whereNotIn('payment_method', ['cash', 'cod'])
                                     ->where('payment_status', 'paid');
                         });
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