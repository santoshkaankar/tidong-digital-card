<!-- File Path: resources/views/member/partials/header.blade.php -->

<header class="navbar navbar-expand bg-white border-bottom sticky-top px-3 py-2" style="z-index: 1020;">
    <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
        
        <!-- Left: Mobile Toggle Button & Title -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light d-lg-none border-0 p-2" type="button" id="memberSidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fw-bold fs-6 text-dark d-lg-none">Member Portal</span>
        </div>

        <!-- Right: Profile Dropdown -->
        <div class="d-flex align-items-center gap-2 ms-auto">
            <div class="dropdown ms-2">
                <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2 border-0 bg-transparent" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" class="rounded-circle" width="34" height="34" alt="Profile">
                    <span class="fw-semibold small text-dark d-none d-md-inline">{{ Auth::user()->name }}</span>
                    <i class="bi bi-chevron-down text-muted small ms-1"></i>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userProfileDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ url('/member/profile') }}">
                            <i class="bi bi-gear me-2"></i> Profile Settings
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>

                    <!-- 1. Language Switcher Partial -->
                    @if(View::exists('member.partials.language_switcher'))
                        @include('member.partials.language_switcher')
                    @endif

                    <li><hr class="dropdown-divider"></li>

                    <!-- 2. Theme Switcher Partial -->
                    @if(View::exists('member.partials.theme_switcher'))
                        @include('member.partials.theme_switcher')
                    @endif
                    
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</header>