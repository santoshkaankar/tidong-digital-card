<?php

namespace App\Gateways;

use Illuminate\Support\Facades\Http;

class PhonePeGateway implements PaymentGatewayInterface
{
    protected $merchantId;
    protected $saltKey;
    protected $saltIndex;
    protected $baseUrl;

    public function __construct()
    {
        $this->merchantId = env('PHONEPE_MERCHANT_ID');
        $this->saltKey = env('PHONEPE_SALT_KEY');
        $this->saltIndex = env('PHONEPE_SALT_INDEX', 1);
        $this->baseUrl = env('PHONEPE_ENV', 'sandbox') === 'production' 
            ? 'https://api.phonepe.com/apis/hermes/pg/v1/pay' 
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay';
    }

    public function createOrder($orderId, $amount)
    {
        $payload = [
            'merchantId' => $this->merchantId,
            'merchantTransactionId' => 'TIDONG_' . $orderId . '_' . time(),
            'merchantUserId' => 'CUST_' . auth()->id(),
            'amount' => $amount * 100, // Amount in paise
            'redirectUrl' => route('payment.callback', ['order_id' => $orderId]),
            'redirectMode' => 'POST',
            'paymentInstrument' => [
                'type' => 'PAY_PAGE' // PhonePe Open Pay Page with GPay, Paytm, NetBanking, Cards
            ]
        ];

        $encode = base64_encode(json_encode($payload));
        $string = $encode . "/pg/v1/pay" . $this->saltKey;
        $sha256 = hash("sha256", $string);
        $finalXHeader = $sha256 . "###" . $this->saltIndex;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $finalXHeader,
        ])->post($this->baseUrl, ['request' => $encode]);

        return $response->json();
    }

    public function verifyPayment(array $paymentData)
    {
        // PhonePe Status Verification Check
        return isset($paymentData['code']) && $paymentData['code'] === 'PAYMENT_SUCCESS';
    }
}