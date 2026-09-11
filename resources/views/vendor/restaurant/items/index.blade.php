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

        /* Sidebar overlapping fix */
        .sidebar-area {
            width: 260px;
            flex-shrink: 0;
        }

        .main-viewport { 
            flex-grow: 1; 
            min-width: 0; 
            width: calc(100% - 260px);
        }

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
                <p class="text-muted small mb-0">Manage menu pricing, MRP, and discounted offer rates.</p>
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
                    <span>Add New Item</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
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
                            <th>MRP (₹)</th>
                            <th>Selling Price (₹)</th>
                            <th class="text-end" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $item)
                            <tr>
                                <td class="fw-bold text-muted">{{ $key + 1 }}</td>
                                <td class="fw-semibold">{{ $item->globalItem->item_name ?? $item->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->category->name ?? $item->category_name ?? 'General' }}</span></td>
                                <td>
                                    @php $type = $item->globalItem->food_type ?? $item->type ?? 'veg'; @endphp
                                    <span class="badge {{ $type == 'veg' ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $type == 'veg' ? 'text-success' : 'text-danger' }}">
                                        {{ ucfirst($type) }}
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
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam display-5 d-block mb-2 opacity-50"></i>
                                    No food items added yet. Click on "Select Global Item" or "Add New Item".
                                </td>
                            </tr>
                        @endforelse
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
                        <select name="global_item_id" class="form-select form-select-lg rounded-3 fs-6" required>
                            <option value="" selected disabled>-- Select Food Item --</option>
                            @if(isset($globalItems))
                                @foreach($globalItems as $gItem)
                                    <option value="{{ $gItem->id }}">
                                        {{ $gItem->item_name }} ({{ ucfirst($gItem->food_type ?? 'veg') }})
                                    </option>
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
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Add to Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Add Custom Item -->
<div class="modal fade" id="addCustomModal" tabindex="-1" aria-labelledby="addCustomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="addCustomModalLabel"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Add New Custom Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor.restaurant.items.store-custom') }}" method="POST">
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
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select rounded-3" required>
                            <option value="veg">Veg</option>
                            <option value="non-veg">Non-Veg</option>
                            <option value="egg">Egg</option>
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
    document.getElementById('customCategorySelect')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const categoryName = selectedOption.getAttribute('data-name') || 'General';
        document.getElementById('hiddenCategoryName').value = categoryName;
    });
</script>
</body>
</html>