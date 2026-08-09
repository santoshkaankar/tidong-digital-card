<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Digital Visiting Card</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        /* Sidebar Styling */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #212529;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
        }
        #sidebar .brand {
            font-size: 1.2rem;
            padding: 20px;
            background: #1a1e21;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #373b3e;
        }
        #sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            margin: 4px 10px;
            border-radius: 8px;
            transition: 0.2s;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        /* Main Content Styling */
        #main-content {
            margin-left: 260px;
            padding: 20px;
        }
        @media (max-width: 768px) {
            #sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            #main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar" class="d-flex flex-column">
        <div class="brand">
            <i class="fas fa-shield-alt text-primary me-2"></i> Admin Panel
        </div>
        <ul class="nav nav-pills flex-column mb-auto p-2">
            <li class="nav-item mb-1">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('card.create') }}" class="nav-link {{ request()->routeIs('card.create') ? 'active' : '' }}">
                    <i class="fas fa-id-card me-2"></i> Create Card
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
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand navbar-light bg-white px-4 rounded-4 shadow-sm mb-4">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h5 fw-bold text-secondary">Welcome, Admin 👋</span>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <span class="fw-bold text-dark">{{ Auth::user()->name ?? 'User' }}</span>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dynamic Page Content -->
        <div class="container-fluid px-0">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>