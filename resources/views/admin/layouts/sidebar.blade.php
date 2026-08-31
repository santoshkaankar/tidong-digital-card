<!-- Complete Sidebar with All Options Intact -->
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

        <!-- MANAGEMENT HUB -->
        <div class="px-3 pt-3 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Management Hub</div>
        
        <!-- 1. Member Management -->
        <li class="nav-item">
            <a href="#memberSubmenu" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users-cog me-2"></i> Member Management</span>
                <i class="fas fa-angle-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 pb-2" id="memberSubmenu">
                <!-- Search User ko '#' kar diya -->
                <li class="nav-item mt-1">
                    <a href="{{ route('admin.members.search_member') }}" class="nav-link py-1"><i class="fas fa-search me-2"></i> Search User</a>
                </li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-users me-2"></i> Total Users List</a></li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-id-card me-2"></i> View Cards</a></li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-address-card me-2"></i> Create Cards</a></li>
                <!-- Config Cards par 360 Dashboard / Search route laga diya -->
                <li class="nav-item mt-1">
                    <a href="{{ route('admin.members.manage') }}" class="nav-link py-1"><i class="fas fa-sliders-h me-2"></i> Config Cards</a>
                </li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-shopping-cart me-2"></i> User Order</a></li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-wallet me-2"></i> User Wallet</a></li>
            </ul>
        </li>

        <!-- 2. Vendor Management -->
        <li class="nav-item">
            <a href="#vendorSubmenu" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center">
                <span><i class="fas fa-store me-2"></i> Vendor Management</span>
                <i class="fas fa-angle-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 pb-2" id="vendorSubmenu">
                <li class="nav-item mt-1">
                    <a href="{{ route('admin.vendors.manage') }}" class="nav-link py-1"><i class="fas fa-search me-2"></i> Search Vendor</a>
                </li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-store text-info me-2"></i> Registered Businesses</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.pending.items') }}" class="nav-link py-1"><i class="fas fa-user-check text-success me-2"></i> Approved Vendors</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.pending.items') }}" class="nav-link py-1"><i class="fas fa-user-clock text-warning me-2"></i> Unapproved Vendors</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.menu.create') }}" class="nav-link py-1"><i class="fas fa-utensils me-2"></i> Menu & QR Setup</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.global.item.create') }}" class="nav-link py-1"><i class="fas fa-plus-circle me-2"></i> Create Global Item</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.vendor.categories') }}" class="nav-link py-1"><i class="fas fa-list-alt me-2"></i> Vendor Categories</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.pending.items') }}" class="nav-link py-1"><i class="fas fa-clock text-warning me-2"></i> Item Approvals</a></li>
                <li class="nav-item mt-1"><a href="{{ route('admin.global.items.index') }}" class="nav-link"><i class="fas fa-list"></i> Manage Global Items</a></li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-user-slash text-danger me-2"></i> Vendor Ban / Moderation</a></li>
            </ul>
        </li>

        <!-- 3. Employee Management -->
        <li class="nav-item">
            <a href="#employeeSubmenu" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-tie me-2"></i> Employee Management</span>
                <i class="fas fa-angle-down small"></i>
            </a>
            <ul class="collapse list-unstyled ps-3 pb-2" id="employeeSubmenu">
                <li class="nav-item mt-1">
                    <a href="{{ route('admin.employees.manage') }}" class="nav-link py-1"><i class="fas fa-search me-2"></i> Search Employee</a>
                </li>
                <li class="nav-item mt-1"><a href="#" class="nav-link py-1"><i class="fas fa-users me-2"></i> Total Employees List</a></li>
            </ul>
        </li>

        <!-- FINANCE -->
        <div class="px-3 pt-3 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Finance</div>
        <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-wallet text-info me-2"></i> Wallet Management</a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="fas fa-receipt text-light me-2"></i> Transactions</a></li>

        <!-- SYSTEM -->
        <div class="px-3 pt-3 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">System</div>
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