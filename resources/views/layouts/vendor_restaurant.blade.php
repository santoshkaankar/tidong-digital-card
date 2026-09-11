<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('vendor.restaurant.partials.head')
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

        /* Sidebar Styling Fix */
        .restaurant-layout-wrapper > aside,
        .restaurant-layout-wrapper > .sidebar {
            width: 260px;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1030;
            overflow-y: auto;
        }

        /* Main Viewport Shift Fix */
        .restaurant-main-viewport {
            flex-grow: 1;
            margin-left: 260px; /* Sidebar width matching offset */
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .restaurant-main-viewport > main {
            flex: 1;
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .restaurant-layout-wrapper > aside,
            .restaurant-layout-wrapper > .sidebar {
                margin-left: -260px;
            }
            .restaurant-main-viewport {
                margin-left: 0;
            }
            .restaurant-layout-wrapper.toggled > aside,
            .restaurant-layout-wrapper.toggled > .sidebar {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="restaurant-layout-wrapper">
        <!-- Dedicated Restaurant Sidebar -->
        @include('vendor.restaurant.partials.sidebar')

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

    @stack('scripts')
</body>
</html>