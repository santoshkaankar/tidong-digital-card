@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .main-viewport { margin-top: 60px; }
    .filter-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
    .card-header-blue { background-color: #0d6efd; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: 700; }
    .custom-checkbox-border {
        border: 2px solid #cbd5e1 !important;
        padding: 8px 12px;
        border-radius: 6px;
        background: #fff;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .custom-checkbox-border:hover {
        border-color: #0d6efd !important;
    }
</style>
@endpush

@section('content')
<div class="main-viewport p-2 p-sm-3 p-lg-4">

    <!-- Header with Quick Navigation & QR Code -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-collection text-primary me-2"></i>Proceed Tiffin - Interactive Manager</h4>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('vendor.restaurant.proceed-tiffin.list') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-list-ul me-1"></i> Saved List
            </a>
            <a href="{{ route('vendor.restaurant.proceed-tiffin.schedule') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-calendar-range me-1"></i> Schedule & Filter
            </a>
            <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#qrModal">
                <i class="bi bi-qr-code me-1"></i> Vendor QR Code
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Step 1: Filters & Selection Form (GET) -->
    <div class="filter-card shadow-sm">
        <form method="GET" action="{{ route('vendor.restaurant.proceed-tiffin.index') }}" id="tiffinFilterForm">
            <div class="row g-3 align-items-end">
                
                <!-- Meal Types Selection -->
                <div class="col-md-5">
                    <label class="form-label fw-bold small text-dark mb-2">Select Meal Types:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <label class="custom-checkbox-border">
                            <input class="form-check-input m-0" type="checkbox" name="meal_types[]" value="breakfast" {{ in_array('breakfast', $mealTypes ?? []) ? 'checked' : '' }}>
                            <span class="small fw-semibold text-dark">Breakfast</span>
                        </label>
                        <label class="custom-checkbox-border">
                            <input class="form-check-input m-0" type="checkbox" name="meal_types[]" value="lunch" {{ in_array('lunch', $mealTypes ?? []) ? 'checked' : '' }}>
                            <span class="small fw-semibold text-dark">Lunch</span>
                        </label>
                        <label class="custom-checkbox-border">
                            <input class="form-check-input m-0" type="checkbox" name="meal_types[]" value="snacks" {{ in_array('snacks', $mealTypes ?? []) ? 'checked' : '' }}>
                            <span class="small fw-semibold text-dark">Snacks</span>
                        </label>
                        <label class="custom-checkbox-border">
                            <input class="form-check-input m-0" type="checkbox" name="meal_types[]" value="dinner" {{ in_array('dinner', $mealTypes ?? []) ? 'checked' : '' }}>
                            <span class="small fw-semibold text-dark">Dinner</span>
                        </label>
                    </div>
                </div>

                <!-- Duration Type -->
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-dark">Duration Plan:</label>
                    <select name="duration" id="durationSelect" class="form-select form-select-sm" onchange="document.getElementById('tiffinFilterForm').submit();">
                        <option value="1d" {{ ($duration ?? '') == '1d' ? 'selected' : '' }}>1 Day (1D)</option>
                        <option value="1w" {{ ($duration ?? '') == '1w' ? 'selected' : '' }}>1 Week (1W)</option>
                        <option value="1m" {{ ($duration ?? '') == '1m' ? 'selected' : '' }}>1 Month (1M)</option>
                        <option value="custom" {{ ($duration ?? '') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- Date Pickers Fixed -->
                @if(($duration ?? '1d') == '1d')
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-dark">Select Date:</label>
                        <input type="date" name="date" value="{{ $selectedDate ?? '' }}" class="form-control form-control-sm" onchange="document.getElementById('tiffinFilterForm').submit();">
                    </div>
                @endif

                @if(($duration ?? '') == '1w' or ($duration ?? '') == '1m' or ($duration ?? '') == 'custom')
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-dark">From Date:</label>
                        <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small text-dark">To Date:</label>
                        <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control form-control-sm">
                    </div>
                @endif

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter"></i> Apply
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- Step 2: Store Form (POST) -->
    @if(isset($catalogs) && count($catalogs) > 0)
        <form method="POST" action="{{ route('vendor.restaurant.proceed-tiffin.store') }}">
            @csrf
            
            <input type="hidden" name="duration" value="{{ $duration ?? '' }}">
            <input type="hidden" name="from_date" value="{{ $fromDate ?? '' }}">
            <input type="hidden" name="to_date" value="{{ $toDate ?? '' }}">
            <input type="hidden" name="date" value="{{ $selectedDate ?? '' }}">
            
            @if(isset($mealTypes) && is_array($mealTypes))
                @foreach($mealTypes as $mType)
                    <input type="hidden" name="meal_types[]" value="{{ $mType }}">
                @endforeach
            @endif

            <!-- POS Style Customer Section -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Customer Details (POS Style)</h5>
                </div>
                <div class="card-body bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Customer Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="customer_mobile" id="customer_mobile" class="form-control" placeholder="Enter 10-digit mobile number" required onkeyup="checkCustomer(this.value)">
                            <small class="text-muted">Enter Mobile number for pick/create your user account.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Enter Customer Name" required>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-list-check me-2"></i>Available Catalogs for {{ $dayName ?? '' }} (Auto-Selected)</h5>

            <div class="row g-3">
                @foreach($catalogs as $catalog)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header-blue d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 border-2" type="checkbox" name="selected_catalogs[]" value="{{ $catalog->id }}" checked style="transform: scale(1.3);">
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
                                                <strong>{{ ucfirst($catItem->meal_type) }} ({{$catItem->day }}) :</strong> 
                                                @if(is_array($catItem->item_ids))
                                                    @php
                                                        $customNames = App\Models\Restaurant\RestaurantCustomItem::whereIn('id',$catItem->item_ids)->pluck('name')->toArray();
                                                        $regNames = App\Models\Restaurant\RestaurantItem::whereIn('id',$catItem->item_ids)
                                                            ->with('globalItem')
                                                            ->get()
                                                            ->map(function($ri) {
                                                                return $ri->globalItem->item_name ?? $ri->globalItem->name ?? null;
                                                            })
                                                            ->filter()
                                                            ->toArray();
                                                        $allNames = array_merge($customNames,$regNames);
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
        <div class="alert alert-info text-center">Is date ya day ke liye koi tiffin catalog nahi mila.</div>
    @endif

</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold w-100 text-center ms-4">Scan to Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <!-- Vendor Name & Address -->
                <div class="mb-3">
                    <h4 class="fw-bold text-primary mb-1">{{ Auth::user()->name ?? 'Restaurant Hub' }}</h4>
                    <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ Auth::user()->restaurant->address ?? 'Kota, Rajasthan' }}</p>
                </div>

                <!-- English Tag -->
                <p class="fw-semibold text-dark small bg-light py-1 px-2 rounded border mb-3">Point camera for order</p>

                <!-- QR Code Image -->
                <div class="bg-white p-2 d-inline-block border rounded shadow-sm mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('vendor.restaurant.proceed-tiffin.index')) }}" alt="Vendor QR Code" class="img-fluid">
                </div>

                <!-- Link with Copy Symbol / Button -->
                <div class="input-group input-group-sm">
                    <input type="text" id="storeLinkInput" class="form-control text-center bg-white" value="{{ route('vendor.restaurant.proceed-tiffin.index') }}" readonly>
                    <button class="btn btn-outline-primary" type="button" onclick="copyStoreLink()" title="Copy Link">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
                <small id="copySuccessMsg" class="text-success fw-bold d-none mt-1" style="font-size: 11px;">Link copied to clipboard!</small>
            </div>
        </div>
    </div>
</div>

<script>
function copyStoreLink() {
    const copyText = document.getElementById("storeLinkInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(copyText.value);
    
    const msg = document.getElementById("copySuccessMsg");
    msg.classList.remove("d-none");
    setTimeout(() => {
        msg.classList.add("d-none");
    }, 2000);
}
</script>
@endsection