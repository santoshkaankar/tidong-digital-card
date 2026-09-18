<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Retail Dashboard') — Retail Shop</title>
    
    <!-- Tailwind / Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col md:flex-row relative">
        
        <!-- Sidebar Partial Include -->
        @include('retail.layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0" style="margin-left: 260px;">
            
            <!-- Header Partial Include -->
            @include('retail.layouts.partials.header')

            <!-- Main Content Section -->
            <main class="flex-grow p-4">
                @yield('content')
            </main>

            <!-- Footer Partial -->
            @include('retail.layouts.partials.footer')
        </div>
    </div>

    @include('retail.layouts.partials.scripts')
    @stack('scripts')
</body>
</html>