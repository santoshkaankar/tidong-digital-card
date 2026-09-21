<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Items — Restaurant Hub</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; overflow-x: hidden; }
        .dashboard-wrapper { display: flex; min-height: 100vh; width: 100%; }
        .sidebar-area { width: 260px; flex-shrink: 0; }
        .main-viewport { flex-grow: 1; min-width: 0; width: calc(100% - 260px); }
        .table-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
        .custom-table th { background-color: #f1f5f9; color: #475569; font-size: 0.75rem; padding: 1rem; }
        .custom-table td { padding: 1rem; vertical-align: middle; }

        @media (max-width: 991.98px) {
            .sidebar-area { width: 0; }
            .main-viewport { width: 100%; padding: 1rem !important; }
            .action-buttons { width: 100%; }
            .action-buttons .btn { flex: 1; text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar Wrapper -->
    <div class="sidebar-area">
        @include('vendor.restaurant.partials.sidebar')
    </div>

    <!-- Main Viewport Content -->
    <div class="main-viewport p-3 p-md-4 p-lg-5">
        
        <!-- Top Header Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">FOOD CATALOG</span>
                <h2 class="fw-bold mt-2 mb-0">Food Items Management</h2>
                <p class="text-muted small mb-0">Manage menu pricing, MRP, taxes, and custom restaurant items.</p>
            </div>

            <div class="d-flex flex-wrap gap-2 action-buttons align-items-center">
                @if(Route::has('vendor.restaurant.dashboard'))
                    <a href="{{ route('vendor.restaurant.dashboard') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span>Dashboard</span>
                    </a>
                @endif

                <button type="button" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#selectGlobalModal">
                    <i class="bi bi-list-check"></i>
                    <span>Select Global Item</span>
                </button>

                <button type="button" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCustomModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Add New Custom Item</span>
                </button>
                <a href="{{ route('vendor.restaurant.weekly-menu.index') }}" class="btn btn-success rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-2">
    <i class="bi bi-box-seam"></i>
    <span>Add Tiffin Item</span>
</a>
            </div>
        </div>
        

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Table Display -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table custom-table mb-0 align-middle" style="min-width: 700px;">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Tax</th>
                            <th>MRP (₹)</th>
                            <th>Selling Price (₹)</th>
                            <th class="text-end" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $counter = 1; @endphp

                        <!-- 1. Normal Global / Imported Items -->
                        @foreach($items as $item)
                            <tr>
                                <td class="fw-bold text-muted">{{ $counter++ }}</td>
                                <td class="fw-semibold">{{ $item->globalItem->item_name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->category->name ?? 'General' }}</span></td>
                                <td>
                                    @php $type = $item->globalItem->food_type ?? 'veg'; @endphp
                                    <span class="badge {{ $type == 'veg' ? 'bg-success' : ($type == 'non-veg' ? 'bg-danger' : 'bg-warning text-dark') }} bg-opacity-10">
                                        {{ ucfirst($type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $item->tax?->tax_name ?? 'No Tax' }} {{ isset($item->tax) ? '(' . $item->tax->tax_percentage . '%)' : '' }}
                                    </span>
                                </td>
                                <td class="text-muted">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                                <td>
                                    @if(isset($item->price) && $item->price < $item->mrp && $item->price > 0)
                                        <del class="text-muted small me-1">₹{{ number_format($item->mrp, 2) }}</del>
                                        <span class="fw-bold text-success fs-6">₹{{ number_format($item->price, 2) }}</span>
                                    @else
                                        <span class="fw-bold text-dark fs-6">₹{{ number_format($item->mrp ?? $item->price ?? 0, 2) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('vendor.restaurant.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove item from menu?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger p-1.5"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        <!-- 2. Custom Thali / Items (Restaurant Personal) -->
                        @if(isset($customItems))
                            @foreach($customItems as $custom)
                                <tr>
                                    <td class="fw-bold text-muted">{{ $counter++ }}</td>
                                    <td class="fw-semibold d-flex align-items-center gap-2">
                                        @if($custom->image)
                                            <img src="{{ asset('storage/' . $custom->image) }}" width="36" height="36" class="rounded-circle object-fit-cover border">
                                        @else
                                            <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center text-secondary" style="width: 36px; height: 36px; font-size: 14px;"><i class="bi bi-image"></i></div>
                                        @endif
                                        <div>
                                            {{ $custom->name }}
                                            <span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size: 10px;">Custom</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $custom->category->name ?? 'General' }}</span></td>
                                    <td>
                                        <span class="badge {{ $custom->type == 'veg' ? 'bg-success' : ($custom->type == 'non-veg' ? 'bg-danger' : 'bg-warning text-dark') }} bg-opacity-10">
                                            {{ ucfirst($custom->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $custom->tax?->tax_name ?? 'No Tax' }}
                                        </span>
                                    </td>
                                    <td class="text-muted">₹{{ number_format($custom->mrp ?? 0, 2) }}</td>
                                    <td>
                                        @if(isset($custom->price) && $custom->price < $custom->mrp && $custom->price > 0)
                                            <del class="text-muted small me-1">₹{{ number_format($custom->mrp, 2) }}</del>
                                            <span class="fw-bold text-success fs-6">₹{{ number_format($custom->price, 2) }}</span>
                                        @else
                                            <span class="fw-bold text-dark fs-6">₹{{ number_format($custom->mrp ?? 0, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('vendor.restaurant.custom-items.destroy', $custom->id) }}" method="POST" onsubmit="return confirm('Remove custom item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger p-1.5"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        
                        @if($items->isEmpty() && (empty($customItems) || $customItems->isEmpty()))
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam display-5 d-block mb-2 opacity-50"></i>
                                    No food items added yet. Click on "Select Global Item" or "Add New Custom Item".
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal 1: Select Global Item -->
<div class="modal fade" id="selectGlobalModal" tabindex="-1" aria-labelledby="selectGlobalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="selectGlobalModalLabel"><i class="bi bi-card-list text-primary me-2"></i>Select Global Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor.restaurant.items.select-global') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Choose Global Item</label>
                        <select name="global_item_id" id="globalItemSelect" class="form-select form-select-lg rounded-3 fs-6" required>
                            <option value="" selected disabled>-- Select Food Item --</option>
                            @if(isset($globalItems))
                                @foreach($globalItems as $gItem)
                                    <option value="{{ $gItem->id }}" 
                                            data-mrp="{{ $gItem->mrp }}" 
                                            data-tax="{{ $gItem->tax_id }}">
                                        {{ $gItem->item_name }} ({{ ucfirst($gItem->food_type ?? 'veg') }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tax Slab</label>
                        <select name="tax_id" id="globalTaxSelect" class="form-select rounded-3">
                            <option value="">-- Choose Tax (Optional) --</option>
                            @if(isset($taxes))
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->tax_name }} ({{ $tax->tax_percentage }}%)</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" id="globalMrpInput" class="form-control rounded-3" placeholder="200.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Selling Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="Optional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Add to Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Add Custom Item (Direct Popup Modal) -->
<div class="modal fade" id="addCustomModal" tabindex="-1" aria-labelledby="addCustomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="addCustomModalLabel"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Add New Custom Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor.restaurant.items.storecustom') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Item Name</label>
                        <input type="text" name="name" class="form-control form-control-lg rounded-3" placeholder="e.g. Special Paneer Tikka" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="restaurant_category_id" id="customCategorySelect" class="form-select form-select-lg rounded-3">
                            <option value="" selected>-- Select Category (Optional) --</option>
                            @if(isset($categories))
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" data-name="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" name="category_name" id="hiddenCategoryName" value="General">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type (Veg / Non-Veg / Egg)</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="veg">Veg</option>
                            <option value="non-veg">Non-Veg</option>
                            <option value="egg">Egg</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tax Slab</label>
                        <select name="tax_id" class="form-select rounded-3">
                            <option value="">-- Choose Tax (Optional) --</option>
                            @if(isset($taxes))
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->tax_name }} ({{ $tax->tax_percentage }}%)</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" class="form-control rounded-3" placeholder="200.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Selling Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control rounded-3" placeholder="Optional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Save & Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Custom Category selection handler
    document.getElementById('customCategorySelect')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const categoryName = selectedOption.getAttribute('data-name') || 'General';
        document.getElementById('hiddenCategoryName').value = categoryName;
    });

    // Global Item auto-fill handler for MRP and Tax
    document.getElementById('globalItemSelect')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        const mrp = selectedOption.getAttribute('data-mrp');
        const taxId = selectedOption.getAttribute('data-tax');

        const mrpInput = document.getElementById('globalMrpInput');
        if (mrpInput && mrp) {
            mrpInput.value = mrp;
        }

        const taxSelect = document.getElementById('globalTaxSelect');
        if (taxSelect) {
            taxSelect.value = taxId ? taxId : "";
        }
    });
</script>
</body>
</html>