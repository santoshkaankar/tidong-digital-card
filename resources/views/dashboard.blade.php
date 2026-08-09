<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Dashboard - Tidong®</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #1e293b;
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .brand-header {
            padding: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            background: #0f172a;
            border-bottom: 1px solid #334155;
        }
        .sidebar-menu {
            list-style: none;
            padding: 15px 10px;
            margin: 0;
        }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu a {
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-radius: 8px;
            transition: 0.2s;
            font-size: 0.95rem;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: #fff;
            background: #2563eb;
        }
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        /* Main Layout */
        .main-content {
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        /* Top Navbar */
        .top-navbar {
            height: 70px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .content-body { padding: 30px; flex: 1; }
        .card-box {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand-header">
            <i class="fas fa-store me-2 text-primary"></i> Business Panel
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('vendor.dashboard') }}" class="active"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            
            <!-- Catalog & Inventory -->
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">Catalog & Items</li>
            <li>
                <a href="{{ route('vendor.catalog') }}"><i class="fas fa-boxes"></i> Manage Catalog</a>
            </li>
            <li>
                <a href="{{ route('vendor.catalog') }}"><i class="fas fa-list-alt"></i> Add / Edit Categories</a>
            </li>
            <li>
                <a href="{{ route('vendor.catalog') }}"><i class="fas fa-utensils"></i> Create / Edit Items</a>
            </li>

            <!-- Orders & Area Tracking -->
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">Orders & Sales</li>
            <li>
                <a href="#"><i class="fas fa-shopping-cart"></i> Order Status & Areas</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-credit-card"></i> Payment Status</a>
            </li>

            <!-- QR Code Section -->
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">QR & Tables</li>
            <li>
                <a href="{{ route('vendor.qrcode') }}"><i class="fas fa-qrcode"></i> Table QR Code</a>
            </li>

            <!-- Account Settings / Logout -->
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">Account</li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content Panel -->
    <div class="main-content">
        
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary me-2">{{ ucfirst(Auth::user()->role ?? 'Business') }}</span>
                <span class="text-muted small">Business Type: <strong class="text-dark">{{ ucfirst(Auth::user()->business_type ?? 'General Store') }}</strong></span>
            </div>

            <!-- Profile Dropdown with Avatar & 3-line style menu integration -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=2563eb&color=fff" alt="Profile" class="me-2">
                    <span class="fw-semibold">{{ Auth::user()->name ?? 'Business User' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                    <li><h6 class="dropdown-header">Welcome, {{ Auth::user()->name }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user fa-fw me-2"></i> Profile Settings</a></li>
                    <li><a class="dropdown-item" href="{{ route('vendor.qrcode') }}"><i class="fas fa-qrcode fa-fw me-2"></i> My QR Code</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dashboard Content Body -->
        <div class="content-body">
            
            <!-- Welcome Header -->
            <div class="mb-4">
                <h3 class="fw-bold text-dark">Welcome back, {{ Auth::user()->name }}! 👋</h3>
                <p class="text-muted">Here is your live business overview, catalog items status, and table management shortcuts.</p>
            </div>

            <!-- Quick Metrics Row -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card-box text-center">
                        <h6 class="text-muted small text-uppercase">Active Categories</h6>
                        <h3 class="fw-bold text-primary mt-2">{{ $totalCategories ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-box text-center">
                        <h6 class="text-muted small text-uppercase">Shop Inventory Items</h6>
                        <h3 class="fw-bold text-success mt-2">{{ $totalItems ?? 0 }}</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-box text-center">
                        <h6 class="text-muted small text-uppercase">Pending Orders</h6>
                        <h3 class="fw-bold text-warning mt-2">0</h3>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card-box text-center">
                        <h6 class="text-muted small text-uppercase">Today's Revenue</h6>
                        <h3 class="fw-bold text-info mt-2">₹0.00</h3>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards (Catalog & QR) -->
            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="card-box h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-primary fs-3 mb-2"><i class="fas fa-boxes"></i></div>
                            <h5 class="fw-bold">Catalog & Inventory Management</h5>
                            <p class="text-muted small">Select your business operating categories, pick items from global master, and manage custom prices.</p>
                        </div>
                        <a href="{{ route('vendor.catalog') }}" class="btn btn-primary mt-3 w-100"><i class="fas fa-arrow-right me-1"></i> Open Catalog Setup</a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-box h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-success fs-3 mb-2"><i class="fas fa-qrcode"></i></div>
                            <h5 class="fw-bold">Table QR Code</h5>
                            <p class="text-muted small">Generate and download your unique digital menu QR code to display on tables or billing desks.</p>
                        </div>
                        <a href="{{ route('vendor.qrcode') }}" class="btn btn-success mt-3 w-100"><i class="fas fa-qrcode me-1"></i> View / Download QR</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>