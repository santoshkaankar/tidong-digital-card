<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $menu->business_name }} - Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">
    <div class="container mt-4" style="max-width: 600px;">
        <div class="text-center mb-4">
            <h2>{{ $menu->business_name }}</h2>
            <span class="badge bg-danger fs-6">Location: {{ $location }}</span>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Running Order Details if any -->
        @if($runningOrder)
            <div class="card border-warning mb-4 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span><strong>Active Running Order</strong></span>
                    <span>Total: ₹{{ $runningOrder->total_amount }}</span>
                </div>
                <div class="card-body">
                    <ul class="mb-3">
                        @foreach($runningOrder->orderItems as $oItem)
                            <li>{{ $oItem->item_name }} (Qty: {{ $oItem->quantity }}) - ₹{{ $oItem->price }}</li>
                        @endforeach
                    </ul>
                    
                    <!-- Complete Order & Payment Option Button -->
                    <form action="{{ route('order.complete', $runningOrder->id) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <select name="payment_mode" class="form-control" required>
                            <option value="">Select Payment Method for Bill</option>
                            <option value="cash">Cash (Give to staff)</option>
                            <option value="online">Online Payment (UPI/QR)</option>
                        </select>
                        <button type="submit" class="btn btn-success text-nowrap">Complete & Pay</button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Menu Items Form to Order -->
        <form action="{{ route('menu.order', $menu->id) }}" method="POST">
            @csrf
            <input type="hidden" name="location" value="{{ $location }}">

            <div class="list-group shadow-sm mb-4">
                @foreach($menu->items as $item)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-1">{{ $item->item_name }}</h5>
                            @if($item->description)
                                <p class="mb-1 text-muted small">{{ $item->description }}</p>
                            @endif
                            <span class="text-success fw-bold">₹{{ $item->price }}</span>
                        </div>
                        <div style="width: 100px;">
                            <input type="number" name="items[{{ $item->id }}]" value="0" min="0" class="form-control text-center">
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-5 shadow">Send Order via WhatsApp 🚀</button>
        </form>
    </div>
</body>
</html>