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
            <!-- Live Order Status Badge -->
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
                <!-- 1. Add More Items -->
                <a href="{{ route('catalogs.public', $catalog->slug) }}" class="btn btn-outline-primary btn-lg fw-bold rounded-3">
                    <i class="fas fa-plus me-1"></i> और आइटम जोड़ें (+ Add Items)
                </a>

                <!-- 2. Refresh Status -->
                <button onclick="window.location.reload()" class="btn btn-light btn-sm text-muted">
                    <i class="fas fa-sync-alt me-1"></i> स्टेटस अपडेट करें
                </button>

                <!-- 3. Vacate Table / Complete Session -->
                <form action="{{ route('guest.order.vacate', $order->id) }}" method="POST" class="mt-3" onsubmit="return confirm('क्या आपका भोजन पूरा हो गया है? टेबल खाली की जा रही है।');">
                    @csrf
                    <input type="hidden" name="catalog_slug" value="{{ $catalog->slug }}">
                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2 rounded-3">
                        <i class="fas fa-sign-out-alt me-1"></i> टेबल खाली करें (Vacate Table)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>