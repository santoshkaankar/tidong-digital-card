<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\RestaurantOrder;

class GlobalPaymentController extends Controller
{
    /**
     * Checkout View Page / Modal Data
     */
    public function checkout($orderId)
    {
        $order = RestaurantOrder::findOrFail($orderId);

        // Merchant Details
        $upiId = "6395392537@ybl";
        $payeeName = "MEENU SHARMA";
        $amount = number_format($order->total_amount, 2, '.', '');

        // Dynamic UPI Link for automatic amount filling in UPI apps
        $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($payeeName) . "&am={$amount}&cu=INR&tn=" . urlencode("Order #" . $order->id);

        // QR Code Generator API
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upiString);

        return view('payment.checkout', compact('order', 'upiId', 'payeeName', 'qrCodeUrl'));
    }

    /**
     * Process Payment Request (AJAX)
     */
    public function processPayment(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required',
                'gateway'  => 'nullable|string'
            ]);

            $order = RestaurantOrder::find($request->order_id);
            if (!$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
            }

            $selectedGateway = $request->gateway ?? $request->payment_method ?? 'qr';

            // 1. Direct UPI QR / Manual Confirmation / Cash
            if ($selectedGateway === 'qr' || $selectedGateway === 'manual' || $selectedGateway === 'qr_code' || $selectedGateway === 'cash') {
                
                try {
                    $order->update([
                        'payment_status' => 'unpaid',
                        'status'         => 'sent_to_kitchen'
                    ]);
                } catch (\Throwable $e) {
                    $order->payment_status = 'unpaid';
                    $order->save();
                }

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Payment request submitted successfully! Your order has been sent to the kitchen.'
                ]);
            }

            // 2. PhonePe Gateway Request
            if ($selectedGateway === 'phonepe') {
                if (class_exists('App\Gateways\PhonePeGateway')) {
                    $phonepeClass = 'App\Gateways\PhonePeGateway';
                    $phonepe = new $phonepeClass();
                    $res = $phonepe->createOrder($order->id, $order->total_amount);

                    if (isset($res['data']['instrumentResponse']['redirectInfo']['url'])) {
                        return response()->json([
                            'status' => 'redirect',
                            'url'    => $res['data']['instrumentResponse']['redirectInfo']['url']
                        ]);
                    } elseif (isset($res['message'])) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'PhonePe Error: ' . $res['message']
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'PhonePe Gateway class is not available.'
                    ], 400);
                }
            }

            // 3. Razorpay Gateway Request (Fallback)
            $razorpayKey = env('RAZORPAY_KEY');
            if ($razorpayKey && $razorpayKey !== 'your_key_here' && class_exists('Razorpay\Api\Api')) {
                $razorpayClass = 'App\Gateways\RazorpayGateway';
                $razorpay = new $razorpayClass();
                $razorpayOrder = $razorpay->createOrder($order->id, $order->total_amount);

                $order->gateway_order_id = $razorpayOrder['id'] ?? null;
                $order->save();

                return response()->json([
                    'status'            => 'modal',
                    'razorpay_order_id' => $razorpayOrder['id'] ?? null,
                    'amount'            => $order->total_amount * 100,
                    'key'               => $razorpayKey,
                    'order_id'          => $order->id
                ]);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Payment gateway is currently inactive. Please pay using the UPI QR Code.'
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Payment Callback Status Verification
     */
    public function paymentCallback(Request $request)
    {
        try {
            $order = RestaurantOrder::findOrFail($request->order_id ?? $request->merchantTransactionId);

            $commissionRate = 0.05;
            $adminCommission = $order->total_amount * $commissionRate;
            $vendorPayout = $order->total_amount - $adminCommission;

            $order->update([
                'payment_status'   => 'paid',
                'transaction_id'   => $request->transaction_id ?? $request->razorpay_payment_id ?? ('TXN_' . time()),
                'admin_commission' => $adminCommission,
                'vendor_payout'     => $vendorPayout,
                'status'           => 'sent_to_kitchen'
            ]);

            return response()->json(['status' => 'success', 'message' => 'Payment Successful!']);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}