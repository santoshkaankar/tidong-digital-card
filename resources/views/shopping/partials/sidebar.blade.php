<!-- File Path: resources/views/shopping/partials/sidebar.blade.php -->

<style>
    .shopping-sidebar {
        width: 100%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: sticky;
        top: 20px;
        z-index: 1020;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .sidebar-top-content {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .brand-header {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .brand-icon {
        width: 38px;
        height: 38px;
        background: #4f46e5;
        color: #fff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .sidebar-menu {
        list-style: none;
        padding: 12px;
        margin: 0;
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }
    .menu-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.8px;
        padding: 12px 10px 4px 10px;
    }
    .nav-item-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        color: #475569;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.84rem;
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
        width: 20px;
        text-align: center;
        color: #64748b;
    }
    .sidebar-footer {
        padding: 12px;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 9px 12px;
        border: none;
        background: #fef2f2;
        color: #ef4444;
        font-weight: 600;
        border-radius: 8px;
        font-size: 0.85rem;
    }
    @media (max-width: 991.98px) {
        .shopping-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100vh;
            border-radius: 0;
            z-index: 99999;
            transition: left 0.3s ease-in-out;
            box-shadow: 5px 0 25px rgba(0,0,0,0.15);
        }
        .shopping-sidebar.show-mobile-sidebar {
            left: 0;
        }
    }
</style>

<aside class="shopping-sidebar" id="shoppingSidebar">
    <div class="sidebar-top-content">
        <div class="brand-header justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Shopping Store</h6>
                    <small class="text-muted fw-semibold" style="font-size: 0.7rem;">Vendor Panel</small>
                </div>
            </div>
            <button type="button" class="btn-close d-lg-none" id="closeSidebarBtn" aria-label="Close"></button>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label">Store Overview</li>
            <li>
                <a href="{{ route('shopping.shop-dashboard') }}" class="nav-item-link {{ request()->routeIs('shopping.shop-dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>

            <li class="menu-label mt-2">Catalog & Products</li>
            <li>
                <a href="{{ route('shopping.items.index') }}" class="nav-item-link {{ request()->routeIs('shopping.items.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Products Management
                </a>
            </li>
            <li>
                <a href="{{ route('shopping.categories.index') }}" class="nav-item-link {{ request()->routeIs('shopping.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Categories & Subcategories
                </a>
            </li>
            <li>
                <a href="{{ route('shopping.brands.index') }}" class="nav-item-link {{ request()->routeIs('shopping.brands.*') ? 'active' : '' }}">
                    <i class="fas fa-award"></i> Brands & Manufacturers
                </a>
            </li>
            <li>
                <a href="{{ route('shopping.variants.index') }}" class="nav-item-link {{ request()->routeIs('shopping.variants.*') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i> Variants & Attributes
                </a>
            </li>
            <li>
                <a href="{{ route('shopping.units.index') }}" class="nav-item-link {{ request()->routeIs('shopping.units.*') ? 'active' : '' }}">
                    <i class="fas fa-balance-scale"></i> Units of Measure (UOM)
                </a>
            </li>

            <li class="menu-label mt-2">Orders & Customers</li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="fas fa-shopping-bag"></i> Customer Orders
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="fas fa-undo"></i> Returns & Refunds
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="fas fa-star text-warning"></i> Store Reviews
                </a>
            </li>

            <li class="menu-label mt-2">Marketing & Finance</li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="fas fa-ticket-alt"></i> Store Coupons & Discounts
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.wallet.index') }}" class="nav-item-link {{ request()->routeIs('vendor.wallet.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i> Earnings & Wallet
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="fas fa-cog"></i> Store Settings
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>