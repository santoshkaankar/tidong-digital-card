<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background: #1e293b; color: #fff; overflow-y: auto; z-index: 1000; }
        .sidebar .brand-header { padding: 20px; font-size: 1.2rem; font-weight: bold; background: #0f172a; border-bottom: 1px solid #334155; }
        .sidebar-menu { list-style: none; padding: 15px 10px; margin: 0; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; display: flex; align-items: center; padding: 10px 15px; border-radius: 8px; transition: 0.2s; font-size: 0.95rem; }
        .sidebar-menu a:hover, .sidebar-menu a.active { color: #fff; background: #2563eb; }
        .sidebar-menu a i { margin-right: 10px; width: 20px; text-align: center; }
        .main-content { margin-left: 260px; display: flex; flex-direction: column; min-height: 100vh; }
        .top-navbar { height: 70px; background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); display: flex; align-items: center; justify-content: space-between; padding: 0 30px; }
        .content-body { padding: 30px; flex: 1; }
        .card-box { background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand-header"><i class="fas fa-store me-2 text-primary"></i> Business Panel</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('vendor.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">Catalog & Items</li>
            <li><a href="{{ route('vendor.categories.index') }}"><i class="fas fa-tags"></i> Manage Categories</a></li>
            <li><a href="{{ route('vendor.inventory.index') }}" class="active"><i class="fas fa-boxes"></i> Manage Inventory</a></li>
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">QR & Tables</li>
            <li><a href="{{ route('vendor.qrcode') }}"><i class="fas fa-qrcode"></i> Table QR Code</a></li>
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">Account</li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary me-2">{{ ucfirst(Auth::user()->role ?? 'Business') }}</span>
                <span class="text-muted small">Business Type: <strong class="text-dark">{{ ucfirst(Auth::user()->business_type ?? 'General Store') }}</strong></span>
            </div>
        </nav>

        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <h3 class="fw-bold text-dark">Manage Inventory & Items</h3>
                <p class="text-muted">Select items from the dropdown below and add them directly to your shop inventory.</p>
            </div>

            <!-- Dropdown Selection Form -->
            <div class="card-box">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-plus-circle me-2"></i> Add Item from Global Master</h5>
                
                <form action="{{ route('vendor.inventory.add') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-9">
                        <label class="form-label fw-bold text-muted small">Select Item</label>
                        <select name="global_item_ids[]" class="form-select form-select-lg" required>
                            <option value="">-- Choose an item from master catalog --</option>
                            @foreach($globalItems ?? [] as $gItem)
                                <option value="{{ $gItem->id }}">
                                    {{ $gItem->item_name }} ({{ $gItem->category }}) - ₹{{ $gItem->mrp ?? $gItem->default_price ?? 0 }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success btn-lg w-100"><i class="fas fa-check me-1"></i> Add to Inventory</button>
                    </div>
                </form>
            </div>

            <!-- My Shop Inventory List -->
            <div class="card-box">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-boxes me-2"></i> My Shop Inventory List</h5>
                <div class="table-responsive">
                    <table class="table align-middle table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Item Name</th>
                                <th>Category</th>
                                <th>MRP (₹)</th>
                                <th>Selling Price (₹)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myInventory ?? [] as $inv)
                            <tr>
                                <td class="fw-bold text-start">{{ $inv->item_name }}</td>
                                <td><span class="badge bg-secondary">{{ $inv->category }}</span></td>
                                <td><span class="text-muted text-decoration-line-through">₹{{ $inv->mrp ?? $inv->price ?? 0 }}</span></td>
                                <td class="text-success fw-bold">₹{{ $inv->price ?? 0 }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($inv->status ?? 'active') }}</span></td>
                                <td>
                                    <form action="{{ route('vendor.items.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('क्या आप इस आइटम को इन्वेंटरी से हटाना चाहते हैं?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Your inventory is empty right now. Choose an item from the dropdown above to add.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>