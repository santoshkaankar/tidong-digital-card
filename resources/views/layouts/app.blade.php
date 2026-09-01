<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- WhatsApp & Social Media Preview Tags -->
    <meta property="og:title" content="My Digital Visiting Card">
    <meta property="og:description" content="Click here to view my digital visiting card and connect with me.">
    <meta property="og:image" content="https://tidong.in/images/card-banner.png">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Global Responsive & Material Styles -->
    <style>
        /* Mobile Responsive Sidebar Fix */
        @media (max-width: 991.98px) {
            .sidebar, aside {
                position: fixed !important;
                top: 0 !important;
                left: -300px !important; /* Mobile view me default hide rahega */
                width: 260px !important;
                height: 100vh !important;
                z-index: 1050 !important;
                transition: all 0.3s ease-in-out !important;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }
            
            /* Sidebar jab open hoga */
            .sidebar.show, aside.show {
                left: 0 !important;
            }

            /* Dark Overlay Background */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }
            
            .sidebar-overlay.show {
                display: block !important;
            }
        }

        /* Card Material Wrapper Pattern */
        .card-material-wrapper {
            background-size: cover !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            transition: background 0.3s ease-in-out, color 0.3s ease-in-out;
        }
    </style>
</head>
<body class="font-sans antialiased">
    
    <!-- Mobile Overlay Div -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="min-h-screen bg-light">
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Global Sidebar Toggle Script -->
    <script>
    function toggleSidebar() {
        let sidebar = document.querySelector('.sidebar') || document.querySelector('aside');
        let overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar) {
            sidebar.classList.toggle('show');
        }
        if (overlay) {
            overlay.classList.toggle('show');
        }
    }
    </script>
</body>
</html>