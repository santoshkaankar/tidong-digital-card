<?php

namespace App\Gateways;

use Razorpay\Api\Api;

class RazorpayGateway implements PaymentGatewayInterface
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function createOrder($orderId, $amount)
    {
        return $this->api->order->create([
            'receipt'         => 'tidong_ord_' . $orderId,
            'amount'          => $amount * 100, // INR to Paisa
            'currency'        => 'INR',
            'payment_capture' => 1
        ]);
    }

    public function verifyPayment(array $paymentData)
    {
        $attributes = [
            'razorpay_order_id' => $paymentData['razorpay_order_id'],
            'razorpay_payment_id' => $paymentData['razorpay_payment_id'],
            'razorpay_signature' => $paymentData['razorpay_signature']
        ];

        $this->api->utility->verifyPaymentSignature($attributes);
        return true;
    }
}