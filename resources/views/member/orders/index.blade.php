@extends('member.partials.layout')

@section('content')
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container-fluid py-4 px-4">

    <!-- Active Delivery OTP Alert Top Banner -->
    @php
        $activeOtpOrder = isset($orders) ? $orders->first(function($o) {
            $st = strtolower(str_replace(' ', '_', $o->status ?? ''));
            return in_array($st, ['pickedup', 'picked_up', 'out_for_delivery']) && !empty($o->delivery_otp);
        }) : null;
    @endphp

    @if($activeOtpOrder)
        <div class="alert border-0 shadow-sm rounded-4 p-4 mb-4 text-dark" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-left: 6px solid #f97316 !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-warning text-dark rounded-circle fs-3 shadow-sm">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <span class="badge bg-danger text-white uppercase px-2.5 py-1 rounded-pill fw-bold mb-1">Out For Delivery</span>
                        <h5 class="fw-bold text-dark mb-0">Delivery OTP Code: <span class="font-mono text-danger fs-3 ms-1">{{ $activeOtpOrder->delivery_otp }}</span></h5>
                        <p class="text-muted small mb-0 mt-1">Delivery partner ko order receive karte waqt yeh code batayein (Order #{{ $activeOtpOrder->id }}).</p>
                    </div>
                </div>
                <a href="{{ route('member.orders.show', $activeOtpOrder->id) }}" class="btn btn-dark btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm">
                    View Details <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-shopping-bag text-danger"></i> Order History
            </h3>
            <p class="text-muted small mb-0">Aapke sabhi restaurant food orders aur visiting card subscriptions ka record.</p>
        </div>
        <a href="{{ url('/member/dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <!-- Order Type Tabs -->
    <div class="d-flex gap-2 mb-4">
        <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-xs d-flex align-items-center gap-2">
            <i class="fas fa-utensils"></i> Food & Restaurant Orders ({{ isset($orders) ? count($orders) : 0 }})
        </button>
        <button class="btn btn-light text-secondary rounded-pill px-4 py-2 fw-bold text-xs d-flex align-items-center gap-2 border">
            <i class="fas fa-id-card"></i> Visiting Cards (0)
        </button>
    </div>

    <!-- Orders Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted uppercase text-xs">
                    <tr>
                        <th class="ps-4 py-3">ORDER ID</th>
                        <th class="py-3">TYPE / ADDRESS</th>
                        <th class="py-3">AMOUNT</th>
                        <th class="py-3">PAYMENT</th>
                        <th class="py-3">ORDER STATUS</th>
                        <th class="py-3">DATE</th>
                        <th class="pe-4 py-3 text-end">ACTION</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($orders ?? [] as $order)
                        @php
                            $statusRaw = strtolower(trim($order->status ?? 'pending'));
                            $statusNormalized = str_replace(' ', '_', $statusRaw);

                            $statusBadge = match($statusNormalized) {
                                'pending' => 'bg-info text-white',
                                'out_for_delivery', 'pickedup', 'picked_up' => 'bg-info text-white font-bold',
                                'served', 'completed', 'delivered' => 'bg-info text-white',
                                'cancelled' => 'bg-danger text-white',
                                default => 'bg-secondary text-white',
                            };

                            // Safe Date Formatting
                            $formattedDate = 'N/A';
                            if (!empty($order->created_at)) {
                                $formattedDate = is_string($order->created_at) 
                                    ? \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') 
                                    : $order->created_at->format('d M Y, h:i A');
                            }
                        @endphp
                        <tr>
                            <!-- Order ID -->
                            <td class="ps-4 fw-bold text-dark">#{{ $order->id }}</td>

                            <!-- Type & Address -->
                            <td>
                                <span class="badge bg-primary text-white font-bold uppercase mb-1">
                                    {{ $order->type ?? 'DELIVERY' }}
                                </span>
                                <div class="text-muted small text-truncate" style="max-width: 220px;">
                                    {{ $order->delivery_address ?? 'Address Not Mentioned' }}
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="fw-bold text-danger">₹{{ number_format($order->sub_total ?? $order->total_amount ?? 0, 2) }}</td>

                            <!-- Payment Status -->
                            <td>
                                <span class="badge bg-warning text-dark font-bold uppercase px-2.5 py-1.5 rounded-2">
                                    {{ $order->payment_status ?? 'Pending' }}
                                </span>
                            </td>

                            <!-- Order Status & OTP Box -->
                            <td>
                                <div>
                                    <span class="badge {{ $statusBadge }} font-bold uppercase px-2.5 py-1.5 rounded-2">
                                        {{ strtoupper(str_replace('_', ' ', $statusNormalized)) }}
                                    </span>
                                </div>

                                <!-- OTP DISPLAY BOX -->
                                @if(in_array($statusNormalized, ['pickedup', 'picked_up', 'out_for_delivery']))
                                    @if(!empty($order->delivery_otp))
                                        <div class="mt-1 d-inline-flex align-items-center gap-1 bg-warning bg-opacity-10 border border-warning px-2 py-1 rounded-2">
                                            <i class="fas fa-key text-warning small"></i>
                                            <span class="small fw-bold text-dark">OTP:</span>
                                            <span class="fw-bold text-danger font-mono tracking-wider ms-1">{{ $order->delivery_otp }}</span>
                                        </div>
                                    @else
                                        <!-- Fallback testing view if column name is otp or delivery_code -->
                                        @php $otpVal = $order->otp ?? $order->delivery_code ?? null; @endphp
                                        @if($otpVal)
                                            <div class="mt-1 d-inline-flex align-items-center gap-1 bg-warning bg-opacity-10 border border-warning px-2 py-1 rounded-2">
                                                <i class="fas fa-key text-warning small"></i>
                                                <span class="small fw-bold text-dark">OTP:</span>
                                                <span class="fw-bold text-danger font-mono tracking-wider ms-1">{{ $otpVal }}</span>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="text-muted small">
                                {{ $formattedDate }}
                            </td>

                            <!-- View Action Button -->
                            <td class="pe-4 text-end">
                                <a href="{{ route('member.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 font-bold">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                Koi orders record nahi mile.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection