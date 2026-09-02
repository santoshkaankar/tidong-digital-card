<?php

namespace App\Gateways;

interface PaymentGatewayInterface
{
    public function createOrder($orderId, $amount);
    public function verifyPayment(array $paymentData);
}