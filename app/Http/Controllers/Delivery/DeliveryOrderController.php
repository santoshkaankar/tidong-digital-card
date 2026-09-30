<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use App\Models\Restaurant\RestaurantOrderItem;
use App\Models\Restaurant\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryOrderController extends Controller
{
    // Live Pickups & Active Orders List
    public function index()
    {
        $deliveryBoyId = auth()->id();

        // 1. Available restaurant orders for pickup
        $availableOrders = RestaurantOrder::whereIn('status', ['ready', 'ready_for_pickup'])
            ->whereNull('delivery_boy_id')
            ->latest()
            ->get();

        // 2. Orders currently being delivered by this delivery boy
        $activeOrders = RestaurantOrder::where('delivery_boy_id', $deliveryBoyId)
            ->whereIn('status', ['accepted', 'picked_up', 'out_for_delivery'])
            ->latest()
            ->get();

        return view('delivery.orders.index', compact('availableOrders', 'activeOrders'));
    }

    // Accept Order (Pickup) & System Auto-Generate OTPs
    public function accept($id)
    {
        $order = RestaurantOrder::findOrFail($id);

        if (!in_array($order->status, ['ready', 'ready_for_pickup'])) {
            return back()->with('error', 'Order is no longer available for pickup.');
        }

        // Generate In-House 4-Digit Pickup & Delivery OTPs if not generated
        if (empty($order->pickup_otp)) {
            $order->pickup_otp = rand(1000, 9999);
        }
        if (empty($order->delivery_otp)) {
            $order->delivery_otp = rand(1000, 9999);
        }

        $order->delivery_boy_id = auth()->id();
        $order->status = 'accepted';
        $order->save();

        return back()->with('success', 'Order accepted! Proceed to pickup store.');
    }

    // 1. Verify Pickup OTP (Store -> Delivery Boy)
    public function verifyPickupOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:4'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', auth()->id())
            ->firstOrFail();

        if ($order->pickup_otp == $request->otp) {
            $order->status = 'out_for_delivery';
            $order->save();

            RestaurantOrderItem::where('order_id', $order->id)
                ->update(['kitchen_status' => 'served']);

            return response()->json([
                'success' => true,
                'message' => 'Pickup OTP matched! Order picked up successfully.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid Pickup OTP.'], 422);
    }

    // 2. Verify Delivery OTP & Auto-Update Dedicated Wallet
    public function verifyDeliveryOtp(Request $request, $id)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:4'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', auth()->id())
            ->firstOrFail();

        if ($order->delivery_otp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid Delivery OTP.'], 422);
        }

        DB::transaction(function () use ($order) {
            // A. Mark Order Delivered
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->payment_status = 'paid';
            $order->save();

            // Release table if associated
            if ($order->table_id) {
                RestaurantTable::where('id', $order->table_id)->update([
                    'status'           => 'available',
                    'current_order_id' => null
                ]);
            }

            // B. Get or Create Dedicated Delivery Wallet
            $userId = auth()->id();
            $wallet = DB::table('delivery_wallets')->where('user_id', $userId)->first();

            if (!$wallet) {
                $walletId = DB::table('delivery_wallets')->insertGetId([
                    'user_id'         => $userId,
                    'earning_balance' => 0.00,
                    'cash_in_hand'    => 0.00,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $wallet = DB::table('delivery_wallets')->where('id', $walletId)->first();
            }

            // C. Add Delivery Earning Commission
            $deliveryFee = $order->delivery_charge ?? $order->delivery_fee ?? 40.00;

            DB::table('delivery_wallets')
                ->where('user_id', $userId)
                ->increment('earning_balance', $deliveryFee);

            DB::table('delivery_wallet_transactions')->insert([
                'user_id'     => $userId,
                'order_id'    => $order->id,
                'type'        => 'earning_credit',
                'amount'      => $deliveryFee,
                'description' => "Delivery payout for Order #{$order->id}",
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // D. Check COD and Track Cash in Hand
            $paymentType = strtolower($order->payment_type ?? $order->payment_mode ?? $order->payment_method ?? '');
            if (in_array($paymentType, ['cod', 'cash'])) {
                $codAmount = $order->total_amount ?? $order->grand_total ?? 0.00;

                DB::table('delivery_wallets')
                    ->where('user_id', $userId)
                    ->increment('cash_in_hand', $codAmount);

                DB::table('delivery_wallet_transactions')->insert([
                    'user_id'     => $userId,
                    'order_id'    => $order->id,
                    'type'        => 'cod_cash_collected',
                    'amount'      => $codAmount,
                    'description' => "Cash Collected (COD) for Order #{$order->id}",
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery OTP verified successfully! Order marked delivered.'
        ]);
    }

    // Direct Complete (Backup Method)
    public function complete($id)
    {
        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', auth()->id())
            ->firstOrFail();

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'payment_status' => 'paid',
        ]);

        if ($order->table_id) {
            RestaurantTable::where('id', $order->table_id)->update([
                'status'           => 'available',
                'current_order_id' => null
            ]);
        }

        return redirect()->route('delivery.orders.history')
            ->with('success', 'Order delivered successfully!');
    }

    // Delivery History
    public function history()
    {
        $orders = RestaurantOrder::where('delivery_boy_id', auth()->id())
            ->where('status', 'delivered')
            ->latest()
            ->get();

        return view('delivery.orders.history', compact('orders'));
    }
}