<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Global Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 480px;">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="text-center mb-4">
            <h5 class="fw-bold text-primary mb-1"><i class="fas fa-shield-alt me-2"></i>Tidong Secure Payment</h5>
            <p class="text-muted small">ऑर्डर #{{ $order->id }} के लिए भुगतान का माध्यम चुनें</p>
            <div class="p-3 bg-light rounded-3 my-2">
                <span class="text-muted d-block small">कुल राशि</span>
                <h2 class="fw-bold text-dark m-0">₹{{ number_format($order->total_amount, 2) }}</h2>
            </div>
        </div>

        <!-- Payment Options Grid -->
        <div class="d-grid gap-2">
            <button onclick="startPayment('razorpay')" class="btn btn-outline-primary btn-lg text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                <div>
                    <i class="fas fa-mobile-alt me-2 text-primary"></i>
                    <strong>UPI Apps / PhonePe / GPay / Paytm</strong>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </button>

            <button onclick="startPayment('razorpay')" class="btn btn-outline-dark btn-lg text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                <div>
                    <i class="fas fa-university me-2 text-dark"></i>
                    <strong>Net Banking (All Indian Banks)</strong>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </button>

            <button onclick="startPayment('razorpay')" class="btn btn-outline-secondary btn-lg text-start d-flex align-items-center justify-content-between p-3 rounded-3">
                <div>
                    <i class="fas fa-credit-card me-2 text-secondary"></i>
                    <strong>Debit / Credit Card</strong>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
            </button>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function startPayment(gatewayType) {
    fetch("{{ route('payment.process') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ 
            order_id: "{{ $order->id }}",
            gateway: gatewayType
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'redirect') {
            window.location.href = data.url; // PhonePe direct page
            return;
        }

        // Razorpay All-in-One Checkout Modal
        var options = {
            "key": data.key,
            "amount": data.amount,
            "currency": "INR",
            "name": "Tidong Marketplace",
            "description": "Order #{{ $order->id }}",
            "order_id": data.razorpay_order_id,
            "handler": function (response) {
                fetch("{{ route('payment.callback') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(res => res.json())
                .then(resData => {
                    alert("पेमेंट सफलतापूर्वक पूरा हुआ!");
                    window.location.href = "/";
                });
            },
            "theme": { "color": "#2563eb" }
        };
        var rzp = new Razorpay(options);
        rzp.open();
    });
}
</script>
</body>
</html>