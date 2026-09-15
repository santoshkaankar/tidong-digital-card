<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard - Tidong®')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; background: #0f172a; color: #fff; transition: all 0.3s ease; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; }
        #sidebar .sidebar-header { padding: 20px; background: #1e293b; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; color: #38bdf8; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; transition: all 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active a { color: #fff; background: #1e293b; border-left: 4px solid #38bdf8; }
        #content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; transition: all 0.3s ease; }
        .top-navbar { background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .ad-box { border-radius: 16px; color: #fff; padding: 20px; transition: all 0.3s ease; cursor: pointer; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08); position: relative; overflow: hidden; }
        .ad-box:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .info-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border: none; height: 100%; display: flex; flex-direction: column; }
        .mobile-card-action-btn { transition: all 0.2s ease; border-radius: 12px; }
        .mobile-card-action-btn:hover { transform: translateY(-2px); }
        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #sidebar.active { margin-left: 0; } #content { margin-left: 0; width: 100%; } }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Include Sidebar Component -->
    @include('member.partials.sidebar')

    <!-- Page Content Holder -->
    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand fw-bold text-dark mb-0 h6 d-flex align-items-center gap-2">
                <span class="text-secondary fw-normal">Welcome back,</span> <span class="text-primary">{{ Auth::user()->name }}!</span> 🚀
            </div>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-2" style="width: 280px;">
                        <li><h6 class="dropdown-header fw-bold">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small text-wrap rounded-2" href="#">🎉 Welcome to Tidong International Platform!</a></li>
                        <li><a class="dropdown-item small text-wrap rounded-2 mt-1" href="#">💼 Your digital card was viewed 14 times today.</a></li>
                    </ul>
                </div>
                <!-- Profile Settings Link -->
                <a href="{{ url('/member/configure') }}" class="fw-bold text-dark text-decoration-none d-none d-md-inline">
                    <i class="fas fa-user-circle text-primary me-1"></i> {{ Auth::user()->name }}
                </a>
            </div>
        </nav>

        <!-- Main Dynamic Content -->
        @yield('content')

        <footer class="text-center py-4 text-muted small border-top">
            &copy; {{ date('Y') }} Tidong® Portal. All rights reserved. Built for Global Reach.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
    </script>
    @stack('scripts')
</body>
</html>