<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong® - Smart Digital Visiting Cards & Dynamic Catalogs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #333; }
        .hero-section { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; padding: 100px 0; }
        
        /* Enhanced Feature Cards with Modern Shadows and Borders */
        .feature-card { 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            border-radius: 16px; 
            transition: all 0.4s ease; 
            background: #ffffff; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #38bdf8);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .feature-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 35px -10px rgba(37, 99, 235, 0.15), 0 12px 15px -8px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.3);
        }
        .feature-card:hover::before {
            opacity: 1;
        }

        .btn-custom-primary { background: #2563eb; color: #fff; padding: 8px 20px; border-radius: 50px; font-weight: 600; }
        .btn-custom-primary:hover { background: #1d4ed8; color: #fff; }
        .navbar { background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .icon-box { width: 55px; height: 55px; display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; background: rgba(37, 99, 235, 0.08); color: #2563eb; font-size: 1.5rem; margin-bottom: 20px; transition: all 0.3s ease; }
        .feature-card:hover .icon-box { background: #2563eb; color: #fff; transform: scale(1.05); }
        .user-avatar { width: 35px; height: 35px; object-fit: cover; border-radius: 50%; border: 2px solid #2563eb; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-primary" href="#">
                <i class="fas fa-layer-group me-2"></i>Tidong<span class="text-dark">®</span> Digital
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#howitworks">How It Works</a></li>
                    
                    @auth
                        <!-- Dashboard Link Based on Role (Fixed Routes) -->
                        <li class="nav-item">
                            @php
                                $role = Auth::user()->role ?? 'user';
                                $dashboardRoute = match($role) {
                                    'admin' => route('admin.dashboard'),
                                    'business' => route('vendor.dashboard'), 
                                    'employee' => route('employee.dashboard'),
                                    'customer' => route('customer.dashboard'),
                                    default => route('customer.dashboard')
                                };
                            @endphp
                            <a href="{{ $dashboardRoute }}" class="btn btn-dark btn-sm px-3 rounded-pill">
                                <i class="fas fa-columns me-1"></i> Dashboard
                            </a>
                        </li>

                        <!-- Logged In User Profile & Logout -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->profile_pic ?? false)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_pic) }}" alt="Profile" class="user-avatar">
                                @else
                                    <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-6">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="fw-bold text-dark">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-semibold">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- Logged Out Options -->
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-custom-primary btn-sm px-4 shadow-sm">Register</a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge bg-primary bg-opacity-25 text-primary mb-3 px-3 py-2 rounded-pill fw-bold">✨ Built For Everyone & Every Business</span>
                    <h1 class="display-4 fw-bold mb-4 lh-base">Your Interactive Digital Identity & Business Catalogs, Shared in One Click</h1>
                    <p class="lead text-muted mb-5">Create your stunning personal visiting card or business profile. Share it instantly with anyone—allowing them to chat on WhatsApp, call, or browse your product catalogs with a single tap.</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                        @auth
                            <a href="{{ $dashboardRoute ?? route('vendor.dashboard') }}" class="btn btn-custom-primary btn-lg shadow">Go to Dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-custom-primary btn-lg shadow">Create Your Card Now</a>
                        @endauth
                        <a href="#features" class="btn btn-outline-light btn-lg rounded-pill px-4">Explore Features</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 shadow-lg backdrop-blur border border-secondary border-opacity-25">
                        <div class="d-flex justify-content-center gap-3 mb-3">
                            <span class="badge bg-success p-2 fs-5 rounded-circle shadow-sm"><i class="fab fa-whatsapp"></i></span>
                            <span class="badge bg-primary p-2 fs-5 rounded-circle shadow-sm"><i class="fas fa-phone"></i></span>
                            <span class="badge bg-danger p-2 fs-5 rounded-circle shadow-sm"><i class="fas fa-envelope"></i></span>
                        </div>
                        <h4 class="fw-bold text-white mb-2">One-Tap Direct Connection</h4>
                        <p class="text-light small mb-0">No apps required for viewers. They click WhatsApp icon and land straight into your WhatsApp chat instantly!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Why Everyone Loves Tidong® Platform</h2>
                <p class="text-muted">Designed for seamless networking, personal branding, and multi-business management</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-box"><i class="fas fa-id-card"></i></div>
                        <h4 class="fw-bold h5 mb-3 text-dark">Interactive Digital Visiting Card</h4>
                        <p class="text-muted small mb-0">Create your powerful digital identity with clickable social links, WhatsApp direct messaging, call buttons, and address links.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-box"><i class="fas fa-box-open"></i></div>
                        <h4 class="fw-bold h5 mb-3 text-dark">Smart Catalogs & Product Lists</h4>
                        <p class="text-muted small mb-0">For shops, vendors, & restaurants: select products/items from the global master, set custom pricing (MRP/Sale), and generate QR codes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-box"><i class="fas fa-share-alt"></i></div>
                        <h4 class="fw-bold h5 mb-3 text-dark">Instant Share & Connect</h4>
                        <p class="text-muted small mb-0">Share your unique card or business catalog link via WhatsApp or social media with anyone, anywhere without paper hassle.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <div class="mb-2 d-flex justify-content-center gap-4 small">
                <a href="#" class="text-decoration-none text-muted">About Us</a>
                <a href="#" class="text-decoration-none text-muted">Terms & Conditions</a>
                <a href="#" class="text-decoration-none text-muted">Privacy Policy</a>
                <a href="#" class="text-decoration-none text-muted">Contact Us</a>
            </div>
            <hr class="border-secondary my-3">
            <p class="mb-1 small text-muted">&copy; 2023 - 2026 Tidong Marketing Pvt. Ltd. All rights reserved.</p>
            <p class="mb-0 text-secondary" style="font-size: 12px;">Tidong® is a registered trademark.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>