<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Gateways\RazorpayGateway;
use App\Gateways\PhonePeGateway;
use App\Models\Vendor\Order;

class GlobalPaymentController extends Controller
{
    // 1. Checkout UI rendering
    public function checkout($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('payment.checkout', compact('order'));
    }

    // 2. Dynamic Gateway Switcher Logic
    public function processPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'gateway' => 'nullable|string' // razorpay OR phonepe
        ]);

        $order = Order::findOrFail($request->order_id);
        $selectedGateway = $request->gateway ?? 'razorpay';

        if ($selectedGateway === 'phonepe') {
            $phonepe = new PhonePeGateway();
            $res = $phonepe->createOrder($order->id, $order->total_amount);

            if (isset($res['data']['instrumentResponse']['redirectInfo']['url'])) {
                return response()->json([
                    'status' => 'redirect',
                    'url' => $res['data']['instrumentResponse']['redirectInfo']['url']
                ]);
            }
        }

        // Default: Razorpay Standard Checkout (All options included)
        $razorpay = new RazorpayGateway();
        $razorpayOrder = $razorpay->createOrder($order->id, $order->total_amount);

        $order->gateway_order_id = $razorpayOrder['id'];
        $order->save();

        return response()->json([
            'status' => 'modal',
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $order->total_amount * 100,
            'key' => env('RAZORPAY_KEY'),
            'order_id' => $order->id
        ]);
    }

    // 3. Callback Verification
    public function paymentCallback(Request $request)
    {
        try {
            $razorpay = new RazorpayGateway();
            $razorpay->verifyPayment($request->all());

            $order = Order::where('gateway_order_id', $request->razorpay_order_id)->firstOrFail();

            // Tidong Platform Commission Logic
            $commissionRate = 0.05; // 5% Admin Commission
            $adminCommission = $order->total_amount * $commissionRate;
            $vendorPayout = $order->total_amount - $adminCommission;

            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $request->razorpay_payment_id,
                'admin_commission' => $adminCommission,
                'vendor_payout' => $vendorPayout,
                'status' => 'kitchen'
            ]);

            return response()->json(['status' => 'success', 'message' => 'Payment Verified Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}