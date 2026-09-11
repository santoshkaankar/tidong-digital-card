<header class="navbar navbar-expand bg-white border-bottom sticky-top px-3 py-2" style="z-index: 1000;">
    <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
        
        <!-- Left: Mobile Toggle & Title -->
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light d-lg-none border-0 p-2" type="button" id="restaurantSidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fw-bold fs-6 text-dark d-lg-none">Restaurant Hub</span>
        </div>

        <!-- Right: Actions & User Info -->
        <div class="d-flex align-items-center gap-2 ms-auto">

            <!-- Profile Dropdown -->
            <div class="dropdown ms-2">
                <button class="btn btn-link text-decoration-none p-0 d-flex align-items-center gap-2 border-0 bg-transparent" type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" class="rounded-circle" width="34" height="34" alt="Profile">
                    <span class="fw-semibold small text-dark d-none d-md-inline">{{ Auth::user()->name }}</span>
                    <i class="bi bi-chevron-down text-muted small ms-1"></i>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="userProfileDropdown">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-gear me-2"></i> Settings
                        </a>
                    </li>
                    
                    @if(View::exists('vendor.restaurant.partials.language_switcher'))
                        <li>
                            <div class="px-3 py-1">
                                @include('vendor.restaurant.partials.language_switcher')
                            </div>
                        </li>
                    @endif

                    @if(View::exists('vendor.restaurant.partials.theme_switcher'))
                        <li>
                            <div class="px-3 py-1">
                                @include('vendor.restaurant.partials.theme_switcher')
                            </div>
                        </li>
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

<!-- Bootstrap 5 JS Bundle (Check karein ki ye aapke main layout file me </body> tag se pehle shaamil ho) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>