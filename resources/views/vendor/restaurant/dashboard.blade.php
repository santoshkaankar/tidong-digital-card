@extends('layouts.vendor_restaurant')

@section('content')
<!-- Hero Welcome Banner -->
<div class="hero-banner mb-5 p-4 p-lg-5 rounded-4 bg-primary text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="bi bi-patch-check-fill me-1 text-warning"></i> RESTAURANT MANAGEMENT SYSTEM
            </span>
            <h1 class="fw-bold display-6 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-white-50 mb-0">Here is what's happening in your restaurant operations today.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('vendor.restaurant.kitchen.screen') }}" class="btn btn-warning shadow-sm py-3 px-4 fw-semibold">
                <i class="bi bi-tv-fill me-2"></i> Open Kitchen Display System
            </a>
        </div>
    </div>
</div>

<!-- Global Metric Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">TOTAL ORDERS</span>
                <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-bag-check fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ $stats['total_requests'] ?? 0 }}</h3>
            <small class="text-success"><i class="bi bi-arrow-up-short"></i> Live Sync Active</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">ACTIVE / RUNNING</span>
                <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-lightning-charge fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ $stats['active_running'] ?? 0 }}</h3>
            <small class="text-warning"><i class="bi bi-clock"></i> In-kitchen orders</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">COMPLETED</span>
                <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ $stats['completed'] ?? 0 }}</h3>
            <small class="text-muted">Fulfilled successfully</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">TODAY'S REVENUE</span>
                <div class="p-2 rounded-3 bg-dark bg-opacity-10 text-dark">
                    <i class="bi bi-currency-rupee fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">₹{{ number_format($stats['today_collection'] ?? 0, 2) }}</h3>
            <small class="text-success"><i class="bi bi-graph-up-arrow"></i> Real-time Sales</small>
        </div>
    </div>
</div>

<!-- Quick Management Hub -->
<h5 class="fw-bold mb-4">Quick Operations Hub</h5>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 p-4 rounded-3 bg-white d-flex flex-column justify-content-between">
            <div>
                <div class="p-3 bg-primary text-white mb-4 rounded-3 d-inline-block">
                    <i class="bi bi-journal-richtext fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Menu & Catalog</h5>
                <p class="text-muted small mb-4">Configure food categories, dish pricing, inventory availability, and recipe variants.</p>
            </div>
            <a href="{{ route('vendor.restaurant.items.index') }}" class="btn btn-outline-primary w-100">
                Manage Menu Catalog <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 p-4 rounded-3 bg-white d-flex flex-column justify-content-between">
            <div>
                <div class="p-3 bg-success text-white mb-4 rounded-3 d-inline-block">
                    <i class="bi bi-qr-code-scan fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Dining Tables & QR</h5>
                <p class="text-muted small mb-4">Set up physical tables, generate high-resolution QR codes, and monitor direct customer ordering.</p>
            </div>
            <a href="{{ route('vendor.restaurant.tables.index') }}" class="btn btn-outline-success w-100">
                Configure Tables <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 p-4 rounded-3 bg-white d-flex flex-column justify-content-between">
            <div>
                <div class="p-3 bg-danger text-white mb-4 rounded-3 d-inline-block">
                    <i class="bi bi-calculator fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">POS Billing Terminal</h5>
                <p class="text-muted small mb-4">Process walk-in dining, manage takeaway bills, and issue instant Kitchen Order Tickets (KOT).</p>
            </div>
            <a href="{{ route('vendor.restaurant.pos.index') }}" class="btn btn-outline-danger w-100">
                Launch POS System <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
@endsection