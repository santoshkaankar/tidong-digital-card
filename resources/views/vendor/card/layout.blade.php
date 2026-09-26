<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Digital Card Management')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card-navbar {
            background-color: #1e293b;
        }
        .nav-card-link {
            color: #cbd5e1;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .nav-card-link:hover, .nav-card-link.active {
            background-color: #334155;
            color: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>

    @php
        // Dynamic Back to Dashboard Link Logic
        // Ye current URL ya user role ke basis par automatic dashboard route set karega
        $user = auth()->user();
        $dashboardUrl = url('/dashboard'); // Default Fallback

        if ($user) {
            if (isset($user->role) && Route::has($user->role . '.dashboard')) {
                $dashboardUrl = route($user->role . '.dashboard');
            } elseif (request()->is('taxi*') || (isset($user->type) && $user->type == 'taxi')) {
                $dashboardUrl = url('/taxi/dashboard');
            } elseif (request()->is('hotel*') || (isset($user->type) && $user->type == 'hotel')) {
                $dashboardUrl = url('/hotel/dashboard');
            } elseif (request()->is('retail*') || (isset($user->type) && $user->type == 'retail')) {
                $dashboardUrl = url('/retail/dashboard');
            } elseif (request()->is('emporium*') || (isset($user->type) && $user->type == 'emporium')) {
                $dashboardUrl = url('/emporium/dashboard');
            }
        }
    @endphp

    <!-- Card Navigation Bar -->
    <nav class="navbar navbar-expand-lg card-navbar navbar-dark sticky-top shadow-sm py-2">
        <div class="container-fluid px-4">
            <!-- Brand / Title -->
            <a class="navbar-brand fw-bold text-warning d-flex align-items-center me-4" href="{{ route('vendor.card.index') }}">
                <i class="fa-solid fa-address-card fa-lg me-2"></i> Digital Card Studio
            </a>

            <!-- Quick Navigation Links -->
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('vendor.card.index') }}" class="nav-card-link {{ request()->routeIs('vendor.cards.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group me-1"></i> All Cards
                </a>
                <a href="{{ route('vendor.card.create') }}" class="nav-card-link {{ request()->routeIs('vendor.cards.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus-circle me-1"></i> Create / Config Card
                </a>
            </div>

            <!-- Dynamic Back To Main Dashboard Button -->
            <div class="ms-auto d-flex align-items-center gap-2">
                <a href="{{ $dashboardUrl }}" class="btn btn-outline-light btn-sm px-3 fw-bold rounded-pill">
                    <i class="fa-solid fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>