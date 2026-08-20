<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tidong') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: #0f172a;
                background-image: 
                    radial-gradient(at 10% 10%, rgba(37, 99, 235, 0.35) 0px, transparent 50%),
                    radial-gradient(at 90% 90%, rgba(99, 102, 241, 0.25) 0px, transparent 50%);
                min-height: 100vh;
            }

            .crystal-plate {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 2px solid rgba(255, 255, 255, 1);
                border-radius: 24px;
                box-shadow: 
                    0 25px 50px -12px rgba(0, 0, 0, 0.5),
                    0 0 30px rgba(59, 130, 246, 0.3);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4">
            
            <!-- Branding Header Logo -->
            <div class="mb-6 text-center">
                <a href="/" class="text-3xl font-extrabold text-white tracking-wide inline-flex items-center justify-center gap-2 drop-shadow-lg">
                    <i class="fas fa-layer-group text-blue-400"></i>
                    <span>Tidong<span class="text-blue-400">®</span> Digital</span>
                </a>
            </div>

            <!-- Floating Bright Crystal Glass Form Box -->
            <div class="w-full sm:max-w-md px-8 py-8 crystal-plate mb-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>