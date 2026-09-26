<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tidong Secure Payment</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .payment-card {
            max-width: 480px;
            margin: 40px auto;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .payment-btn {
            text-align: left;
            padding: 16px 20px;
            font-weight: 600;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .qr-box {
            border: 2px dashed #0d6efd;
            padding: 12px;
            border-radius: 12px;
            background: #fff;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card payment-card p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-primary mb-1">
                <i class="fas fa-shield-alt me-2"></i>Tidong Secure Payment
            </h4>
            <p class="text-muted small">Select payment method for Order #{{ $order->id }}</p>
        </div>

        <div class="bg-light rounded-3 p-3 text-center mb-3 border">
            <span class="text-muted small d-block mb-1">Total Amount</span>
            <h2 class="fw-bold mb-0 text-dark">₹{{ number_format($order->total_amount, 2) }}</h2>
        </div>

        @php
            $upiId = "6395392537@ybl";
            $payeeName = "MEENU SHARMA";
            $billAmount = number_format($order->total_amount, 2, '.', '');
            $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($payeeName) . "&am={$billAmount}&cu=INR&tn=" . urlencode("Order #" . $order->id);
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($upiString);
        @endphp

        <!-- Dynamic QR Code Option -->
        <div class="text-center mb-3">
            <div class="qr-box">
                <img src="{{ $qrApiUrl }}" alt="Scan & Pay QR" style="width: 170px; height: 170px; object-fit: contain;">
            </div>
            <p class="small text-muted mt-2 mb-1">Scan & Pay via Any UPI App (MEENU SHARMA)</p>
            <button onclick="processCheckout('qr')" class="btn btn-sm btn-success fw-bold w-100 py-2">
                <i class="fas fa-check-circle me-1"></i> I Have Paid via QR Code
            </button>
        </div>

        <div class="text-center text-muted small mb-3">─── OR PAY VIA GATEWAY ───</div>

        <!-- Payment Options -->
        <div class="d-grid gap-3">
            <button onclick="processCheckout('phonepe')" class="btn btn-primary payment-btn">
                <span><i class="fas fa-mobile-alt me-3"></i>UPI Apps / PhonePe / GPay / Paytm</span>
                <i class="fas fa-chevron-right"></i>
            </button>

            <button onclick="processCheckout('razorpay')" class="btn btn-outline-dark payment-btn">
                <span><i class="fas fa-university me-3"></i>Net Banking (All Indian Banks)</span>
                <i class="fas fa-chevron-right"></i>
            </button>

            <button onclick="processCheckout('razorpay')" class="btn btn-outline-dark payment-btn">
                <span><i class="far fa-credit-card me-3"></i>Debit / Credit Card</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div id="loadingSpinner" class="text-center mt-4 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="small text-muted mt-2">Processing payment request...</p>
        </div>
    </div>
</div>

<!-- Razorpay Standard Checkout SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
function processCheckout(gateway) {
    const loadingSpinner = document.getElementById('loadingSpinner');
    loadingSpinner.classList.remove('d-none');

    fetch("{{ route('payment.process') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_id: "{{ $order->id }}",
            gateway: gateway
        })
    })
    .then(async response => {
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Payment Error");
        }
        if (!response.ok) {
            throw new Error(data.message || 'Payment Error');
        }
        return data;
    })
    .then(data => {
        loadingSpinner.classList.add('d-none');

        if (data.status === 'redirect' && data.url) {
            window.location.href = data.url;
        } 
        else if (data.status === 'success') {
            alert('Payment request submit ho gaya hai! Kripya prateeksha karein.');
            window.location.href = "/";
        }
        else if (data.status === 'modal') {
            const options = {
                "key": data.key,
                "amount": data.amount,
                "currency": "INR",
                "name": "Tidong Platform",
                "description": "Order #{{ $order->id }} Payment",
                "order_id": data.razorpay_order_id,
                "handler": function (response) {
                    verifyPayment(response);
                },
                "theme": {
                    "color": "#0d6efd"
                }
            };
            const rzp = new Razorpay(options);
            rzp.open();
        } else {
            // Customer ko generic "try again" message dikhega
            alert('Payment process nahi ho saka. Kripya dubara try karein ya QR Code se pay karein.');
        }
    })
    .catch(error => {
        loadingSpinner.classList.add('d-none');
        // Technical errors ko hide karke simple friendly prompt
        alert('Payment process nahi ho saka. Kripya dubara try karein ya upper diye QR Code se scan karke pay karein.');
    });
}

function verifyPayment(paymentData) {
    fetch("{{ route('payment.callback') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(paymentData)
    })
    .then(async response => {
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error("Server Error");
        }
        if (!response.ok) {
            throw new Error(data.message || 'Verification failed');
        }
        return data;
    })
    .then(data => {
        if (data.status === 'success') {
            alert('Payment Successful!');
            window.location.href = "/";
        } else {
            alert('Payment verify nahi ho saka. Kripya dubara try karein.');
        }
    })
    .catch(error => {
        alert('Payment verify nahi ho saka. Kripya dubara try karein.');
    });
}
</script>
</body>
</html>