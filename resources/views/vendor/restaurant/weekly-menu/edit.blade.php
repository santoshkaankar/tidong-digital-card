@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .main-viewport { margin-top: 60px; }
    .card-header-blue { background-color: #0d6efd; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: 700; }
    .item-selection-box { max-height: 280px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background-color: #f8fafc; }
    .item-card-mini { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; margin-bottom: 6px; }
</style>
@endpush

@section('content')
<div class="main-viewport p-2 p-sm-3 p-lg-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Tiffin Catalog: {{ $catalog->title }}</h4>
        <a href="{{ route('vendor.restaurant.weekly-menu.index') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @php
        // Already selected item IDs nikaalne ke liye
        $selectedItemIds = [];
        if($catalog->items) {
            foreach($catalog->items as $ci) {
                if(is_array($ci->item_ids)) {
                    $selectedItemIds = array_merge($selectedItemIds, $ci->item_ids);
                }
            }
        }
        
        // Old meal type aur day parse karne ke liye (Title format: "Lunch - Monday")
        $parts = explode(' - ', $catalog->title);
        $currentMeal = strtolower(trim($parts[0] ?? 'lunch'));
        $currentDay = trim($parts[1] ?? 'Monday');
    @endphp

    <form action="{{ route('vendor.restaurant.weekly-menu.update', $catalog->id) }}" method="POST">     
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header-blue d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-fill fs-5"></i>
                        <span>Modify Meal & Day Catalog</span>
                    </div>
                    <div class="card-body p-3">
                        
                        <div class="row g-2 mb-3">
                            <!-- Meal Type -->
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Meal Type <span class="text-danger">*</span></label>
                                <select name="meal_type" class="form-select form-select-sm" required>
                                    <option value="breakfast" {{ $currentMeal == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                                    <option value="lunch" {{ $currentMeal == 'lunch' ? 'selected' : '' }}>Lunch</option>
                                    <option value="dinner" {{ $currentMeal == 'dinner' ? 'selected' : '' }}>Dinner</option>
                                    <option value="snacks" {{ $currentMeal == 'snacks' ? 'selected' : '' }}>Snacks</option>
                                </select>
                            </div>
                            <!-- Day Selection -->
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Select Day <span class="text-danger">*</span></label>
                                <select name="day" class="form-select form-select-sm" required>
                                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $dayName)
                                        <option value="{{ $dayName }}" {{ $currentDay == $dayName ? 'selected' : '' }}>{{ $dayName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">Select Menu Items (Multiple)</label>
                                <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size: 0.7rem;" onclick="selectAllItems()">Select All</button>
                            </div>

                            <div class="item-selection-box">
                                @php $itemFound = false; @endphp

                                @if(isset($categories) && count($categories) > 0)
                                    @foreach($categories as $category)
                                        @if(isset($category->items) && count($category->items) > 0)
                                            @foreach($category->items as $item)
                                                @php 
                                                    $itemFound = true; 
                                                    $price = $item->price ?? 0;
                                                    $itemName = $item->globalItem->item_name ?? $item->globalItem->name ?? $item->item_name ?? $item->name ?? 'Menu Item';
                                                    $isChecked = in_array($item->id, $selectedItemIds) ? 'checked' : '';
                                                @endphp

                                                <div class="item-card-mini shadow-sm">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="{{ $item->id }}" data-price="{{ $price }}" id="chk-item-{{ $item->id }}" {{ $isChecked }}>
                                                        <label class="form-check-label fw-bold text-dark d-block text-truncate small mb-0 w-100" for="chk-item-{{ $item->id }}" style="cursor: pointer;">
                                                            {{ $itemName }} <span class="text-success float-end">₹{{ number_format($price, 2) }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif

                                @if(isset($customItems) && count($customItems) > 0)
                                    @foreach($customItems as $custom)
                                        @php 
                                            $itemFound = true; 
                                            $cPrice = $custom->price ?? 0;
                                            $cName = $custom->name ?? $custom->item_name ?? 'Custom Item';
                                            $isCustomChecked = in_array($custom->id, $selectedItemIds) ? 'checked' : '';
                                        @endphp

                                        <div class="item-card-mini shadow-sm border-warning">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="{{ $custom->id }}" data-price="{{ $cPrice }}" id="chk-custom-{{ $custom->id }}" {{ $isCustomChecked }}>
                                                <label class="form-check-label fw-bold text-dark d-block text-truncate small mb-0 w-100" for="chk-custom-{{ $custom->id }}" style="cursor: pointer;">
                                                    {{ $cName }} <span class="badge bg-warning text-dark" style="font-size: 0.55rem;">Custom</span> <span class="text-success float-end">₹{{ number_format($cPrice, 2) }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                @if(!$itemFound)
                                    <div class="text-center text-muted py-3 small">No items found.</div>
                                @endif
                            </div>
                        </div>

                        <!-- 1. Calculated MRP Display (Read-only reference) -->
                        <div class="mb-2">
                            <label class="form-label fw-bold text-muted small mb-1">Calculated MRP (From Items)</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="number" step="0.01" id="mrp_day_rate" class="form-control form-control-sm bg-light" placeholder="1D MRP" readonly>
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" id="mrp_week_rate" class="form-control form-control-sm bg-light" placeholder="1W MRP" readonly>
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" id="mrp_month_rate" class="form-control form-control-sm bg-light" placeholder="1M MRP" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Selling Price Setup (Editable for Discounts) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small mb-1">Rates Setup (Selling Price) <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="number" step="0.01" name="single_day_rate" id="single_day_rate" class="form-control form-control-sm border-success" value="{{ $catalog->single_day_rate }}" required>
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" name="full_week_rate" id="full_week_rate" class="form-control form-control-sm border-success" value="{{ $catalog->full_week_rate }}" required>
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" name="full_month_rate" id="full_month_rate" class="form-control form-control-sm border-success" value="{{ $catalog->full_month_rate }}" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold btn-sm py-2">
                            <i class="bi bi-floppy-fill me-1"></i> Update Tiffin Catalog
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let offerEdited = true; // Edit page par pehle se rate bhare hain, isliye default auto-sync off rakhenge taaki user ki purani selling price overwrite na ho jab tak woh item change na kare

    document.getElementById('single_day_rate').addEventListener('input', () => { offerEdited = true; });
    document.getElementById('full_week_rate').addEventListener('input', () => { offerEdited = true; });
    document.getElementById('full_month_rate').addEventListener('input', () => { offerEdited = true; });

    function selectAllItems() {
        document.querySelectorAll('.item-checkbox').forEach(c => { c.checked = true; });
        offerEdited = false; // Select all par fresh calculation hogi
        calculateRates();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-checkbox')) {
            offerEdited = false; // Item change hone par auto update enable ho jayega
            calculateRates();
        }
    });

    function calculateRates() {
        let total = 0;
        document.querySelectorAll('.item-checkbox:checked').forEach(function(chk) {
            total += parseFloat(chk.getAttribute('data-price')) || 0;
        });

        let calcWeek = total * 7;
        let calcMonth = total * 30;

        // MRP fields update
        document.getElementById('mrp_day_rate').value = total.toFixed(2);
        document.getElementById('mrp_week_rate').value = calcWeek.toFixed(2);
        document.getElementById('mrp_month_rate').value = calcMonth.toFixed(2);

        // Agar item change kiya gaya hai tabhi selling price update hogi
        if (!offerEdited) {
            document.getElementById('single_day_rate').value = total.toFixed(2);
            document.getElementById('full_week_rate').value = calcWeek.toFixed(2);
            document.getElementById('full_month_rate').value = calcMonth.toFixed(2);
        }
    }

    // Page load hote hi MRP calculate kar lo taaki read-only boxes bhar jayein
    window.addEventListener('DOMContentLoaded', () => {
        let total = 0;
        document.querySelectorAll('.item-checkbox:checked').forEach(function(chk) {
            total += parseFloat(chk.getAttribute('data-price')) || 0;
        });
        document.getElementById('mrp_day_rate').value = total.toFixed(2);
        document.getElementById('mrp_week_rate').value = (total * 7).toFixed(2);
        document.getElementById('mrp_month_rate').value = (total * 30).toFixed(2);
    });
</script>
@endpush