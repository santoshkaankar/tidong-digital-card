@extends('member.partials.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Order Details #{{ $order->id }}</h3>
            <p class="text-muted small mb-0">Order Placed on: {{ isset($order->created_at) ? \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') : 'N/A' }}</p>
        </div>
        <a href="{{ Route::has('member.orders.index') ? route('member.orders.index') : url('/member/orders') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            &larr; Back to Orders
        </a>
    </div>

    <div class="row g-4">
        <!-- Left: Ordered Items Table -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-receipt text-danger me-2"></i>Ordered Items
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small uppercase">
                            <tr>
                                <th class="ps-4">Item Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end pe-4">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orderItems as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        {{ $item->item_name ?? $item->name ?? 'Food Item' }}
                                    </td>
                                    <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                                    <td class="text-end">₹{{ number_format($item->price ?? 0, 2) }}</td>
                                    <td class="text-end pe-4 fw-bold text-dark">₹{{ number_format($item->subtotal ?? (($item->price ?? 0) * ($item->quantity ?? 1)), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No items found for this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Order Summary & Delivery Details -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold text-dark mb-3">Order Status</h6>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Status:</span>
                    <span class="badge bg-info text-dark uppercase px-2 py-1">{{ $order->status ?? 'Pending' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Payment Status:</span>
                    <span class="badge bg-warning text-dark uppercase px-2 py-1">{{ $order->payment_status ?? 'Unpaid' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Payment Method:</span>
                    <span class="fw-bold text-dark uppercase small">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                </div>

                <hr class="my-3 border-light">

                <h6 class="fw-bold text-dark mb-2">Delivery Address</h6>
                <p class="text-secondary small mb-3">
                    {{ $order->delivery_address ?? 'N/A' }}
                </p>

                <hr class="my-3 border-light">

                <h6 class="fw-bold text-dark mb-3">Bill Breakdown</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Item Subtotal</span>
                    <span class="fw-bold text-dark">₹{{ number_format($order->sub_total ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Delivery Partner Fee</span>
                    <span class="text-success fw-bold">+ ₹{{ number_format($order->delivery_charge ?? 0, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-2">
                    <h5 class="fw-bold text-danger mb-0">Total Payable:</h5>
                    <h4 class="fw-bold text-danger mb-0">₹{{ number_format($order->total_amount ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection