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

        // Fetch active categories
        $categories = RestaurantCategory::where('user_id', $vendorId)
            ->where('status', true)
            ->get();

        // Fetch available items with relationship to get global item name
        $items = RestaurantItem::where('user_id', $vendorId)
            ->where('is_available', true)
            ->with(['globalItem', 'category'])
            ->get();

        // Fetch restaurant tables
        $tables = RestaurantTable::where('user_id', $vendorId)->get();

        return view('vendor.restaurant.pos.index', compact('categories', 'items', 'tables'));
    }

    /**
     * Store POS Order via AJAX
     */
    public function storePosOrder(Request $request)
    {
        $request->validate([
            'order_type'      => 'required|in:dine_in,takeaway,delivery',
            'table_id'        => 'nullable|exists:restaurant_tables,id',
            'cart'            => 'required|array|min:1',
            'cart.*.id'       => 'required|exists:restaurant_items,id',
            'cart.*.name'     => 'required|string',
            'cart.*.price'    => 'required|numeric',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $vendorId = Auth::id();
            $cart = $request->cart;

            // Calculate Subtotal
            $subTotal = 0;
            foreach ($cart as $item) {
                $subTotal += ($item['price'] * $item['quantity']);
            }

            // Generate Order Number
            $orderNumber = 'ORD-' . strtoupper(Str::random(6)) . '-' . time();

            // Create Order
            $order = RestaurantOrder::create([
                'user_id'         => $vendorId,
                'customer_id'     => null,
                'table_id'        => $request->order_type === 'dine_in' ? $request->table_id : null,
                'order_number'    => $orderNumber,
                'order_type'      => $request->order_type,
                'is_guest'        => true,
                'customer_name'   => 'Counter Customer',
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

            // Create Order Items
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

            // Update Table Status if Dine-In
            if ($request->order_type === 'dine_in' && $request->table_id) {
                RestaurantTable::where('id', $request->table_id)
                    ->update([
                        'status'           => 'occupied',
                        'current_order_id' => $order->id
                    ]);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Order placed successfully!',
                'order_id' => $order->id
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print Receipt
     */
    public function printReceipt($id)
    {
        $order = RestaurantOrder::with(['items', 'table'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('vendor.restaurant.orders.print', compact('order'));
    }
}