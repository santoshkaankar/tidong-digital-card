<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <title>Kitchen Display System (KDS)</title>
    <style>
        .kds-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .kds-sidebar {
            width: 260px;
            flex-shrink: 0;
        }
        .kds-content {
            flex-grow: 1;
            padding: 1.5rem;
            background-color: var(--bs-body-bg, #f8f9fa);
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="kds-wrapper">
    <!-- Sidebar -->
    <div class="kds-sidebar border-end">
        @include('vendor.restaurant.partials.sidebar')
    </div>

    <!-- Main Content Area -->
    <div class="kds-content">
        <!-- Top Bar -->
        @include('vendor.restaurant.kitchen.topbar')

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

        <!-- Running Orders Module (Sirf Pending aur Cooking Orders ke liye) -->
        <div id="running-orders-container">
            @include('vendor.restaurant.kitchen.running_orders')
        </div>

    </div>
</div>

<!-- Order Detail Popup Modal Partial -->
@include('vendor.restaurant.kitchen.order_detail_modal')

<!-- Core Global Scripts -->
@include('partials.scripts')

<!-- Dedicated Script Partials -->
@include('vendor.restaurant.kitchen.order_detail_script')
@include('vendor.restaurant.kitchen.sound_notifier', [
    'waiterCalls' => $waiterCalls ?? [],
    'cashRequests' => $cashRequests ?? []
])
@include('vendor.restaurant.kitchen.order_workflow_script')
</body>
</html>