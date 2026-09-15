@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .card-header-blue {
        background-color: #0d6efd;
        color: #ffffff;
        border-radius: 8px 8px 0 0;
        padding: 12px 16px;
        font-weight: 700;
    }
    .card-header-dark {
        background-color: #212529;
        color: #ffffff;
        border-radius: 8px 8px 0 0;
        padding: 12px 16px;
        font-weight: 700;
    }
    .item-selection-box {
        max-height: 380px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        background-color: #f8fafc;
    }
    .item-card-mini {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
    }
    @media (max-width: 767.98px) {
        .header-buttons {
            width: 100%;
            justify-content: flex-start;
        }
        .header-buttons .btn {
            flex: 1;
        }
        .action-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            justify-content: flex-end;
        }
        .action-btn-group .btn, 
        .action-btn-group form {
            display: inline-block;
            margin: 0 !important;
        }
        .action-btn-group .btn {
            padding: 0.25rem 0.4rem !important;
            font-size: 0.72rem !important;
        }
        .table-responsive {
            border: 0;
        }
        .item-selection-box {
            max-height: 280px;
        }
    }
    @media print {
        body * {
            visibility: hidden !important;
        }
        #printableQrCard, #printableQrCard * {
            visibility: visible !important;
        }
        #printableQrCard {
            position: fixed !important;
            left: 50% !important;
            top: 40% !important;
            transform: translate(-50%, -50%) !important;
            width: 320px !important;
            padding: 24px !important;
            border: 2px solid #000 !important;
            border-radius: 16px !important;
            text-align: center !important;
            background: #fff !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="main-viewport p-2 p-sm-3 p-lg-4">

    <!-- Flash Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-qr-code-scan text-primary me-2"></i>Smart Catalogs</h4>
        <div class="d-flex gap-2 header-buttons">
            @if(Route::has('vendor.restaurant.dashboard'))
                <a href="{{ route('vendor.restaurant.dashboard') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            @endif
            @if(Route::has('vendor.restaurant.items.index'))
                <a href="{{ route('vendor.restaurant.items.index') }}" class="btn btn-primary btn-sm fw-semibold">
                    <i class="bi bi-box-seam me-1"></i> Inventory
                </a>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <!-- Left Column: Create Form -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header-blue d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle-fill fs-5"></i>
                    <span>Create New Catalog</span>
                </div>
                <div class="card-body p-3">
                    <form action="{{ Route::has('vendor.restaurant.menu-card.store') ? route('vendor.restaurant.menu-card.store') : (Route::has('vendor.restaurant.catalogs.store') ? route('vendor.restaurant.catalogs.store') : '#') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                Address / Table / Room No <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="table_name" class="form-control" placeholder="e.g. Table 01 or Room 102" required>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-dark small mb-0">Select Items</label>
                            @if(Route::has('vendor.restaurant.items.index'))
                                <a href="{{ route('vendor.restaurant.items.index') }}" class="small text-primary text-decoration-none fw-semibold">
                                    <i class="bi bi-pencil-square me-1"></i> Manage Inventory
                                </a>
                            @endif
                        </div>

                        <!-- Item List Box -->
                        <div class="item-selection-box mb-3">
                            @php $itemFound = false; @endphp

                            <!-- 1. Regular Menu Items -->
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $category)
                                    @if(isset($category->items) && count($category->items) > 0)
                                        @foreach($category->items as $item)
                                            @php 
                                                $itemFound = true; 
                                                $price = $item->price ?? 0;
                                                $mrp = $item->mrp ?? $item->original_price ?? null;
                                                $itemName = $item->globalItem->item_name ?? $item->globalItem->name ?? $item->item_name ?? $item->name ?? 'Menu Item';
                                                $rawImg = $item->globalItem->image ?? $item->globalItem->image_url ?? $item->image ?? $item->image_url ?? null;
                                                $imagePath = !empty($rawImg) ? (Str::startsWith($rawImg, 'http') ? $rawImg : asset('storage/' . $rawImg)) : null;
                                                $foodType = $item->globalItem->food_type ?? $item->food_type ?? 'veg';
                                            @endphp

                                            <div class="item-card-mini shadow-sm">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="{{ $item->id }}" id="chk-{{ $item->id }}" checked>
                                                    <div style="width: 40px; height: 40px; flex-shrink: 0;">
                                                        @if($imagePath)
                                                            <img src="{{ $imagePath }}" alt="{{ $itemName }}" class="rounded border" style="width: 100%; height: 100%; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 100%; height: 100%; font-size: 1rem;">
                                                                <i class="bi bi-card-image"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-truncate w-100 ms-1">
                                                        <label class="form-check-label fw-bold text-dark d-block text-truncate small mb-0" for="chk-{{ $item->id }}" style="cursor: pointer;">
                                                            <span class="me-1">
                                                                @if($foodType == 'veg' || ($item->is_veg ?? 1) == 1)
                                                                    <span style="color: #28a745; font-size: 0.85rem;">🟢</span>
                                                                @else
                                                                    <span style="color: #dc3545; font-size: 0.85rem;">🔴</span>
                                                                @endif
                                                            </span>
                                                            {{ $itemName }} <span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">Inventory</span>
                                                        </label>
                                                        <div class="small mt-1">
                                                            <span class="text-success fw-bold">₹{{ number_format($price, 2) }}</span>
                                                            @if(!empty($mrp) && $mrp > $price)
                                                                <span class="text-muted text-decoration-line-through ms-1 ms-sm-2" style="font-size: 0.78rem;">
                                                                    ₹{{ number_format($mrp, 2) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif

                            <!-- 2. Custom Items -->
                            @if(isset($customItems) && count($customItems) > 0)
                                @foreach($customItems as $custom)
                                    @php 
                                        $itemFound = true; 
                                        $cPrice = $custom->price ?? 0;
                                        $cName = $custom->name ?? $custom->item_name ?? 'Custom Item';
                                        $cRawImg = $custom->image ?? $custom->image_url ?? null;
                                        $cImagePath = !empty($cRawImg) ? (Str::startsWith($cRawImg, 'http') ? $cRawImg : asset('storage/' . $cRawImg)) : null;
                                        $cFoodType = $custom->food_type ?? 'veg';
                                    @endphp

                                    <div class="item-card-mini shadow-sm border-warning">
                                        <div class="d-flex align-items-center gap-2">
                                            <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="{{ $custom->id }}" id="chk-custom-{{ $custom->id }}" checked>
                                            <div style="width: 40px; height: 40px; flex-shrink: 0;">
                                                @if($cImagePath)
                                                    <img src="{{ $cImagePath }}" alt="{{ $cName }}" class="rounded border" style="width: 100%; height: 100%; object-fit: cover;">
                                                @else
                                                    <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 100%; height: 100%; font-size: 1rem;">
                                                        <i class="bi bi-star"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-truncate w-100 ms-1">
                                                <label class="form-check-label fw-bold text-dark d-block text-truncate small mb-0" for="chk-custom-{{ $custom->id }}" style="cursor: pointer;">
                                                    <span class="me-1">
                                                        @if($cFoodType == 'veg')
                                                            <span style="color: #28a745; font-size: 0.85rem;">🟢</span>
                                                        @else
                                                            <span style="color: #dc3545; font-size: 0.85rem;">🔴</span>
                                                        @endif
                                                    </span>
                                                    {{ $cName }} <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.65rem;">Custom</span>
                                                </label>
                                                <div class="small mt-1">
                                                    <span class="text-success fw-bold">₹{{ number_format($cPrice, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if(!$itemFound)
                                <div class="text-center text-muted py-4 small">
                                    <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                                    No active inventory or custom items found.
                                </div>
                            @endif
                        </div>

                        <div class="row g-2">
                            <div class="col-7 col-sm-8">
                                <button type="submit" class="btn btn-primary w-100 fw-bold btn-sm py-2">
                                    <i class="bi bi-floppy-fill me-1"></i> Save Catalog
                                </button>
                            </div>
                            <div class="col-5 col-sm-4">
                                <button type="button" class="btn btn-outline-secondary w-100 fw-semibold btn-sm py-2" onclick="document.querySelectorAll('.item-checkbox').forEach(c => c.checked = true)">
                                    <i class="bi bi-check2-all me-1"></i> Select All
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Catalogs List -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header-dark d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-task me-1"></i> Your Catalogs</span>
                    <span class="badge bg-secondary rounded-pill">{{ isset($tables) ? count($tables) : 0 }} Total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 580px;">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Address / Table</th>
                                    <th>Items</th>
                                    <th>Public Link</th>
                                    <th class="pe-3 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($tables) && count($tables) > 0)
                                    @foreach($tables as $index => $tbl)
                                        @php 
                                            $token = $tbl->qr_code_token ?? $tbl->token ?? 'demo';
                                            $publicUrl = Route::has('customer.restaurant.menu') ? route('customer.restaurant.menu', $token) : url('/c/' . $token);
                                            $selectedArr = $tbl->selected_items ?? [];
                                            if (is_string($selectedArr)) {
                                                $selectedArr = json_decode($selectedArr, true) ?? [];
                                            }
                                            if (!is_array($selectedArr)) {
                                                $selectedArr = [];
                                            }
                                            $itemCount = count($selectedArr);
                                            $tableName = $tbl->table_number ?? $tbl->table_name ?? $tbl->name ?? 'Table';
                                        @endphp
                                        <tr>
                                            <td class="ps-3 fw-bold small text-muted">{{ $index + 1 }}</td>
                                            <td class="fw-bold text-primary small">{{ $tableName }}</td>
                                            <td>
                                                <span class="badge bg-info text-dark font-monospace">
                                                    {{ $itemCount }} Items
                                                </span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 130px;">
                                                    <input type="text" class="form-control bg-light" value="{{ $publicUrl }}" readonly>
                                                    <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $publicUrl }}'); alert('Link Copied!');">
                                                        <i class="bi bi-copy"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="pe-3 text-end">
                                                <div class="action-btn-group">
                                                    <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline-info btn-sm" title="View Public Menu">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    
                                                    @if(Route::has('vendor.restaurant.menu-card.copy'))
                                                        <form action="{{ route('vendor.restaurant.menu-card.copy', $tbl->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning btn-sm text-dark fw-semibold" title="Copy To Next Catalog">
                                                                <i class="bi bi-files"></i> Copy
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <button type="button" class="btn btn-outline-primary btn-sm" title="Edit Catalog" onclick='openEditModal(@json($tbl->id), @json($tableName), @json($selectedArr))'>
                                                        <i class="bi bi-pencil"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" onclick='openQrModal(@json(auth()->user()->restaurant_name ?? auth()->user()->name ?? "Restaurant Name"), @json($tableName), @json(auth()->user()->address ?? "Restaurant Address"), @json($publicUrl))'>
                                                        View QR
                                                    </button>

                                                    @if(Route::has('vendor.restaurant.menu-card.destroy'))
                                                        <form action="{{ route('vendor.restaurant.menu-card.destroy', $tbl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this catalog?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4 small">
                                            <i class="bi bi-folder-x fs-3 d-block mb-1 text-secondary"></i>
                                            No catalogs created yet.
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

<!-- QR View Modal -->
<div class="modal fade" id="qrViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow text-center p-3">
            <div id="printableQrCard" class="p-2">
                <h4 class="fw-bold text-dark mb-1" id="qrRestName" style="font-family: sans-serif;">Restaurant Name</h4>
                <h6 class="text-muted mb-2 fw-semibold" id="qrModalTitle" style="font-family: sans-serif;">Table No</h6>
                <div class="my-2">
                    <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.82rem; font-weight: 600;">
                        📱 Scan to View Menu & Order
                    </span>
                </div>
                <img id="qrModalImg" src="" alt="Scan QR Code" class="img-fluid border p-2 rounded-3 my-1 mx-auto" style="max-width: 190px;">
                <p class="small text-secondary mb-0 mt-1" style="font-size: 0.72rem;">Point camera to scan digital menu</p>
                <p class="small text-muted mb-0 mt-2 px-1 fw-semibold" id="qrRestAddress" style="word-break: break-word; font-family: sans-serif;">Restaurant Address</p>
            </div>
            <div class="d-flex gap-2 justify-content-center mt-3 no-print">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print QR
                </button>
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-bold" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Catalog Modal -->
<div class="modal fade" id="editCatalogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="card-header-blue d-flex justify-content-between align-items-center">
                <span id="editModalTitle"><i class="bi bi-pencil-square me-1"></i> Edit Catalog</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCatalogForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small mb-1">
                            Address / Table / Room No <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="edit_table_name" name="table_name" class="form-control" required>
                    </div>

                    <label class="form-label fw-bold text-dark small mb-2">Select Items</label>
                    <div class="item-selection-box mb-3" style="max-height: 250px;">
                        @if(isset($categories) && count($categories) > 0)
                            @foreach($categories as $category)
                                @if(isset($category->items) && count($category->items) > 0)
                                    @foreach($category->items as $item)
                                        @php
                                            $editItemName = $item->globalItem->item_name ?? $item->globalItem->name ?? $item->item_name ?? $item->name ?? 'Menu Item';
                                        @endphp
                                        <div class="item-card-mini shadow-sm">
                                            <div class="d-flex align-items-center gap-2">
                                                <input class="form-check-input edit-item-checkbox" type="checkbox" name="items[]" value="{{ $item->id }}" id="edit-chk-{{ $item->id }}">
                                                <label class="form-check-label fw-bold text-dark small mb-0 ms-1" for="edit-chk-{{ $item->id }}">
                                                    {{ $editItemName }} (₹{{ number_format($item->price ?? 0, 2) }}) <span class="badge bg-secondary font-monospace" style="font-size: 0.65rem;">Inventory</span>
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
                                    $editCustomName = $custom->name ?? $custom->item_name ?? 'Custom Item';
                                @endphp
                                <div class="item-card-mini shadow-sm border-warning">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input edit-item-checkbox" type="checkbox" name="items[]" value="{{ $custom->id }}" id="edit-chk-custom-{{ $custom->id }}">
                                        <label class="form-check-label fw-bold text-dark small mb-0 ms-1" for="edit-chk-custom-{{ $custom->id }}">
                                            {{ $editCustomName }} (₹{{ number_format($custom->price ?? 0, 2) }}) <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.65rem;">Custom</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">Update Catalog</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('vendor.restaurant.menu-card.menu_card_script')
@endpush