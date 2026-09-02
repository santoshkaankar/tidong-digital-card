<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Dashboard - Tidong®</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Desktop Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #1e293b;
            color: #fff;
            overflow-y: auto;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease-in-out;
        }
        .sidebar .brand-header {
            padding: 18px 20px;
            font-size: 1.15rem;
            font-weight: bold;
            background: #0f172a;
            border-bottom: 1px solid #334155;
            letter-spacing: 0.3px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 12px 12px;
            margin: 0;
            flex: 1;
        }
        .sidebar-menu li { margin-bottom: 3px; }
        .sidebar-menu .menu-section-title {
            font-size: 0.68rem;
            letter-spacing: 0.8px;
            color: #64748b;
            padding: 10px 12px 4px 12px;
            font-weight: 700;
        }
        .sidebar-menu a {
            color: #94a3b8;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            color: #fff;
            background: #2563eb;
        }
        .sidebar-menu a i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
            font-size: 0.95rem;
        }

        /* Desktop Main Layout */
        .main-content {
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s ease-in-out;
        }

        /* Top Navbar */
        .top-navbar {
            height: 70px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .content-body { padding: 25px; flex: 1; }
        .card-box {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Mobile Overlay Styling */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        /* Mobile View Media Queries (< 992px) */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -270px;
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .content-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Mobile Overlay Background -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="sidebar">
        <div class="brand-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-store me-2 text-primary"></i> Partner Panel</span>
            <button class="btn btn-sm text-white d-lg-none" onclick="toggleSidebar()">✕</button>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('vendor.dashboard') }}" class="{{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            
            @php $serviceType = Auth::user()->service_type ?? 'food'; @endphp

            <!-- DYNAMIC SIDEBAR OPTIONS BASED ON SERVICE TYPE -->
            @if($serviceType == 'food')
                <li class="my-2">
                    <a href="{{ route('vendor.kitchen.dashboard') }}" class="btn btn-warning text-dark fw-bold w-100 justify-content-center shadow-sm">
                        <i class="bi bi-shop me-1"></i> 🔔 Kitchen Live Screen
                    </a>
                </li>
                <li class="menu-section-title">CATALOG SETUP</li>
                <li><a href="{{ route('vendor.categories.index') }}"><i class="fas fa-tags"></i> Add Category</a></li>
                <li><a href="{{ route('vendor.inventory.index') }}"><i class="fas fa-boxes"></i> Add Item</a></li>
                <li><a href="javascript:void(0)" onclick="openRequestModal()"><i class="fas fa-plus-circle"></i> Request New Item</a></li>
                <li><a href="{{ route('vendor.pricing.index') }}"><i class="fas fa-rupee-sign"></i> Add Price</a></li>
                <li><a href="{{ route('vendor.catalogs.index') }}"><i class="fas fa-book-open"></i> Catalog</a></li>
                <li class="menu-section-title">QR & TABLES</li>
                <li><a href="{{ route('vendor.qrcode') }}"><i class="fas fa-qrcode"></i> Table QR Code</a></li>
            
            @elseif($serviceType == 'taxi')
                <li class="menu-section-title">TAXI & DRIVER OPS</li>
                <li><a href="javascript:void(0)"><i class="fas fa-route text-warning"></i> Active Rides</a></li>
                <li><a href="javascript:void(0)"><i class="fas fa-history"></i> Ride History</a></li>
                <li><a href="javascript:void(0)"><i class="fas fa-car-side"></i> Vehicle Documents</a></li>

            @elseif($serviceType == 'money_exchange')
                <li class="menu-section-title">FX CURRENCY OPS</li>
                <li><a href="javascript:void(0)"><i class="fas fa-coins text-success"></i> Exchange Rates Master</a></li>
                <li><a href="javascript:void(0)"><i class="fas fa-hand-holding-usd"></i> Today Cash Delivery</a></li>
                <li><a href="javascript:void(0)"><i class="fas fa-file-invoice-dollar"></i> Forex Receipts</a></li>

            @elseif($serviceType == 'emporium')
                <li class="menu-section-title">EMPORIUM SHOWCASE</li>
                <li><a href="{{ route('vendor.categories.index') }}"><i class="fas fa-tags"></i> Craft Categories</a></li>
                <li><a href="{{ route('vendor.inventory.index') }}"><i class="fas fa-gem text-info"></i> Souvenir Items</a></li>
                <li><a href="{{ route('vendor.catalogs.index') }}"><i class="fas fa-store"></i> Digital Showcase</a></li>
                <li><a href="{{ route('vendor.qrcode') }}"><i class="fas fa-qrcode"></i> Store QR Standee</a></li>

            @elseif($serviceType == 'guide')
                <li class="menu-section-title">TOURIST GUIDE OPS</li>
                <li><a href="javascript:void(0)"><i class="fas fa-flag text-danger"></i> Tour Bookings</a></li>
                <li><a href="javascript:void(0)"><i class="fas fa-id-card"></i> Badge & Credentials</a></li>
                <li><a href="javascript:void(0)"><i class="fas fa-language"></i> Spoken Languages</a></li>
            @endif

            <!-- Common Operations & Tracking -->
            <li class="menu-section-title">OPERATIONS</li>
            <li>
                <a href="{{ Route::has('vendor.orders.index') ? route('vendor.orders.index') : '#' }}"><i class="fas fa-shopping-cart"></i> Booking / Orders</a>
            </li>
            <li>
                <a href="{{ Route::has('vendor.payments.index') ? route('vendor.payments.index') : '#' }}"><i class="fas fa-wallet"></i> Payment Status</a>
            </li>

            <!-- Account Settings / Logout -->
            <li class="menu-section-title">ACCOUNT</li>
            <li>
                <a href="{{ route('vendor.profile') }}" class="{{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
                    <i class="fas fa-user-cog text-info"></i> Profile & QR Setup
                </a>
            </li>
            <li class="mb-2">
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
                <!-- Toggle Button for Mobile View -->
                <button class="btn btn-light border me-3 d-lg-none" type="button" onclick="toggleSidebar()">
                    <i class="fas fa-bars fs-5"></i>
                </button>
                <span class="badge bg-primary me-2">{{ ucfirst(Auth::user()->role ?? 'Business') }}</span>
                <span class="badge bg-dark text-uppercase me-2">{{ Auth::user()->service_type ?? 'Food' }}</span>
                <span class="text-muted small d-none d-sm-inline">Mobile: <strong class="text-dark">{{ Auth::user()->mobile ?? 'N/A' }}</strong></span>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=2563eb&color=fff" alt="Profile" class="me-2">
                    <span class="fw-semibold d-none d-sm-inline">{{ Auth::user()->name ?? 'Business User' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                    <li><h6 class="dropdown-header">Welcome, {{ Auth::user()->name ?? 'User' }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('vendor.profile') }}"><i class="fas fa-user-cog fa-fw me-2 text-primary"></i> Profile & QR Settings</a></li>
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
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Welcome Header -->
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-dark">Welcome back, {{ Auth::user()->name ?? 'User' }}! 👋</h3>
                    <p class="text-muted mb-0">Managing Service: <strong class="text-primary text-uppercase">{{ Auth::user()->service_type ?? 'Food & Restaurant' }}</strong></p>
                </div>
            </div>

            <!-- Quick Metrics Row -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card-box d-flex align-items-center justify-content-between p-3">
                        <div>
                            <p class="text-muted small text-uppercase mb-1 fw-bold" style="font-size: 0.75rem;">Total Requests</p>
                            <h3 class="fw-bold text-primary mb-0">{{ $totalOrders ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card-box d-flex align-items-center justify-content-between p-3">
                        <div>
                            <p class="text-muted small text-uppercase mb-1 fw-bold" style="font-size: 0.75rem;">Active / Running</p>
                            <h3 class="fw-bold text-warning mb-0">{{ $runningOrders ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card-box d-flex align-items-center justify-content-between p-3">
                        <div>
                            <p class="text-muted small text-uppercase mb-1 fw-bold" style="font-size: 0.75rem;">Completed</p>
                            <h3 class="fw-bold text-success mb-0">{{ $completedOrders ?? 0 }}</h3>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card-box d-flex align-items-center justify-content-between p-3">
                        <div>
                            <p class="text-muted small text-uppercase mb-1 fw-bold" style="font-size: 0.75rem;">Today Collection</p>
                            <h3 class="fw-bold text-info mb-0">₹{{ number_format($todayCollection ?? 0, 2) }}</h3>
                        </div>
                        <div class="stat-icon bg-info-subtle text-info">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DYNAMIC DASHBOARD CONTENT BASED ON SERVICE TYPE -->
            @if(Auth::user()->service_type == 'taxi')
                <div class="card-box border-start border-4 border-warning mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold text-dark"><i class="fas fa-taxi me-2 text-warning"></i> Taxi Driver Duty Console</h5>
                            <p class="text-muted small mb-0">Vehicle Number: <strong>{{ Auth::user()->vehicle_no ?? 'Not Added' }}</strong></p>
                        </div>
                        <button class="btn btn-success fw-bold"><i class="fas fa-power-off me-1"></i> Switch ONLINE</button>
                    </div>
                </div>
            @elseif(Auth::user()->service_type == 'money_exchange')
                <div class="card-box border-start border-4 border-success mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold text-dark"><i class="fas fa-coins me-2 text-success"></i> Currency Exchange Rate Console</h5>
                            <p class="text-muted small mb-0">RBI/Govt License: <strong>{{ Auth::user()->license_no ?? 'Not Added' }}</strong></p>
                        </div>
                        <button class="btn btn-primary fw-bold btn-sm"><i class="fas fa-sync me-1"></i> Update FX Rates</button>
                    </div>
                    <div class="row g-2 text-center">
                        <div class="col-3"><div class="p-2 bg-light rounded border"><small class="text-muted font-bold">USD</small><div class="fw-bold">₹83.20</div></div></div>
                        <div class="col-3"><div class="p-2 bg-light rounded border"><small class="text-muted font-bold">EUR</small><div class="fw-bold">₹90.10</div></div></div>
                        <div class="col-3"><div class="p-2 bg-light rounded border"><small class="text-muted font-bold">GBP</small><div class="fw-bold">₹105.40</div></div></div>
                        <div class="col-3"><div class="p-2 bg-light rounded border"><small class="text-muted font-bold">JPY</small><div class="fw-bold">₹0.56</div></div></div>
                    </div>
                </div>
            @endif

            <!-- Action Cards Section -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-box h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-primary fs-3 mb-2"><i class="fas fa-boxes"></i></div>
                            <h5 class="fw-bold">Catalog & Inventory Setup</h5>
                            <p class="text-muted small">Manage your categories, items inventory, pricing master, and catalog seamlessly.</p>
                        </div>
                        <a href="{{ route('vendor.inventory.index') }}" class="btn btn-primary mt-3 w-100"><i class="fas fa-arrow-right me-1"></i> Manage Inventory & Catalog</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-box h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-success fs-3 mb-2"><i class="fas fa-qrcode"></i></div>
                            <h5 class="fw-bold">QR Code & Hub Links</h5>
                            <p class="text-muted small">Generate and download unique digital QR codes for tables, rooms, or counters.</p>
                        </div>
                        <a href="{{ route('vendor.qrcode') }}" class="btn btn-success mt-3 w-100"><i class="fas fa-qrcode me-1"></i> View / Download QR Codes</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-box h-100 d-flex flex-column justify-content-between border-start border-4 border-info">
                        <div>
                            <div class="text-info fs-3 mb-2"><i class="fas fa-user-cog"></i></div>
                            <h5 class="fw-bold">Profile & Payment Settings</h5>
                            <p class="text-muted small">Upload your PhonePe/GPay QR scanner image for customer online payments.</p>
                        </div>
                        <a href="{{ route('vendor.profile') }}" class="btn btn-info text-white fw-bold mt-3 w-100"><i class="fas fa-cog me-1"></i> Setup Profile & Payment QR</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Request New Item Modal Popup -->
    <div class="modal fade" id="requestNewItemModal" tabindex="-1" aria-hidden="true" style="z-index: 99999;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-dark">
                <form action="{{ route('vendor.item.request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-plus-circle me-2"></i>Request New Item to Admin
                        </h5>
                        <button type="button" class="btn-close btn-close-white" onclick="closeRequestModal()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" class="form-control" placeholder="e.g. Item / Product Name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            @php
                                $categories = \App\Models\Vendor\ItemCategory::pluck('name');
                                if($categories->isEmpty()){
                                    $categories = collect(['General', 'Main Course', 'Starters', 'Beverages', 'Desserts', 'Souvenirs', 'Handicrafts']);
                                }
                            @endphp
                            <select name="category" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" placeholder="e.g. 250">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Image (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeRequestModal()">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i> Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    function toggleSidebar() {
        let sidebar = document.getElementById('sidebar');
        let overlay = document.getElementById('sidebarOverlay');
        
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    }

    function openRequestModal() {
        var modalElement = document.getElementById('requestNewItemModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var myModal = new bootstrap.Modal(modalElement);
            myModal.show();
        } else {
            modalElement.classList.add('show');
            modalElement.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }

    function closeRequestModal() {
        var modalElement = document.getElementById('requestNewItemModal');
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
    </script>
</body>
</html>