@extends('member.partials.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .restaurant-header { background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); border: 1px solid #e2e8f0; border-radius: 16px; }
    .menu-item-card { border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease; background: #ffffff; }
    .menu-item-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .badge-veg { border: 1px solid #16a34a; color: #16a34a; font-size: 0.75rem; }
    .badge-nonveg { border: 1px solid #dc2626; color: #dc2626; font-size: 0.75rem; }
    .badge-thali { background: #fef3c7; color: #d97706; font-weight: bold; font-size: 0.75rem; border: 1px solid #f59e0b; }
    .badge-tiffin { background: #e0e7ff; color: #4338ca; font-weight: bold; font-size: 0.75rem; border: 1px solid #6366f1; }
    
    .floating-order-bar { position: fixed; bottom: 15px; left: 50%; transform: translateX(-50%); width: 92%; max-width: 600px; background: #ffffff; border-radius: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); z-index: 1050; padding: 10px 20px; }
    .mobile-category-scroll { display: flex; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch; scrollbar-width: none; padding-bottom: 4px; }
    .mobile-category-scroll::-webkit-scrollbar { display: none; }

    @media (max-width: 575.98px) {
        .floating-order-bar { padding: 8px 16px; }
        .item-details-container { max-width: 170px !important; }
    }
</style>

<div class="container-fluid py-3 px-2 px-md-4 pb-5">

    <!-- 1. Header Section -->
    @include('member.restaurant.partials.show-header')

    <!-- 2. Main Content Grid -->
    <div class="row g-3">
        <!-- Categories Sidebar / Top Navigation -->
        <div class="col-12 col-lg-3">
            @include('member.restaurant.partials.show-categories')
        </div>

        <!-- Food Items Tab Content -->
        <div class="col-12 col-lg-9">
            @include('member.restaurant.partials.show-items')
        </div>
    </div>

</div>

<!-- 3. Floating Bottom Order Bar -->
<div id="floatingOrderBar" class="floating-order-bar d-none">
    <div class="d-flex justify-content-between align-items-center gap-2">
        <div>
            <span class="fw-bold text-muted small d-block" id="selectedItemsCount">0 Items Selected</span>
            <span class="fw-bold text-success fs-6 fs-md-5" id="selectedTotalAmount">₹0.00</span>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-md-md rounded-pill px-3 px-md-4 py-2 fw-bold shadow-sm" onclick="submitLiveOrder()">
            Send Order <i class="fas fa-arrow-right ms-1"></i>
        </button>
    </div>
</div>

<!-- 4. Modals (Tiffin Booking & Delivery Address) -->
@include('member.restaurant.partials.show-modals')

@endsection

@push('scripts')
    <!-- 5. JavaScript Handler -->
    @include('member.restaurant.partials.show-scripts')
@endpush