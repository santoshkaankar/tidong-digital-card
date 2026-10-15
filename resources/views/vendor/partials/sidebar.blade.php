<!-- Vendor Master Admin Sidebar Partial -->
<div class="sidebar bg-dark text-white p-3 d-flex flex-column flex-shrink-0" style="width: 260px; min-height: 100vh;" id="sidebarMenu">
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-secondary">
        <div class="bg-primary text-white p-2 rounded-3 me-2">
            <i class="fas fa-store-alt fs-5"></i>
        </div>
        <div>
            <h6 class="mb-0 fw-bold text-white">Vendor Central</h6>
            <small class="text-muted" style="font-size: 11px;">Master Admin Panel</small>
        </div>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="{{ route('vendor.dashboard') }}" class="nav-link text-white {{ request()->routeIs('vendor.dashboard') ? 'active bg-primary' : 'hover-dark' }}">
                <i class="fas fa-home me-2"></i> Master Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="{{ route('vendor.businesses.index') }}" class="nav-link text-white {{ request()->routeIs('vendor.businesses.*') ? 'active bg-primary' : 'hover-dark' }}">
                <i class="fas fa-building me-2"></i> Outlets Management
            </a>
        </li>
        <li class="nav-item mb-1">
    <a href="{{ route('vendor.employees.index') }}" class="nav-link text-white {{ request()->routeIs('vendor.employees.*') ? 'active bg-primary' : '' }}">
        <i class="fas fa-users-cog me-2"></i> Employee & Staff
    </a>
</li>
        <li class="nav-item mb-1">
            <a href="{{ route('vendor.card.index') ?? '#' }}" class="nav-link text-white">
                <i class="fas fa-id-card me-2"></i> Digital Cards & VCard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                <i class="fas fa-chart-line me-2"></i> Financial Reports
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="#" class="nav-link text-white">
                <i class="fas fa-cog me-2"></i> Account Settings
            </a>
        </li>
    </ul>

    <hr class="border-secondary">

    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 text-start py-2">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Mobile Offcanvas Sidebar Style / Script support -->
<style>
@media (max-width: 991.98px) {
    #sidebarMenu {
        position: fixed;
        top: 0;
        left: -260px;
        height: 100%;
        z-index: 1050;
        transition: all 0.3s ease-in-out;
    }
    #sidebarMenu.show {
        left: 0;
    }
}
</style>