<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Tidong®</title>
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
            <li><a href="{{ route('vendor.categories.index') }}" class="active"><i class="fas fa-tags"></i> Manage Categories</a></li>
            <li><a href="{{ route('vendor.inventory.index') }}"><i class="fas fa-boxes"></i> Manage Inventory</a></li>
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
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <h3 class="fw-bold text-dark">Manage Operating Categories</h3>
                <p class="text-muted">Select your business operating categories from the dropdown below.</p>
            </div>

            <div class="card-box">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-tags me-2"></i> Add Operating Categories</h5>
                
                <form action="{{ route('vendor.categories.save') }}" method="POST">
                    @csrf
                    <div class="row align-items-end g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">-- Choose Category --</option>
                                @foreach($allCategories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i> Add Category</button>
                        </div>
                    </div>
                </form>

                <h6 class="fw-bold text-dark mt-4 mb-2">Your Selected Categories:</h6>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($selectedCategories ?? [] as $sCat)
                        @php $catName = is_array($sCat) ? ($sCat['name'] ?? '') : (is_object($sCat) ? $sCat->name : $sCat); @endphp
                        @if(!empty($catName))
                            <div class="badge bg-secondary p-2 fs-6 d-flex align-items-center gap-2">
                                <span>{{ $catName }}</span>
                                <form action="{{ route('vendor.categories.destroy', urlencode($catName)) }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to remove this category?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-close btn-close-white" style="font-size: 0.65rem;" aria-label="Remove"></button>
                                </form>
                            </div>
                        @endif
                    @empty
                        <p class="text-muted small mb-0">No categories added yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>