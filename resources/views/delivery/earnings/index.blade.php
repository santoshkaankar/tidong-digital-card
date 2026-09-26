@extends('delivery.partials.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">My Earnings</h3>
    </div>

    <!-- Earnings Summary Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <span class="text-muted fw-semibold">Total Earnings</span>
                    <h2 class="text-success fw-bold mt-2 mb-0">₹{{ number_format($totalEarnings ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Earnings List -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">Completed Order Earnings</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th class="text-end">Delivery Fee</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders ?? [] as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    +₹{{ number_format($order->delivery_fee, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    Abhi tak koi completed earning record nahi hai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection