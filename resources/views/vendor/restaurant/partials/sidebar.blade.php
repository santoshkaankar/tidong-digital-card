<style>
    /* Desktop Default Styles */
    .restaurant-sidebar {
        width: 260px;
        min-width: 260px;
        background: var(--sidebar-bg, #ffffff);
        border-right: 1px solid var(--border-color, #e2e8f0);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
        z-index: 1050;
        transition: transform 0.3s ease-in-out;
    }

    .brand-header {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid var(--border-color, #f1f5f9);
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
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .sidebar-menu {
        list-style: none;
        padding: 16px 12px;
        margin: 0;
        overflow-y: auto;
        max-height: calc(100vh - 140px);
    }

    .menu-label {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.8px;
        padding: 12px 12px 6px 12px;
    }

    .nav-item-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        color: var(--text-muted, #64748b);
        text-decoration: none;
        font-weight: 500;
        font-size: 0.875rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        margin-bottom: 2px;
    }

    .nav-item-link:hover {
        color: #4f46e5;
        background: rgba(79, 70, 229, 0.05);
    }

    .nav-item-link.active {
        color: #4f46e5;
        background: #eef2ff;
        font-weight: 600;
    }

    .nav-item-link i {
        font-size: 1rem;
        width: 20px;
        text-align: center;
    }

    .sidebar-footer {
        padding: 16px 12px;
        border-top: 1px solid var(--border-color, #f1f5f9);
        background: var(--sidebar-bg, #fff);
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
        transition: all 0.2s ease;
    }

    .logout-btn:hover {
        background: #fee2e2;
    }

    /* Mobile Offcanvas Styles */
    @media (max-width: 991.98px) {
        .restaurant-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            transform: translateX(-100%);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .restaurant-sidebar.show {
            transform: translateX(0) !important;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .sidebar-backdrop.show {
            display: block;
        }
    }
</style>

<aside class="restaurant-sidebar" id="restaurantSidebar">
    <div>
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
            <!-- Mobile Close Cross Icon -->
            <button class="btn-close d-lg-none" id="closeSidebarBtn" aria-label="Close"></button>
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
                    <i class="bi bi-receipt me-2"></i> Orders History
                </a>
            </li>

            <li class="menu-label mt-2">Menu & Catalog</li>
            <li>
                <a href="{{ route('vendor.restaurant.categories.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Select Category
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.items.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.items.*') ? 'active' : '' }}">
                    <i class="bi bi-card-list"></i> Select Items
                </a>
            </li>
            <li>
                <a href="{{ route('vendor.restaurant.menu-card.index') }}" class="nav-item-link {{ request()->routeIs('vendor.restaurant.menu-card.*') ? 'active' : '' }}">
                    <i class="bi bi-book"></i> Create Catalog / Menu Card
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
                <a href="#" class="nav-item-link">
                    <i class="bi bi-wallet2"></i> Wallet
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

<!-- Backdrop Overlay for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById("restaurantSidebar");
        const backdrop = document.getElementById("sidebarBackdrop");
        const closeBtn = document.getElementById("closeSidebarBtn");

        function hideSidebar() {
            if (sidebar) sidebar.classList.remove("show");
            if (backdrop) backdrop.classList.remove("show");
        }

        if (backdrop) backdrop.addEventListener("click", hideSidebar);
        if (closeBtn) closeBtn.addEventListener("click", hideSidebar);

        // Mobile menu me link click hone par auto close ho jaye
        document.querySelectorAll("#restaurantSidebar .nav-item-link").forEach(link => {
            link.addEventListener("click", function () {
                if (window.innerWidth < 992) {
                    hideSidebar();
                }
            });
        });
    });
</script>