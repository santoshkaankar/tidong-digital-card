<nav class="navbar navbar-expand-lg navbar-light sticky-top py-3 bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-primary" href="#">
            <i class="fas fa-layer-group me-2"></i>Tidong<span class="text-dark">®</span> Digital
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-3">
                <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#quick-services">Services</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="#ads-section">Sponsored Ads</a></li>
                
                @auth
                    <li class="nav-item">
                        @php
                            $role = Auth::user()->role ?? 'user';
                            $dashboardRoute = match($role) {
                                'admin' => route('admin.dashboard'),
                                'business' => route('vendor.dashboard'), 
                                'employee' => route('employee.dashboard'),
                                'customer' => route('member.dashboard'),
                                default => route('member.dashboard')
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="btn btn-dark btn-sm px-3 rounded-pill">
                            <i class="fas fa-columns me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-0" href="#" role="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->profile_pic ?? false)
                                <img src="{{ asset('storage/' . Auth::user()->profile_pic) }}" alt="Profile" class="user-avatar" style="width:35px; height:35px; border-radius:50%; object-fit:cover;">
                            @else
                                <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-6" style="width:35px; height:35px; border-radius:50%;">
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
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill">Login</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4 shadow-sm rounded-pill">Register</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</nav>