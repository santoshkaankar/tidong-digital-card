@extends('member.partials.layout')

@section('content')
<div class="container-fluid py-3">
    <!-- Header Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fas fa-shopping-bag text-danger me-2"></i>Order History</h3>
            <p class="text-muted small mb-0">Aapke sabhi restaurant food orders aur visiting card subscriptions ka record.</p>
        </div>
        <a href="{{ url('/member/dashboard') }}" class="btn btn-outline-secondary rounded-3 btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-3 border-bottom pb-2" id="orderTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold rounded-3 me-2" id="food-tab" data-bs-toggle="pill" data-bs-target="#food-orders" type="button" role="tab">
                <i class="fas fa-utensils me-1"></i> Food & Restaurant Orders ({{ count($restaurantOrders) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold rounded-3 text-secondary" id="cards-tab" data-bs-toggle="pill" data-bs-target="#cards-orders" type="button" role="tab">
                <i class="fas fa-id-card me-1"></i> Visiting Cards ({{ count($cardOrders) }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="orderTabsContent">
        <!-- 1. FOOD & RESTAURANT ORDERS TAB -->
        <div class="tab-pane fade show active" id="food-orders" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">ORDER ID</th>
                                    <th>TYPE / ADDRESS</th>
                                    <th>AMOUNT</th>
                                    <th>PAYMENT</th>
                                    <th>ORDER STATUS</th>
                                    <th>DATE</th>
                                    <th class="text-end pe-3">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($restaurantOrders as $rOrder)
                                    <tr>
                                        <td class="ps-3 fw-bold">#{{ $rOrder->id }}</td>
                                        <td>
                                            <span class="badge bg-primary text-uppercase">{{ $rOrder->order_type ?? 'Delivery' }}</span>
                                            <div class="small text-muted mt-1">{{ Str::limit($rOrder->delivery_address ?? 'Takeaway/Dine-in', 30) }}</div>
                                        </td>
                                        <td class="fw-bold text-danger">₹{{ number_format($rOrder->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            @if(($rOrder->payment_status ?? 'pending') == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = strtolower($rOrder->status ?? 'pending');
                                                $badgeClass = 'bg-info text-dark';
                                                if($status == 'completed' || $status == 'delivered') $badgeClass = 'bg-success';
                                                if($status == 'cancelled') $badgeClass = 'bg-danger';
                                            @endphp
                                            <span class="badge {{ $badgeClass }} text-uppercase">{{ $rOrder->status ?? 'Processing' }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            {{ \Carbon\Carbon::parse($rOrder->created_at)->format('d M Y, h:i A') }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ url('/member/orders/' . $rOrder->id) }}" class="btn btn-sm btn-light border rounded-3">
                                                <i class="fas fa-eye text-primary"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-utensils fs-3 d-block mb-2 text-secondary"></i>
                                            Koi food order nahi mila.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. VISITING CARDS TAB -->
        <div class="tab-pane fade" id="cards-orders" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">ORDER ID</th>
                                    <th>PLAN / ITEM</th>
                                    <th>AMOUNT</th>
                                    <th>PAYMENT STATUS</th>
                                    <th>ORDER STATUS</th>
                                    <th>DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cardOrders as $cOrder)
                                    <tr>
                                        <td class="ps-3 fw-bold">#{{ $cOrder->id }}</td>
                                        <td>
                                            <div class="fw-semibold">Digital Visiting Card</div>
                                            <div class="small text-muted">Subscription / Activation</div>
                                        </td>
                                        <td class="fw-bold">₹{{ number_format($cOrder->amount ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ ucfirst($cOrder->payment_status ?? 'Pending') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ ucfirst($cOrder->status ?? 'Processing') }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            {{ \Carbon\Carbon::parse($cOrder->created_at)->format('d M Y, h:i A') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-id-card fs-3 d-block mb-2 text-secondary"></i>
                                            Koi visiting card order nahi mila.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection