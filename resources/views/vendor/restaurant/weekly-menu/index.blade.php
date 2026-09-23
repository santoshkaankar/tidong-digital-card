@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .main-viewport { margin-top: 60px; }
    .card-header-blue { background-color: #0d6efd; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: 700; }
    .card-header-dark { background-color: #212529; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: 700; }
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
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-week text-primary me-2"></i>Tiffin Menu & Catalog Manager</h4>
    </div>
    
    <div class="row g-3">
        <!-- Left Side: Create Form -->
        <div class="col-12 col-lg-5">
            <form action="{{ route('vendor.restaurant.weekly-menu.index') }}" method="POST">    
                @csrf
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header-blue d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill fs-5"></i>
                        <span>Create Meal & Day Catalog</span>
                    </div>
                    <div class="card-body p-3">
                        
                        <div class="row g-2 mb-3">
                            <!-- Meal Type -->
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Meal Type <span class="text-danger">*</span></label>
                                <select name="meal_type" class="form-select form-select-sm" required>
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch" selected>Lunch</option>
                                    <option value="dinner">Dinner</option>
                                    <option value="snacks">Snacks</option>
                                </select>
                            </div>
                            <!-- Day Selection -->
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Select Day <span class="text-danger">*</span></label>
                                <select name="day" class="form-select form-select-sm" required>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
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
                @endphp

                <div class="item-card-mini shadow-sm p-2 mb-2 bg-white rounded border">
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Left: Checkbox & Item Name -->
                        <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate me-2">
                            <input class="form-check-input item-checkbox m-0" type="checkbox" name="items[{{ $item->id }}][selected]" value="1" data-price="{{ $price }}" id="chk-item-{{ $item->id }}">
                            <label class="form-check-label fw-bold text-dark text-truncate small mb-0 w-100" for="chk-item-{{ $item->id }}" style="cursor: pointer;">
                                {{ $itemName }}
                            </label>
                        </div>

                        <!-- Right: Qty Input & MRP Price Side-by-Side -->
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <!-- Qty Input Box -->
                            <input type="number" name="items[{{ $item->id }}][qty]" value="1" min="1" class="form-control form-control-sm text-center px-1" style="width: 45px; height: 26px; font-size: 11px;">

                            <!-- MRP Price -->
                            <span class="text-success fw-bold small text-end" style="min-width: 60px;">₹{{ number_format($price, 2) }}</span>
                        </div>
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
                                        @endphp

                                        <div class="item-card-mini shadow-sm border-warning">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="{{ $custom->id }}" data-price="{{ $cPrice }}" id="chk-custom-{{ $custom->id }}">
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

                        <!-- 1. Calculated MRP Display -->
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

                        <!-- 2. Selling Price Setup -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small mb-1">Rates Setup (Selling Price) <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="number" step="0.01" name="single_day_rate" id="single_day_rate" class="form-control form-control-sm border-success" value="0.00" required>
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" name="full_week_rate" id="full_week_rate" class="form-control form-control-sm border-success" value="0.00" required>
                                </div>
                                <div class="col-4">
                                    <input type="number" step="0.01" name="full_month_rate" id="full_month_rate" class="form-control form-control-sm border-success" value="0.00" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold btn-sm py-2">
                            <i class="bi bi-floppy-fill me-1"></i> Save Tiffin Catalog
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Side: Saved Catalogs Table -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header-dark d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-task me-1"></i> Saved Tiffin Catalogs</span>
                    <span class="badge bg-secondary rounded-pill">{{ isset($tiffinCatalogs) ? count($tiffinCatalogs) : 0 }} Total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 500px;">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Catalog / Plan</th>
                                    <th>Pricing</th>
                                    <th class="pe-3 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($tiffinCatalogs) && count($tiffinCatalogs) > 0)
                                    @foreach($tiffinCatalogs as $index => $catalog)
                                        <tr>
                                            <td class="ps-3 fw-bold small text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="text-dark">{{ $catalog->title }}</strong><br>
                                                <span class="text-muted small">ID: #{{ $catalog->id }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">1D: ₹{{ $catalog->single_day_rate }}</span>
                                                <span class="badge bg-success">1W: ₹{{ $catalog->full_week_rate }}</span>
                                                <span class="badge bg-info text-dark">1M: ₹{{ $catalog->full_month_rate }}</span>
                                            </td>
                                            <td class="pe-3 text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="{{ route('vendor.restaurant.weekly-menu.show', $catalog->id) }}" class="btn btn-outline-info btn-sm" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('vendor.restaurant.weekly-menu.edit', $catalog->id) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('vendor.restaurant.weekly-menu.destroy', $catalog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this catalog?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4 small">
                                            No tiffin catalogs created yet.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let offerEdited = false;

    document.getElementById('single_day_rate').addEventListener('input', () => { offerEdited = true; });
    document.getElementById('full_week_rate').addEventListener('input', () => { offerEdited = true; });
    document.getElementById('full_month_rate').addEventListener('input', () => { offerEdited = true; });

    function selectAllItems() {
        document.querySelectorAll('.item-checkbox').forEach(c => { c.checked = true; });
        calculateRates();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-checkbox')) {
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

        document.getElementById('mrp_day_rate').value = total.toFixed(2);
        document.getElementById('mrp_week_rate').value = calcWeek.toFixed(2);
        document.getElementById('mrp_month_rate').value = calcMonth.toFixed(2);

        if (!offerEdited) {
            document.getElementById('single_day_rate').value = total.toFixed(2);
            document.getElementById('full_week_rate').value = calcWeek.toFixed(2);
            document.getElementById('full_month_rate').value = calcMonth.toFixed(2);
        }
    }
</script>
@endpush