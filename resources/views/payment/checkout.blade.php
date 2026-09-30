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
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .payment-card {
            max-width: 460px;
            margin: 30px auto;
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }
        .payment-btn {
            text-align: left;
            padding: 14px 18px;
            font-weight: 600;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #1e293b;
            width: 100%;
        }
        .payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .qr-box {
            border: 2px dashed #3b82f6;
            padding: 12px;
            border-radius: 14px;
            background: #ffffff;
            display: inline-block;
        }
        .pulse-box {
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card payment-card p-4">
        <div class="text-center mb-3">
            <h4 class="fw-bold text-primary mb-1">
                <i class="fas fa-shield-alt me-2"></i>Tidong Secure Payment
            </h4>
            <p class="text-muted small">Order #{{ $order->id }} ke liye payment method chunein</p>
        </div>

        <div class="bg-light rounded-3 p-3 text-center mb-3 border">
            <span class="text-muted small d-block mb-1">Total Amount</span>
            <h2 class="fw-bold mb-0 text-dark">₹{{ number_format($order->total_amount, 2) }}</h2>
        </div>

        <!-- Dynamic QR Code Option -->
        <div class="text-center mb-3">
            <div class="qr-box">
                <img src="{{ $qrCodeUrl ?? $qrApiUrl }}" alt="Scan & Pay QR" style="width: 170px; height: 170px; object-fit: contain;">
            </div>
            <p class="small text-muted mt-2 mb-1">Kisi bhi UPI App se Scan karke pay karein ({{ $payeeName }})</p>
            
            <div id="autoDetectAlert" class="alert alert-info py-2 small fw-bold pulse-box my-2">
                <i class="fas fa-spinner fa-spin me-2"></i> Payment status detect ho raha hai...
            </div>

            <button onclick="confirmQrPayment()" id="qrConfirmBtn" class="btn btn-sm btn-success fw-bold w-100 py-2 rounded-3">
                <i class="fas fa-check-circle me-1"></i> Maine QR Code Se Payment Kar Diya
            </button>
        </div>

        <div class="text-center text-muted small my-2">─── YA GATEWAY SE PAY KAREIN ───</div>

        <!-- Payment Options -->
        <div class="d-grid gap-2 mt-2">
            <button onclick="handleUpiClick('{{ $upiString }}')" class="btn payment-btn">
                <span><i class="fas fa-mobile-alt me-3 text-primary"></i>UPI Apps / PhonePe / GPay / Paytm</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </button>

            <button onclick="processCheckout('netbanking')" class="btn payment-btn">
                <span><i class="fas fa-university me-3 text-secondary"></i>Net Banking (All Indian Banks)</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </button>

            <button onclick="processCheckout('card')" class="btn payment-btn">
                <span><i class="far fa-credit-card me-3 text-success"></i>Debit / Credit Card</span>
                <i class="fas fa-chevron-right text-muted"></i>
            </button>
        </div>

        <div id="loadingSpinner" class="text-center mt-3 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="small text-muted mt-2">Payment Gateway Connect Ho Raha Hai...</p>
        </div>
    </div>
</div>

<!-- Razorpay SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
const orderId = "{{ $order->id }}";
const targetOrderUrl = "{{ Route::has('member.orders.show') ? route('member.orders.show', $order->id) : '/member/orders/' . $order->id }}";

// Background Status Checker (Every 3 seconds)
let autoCheckInterval = setInterval(() => {
    fetch("/payment/check-status/" + orderId, {
        method: "GET",
        headers: {
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'paid') {
            clearInterval(autoCheckInterval);
            const alertBox = document.getElementById('autoDetectAlert');
            alertBox.className = "alert alert-success py-2 small fw-bold my-2";
            alertBox.innerHTML = '<i class="fas fa-check-circle me-1"></i> Payment Mil Gaya! Redirecting...';
            setTimeout(() => {
                window.location.href = data.redirect_url || targetOrderUrl;
            }, 1000);
        }
    })
    .catch(err => console.log('Checking payment status...'));
}, 3000);

function handleUpiClick(upiLink) {
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    if (isMobile) {
        window.location.href = upiLink;
    } else {
        alert("Desktop par direct UPI app support nahi hai. Please apne phone se QR Code scan karein.");
    }
}

function confirmQrPayment() {
    const qrBtn = document.getElementById('qrConfirmBtn');
    qrBtn.disabled = true;
    qrBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

    fetch("{{ route('payment.callback') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            order_id: orderId,
            transaction_id: "UPI_QR_" + Date.now()
        })
    })
    .then(async response => {
        const text = await response.text();
        return JSON.parse(text);
    })
    .then(data => {
        if (data.status === 'success') {
            alert('Payment Successful! Aapka order kitchen ko bhej diya gaya hai.');
            window.location.href = data.redirect_url || targetOrderUrl;
        } else {
            alert(data.message || 'Payment verify nahi ho saka.');
            qrBtn.disabled = false;
            qrBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Maine QR Code Se Payment Kar Diya';
        }
    })
    .catch(error => {
        qrBtn.disabled = false;
        qrBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Maine QR Code Se Payment Kar Diya';
        alert('Server connection error. Kripya punah prayas karein.');
    });
}

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
            order_id: orderId,
            gateway: gateway
        })
    })
    .then(async response => {
        const text = await response.text();
        const data = JSON.parse(text);
        if (!response.ok) throw new Error(data.message || 'Payment Error');
        return data;
    })
    .then(data => {
        loadingSpinner.classList.add('d-none');

        if (data.status === 'redirect' && data.url) {
            window.location.href = data.url;
        } else if (data.status === 'success') {
            alert('Payment request submit ho gayi!');
            window.location.href = data.redirect_url || targetOrderUrl;
        } else if (data.status === 'modal') {
            const options = {
                "key": data.key,
                "amount": data.amount,
                "currency": "INR",
                "name": "Tidong Platform",
                "description": "Order #" + orderId + " Payment",
                "order_id": data.razorpay_order_id,
                "handler": function (response) {
                    verifyPayment(response);
                },
                "theme": { "color": "#0d6efd" }
            };
            const rzp = new Razorpay(options);
            rzp.open();
        } else {
            alert('Payment process fail ho gaya. QR Code se pay karein.');
        }
    })
    .catch(error => {
        loadingSpinner.classList.add('d-none');
        alert('Gateway Error: ' + error.message);
    });
}

function verifyPayment(paymentData) {
    paymentData.order_id = orderId;
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
        return JSON.parse(text);
    })
    .then(data => {
        if (data.status === 'success') {
            alert('Payment Successful!');
            window.location.href = data.redirect_url || targetOrderUrl;
        } else {
            alert('Payment verification failed. Please try again.');
        }
    })
    .catch(error => {
        alert('Payment verification failed. Please try again.');
    });
}
</script>
</body>
</html>