<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Catalog - Tidong®</title>
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
                <a href="{{ route('vendor.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            
            <!-- Catalog & Items -->
            <li class="mt-3 text-uppercase text-muted px-3" style="font-size: 0.7rem;">Catalog & Items</li>
            <li>
                <a href="{{ route('vendor.catalog') }}" class="active"><i class="fas fa-boxes"></i> Manage Catalog</a>
            </li>
            <li>
                <a href="{{ route('vendor.items') }}"><i class="fas fa-utensils"></i> Manage Items</a>
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

            <!-- Profile Dropdown -->
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

        <!-- Catalog Content Body -->
        <div class="content-body">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="mb-4">
                <h3 class="fw-bold text-dark">Manage Operating Categories & Menu Availability</h3>
                <p class="text-muted">Select categories and turn items ON/OFF to display on your customer digital menu.</p>
            </div>

            <!-- SECTION 1: Add Operating Categories -->
            <div class="card-box">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-tags me-2"></i> Add Operating Categories</h5>
                <p class="text-muted small">Select a category from the dropdown below to add it to your vendor category list.</p>
                
                <form action="{{ route('vendor.categories.save') }}" method="POST">
                    @csrf
                    <div class="row align-items-end g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Category</label>
                            <select name="categories[]" class="form-select" required>
                                <option value="">-- Choose Category --</option>
                                @foreach($allCategories ?? [] as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i> Add Category</button>
                        </div>
                    </div>
                </form>

                <!-- Vendor's Selected Categories List -->
                <h6 class="fw-bold text-dark mt-4 mb-2">Your Selected Categories:</h6>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($selectedCategories ?? [] as $sCat)
                        <span class="badge bg-secondary p-2 fs-6">{{ $sCat }}</span>
                    @empty
                        <p class="text-muted small mb-0">No categories added yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- SECTION 2: Manage Live Menu Availability (ON/OFF Toggle) -->
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-toggle-on text-success me-2"></i> Live Menu Availability</h5>
                    <span class="badge bg-info text-dark">Total Items: {{ count($myInventory ?? []) }}</span>
                </div>
                <p class="text-muted small">Toggle items ON to display on customer QR menu cards, or OFF to temporarily hide them.</p>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Price (₹)</th>
                                <th class="text-center">Customer Menu Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myInventory ?? [] as $item)
                            <tr>
                                <td class="fw-bold text-dark">{{ $item->item_name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item->category }}</span></td>
                                <td class="fw-bold text-success">₹{{ $item->price }}</td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input item-status-toggle" 
                                               type="checkbox" 
                                               role="switch"
                                               data-id="{{ $item->id }}" 
                                               style="width: 45px; height: 22px; cursor: pointer;"
                                               {{ ($item->is_available ?? 1) ? 'checked' : '' }}>
                                        <span class="ms-2 fw-semibold status-text-{{ $item->id }} {{ ($item->is_available ?? 1) ? 'text-success' : 'text-danger' }}">
                                            {{ ($item->is_available ?? 1) ? 'Active (ON)' : 'Hidden (OFF)' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No items added to your inventory yet. Go to 'Manage Items' to add products.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle & jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AJAX Script for Instant ON/OFF Toggle -->
    <script>
        $(document).ready(function() {
            $('.item-status-toggle').on('change', function() {
                let itemId = $(this).data('id');
                let status = $(this).is(':checked') ? 1 : 0;
                let statusText = $('.status-text-' + itemId);

                $.ajax({
                    url: "{{ route('vendor.item.toggle') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: itemId,
                        status: status
                    },
                    success: function(response) {
                        if(status == 1) {
                            statusText.text('Active (ON)').removeClass('text-danger').addClass('text-success');
                        } else {
                            statusText.text('Hidden (OFF)').removeClass('text-success').addClass('text-danger');
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>