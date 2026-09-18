<style>
    .retail-sidebar {
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
        z-index: 9999 !important;
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
        background: #2563eb;
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
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
        color: #2563eb;
        background: #eff6ff;
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

    @media (max-width: 991.98px) {
        .retail-sidebar {
            transform: translateX(-100%);
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.2);
        }
        .retail-sidebar.show-sidebar {
            transform: translateX(0) !important;
        }
    }
</style>

<aside class="retail-sidebar" id="retailSidebar">
    <div class="sidebar-top-content">
        <!-- Brand Header -->
        <div class="brand-header justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="brand-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.3px;">Retail Hub</h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Partner Panel</small>
                </div>
            </div>
            <button type="button" class="btn-close d-lg-none" id="closeRetailSidebar" aria-label="Close"></button>
        </div>

        <!-- Navigation Links -->
        <ul class="sidebar-menu">
            <li class="menu-label">Main Menu</li>
            <li>
                <a href="{{ route('retail.dashboard') }}" class="nav-item-link {{ request()->routeIs('retail.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
            </li>

            <li class="menu-label mt-2">Catalog & Masters</li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-tags"></i> Category
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-award"></i> Brand
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-rulers"></i> Measure Unit
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-percent"></i> Tax Master
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-geo-alt"></i> Colony / Village (Area)
                </a>
            </li>
            <li>
                <a href="{{ route('retail.shop.index') }}" class="nav-item-link {{ request()->routeIs('retail.shop.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Items / Products
                </a>
            </li>

            <li class="menu-label mt-2">Orders & Sales</li>
            <li>
                <a href="{{ route('retail.cart.index') }}" class="nav-item-link {{ request()->routeIs('retail.cart.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Orders Management
                </a>
            </li>
            <li>
                <a href="{{ route('retail.shop.index') }}" class="nav-item-link">
                    <i class="bi bi-eye"></i> Customer Shop View
                </a>
            </li>

            <li class="menu-label mt-2">Finance & Settings</li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-wallet2"></i> Wallet & Earnings
                </a>
            </li>
            <li>
                <a href="#" class="nav-item-link">
                    <i class="bi bi-gear"></i> Settings
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