<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pricing - Tidong®</title>
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
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">CATALOG SETUP</li>
            <li><a href="{{ route('vendor.categories.index') }}"><i class="fas fa-tags"></i> Add Category</a></li>
            <li><a href="{{ route('vendor.inventory.index') }}"><i class="fas fa-boxes"></i> Add Item</a></li>
            <li><a href="{{ route('vendor.pricing.index') }}" class="active"><i class="fas fa-rupee-sign"></i> Add Price</a></li>
            <li><a href="{{ route('vendor.catalogs.index') }}"><i class="fas fa-book-open"></i> Catalog</a></li>
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">QR & TABLES</li>
            <li><a href="{{ route('vendor.qrcode') }}"><i class="fas fa-qrcode"></i> Table QR Code</a></li>
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">ACCOUNT</li>
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
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <h3 class="fw-bold text-dark">Manage Product Pricing</h3>
                <p class="text-muted">Update MRP and Sale Price for all items in your inventory directly.</p>
            </div>

            <div class="card-box">
                <form action="{{ route('vendor.pricing.update') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th style="width: 200px;">MRP (₹)</th>
                                    <th style="width: 200px;">Sale Price (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myInventory ?? [] as $item)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $item->item_name }}</td>
                                    <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                                    <td>
                                        <input type="number" step="0.01" name="prices[{{ $item->id }}][mrp]" value="{{ $item->mrp ?? $item->price }}" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="prices[{{ $item->id }}][sale_price]" value="{{ $item->price }}" class="form-control fw-bold text-success">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No inventory items found. Add items to inventory first!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(!empty($myInventory) && count($myInventory) > 0)
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-1"></i> Update All Prices</button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>