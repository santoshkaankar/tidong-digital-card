@extends('member.partials.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .restaurant-header {
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .menu-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .menu-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .badge-veg {
        border: 1px solid #16a34a;
        color: #16a34a;
        font-size: 0.75rem;
    }
    .badge-nonveg {
        border: 1px solid #dc2626;
        color: #dc2626;
        font-size: 0.75rem;
    }
</style>

<div class="container-fluid py-4 px-4">

    <!-- Back Button & Breadcrumb -->
    <div class="mb-3">
        <a href="{{ route('member.restaurant.index') }}" class="btn btn-sm btn-light border text-muted rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Restaurants
        </a>
    </div>

    <!-- Restaurant Info Header -->
    <div class="restaurant-header p-4 mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 60px; height: 60px;">
                        {{ strtoupper(substr($restaurant->name ?? 'R', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ $restaurant->name ?? 'Restaurant Name' }}</h3>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ $restaurant->city ?? 'Kota' }}, {{ $restaurant->state ?? 'Rajasthan' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                    <i class="fas fa-check-circle me-1"></i> Accepting Orders
                </span>
            </div>
        </div>
    </div>

    <!-- Menu Section -->
    <div class="row">
        <!-- Categories Sidebar / Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                <h6 class="fw-bold text-dark mb-3">Categories</h6>
                <div class="nav flex-column nav-pills gap-1">
                    <button class="nav-link active text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-all">
                        All Items
                    </button>
                    @foreach($categories as $category)
                        <button class="nav-link text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu Items List -->
        <div class="col-lg-9">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                <h5 class="fw-bold text-dark mb-3">Menu Items</h5>

                <div class="row g-3">
                    @forelse($items as $item)
                        <div class="col-md-6">
                            <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @if(isset($item->item_type) && in_array($item->item_type, ['veg', 'pure_veg']))
                                            <span class="badge badge-veg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> VEG</span>
                                        @else
                                            <span class="badge badge-nonveg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> NON-VEG</span>
                                        @endif
                                        <h6 class="fw-bold text-dark mb-0">{{ $item->name }}</h6>
                                    </div>
                                    <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">
                                        {{ $item->description ?? 'Freshly prepared food item.' }}
                                    </p>
                                    <span class="fw-bold text-dark">₹{{ number_format($item->price ?? 0, 2) }}</span>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-medium">
                                        <i class="fas fa-plus me-1"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center text-muted">
                            <i class="fas fa-utensils fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">No food items added yet for this restaurant.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

</div>
@endsection