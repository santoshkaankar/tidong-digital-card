<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Store Vendor Dashboard - Tidong Digital</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- Global Header -->
    @include('partials.header')

    <!-- Mobile Toggle Bar -->
    <div class="bg-white border-bottom py-2 px-3 d-lg-none d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark small"><i class="fas fa-store text-primary me-1"></i> Shopping Store Panel</span>
        <button class="btn btn-outline-primary btn-sm" id="mobileSidebarToggle">
            <i class="bi bi-list fs-5"></i> Menu
        </button>
    </div>

    <!-- Main Content Container -->
    <div class="container-fluid py-4 min-vh-100">
        <div class="row g-4">
            <!-- Sidebar Column -->
            <div class="col-lg-3 col-md-4">
                @include('shopping.partials.sidebar')
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9 col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h3 class="fw-bold text-dark mb-1 fs-4">
                                <i class="fas fa-store text-primary me-2"></i> Shopping Store Dashboard
                            </h3>
                            <p class="text-muted mb-0 small">Aap apne is retail store ke products, categories, variants aur customer orders ko yahan manage kar rahe hain.</p>
                        </div>
                        <div>
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">Status: Active Store</span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted small mb-1">Total Products</h6>
                            <h3 class="fw-bold text-primary mb-0">145</h3>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted small mb-1">Store Orders</h6>
                            <h3 class="fw-bold text-success mb-0">38</h3>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                            <h6 class="text-muted small mb-1">Total Revenue</h6>
                            <h3 class="fw-bold text-danger mb-0">₹45,200</h3>
                        </div>
                    </div>
                </div>

                <!-- Quick Management Info -->
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-secondary mb-3">Retail Catalog Control</h5>
                    <p class="text-muted mb-0">Left sidebar me diye gaye options se aap naye products add karne, unke attributes aur units manage karne ke sath-sath customer orders ko asani se track kar sakte hain.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Footer -->
    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('mobileSidebarToggle');
            const closeBtn = document.getElementById('closeSidebarBtn');
            const sidebar = document.getElementById('shoppingSidebar');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('show-mobile-sidebar');
                });
            }
            if (closeBtn && sidebar) {
                closeBtn.addEventListener('click', function () {
                    sidebar.classList.remove('show-mobile-sidebar');
                });
            }
        });
    </script>
</body>
</html>