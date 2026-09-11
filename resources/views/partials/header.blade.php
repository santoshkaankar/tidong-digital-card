<header class="top-navbar">
    <div class="search-bar">
        <i class="bi bi-search"></i>
        <input type="text" class="form-control" placeholder="Search orders, menu, or tables...">
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Language Switcher -->
        @include('partials.language_switcher')

        <!-- Theme Switcher -->
        @include('partials.theme_switcher')

        <button class="btn btn-light rounded-circle position-relative p-2" style="width: 40px; height: 40px;">
            <i class="bi bi-bell text-secondary"></i>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
        </button>

        <div class="vr mx-1" style="height: 24px;"></div>

        <!-- User Profile Dropdown (FIXED TEXT COLOR) -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle gap-2" data-bs-toggle="dropdown" style="color: var(--text-main);">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" class="rounded-circle" width="38" height="38" alt="Profile">
                <div class="d-none d-sm-block text-start">
                    <span class="fw-bold d-block lh-1" style="font-size: 0.875rem; color: var(--text-main);">{{ Auth::user()->name }}</span>
                    <small style="font-size: 0.75rem; color: var(--text-muted);">Manager</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>