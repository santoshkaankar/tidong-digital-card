<!-- File Path: resources/views/member/partials/sidebar.blade.php -->
<nav id="sidebar" class="member-sidebar" style="background: #0f172a; display: flex; flex-direction: column; justify-content: space-between; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1050; width: 260px; transition: all 0.3s ease;">
    
    <!-- Top Scrollable Area -->
    <div style="display: flex; flex-direction: column; height: calc(100vh - 70px); overflow: hidden;">
        <!-- Header with Close Button for Mobile -->
        <div class="sidebar-header d-flex align-items-center justify-content-between p-3" style="background: #1e293b; flex-shrink: 0;">
            <div class="d-flex align-items-center gap-2 text-info fw-bold fs-5">
                <i class="fas fa-layer-group"></i> Tidong® Portal
            </div>
            <!-- Mobile Close ('X') Button -->
            <button type="button" class="btn-close btn-close-white d-lg-none" id="closeMemberSidebar" aria-label="Close"></button>
        </div>

        <!-- Navigation Links (Scrollable) -->
        <ul class="list-unstyled components px-2 py-3 mb-0" style="overflow-y: auto; flex-grow: 1;">
            <!-- General Dashboard -->
            <li class="{{ request()->is('member/dashboard') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/dashboard') }}" class="text-decoration-name text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-home text-info"></i> Dashboard
                </a>
            </li>

            <li class="{{ request()->is('member/hub') ? 'active' : '' }} mb-1">
    <a href="{{ url('/member/hub') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
        <i class="fas fa-qrcode text-primary"></i> Tidong Super Hub
    </a>
</li>

            <li class="mb-1">
                <a href="{{ url('/member/dashboard#qr-section') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-camera text-success"></i> QR Code Scanner
                </a>
            </li>

            <!-- Divider Heading: Multi-Business Services -->
            <li class="px-2 pt-3 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Multi-Business Services
            </li>

            <!-- 1. Food & Restaurant -->
            <li class="{{ request()->is('member/food*') ? 'active' : '' }} mb-1">
                <a href="#" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-utensils text-warning"></i> Food & Restaurants
                </a>
            </li>

            <!-- 2. Grocery & Emporium -->
            <li class="{{ request()->is('member/grocery*') ? 'active' : '' }} mb-1">
                <a href="#" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-shopping-basket text-success"></i> Grocery & Emporium
                </a>
            </li>

            <!-- 3. Cab & Taxi Booking -->
            <li class="{{ request()->is('member/taxi*') ? 'active' : '' }} mb-1">
                <a href="#" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-taxi text-danger"></i> Taxi & Cab Rides
                </a>
            </li>

            <!-- 4. Hotel Bookings -->
            <li class="{{ request()->is('member/hotels*') ? 'active' : '' }} mb-1">
                <a href="#" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-hotel text-info"></i> Hotel Stays & Rooms
                </a>
            </li>

            <!-- 5. Money Exchange -->
            <li class="{{ request()->is('member/exchange*') ? 'active' : '' }} mb-1">
                <a href="#" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-exchange-alt text-primary"></i> Money Exchange & Pay
                </a>
            </li>

            <!-- Divider Heading: User & Account Hub -->
            <li class="px-2 pt-3 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Account & Management
            </li>

            <li class="{{ request()->is('member/orders*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/orders') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-receipt text-warning"></i> All Orders & Tracking
                </a>
            </li>

            <li class="{{ request()->is('member/profile*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/profile') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-user-circle text-info"></i> My Profile & Stats
                </a>
            </li>

            <li class="{{ request()->is('member/wallet*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/wallet') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-wallet text-warning"></i> My Wallet & Ledger
                </a>
            </li>

            <li class="{{ request()->is('member/referral*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/referral') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-users-cog text-info"></i> Affiliates Dashboard
                </a>
            </li>

            <li class="mb-1">
                <a href="#" data-bs-toggle="modal" data-bs-target="#referEarnModal" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-gift text-success"></i> Refer & Earn
                </a>
            </li>

            <!-- Friend Circle Submenu -->
            <li class="{{ request()->is('member/friend*') ? 'active' : '' }} mb-1">
                <a href="#friendCircleSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="text-decoration-none text-secondary d-flex align-items-center justify-content-between p-2 rounded-2">
                    <span class="d-flex align-items-center gap-2"><i class="fas fa-users text-primary"></i> Friend Circle</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <ul class="collapse list-unstyled ps-3 mt-1" id="friendCircleSubmenu">
                    <li><a href="{{ url('/member/friend?type=family') }}" class="text-secondary small d-block py-1 text-decoration-none"><i class="fas fa-user-friends me-1"></i> Family</a></li>
                    <li><a href="{{ url('/member/friend?type=relative') }}" class="text-secondary small d-block py-1 text-decoration-none"><i class="fas fa-user-tie me-1"></i> Relatives</a></li>
                    <li><a href="{{ url('/member/friend?type=colleague') }}" class="text-secondary small d-block py-1 text-decoration-none"><i class="fas fa-briefcase me-1"></i> Colleagues</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('member/cards*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/cards') }}" class="text-decoration-none text-secondary d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-id-card text-info"></i> My Visiting Cards
                </a>
            </li>
        </ul>
    </div>

    <!-- Bottom Fixed Logout Section -->
    <div class="p-3 border-top border-secondary bg-dark" style="height: 70px; flex-shrink: 0;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 btn-sm py-2 d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Logout Session
            </button>
        </form>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('memberSidebarToggle');
        const closeBtn = document.getElementById('closeMemberSidebar');
        const sidebar = document.getElementById('sidebar');

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
                sidebar.classList.toggle('show');
            });
        }

        if (closeBtn && sidebar) {
            closeBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                sidebar.classList.remove('active');
                sidebar.classList.remove('show');
            });
        }

        // Bahar click karne par bhi close ho jaye
        document.addEventListener('click', function (e) {
            if (sidebar && sidebar.classList.contains('active') && !sidebar.contains(e.target) && toggleBtn && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
                sidebar.classList.remove('show');
            }
        });
    });
</script>