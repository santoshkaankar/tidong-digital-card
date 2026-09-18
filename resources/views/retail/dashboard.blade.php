@extends('retail.layouts.master')

@section('title', 'Retail Dashboard')

@section('content')
<!-- Hero Welcome Banner -->
<div class="hero-banner mb-4 p-4 p-lg-5 rounded-4 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="bi bi-patch-check-fill me-1 text-warning"></i> RETAIL MANAGEMENT SYSTEM
            </span>
            <h1 class="fw-bold display-6 mb-2">Welcome back, {{ Auth::user()->name ?? 'Retail Partner' }}!</h1>
            <p class="text-white-50 mb-0">Apne retail store, products aur orders ko yahan se manage karein.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('retail.shop.index') }}" class="btn btn-warning shadow-sm py-3 px-4 fw-semibold">
                <i class="bi bi-box-seam me-2"></i> Manage Products
            </a>
        </div>
    </div>
</div>

<!-- Global Metric Cards -->
<div class="row g-4 mb-5">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">TOTAL PRODUCTS</span>
                <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">24</h3>
            <small class="text-success"><i class="bi bi-arrow-up-short"></i> Active Inventory</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">TOTAL ORDERS</span>
                <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-receipt fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">12</h3>
            <small class="text-warning"><i class="bi bi-clock"></i> Lifetime orders</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">PENDING ORDERS</span>
                <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-circle fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">3</h3>
            <small class="text-danger">Needs fulfillment</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 p-3 rounded-3 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">TOTAL REVENUE</span>
                <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-currency-rupee fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1">₹18,450</h3>
            <small class="text-success"><i class="bi bi-graph-up-arrow"></i> Real-time Sales</small>
        </div>
    </div>
</div>
@endsection