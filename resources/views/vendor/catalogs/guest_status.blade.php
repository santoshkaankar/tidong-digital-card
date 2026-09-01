<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ऑर्डर स्टेटस - {{ $order->table_or_room }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 500px;">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-dark text-white text-center py-3 rounded-top-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-utensils me-2 text-warning"></i> {{ $order->table_or_room }}</h5>
            <small class="text-white-50">Order #{{ $order->id }}</small>
        </div>

        <div class="card-body p-4">
            <!-- Order Status Badge -->
            <div class="text-center mb-4">
                @if($order->status == 'running')
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                        <i class="fas fa-spinner fa-spin me-1"></i> किचन में बन रहा है (Preparing)
                    </span>
                @else
                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> {{ strtoupper($order->status) }}
                    </span>
                @endif
            </div>

            <!-- Items List -->
            <h6 class="fw-bold text-uppercase small text-muted mb-3">ऑर्डर किए गए आइटम्स:</h6>
            <ul class="list-group list-group-flush mb-3">
                @foreach($items as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div>
                            <span class="fw-bold me-2">{{ $item->quantity }}x</span>
                            <span>{{ $item->item_name }}</span>
                        </div>
                        <span class="fw-bold">₹{{ number_format($item->price * $item->quantity, 2) }}</span>
                    </li>
                @endforeach
            </ul>

            <!-- Total Amount -->
            <div class="d-flex justify-content-between align-items-center pt-2 border-top fs-5 fw-bold mb-4">
                <span>कुल बिल (Total):</span>
                <span class="text-success">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>

            <!-- Action Buttons -->
            <div class="d-grid gap-2">
                <a href="{{ route('catalogs.public', $catalog->slug) }}" class="btn btn-outline-primary btn-lg fw-bold rounded-3">
                    <i class="fas fa-plus me-1"></i> और आइटम जोड़ें (+ Add Items)
                </a>

                <button onclick="window.location.reload()" class="btn btn-light btn-sm text-muted">
                    <i class="fas fa-sync-alt me-1"></i> स्टेटस अपडेट करें
                </button>

                <!-- Trigger Payment Modal -->
                <button type="button" class="btn btn-danger w-100 fw-bold py-2 rounded-3 mt-3" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-check-circle me-1"></i> खाना पूरा हुआ / भुगतान करें (Complete & Pay)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Selection Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-wallet me-2 text-warning"></i> भुगतान का तरीका चुनें</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <h6 class="text-muted mb-1">कुल भुगतान राशि:</h6>
                <h2 class="fw-bold text-success mb-4">₹{{ number_format($order->total_amount, 2) }}</h2>

                <!-- Option 1: Cash Payment -->
                <form action="{{ route('guest.order.vacate', $order->id) }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="catalog_slug" value="{{ $catalog->slug }}">
                    <input type="hidden" name="payment_mode" value="cash">
                    <input type="hidden" name="total_amount" value="{{ $order->total_amount }}">
                    <button type="submit" class="btn btn-outline-dark btn-lg w-100 fw-bold py-3 text-start d-flex align-items-center justify-content-between rounded-3">
                        <div>
                            <i class="fas fa-money-bill-wave text-success fa-lg me-2"></i> Cash Payment (नकद)
                            <div class="small text-muted fw-normal" style="font-size: 11px;">काउंटर या वेटर को नकद भुगतान करें</div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </button>
                </form>

                <!-- Option 2: Online / UPI Payment -->
                <button type="button" class="btn btn-outline-primary btn-lg w-100 fw-bold py-3 text-start d-flex align-items-center justify-content-between rounded-3" onclick="showUpiQr()">
                    <div>
                        <i class="fas fa-qrcode text-primary fa-lg me-2"></i> Online / UPI (QR Code)
                        <div class="small text-muted fw-normal" style="font-size: 11px;">GPay, PhonePe, Paytm द्वारा पे करें</div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </button>

                <!-- Restaurant/Vendor QR Code Section -->
                <div id="upiQrSection" class="mt-4 p-3 bg-light rounded-3 border" style="display: none;">
                    <h6 class="fw-bold text-dark mb-2">रेस्टोरेंट के QR कोड को स्कैन करके ₹{{ number_format($order->total_amount, 2) }} का भुगतान करें</h6>
                    
                    @php
                        $qrPath = $vendor->payment_qr ?? $vendor->upi_qr ?? $vendor->qr_code ?? null;
                        $restaurantQr = 'https://via.placeholder.com/250?text=Scan+Resturant+QR';
                        if ($qrPath) {
                            $cleanQrPath = str_replace(['public/', 'storage/'], '', $qrPath);
                            $cleanQrPath = ltrim($cleanQrPath, '/');
                            $restaurantQr = asset('storage/' . $cleanQrPath);
                        } else {
                            $upiId = $vendor->upi_id ?? $vendor->phone ?? 'restaurant@upi';
                            $restaurantQr = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa={$upiId}%26pn=" . urlencode($vendor->name ?? 'Restaurant') . "%26am={$order->total_amount}%26cu=INR";
                        }
                    @endphp

                    <div class="p-2 bg-white rounded border d-inline-block mb-2">
                        <img src="{{ $restaurantQr }}" alt="Restaurant UPI QR" class="img-fluid rounded" style="max-width: 200px;">
                    </div>
                    
                    <p class="small text-muted mb-3">भुगतान (Payment) पूरा होने के बाद नीचे दिए गए बटन पर क्लिक करें:</p>

                    <form action="{{ route('guest.order.vacate', $order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="catalog_slug" value="{{ $catalog->slug }}">
                        <input type="hidden" name="payment_mode" value="upi_online">
                        <button type="submit" class="btn btn-success w-100 fw-bold py-2 rounded-3">
                            <i class="fas fa-check me-1"></i> भुगतान कर दिया (Complete Table)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showUpiQr() {
    document.getElementById('upiQrSection').style.display = 'block';
}
</script>

</body>
</html>