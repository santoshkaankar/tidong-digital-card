<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <head>
    <!-- CSS Code ko Style tag ke andar rakhein -->
    <style>
        /* Vendor Dashboard Mobile Layout Fix */
        @media (max-width: 768px) {
            .sidebar, #sidebar {
                position: fixed !important;
                top: 0;
                left: -100%;
                width: 260px !important;
                height: 100vh !important;
                z-index: 1090;
                transition: left 0.3s ease;
            }
            .sidebar.active, #sidebar.active {
                left: 0 !important;
            }
            .main-content, #content, body {
                width: 100% !important;
                max-width: 100vw !important;
                overflow-x: hidden !important;
                margin-left: 0 !important;
                padding: 10px !important;
            }
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS / Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- WhatsApp & Social Media Preview Tags -->
    <meta property="og:title" content="My Digital Visiting Card">
    <meta property="og:description" content="Click here to view my digital visiting card and connect with me.">
    <meta property="og:image" content="https://tidong.in/images/card-banner.png">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Card Material Wrapper Pattern Fix (Added here to support advanced textures) -->
    <style>
    .card-material-wrapper {
        background-size: cover !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
        transition: background 0.3s ease-in-out, color 0.3s ease-in-out;
    }
</style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-light">
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
