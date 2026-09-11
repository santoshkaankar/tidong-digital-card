<!-- File Path: resources/views/vendor/restaurant/layout.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('vendor.restaurant.partials.head')
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .restaurant-layout-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100vw;
            position: relative;
        }

        /* Sidebar Styling */
        .restaurant-layout-wrapper > aside,
        .restaurant-layout-wrapper > .sidebar,
        .restaurant-sidebar {
            width: 260px;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1050 !important;
            overflow-y: auto;
            background: #ffffff;
            transition: all 0.3s ease-in-out;
        }

        /* Main Viewport Shift */
        .restaurant-main-viewport {
            flex-grow: 1;
            margin-left: 260px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
            transition: all 0.3s ease-in-out;
        }

        .restaurant-main-viewport > main {
            flex: 1;
            width: 100%;
        }

        /* Responsive Backdrop Screen */
        .layout-overlay-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1040;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* Mobile Viewport Rules */
        @media (max-width: 991.98px) {
            .restaurant-layout-wrapper > aside,
            .restaurant-layout-wrapper > .sidebar,
            .restaurant-sidebar {
                transform: translateX(-100%);
            }
            .restaurant-main-viewport {
                margin-left: 0;
            }
            /* Jab Sidebar Toggle Hoga Tab Ye Class Active Hogi */
            .restaurant-layout-wrapper.toggled > aside,
            .restaurant-layout-wrapper.toggled > .sidebar,
            .restaurant-layout-wrapper.toggled .restaurant-sidebar {
                transform: translateX(0) !important;
            }
            .restaurant-layout-wrapper.toggled .layout-overlay-backdrop {
                display: block !important;
                opacity: 1 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="restaurant-layout-wrapper" id="layoutWrapper">
        <!-- Dedicated Restaurant Sidebar -->
        @include('vendor.restaurant.partials.sidebar')

        <!-- Mobile Screen Dark Overlay -->
        <div class="layout-overlay-backdrop" id="layoutOverlayBackdrop"></div>

        <div class="restaurant-main-viewport">
            <!-- Dedicated Restaurant Header -->
            @include('vendor.restaurant.partials.header')

            <!-- Page Content -->
            <main class="p-3 p-md-4">
                @yield('content')
            </main>

            <!-- Dedicated Restaurant Footer -->
            @include('vendor.restaurant.partials.footer')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (Profile Dropdown & Controls Ke Liye Sabse Zaroori) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Mobile Sidebar Toggle Handler Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const wrapper = document.getElementById("layoutWrapper");
            const backdrop = document.getElementById("layoutOverlayBackdrop");

            function toggleSidebar() {
                if (wrapper) {
                    wrapper.classList.toggle("toggled");
                }
            }

            function closeSidebar() {
                if (wrapper) {
                    wrapper.classList.remove("toggled");
                }
            }

            // Document Delegation: Handle click on 3 lines button, close icon, or backdrop
            document.addEventListener("click", function (e) {
                if (e.target.closest("#restaurantSidebarToggle")) {
                    e.preventDefault();
                    toggleSidebar();
                } else if (e.target.closest("#closeRestaurantSidebar") || e.target === backdrop) {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>