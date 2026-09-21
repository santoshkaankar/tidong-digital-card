@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .main-viewport { margin-top: 60px; }
    .filter-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
    .card-header-blue { background-color: #0d6efd; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="main-viewport p-2 p-sm-3 p-lg-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-collection text-primary me-2"></i>Proceed Tiffin - Interactive Manager</h4>
        <a href="{{ route('vendor.restaurant.weekly-menu.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Tiffin Menu
        </a>
    </div>

    <!-- Step 1: Filters & Selection Form -->
    <div class="filter-card shadow-sm">
        <form method="GET" action="{{ route('vendor.restaurant.proceed-tiffin.index') }}" id="tiffinFilterForm">
            <div class="row g-3 align-items-end">
                
                <!-- Meal Types Selection -->
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-dark">Select Meal Types:</label>
                    <div class="d-flex gap-3 mt-1">
                        <div class="form-check">
                            <input class="form-check-input meal-checkbox" type="checkbox" name="meal_types[]" value="breakfast" id="mealBreakfast" {{ in_array('breakfast', $mealTypes) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="mealBreakfast">Breakfast</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input meal-checkbox" type="checkbox" name="meal_types[]" value="lunch" id="mealLunch" {{ in_array('lunch', $mealTypes) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="mealLunch">Lunch</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input meal-checkbox" type="checkbox" name="meal_types[]" value="dinner" id="mealDinner" {{ in_array('dinner', $mealTypes) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="mealDinner">Dinner</label>
                        </div>
                    </div>
                </div>

                <!-- Duration Type (1D, 1W, 1M, Custom) -->
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-dark">Duration Plan:</label>
                    <select name="duration" id="durationSelect" class="form-select form-select-sm" onchange="document.getElementById('tiffinFilterForm').submit();">
                        <option value="1d" {{ $duration == '1d' ? 'selected' : '' }}>1 Day (1D)</option>
                        <option value="1w" {{ $duration == '1w' ? 'selected' : '' }}>1 Week (1W)</option>
                        <option value="1m" {{ $duration == '1m' ? 'selected' : '' }}>1 Month (1M)</option>
                        <option value="custom" {{ $duration == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>

                <!-- Date Pickers based on Duration -->
                @if($duration == '1d' || $duration == 'custom')
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-dark">Select Date (Day: <span class="text-primary">{{ $dayName }}</span>):</label>
                        <input type="date" name="date" value="{{ $selectedDate }}" class="form-control form-control-sm" onchange="document.getElementById('tiffinFilterForm').submit();">
                    </div>
                @endif

                @if($duration == '1w' || $duration == '1m' || $duration == 'custom')
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-dark">From Date:</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-dark">To Date:</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="form-control form-control-sm">
                    </div>
                @endif

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- Step 2: Auto-Selected Catalogs List -->
    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-list-check me-2"></i>Available Catalogs for {{ $dayName }} (Auto-Selected)</h5>

    @if(isset($catalogs) && count($catalogs) > 0)
        <form method="POST" action="#">
            @csrf
            <div class="row g-3">
                @foreach($catalogs as $catalog)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header-blue d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Auto select checkbox -->
                                    <input class="form-check-input mt-0" type="checkbox" name="selected_catalogs[]" value="{{ $catalog->id }}" checked style="transform: scale(1.2);">
                                    <span><i class="bi bi-calendar-check me-2"></i>{{ $catalog->title }}</span>
                                </div>
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
                                
                                <div class="small text-muted fw-bold mb-1">Merged Items & Details:</div>
                                <ul class="mb-0 ps-3 small text-dark">
                                    @if(isset($catalog->items) && count($catalog->items) > 0)
                                        @foreach($catalog->items as $catItem)
                                            <li>
                                                <strong>{{ ucfirst($catItem->meal_type) }} ({{ $catItem->day }}) :</strong> 
                                                @if(is_array($catItem->item_ids))
                                                    @php
                                                        $customNames = \App\Models\Restaurant\RestaurantCustomItem::whereIn('id', $catItem->item_ids)->pluck('name')->toArray();
                                                        $regNames = \App\Models\Restaurant\RestaurantItem::whereIn('id', $catItem->item_ids)
                                                            ->with('globalItem')
                                                            ->get()
                                                            ->map(function($ri) {
                                                                return $ri->globalItem->item_name ?? $ri->globalItem->name ?? null;
                                                            })
                                                            ->filter()
                                                            ->toArray();
                                                        $allNames = array_merge($customNames, $regNames);
                                                    @endphp
                                                    {{ count($allNames) > 0 ? implode(', ', $allNames) : 'Standard Tiffin Service' }}
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

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success px-4 fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Save & Proceed Tiffin Order
                </button>
            </div>
        </form>
    @else
        <div class="alert alert-info text-center">Is date ya day ke liye koi tiffin catalog nahi mila. Pehle Tiffin Menu create karein!</div>
    @endif

</div>
@endsection