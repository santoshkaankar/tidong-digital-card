@extends('layouts.vendor_restaurant')

@section('content')


<!-- Cash Request Wrapper -->
<div id="cash-requests-container">
    @include('vendor.restaurant.kitchen.cash_requests')
</div>

<!-- Waiter Call Wrapper -->
<div id="waiter-calls-container">
    @include('vendor.restaurant.kitchen.waiter_calls')
</div>

<!-- Active Orders Section Header -->
<div class="mb-3 d-flex align-items-center justify-content-between">
    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--text-main);">
        <i class="bi bi-list-task"></i> Active Orders List
    </h5>
    <span class="badge bg-secondary rounded-pill px-3 py-2">
        {{ $activeOrdersCount ?? 0 }} Active Orders
    </span>
</div>

<!-- Running Orders Module (Pending & Cooking Orders) -->
<div id="running-orders-container">
    @include('vendor.restaurant.kitchen.running_orders')
</div>

<!-- Order Detail Popup Modal Partial -->
@include('vendor.restaurant.kitchen.order_detail_modal')

<!-- Order Detail Popup Modal Partial -->
@include('vendor.restaurant.kitchen.tiffin')
@endsection

@push('scripts')
<!-- Dedicated Script Partials for KDS Workflows -->
@include('vendor.restaurant.kitchen.order_detail_script')
@include('vendor.restaurant.kitchen.sound_notifier', [
    'waiterCalls' => $waiterCalls ?? [],
    'cashRequests' => $cashRequests ?? []
])
@include('vendor.restaurant.kitchen.order_workflow_script')
@endpush