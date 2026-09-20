@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb / Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('shopping.index') }}">Shopping</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Products Catalog & Filter</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-th text-primary me-2"></i> All Products Catalog & Filter</h2>
        </div>
        <div>
            <a href="{{ route('customer.hub') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-th me-1"></i> Customer Hub
            </a>
        </div>
    </div>

    <!-- Main Module Section -->
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="mb-3">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">Feature Module: shop</span>
                </div>
                <h5 class="fw-bold text-secondary mb-3">Module Overview</h5>
                <p class="text-muted lh-lg">Advanced sidebar filtering by price range, brand, rating, category, and sorting.</p>
                <div class="alert alert-info border-0 rounded-3 mt-3">
                    <i class="fas fa-info-circle me-2"></i> Yeh file poori tarah se setup ho chuki hai. Aap isme apne database data, loops, aur controllers logic ko integrate kar sakte hain.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection