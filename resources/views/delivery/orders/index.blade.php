@extends('delivery.partials.layout')

@section('content')
<div class="container py-3">

    <!-- Flash Alerts (Success / Error) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- 1. ACTIVE / ON-GOING DELIVERIES SECTION -->
    <h3 class="mb-3">On-Going Deliveries</h3>
    @forelse($activeOrders as $order)
        <div class="card my-2 p-3 border-primary shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0 font-weight-bold">Order #{{ $order->id }}</h5>
                <span class="badge bg-warning text-dark">Out for Delivery</span>
            </div>
            
            <p class="mb-1"><strong>Customer:</strong> {{ $order->customer_name ?? 'N/A' }}</p>
            <p class="mb-2"><strong>Phone:</strong> {{ $order->customer_phone ?? 'N/A' }}</p>

            <!-- OTP & COD Complete Form -->
            <form action="{{ route('delivery.orders.complete', $order->id) }}" method="POST" class="bg-light p-3 rounded border mt-2">
                @csrf
                
                <!-- 1. Customer OTP Input -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Customer Delivery OTP</label>
                    <input type="text" name="delivery_otp" maxlength="4" placeholder="Enter 4-digit OTP" required class="form-control text-center font-monospace fs-5">
                </div>

                <!-- 2. COD Amount & Checkbox -->
                @if(strtolower($order->payment_method ?? 'cod') === 'cod')
                    <div class="p-2 mb-3 bg-warning-subtle border border-warning rounded d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block text-dark font-weight-bold">Collect Cash:</span>
                            <span class="fs-5 text-success font-weight-bold">₹{{ number_format($order->sub_total ?? $order->total_amount ?? 0, 2) }}</span>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cash_collected" value="1" id="cashCheck{{ $order->id }}" required>
                            <label class="form-check-label font-weight-bold" for="cashCheck{{ $order->id }}">
                                Cash Received
                            </label>
                        </div>
                    </div>
                @endif

                <button type="submit" class="btn btn-success w-100 font-weight-bold">
                    Confirm & Complete Delivery
                </button>
            </form>
        </div>
    @empty
        <p class="text-muted">No active deliveries at the moment.</p>
    @endforelse

    <hr class="my-4">

    <!-- 2. AVAILABLE ORDERS SECTION -->
    <h3 class="mb-3">Available Orders</h3>
    @forelse($availableOrders as $order)
        <div class="card my-2 p-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Order #{{ $order->id }} - {{ $order->business_type ?? 'Restaurant' }}</h5>
                    <small class="text-muted">Delivery Fee: ₹{{ number_format($order->delivery_fee ?? 0, 2) }}</small>
                </div>
                <form action="{{ route('delivery.orders.accept', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Accept Order</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-muted">No orders available right now.</p>
    @endforelse

</div>
@endsection