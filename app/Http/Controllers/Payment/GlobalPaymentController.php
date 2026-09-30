<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Support\Facades\Route;

class GlobalPaymentController extends Controller
{
    /**
     * Checkout View Page
     */
    public function checkout($orderId)
    {
        $order = RestaurantOrder::findOrFail($orderId);

        // Merchant Details
        $upiId = "6395392537@ybl";
        $payeeName = "MEENU SHARMA";
        $amount = number_format($order->total_amount, 2, '.', '');

        // Dynamic UPI Link & QR Code
        $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($payeeName) . "&am={$amount}&cu=INR&tn=" . urlencode("Order #" . $order->id);
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upiString);

        return view('payment.checkout', compact('order', 'upiId', 'payeeName', 'qrCodeUrl', 'upiString'));
    }

    /**
     * Process Payment Request (AJAX & Standard)
     */
    public function processPayment(Request $request)
    {
        try {
            $orderId = $request->input('order_id') ?? $request->query('order_id') ?? $request->route('order_id');

            if (!$orderId) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Order ID is required.'], 400);
                }
                return redirect()->back()->with('error', 'Order ID is required.');
            }

            $order = RestaurantOrder::find($orderId);
            if (!$order) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Order not found.'], 404);
                }
                return redirect()->back()->with('error', 'Order not found.');
            }

            $selectedGateway = strtolower($request->gateway ?? $request->payment_method ?? 'online');

            // 1. CASH / COD
            if (in_array($selectedGateway, ['cash', 'cod', 'manual'])) {
                $order->update([
                    'payment_status' => 'unpaid',
                    'payment_method' => 'cash',
                    'status'         => 'sent_to_kitchen'
                ]);

                $msg = 'Order placed successfully with Cash on Delivery!';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 'success', 
                        'message' => $msg, 
                        'order_id' => $order->id,
                        'redirect_url' => $this->getRedirectUrl($order->id)
                    ]);
                }

                return $this->redirectToOrderPage($order->id, $msg);
            }

            // 2. RAZORPAY / NET BANKING / CARD
            if (in_array($selectedGateway, ['razorpay', 'card', 'netbanking'])) {
                $razorpayKey = config('services.razorpay.key', 'rzp_test_sample_key');

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status'            => 'modal',
                        'key'               => $razorpayKey,
                        'amount'            => (float)$order->total_amount * 100,
                        'razorpay_order_id' => 'order_' . $order->id . '_' . time(),
                        'order_id'          => $order->id
                    ]);
                }
            }

            // 3. ONLINE / UPI / QR Redirect
            if (in_array($selectedGateway, ['qr', 'qr_code', 'online', 'upi'])) {
                $checkoutUrl = route('payment.checkout', $order->id);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status'   => 'redirect',
                        'url'      => $checkoutUrl,
                        'order_id' => $order->id
                    ]);
                }
                return redirect()->to($checkoutUrl);
            }

            return redirect()->route('payment.checkout', $order->id);

        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Payment Callback Status Verification
     */
    public function paymentCallback(Request $request)
    {
        try {
            $orderId = $request->order_id ?? $request->merchantTransactionId;
            $order = RestaurantOrder::findOrFail($orderId);

            $commissionRate = 0.05;
            $adminCommission = $order->total_amount * $commissionRate;
            $vendorPayout = $order->total_amount - $adminCommission;

            $order->update([
                'payment_status'   => 'paid',
                'transaction_id'   => $request->transaction_id ?? $request->razorpay_payment_id ?? ('TXN_' . time()),
                'admin_commission' => $adminCommission,
                'vendor_payout'    => $vendorPayout,
                'status'           => 'sent_to_kitchen'
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Payment Successful!',
                    'redirect_url' => $this->getRedirectUrl($order->id)
                ]);
            }

            return $this->redirectToOrderPage($order->id, 'Payment Successful! Your order has been sent to the kitchen.');

        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Polling API to check payment status
     */
    public function checkStatus($orderId)
    {
        $order = RestaurantOrder::find($orderId);
        return response()->json([
            'status'       => $order ? $order->payment_status : 'pending',
            'redirect_url' => $order ? $this->getRedirectUrl($order->id) : null
        ]);
    }

    private function getRedirectUrl($orderId)
    {
        if (Route::has('member.orders.show')) {
            return route('member.orders.show', $orderId);
        } elseif (Route::has('orders.show')) {
            return route('orders.show', $orderId);
        }
        return url('/member/orders/' . $orderId);
    }

    private function redirectToOrderPage($orderId, $message)
    {
        return redirect()->to($this->getRedirectUrl($orderId))->with('success', $message);
    }
}