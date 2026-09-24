<!-- File Path: resources/views/vendor/restaurant/partials/sidebar.blade.php -->

<style>
    /* 1. Base Sidebar Styling */
    .restaurant-sidebar {
        width: 260px;
        height: 100vh;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999 !important; /* Highest Z-Index so it appears ABOVE the gray backdrop */
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-top-content {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        overflow: hidden;
    }

    .brand-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
        background: #4f46e5;
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .sidebar-menu {
        list-style: none;
        padding: 12px;
        margin: 0;
        overflow-y: auto;
        flex-grow: 1;
    }

    .menu-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.8px;
        padding: 10px 10px 4px 10px;
    }

    .nav-item-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        color: #64748b;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        margin-bottom: 2px;
    }

    .nav-item-link:hover, .nav-item-link.active {
        color: #4f46e5;
        background: #eef2ff;
        font-weight: 600;
    }

    .nav-item-link i {
        font-size: 0.95rem;
        width: 18px;
        text-align: center;
    }

    .sidebar-footer {
        padding: 12px;
        border-top: 1px solid #f1f5f9;
        background: #fff;
        height: 70px;
        flex-shrink: 0;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 8px 12px;
        border: none;
        background: #fef2f2;
        color: #ef4444;
        font-weight: 600;
        border-radius: 8px;
        font-size: 0.85rem;
    }

    /* 2. Mobile Responsive Rules */
    @media (max-width: 991.98px) {
        .restaurant-sidebar {
            transform: translateX(-100%);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.2);
        }

        /* Active Class when Mobile Button is Clicked */
        .restaurant-sidebar.show-sidebar {
            transform: translateX(0) !important;
        }
    }
</style>

<aside class="restaurant-sidebar" id="restaurantSidebar">
    <div class="sidebar-top-content">
        <!-- Brand Header -->
        <div class="brand-header justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.3px;">Restaurant Hub</h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Partner Panel</small>
                </div>
            </div>
            <button type="button" class="btn-close d-lg-none" id="closeRestaurantSidebar" aria-label="Close"></button>
        </div>

        <!-- Navigation Links -->
        <ul class="sidebar-menu">
            <li class="menu-label">Main Menu</li>
            <li>
                <a href="{{ route('vendor.restaurant.dashboard') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.kitchen.screen') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.kitchen.screen') ? 'active' : '' }}">
                    <i class="bi bi-tv-fill"></i> Live Kitchen (KDS)
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.pos.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.pos.*') ? 'active' : '' }}">
                    <i class="bi bi-calculator-fill"></i> POS / Counter Billing
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.orders.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Orders History
                </a>
            </li>

            <li class="menu-label mt-2">Menu & Catalog</li>
            <li>
                <a href="{{ route('vendor.restaurant.categories.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Select Category
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.items.create_custom') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.items.create_custom') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Thali / Tiffin Items
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.items.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.items.index') ? 'active' : '' }}">
                    <i class="bi bi-card-list"></i> Select Items
                </a>
            </li>
            <li>
    <a href="{{ route('vendor.restaurant.menu-card.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.menu-card.*') ? 'active' : '' }}">
        <i class="bi bi-book"></i> Create Catalog / Menu Card
    </a>
</li>
<li>
    <a href="{{ route('vendor.restaurant.weekly-menu.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.weekly-menu.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-week"></i> Create Tiffin Menu
    </a>
</li>
<li>
    <a href="{{ route('vendor.restaurant.proceed-tiffin.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.proceed-tiffin.index') ? 'active' : '' }}">
        <i class="bi bi-calendar-plus"></i> Proceed Tiffin Order
    </a>
</li>
<li>
    <a href="{{ route('vendor.restaurant.proceed-tiffin.schedule') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.proceed-tiffin.schedule') ? 'active' : '' }}">
        <i class="bi bi-list-check"></i> Order List
    </a>
</li>

            <li class="menu-label mt-2">Operations & Tables</li>
            <li>
                <a href="{{ route('vendor.restaurant.tables.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.tables.*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan"></i> QR Codes & Tables
                </a>
            </li>

            <li class="menu-label mt-2">Marketing & Wallet</li>
            <li>
                <a href="{{ route('vendor.wallet.index') }}" class="nav-item-link {{ request()->routeIs('vendor.wallet.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Business Wallet
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-send-check"></i> Push Notifications / Offers
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Footer / Logout -->
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</aside>
