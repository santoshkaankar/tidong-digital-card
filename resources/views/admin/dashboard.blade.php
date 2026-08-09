<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Compact & Clean Sidebar Styling */
        #sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: 250px;
            background: #1e293b; color: #fff; transition: all 0.3s; z-index: 1000; overflow-y: auto;
        }
        #sidebar .brand {
            font-size: 1.1rem; padding: 15px; background: #0f172a; text-align: center;
            font-weight: bold; border-bottom: 1px solid #334155; color: #fff;
        }
        #sidebar .nav-link {
            color: #94a3b8; padding: 7px 12px; margin: 2px 8px; border-radius: 6px; transition: 0.2s; font-size: 0.88rem;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: #2563eb; }
        
        /* Section Headings inside Sidebar */
        #sidebar .sidebar-heading {
            font-size: 0.7rem; text-transform: uppercase; color: #64748b; font-weight: 700; padding: 0 12px; margin-top: 12px; margin-bottom: 4px; letter-spacing: 0.5px;
        }

        #main-content { margin-left: 250px; padding: 30px; }
        .stat-card { border: none; border-radius: 12px; transition: transform 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.04); text-decoration: none; display: block; }
        .stat-card:hover { transform: translateY(-4px); }
        
        @media (max-width: 768px) {
            #sidebar { width: 100%; height: auto; position: relative; }
            #main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar with Compact Spacing -->
    <div id="sidebar" class="d-flex flex-column">
        <div class="brand">
            <i class="fas fa-shield-alt text-primary me-2"></i> Admin Panel
        </div>
        
        <ul class="nav nav-pills flex-column mb-auto px-1 py-2">
            <!-- Main Dashboard -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>

            <!-- Core Management -->
            <li class="nav-item">
                <a href="{{ route('admin.global.item.create') }}" class="nav-link {{ request()->routeIs('admin.global.item.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle me-2"></i> Create Global Item
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('card.create') }}" class="nav-link {{ request()->routeIs('card.create') ? 'active' : '' }}">
                    <i class="fas fa-id-card me-2"></i> Manage Cards
                </a>
            </li>

            <!-- Master Sections -->
            <div class="sidebar-heading">Masters</div>
            <li class="nav-item">
                <a href="{{ route('admin.vendor.categories') }}" class="nav-link {{ request()->routeIs('admin.vendor.categories*') ? 'active' : '' }}">
                    <i class="fas fa-list-alt me-2"></i> Vendor Categories
                </a>
            </li>

            <!-- User & Vendor Management Sections -->
            <div class="sidebar-heading">Users & Vendors</div>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-users text-primary me-2"></i> Total Users List
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-tie text-secondary me-2"></i> Total Employees List
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-store text-info me-2"></i> Registered Businesses
                </a>
            </li>

            <!-- Approvals & Moderation -->
            <div class="sidebar-heading">Approvals</div>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-check text-success me-2"></i> Approved Vendors
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-clock text-warning me-2"></i> Unapproved Vendors
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.pending.items') }}" class="nav-link {{ request()->routeIs('admin.pending.items') ? 'active' : '' }}">
                    <i class="fas fa-clock text-warning me-2"></i> Item Approvals
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-slash text-danger me-2"></i> User / Vendor Ban
                </a>
            </li>

            <!-- Financial & Wallet -->
            <div class="sidebar-heading">Finance</div>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-wallet text-info me-2"></i> Wallet Management
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-receipt text-light me-2"></i> Transactions
                </a>
            </li>

            <!-- System & Settings -->
            <div class="sidebar-heading">System</div>
            <li class="nav-item">
                <a href="{{ route('menu.create') }}" class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                    <i class="fas fa-utensils me-2"></i> Menu & QR Setup
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.update') }}" class="nav-link {{ request()->routeIs('admin.update') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt me-2"></i> System Update
                </a>
            </li>
        </ul>

        <div class="p-3 border-top border-secondary">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div id="main-content">
        <nav class="navbar navbar-expand navbar-light bg-white px-4 rounded-4 shadow-sm mb-4">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h5 fw-bold text-secondary">Dashboard Overview & Statistics</span>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="fw-bold text-dark"><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Admin' }}</span>
                    </li>
                </ul>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- TOP STATS ROW -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card p-4 bg-white border-start border-primary border-4">
                    <div class="text-muted small fw-semibold text-uppercase">Total Users</div>
                    <h2 class="fw-bold text-dark mt-2 mb-0">{{ $totalUsers ?? 0 }}</h2>
                    <span class="small text-primary mt-2 d-block"><i class="fas fa-users me-1"></i> View All Users List</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-4 bg-white border-start border-secondary border-4">
                    <div class="text-muted small fw-semibold text-uppercase">Total Employees</div>
                    <h2 class="fw-bold text-dark mt-2 mb-0">{{ $totalEmployees ?? 0 }}</h2>
                    <span class="small text-secondary mt-2 d-block"><i class="fas fa-user-tie me-1"></i> View Staff Members</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-4 bg-white border-start border-success border-4">
                    <div class="text-muted small fw-semibold text-uppercase">Approved Vendors</div>
                    <h2 class="fw-bold text-success mt-2 mb-0">{{ $approvedVendors ?? 0 }}</h2>
                    <span class="small text-success mt-2 d-block"><i class="fas fa-check-circle me-1"></i> Paid & Active Vendors</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-4 bg-white border-start border-warning border-4">
                    <div class="text-muted small fw-semibold text-uppercase">Unapproved Vendors</div>
                    <h2 class="fw-bold text-warning mt-2 mb-0">{{ $unapprovedVendors ?? 0 }}</h2>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="small text-warning"><i class="fas fa-clock me-1"></i> Pending Approval</span>
                        @if(($unapprovedVendors ?? 0) > 0)
                            <a href="#" class="btn btn-sm btn-warning fw-bold px-2 py-0" style="font-size: 0.75rem;">View & Action</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SECONDARY STATS ROW -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card stat-card p-4 bg-white h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-id-badge text-primary me-2"></i> Visiting Cards Ratio</h5>
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-2">
                        <span class="fw-semibold text-muted">Total Digital Cards</span>
                        <span class="badge bg-primary fs-6">{{ $totalCards ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                        <span class="fw-semibold text-muted">Pending Cards Approval</span>
                        <span class="badge bg-warning text-dark fs-6">{{ $pendingCards ?? 0 }}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card stat-card p-4 bg-white h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-database text-success me-2"></i> System & Master Database</h5>
                    
                    <a href="{{ route('admin.global.item.create') }}" class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-2 text-decoration-none text-dark">
                        <span class="fw-semibold text-muted"><i class="fas fa-boxes me-2 text-success"></i> Global Master Items</span>
                        <span class="badge bg-success fs-6">{{ $totalGlobalItems ?? 0 }}</span>
                    </a>

                    <a href="{{ route('admin.global.item.create') }}" class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-2 text-decoration-none text-dark">
                        <span class="fw-semibold text-muted"><i class="fas fa-tags me-2 text-primary"></i> Item Categories</span>
                        <span class="badge bg-primary fs-6">{{ $totalItemCategories ?? 0 }}</span>
                    </a>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-2">
                        <span class="fw-semibold text-muted"><i class="fas fa-user me-2 text-secondary"></i> Total Customers (Users)</span>
                        <span class="badge bg-secondary fs-6">{{ $totalCustomers ?? 0 }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3 mb-2">
                        <span class="fw-semibold text-muted"><i class="fas fa-user-shield me-2 text-dark"></i> Total Employees</span>
                        <span class="badge bg-dark fs-6">{{ $totalEmployees ?? 0 }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                        <span class="fw-semibold text-muted"><i class="fas fa-store me-2 text-info"></i> Registered Businesses</span>
                        <span class="badge bg-info text-dark fs-6">{{ $totalBusinesses ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>