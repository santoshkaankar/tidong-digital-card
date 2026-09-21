@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .main-viewport { margin-top: 60px; }
    .card-header-blue { background-color: #0d6efd; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="main-viewport p-2 p-sm-3 p-lg-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-card-list text-primary me-2"></i>All Tiffin Catalogs List</h4>
        <a href="{{ route('vendor.restaurant.weekly-menu.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Manager
        </a>
    </div>

    @if(isset($catalogs) && count($catalogs) > 0)
        <div class="row g-3">
            @foreach($catalogs as $catalog)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header-blue d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-calendar-week me-2"></i>{{ $catalog->title }}</span>
                            <span class="badge bg-light text-dark">ID: #{{ $catalog->id }}</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <span class="fw-bold text-dark small">Pricing: </span>
                                <span class="badge bg-primary">1D: ₹{{ $catalog->single_day_rate }}</span>
                                <span class="badge bg-success">1W: ₹{{ $catalog->full_week_rate }}</span>
                                <span class="badge bg-info text-dark">1M: ₹{{ $catalog->full_month_rate }}</span>
                            </div>
                            
                            <hr class="my-2">
                            
                            <div class="small text-muted fw-bold mb-1">Included Items:</div>
                            <ul class="mb-0 ps-3 small text-dark">
                                @if(isset($catalog->items) && count($catalog->items) > 0)
                                    @foreach($catalog->items as $catItem)
                                        <li>
                                            <strong>{{ ucfirst($catItem->meal_type) }} ({{ $catItem->day }}) :</strong> 
                                            @if(isset($catItem->resolved_item_names) && count($catItem->resolved_item_names) > 0)
                                                {{ implode(', ', $catItem->resolved_item_names) }}
                                            @else
                                                Standard Tiffin Service
                                            @endif
                                        </li>
                                    @endforeach
                                @else
                                    <li>Standard Tiffin Service</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center">No tiffin catalogs found.</div>
    @endif

</div>
@endsection