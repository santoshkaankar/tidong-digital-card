<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3 py-3 shadow-sm">
    <div class="container-fluid">
        <!-- Mobile Toggle Button -->
        <button class="btn btn-outline-primary d-lg-none me-3" type="button" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <span class="navbar-brand fw-bold text-dark fs-6 fs-md-5">
            <i class="fas fa-shield-alt text-primary me-2"></i> Master Vendor Portal
        </span>

        <div class="d-flex align-items-center ms-auto">
            <span class="badge bg-success-subtle text-success border border-success px-2 py-2 me-2 me-md-3 rounded-pill text-truncate" style="max-width: 150px;">
                <small>Active:</small> <strong>{{ session('active_business_name', 'None') }}</strong>
            </span>
            <span class="fw-semibold text-secondary d-none d-md-inline">{{ Auth::user()->name ?? 'Vendor' }}</span>
        </div>
    </div>
</nav>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebarMenu');
    sidebar.classList.toggle('show');
}
</script>