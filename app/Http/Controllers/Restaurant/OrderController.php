<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantCategory;
use App\Models\Restaurant\RestaurantItem;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use App\Models\Restaurant\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display Orders List
     */
    public function index()
    {
        $vendorId = Auth::id();

        $orders = RestaurantOrder::with(['items', 'table'])
            ->where('user_id', $vendorId)
            ->latest()
            ->paginate(15);

        return view('vendor.restaurant.orders.index', compact('orders'));
    }

    /**
     * Display POS / Counter Billing Page
     */
    public function posIndex()
    {
        $vendorId = Auth::id();

        $categories = RestaurantCategory::where('user_id', $vendorId)
            ->where('status', true)
            ->get();

        $items = RestaurantItem::where('user_id', $vendorId)
            ->where('is_available', true)
            ->with(['globalItem', 'category'])
            ->get();

        $tables = RestaurantTable::where('user_id', $vendorId)->get();

        return view('vendor.restaurant.pos.index', compact('categories', 'items', 'tables'));
    }

    /**
     * Store POS Order via AJAX (Includes Active Table Order Validation)
     */
    public function storePosOrder(Request $request)
    {
        $request->validate([
            'order_type'      => 'required|in:dine_in,takeaway,delivery',
            'table_id'        => 'nullable|exists:restaurant_tables,id',
            'customer_phone'  => 'nullable|string|min:10|max:15',
            'customer_name'   => 'nullable|string|max:100',
            'cart'            => 'required|array|min:1',
            'cart.*.id'       => 'required|exists:restaurant_items,id',
            'cart.*.name'     => 'required|string',
            'cart.*.price'    => 'required|numeric',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $vendorId = Auth::id();

        // Check if Table has active running order before placing a new one
        if ($request->order_type === 'dine_in' && $request->table_id) {
            $activeOrderExists = RestaurantOrder::where('table_id', $request->table_id)
                ->where('user_id', $vendorId)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('payment_status', '!=', 'paid')
                ->exists();

            if ($activeOrderExists) {
                return response()->json([
                    'success' => false,
                    'message' => __('This table already has a running order! Please complete or edit the old order first.')
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $cart = $request->cart;

            $subTotal = 0;
            foreach ($cart as $item) {
                $subTotal += ($item['price'] * $item['quantity']);
            }

            $orderNumber = 'ORD-' . strtoupper(Str::random(6)) . '-' . time();

            $order = RestaurantOrder::create([
                'user_id'         => $vendorId,
                'customer_id'     => null,
                'table_id'        => $request->order_type === 'dine_in' ? $request->table_id : null,
                'order_number'    => $orderNumber,
                'order_type'      => $request->order_type,
                'is_guest'        => true,
                'customer_name'   => $request->customer_name ?? __('Counter Customer'),
                'customer_phone'  => $request->customer_phone,
                'sub_total'       => $subTotal,
                'discount_amount' => 0.00,
                'tax_amount'      => 0.00,
                'tip_amount'      => 0.00,
                'total_amount'    => $subTotal,
                'currency_code'   => 'INR',
                'status'          => 'pending',
                'payment_status'  => 'unpaid',
                'payment_method'  => 'cash',
            ]);

            foreach ($cart as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];

                RestaurantOrderItem::create([
                    'order_id'       => $order->id,
                    'item_id'        => $item['id'],
                    'item_name'      => $item['name'],
                    'quantity'       => $item['quantity'],
                    'price'          => $item['price'],
                    'tax_amount'     => 0.00,
                    'subtotal'       => $itemSubtotal,
                    'batch_number'   => 1,
                    'kitchen_status' => 'sent_to_kitchen',
                ]);
            }

            if ($request->order_type === 'dine_in' && $request->table_id) {
                RestaurantTable::where('id', $request->table_id)
                    ->update([
                        'status'           => 'occupied',
                        'current_order_id' => $order->id
                    ]);
            }

            DB::commit();

            $upiId = Auth::user()->upi_id ?? 'merchant@upi';
            $restaurantName = Auth::user()->restaurant_name ?? Auth::user()->name ?? __('Restaurant');
            
            $upiDeepLink = "upi://pay?pa=" . rawurlencode($upiId) . "&pn=" . rawurlencode($restaurantName) . "&am={$subTotal}&cu=INR&tn=" . rawurlencode("Order #{$orderNumber}");

            $whatsappUrl = null;
            if ($request->customer_phone) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $request->customer_phone);
                $msg = __('Thank you for dining at :restaurant!', ['restaurant' => $restaurantName]) . "\n";
                $msg .= __('Order Number:') . " #{$orderNumber}\n";
                $msg .= __('Total Bill:') . " ₹" . number_format($subTotal, 2) . "\n";
                $msg .= __('Pay instantly via GPay / PhonePe / Paytm link:') . "\n" . $upiDeepLink;

                $whatsappUrl = "https://wa.me/91{$cleanPhone}?text=" . urlencode($msg);
            }

            return response()->json([
                'success'      => true,
                'message'      => __('Order placed successfully!'),
                'order_id'     => $order->id,
                'payment_link' => $upiDeepLink,
                'whatsapp_url' => $whatsappUrl
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('Failed to place order: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit Order Page
     */
    public function edit($id)
    {
        $vendorId = Auth::id();

        $order = RestaurantOrder::with(['items'])->where('user_id', $vendorId)->findOrFail($id);
        $tables = RestaurantTable::where('user_id', $vendorId)->get();
        $availableItems = RestaurantItem::where('user_id', $vendorId)
            ->where('is_available', true)
            ->with(['globalItem'])
            ->get();

        return view('vendor.restaurant.orders.edit', compact('order', 'tables', 'availableItems'));
    }

    /**
     * Update Order Data & Recalculate Totals Properly
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_type'      => 'required|in:dine_in,takeaway,delivery',
            'table_id'        => 'nullable|exists:restaurant_tables,id',
            'customer_name'   => 'nullable|string|max:100',
            'customer_phone'  => 'nullable|string|max:15',
            'status'          => 'required|in:pending,preparing,cooking,ready,served,completed,cancelled',
            'payment_status'  => 'required|in:unpaid,paid',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:restaurant_items,id',
            'items.*.price'   => 'required|numeric',
            'items.*.qty'     => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $vendorId = Auth::id();
            $order = RestaurantOrder::where('user_id', $vendorId)->findOrFail($id);

            $subTotal = 0;
            foreach ($request->items as $itemData) {
                $subTotal += ($itemData['price'] * $itemData['qty']);
            }

            $totalAmount = $subTotal + ($order->tax_amount ?? 0) + ($order->tip_amount ?? 0) - ($order->discount_amount ?? 0);

            // Clear old items and recreate updated order list
            RestaurantOrderItem::where('order_id', $order->id)->delete();

            foreach ($request->items as $itemData) {
                $itemSubtotal = $itemData['price'] * $itemData['qty'];
                
                $itemObj = RestaurantItem::with('globalItem')->find($itemData['item_id']);
                $itemName = $itemData['name'] ?? $itemObj->globalItem->item_name ?? $itemObj->name ?? __('Food Item');

                RestaurantOrderItem::create([
                    'order_id'       => $order->id,
                    'item_id'        => $itemData['item_id'],
                    'item_name'      => $itemName,
                    'quantity'       => $itemData['qty'],
                    'price'          => $itemData['price'],
                    'tax_amount'     => 0.00,
                    'subtotal'       => $itemSubtotal,
                    'batch_number'   => 1,
                    'kitchen_status' => 'sent_to_kitchen',
                ]);
            }

            // Update main order table details
            $order->update([
                'order_type'     => $request->order_type,
                'table_id'       => $request->order_type === 'dine_in' ? $request->table_id : null,
                'customer_name'  => $request->customer_name ?? __('Counter Customer'),
                'customer_phone' => $request->customer_phone,
                'status'         => $request->status,
                'payment_status' => $request->payment_status,
                'sub_total'      => $subTotal,
                'total_amount'   => $totalAmount,
            ]);

            // Free or occupy table based on state
            if ($order->table_id) {
                if ($request->status === 'completed' || $request->status === 'cancelled' || $request->payment_status === 'paid') {
                    RestaurantTable::where('id', $order->table_id)->update([
                        'status'           => 'available',
                        'current_order_id' => null
                    ]);
                } else {
                    RestaurantTable::where('id', $order->table_id)->update([
                        'status'           => 'occupied',
                        'current_order_id' => $order->id
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('vendor.restaurant.orders.index')
                             ->with('success', __('Order #:number updated successfully!', ['number' => $order->order_number]));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Update Failed: ') . $e->getMessage());
        }
    }

    /**
     * Print Receipt
     */
    public function printReceipt($id)
    {
        // Added deep relationships loading to prevent blank items
        $order = RestaurantOrder::with(['items.restaurantItem.globalItem', 'table'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('vendor.restaurant.orders.print', compact('order'));
    }
}