<!DOCTYPE html>
<html lang="en">
<head>
    @include('vendor.restaurant.partials.head')
</head>
<body>
    <div class="restaurant-layout-wrapper">
        <!-- Sidebar -->
        @include('vendor.restaurant.partials.sidebar')

        <div class="restaurant-main-viewport">
            <!-- Header -->
            @include('vendor.restaurant.partials.header')

            <!-- Main Page Content -->
            <main class="p-3 p-md-4">
                @yield('content')
            </main>

            <!-- Footer -->
            @include('vendor.restaurant.partials.footer')
        </div>
    </div>
</body>
</html>