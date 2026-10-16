<!-- File Path: resources/views/member/partials/sidebar.blade.php -->
<style>
.member-sidebar .sidebar-link {
    color: #cbd5e1 !important;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}
.member-sidebar .sidebar-link:hover,
.member-sidebar li.active > .sidebar-link {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.12) !important;
}
.member-sidebar .submenu-link {
    color: #94a3b8 !important;
    transition: all 0.2s ease-in-out;
}
.member-sidebar .submenu-link:hover {
    color: #ffffff !important;
}
.badge-demo {
    background-color: #f59e0b;
    color: #000;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}
.badge-pro {
    background-color: #10b981;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
}
</style>

@php
    $hasProSub = \Illuminate\Support\Facades\DB::table('game_subscriptions')
                    ->where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->exists();
@endphp

<nav id="sidebar" class="member-sidebar" style="background: #0f172a; display: flex; flex-direction: column; justify-content: space-between; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1050; width: 260px; transition: all 0.3s ease;">
    
    <div style="display: flex; flex-direction: column; height: calc(100vh - 70px); overflow: hidden;">
        <div class="sidebar-header d-flex align-items-center justify-content-between p-3" style="background: #1e293b; flex-shrink: 0;">
            <div class="d-flex align-items-center gap-2 text-info fw-bold fs-5">
                <i class="fas fa-layer-group"></i> Tidong® Portal
            </div>
            <button type="button" class="btn-close btn-close-white d-lg-none" id="closeMemberSidebar" aria-label="Close"></button>
        </div>

        <ul class="list-unstyled components px-2 py-3 mb-0" style="overflow-y: auto; flex-grow: 1;">
            <li class="{{ request()->is('member/dashboard') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/dashboard') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-home text-info"></i> Dashboard
                </a>
            </li>

            <!-- 🎮 3D Game (Free Demo - Resets) -->
            <li class="{{ request()->is('game/demo*') ? 'active' : '' }} mb-1">
                <a href="{{ route('game.index') }}" class="sidebar-link text-decoration-none d-flex align-items-center justify-content-between p-2 rounded-2" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fas fa-gamepad text-warning"></i> <span>3D Game</span>
                    </span>
                    <span class="badge-demo">RESET</span>
                </a>
            </li>

            <!-- 👑 3D Game Pro (Conditional: Direct Link if Active, Modal if Not) -->
            <li class="{{ request()->is('member/game/pro*') ? 'active' : '' }} mb-1">
                @if($hasProSub)
                    <a href="{{ url('/member/game/pro') }}" class="sidebar-link text-decoration-none d-flex align-items-center justify-content-between p-2 rounded-2" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3);">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-crown text-success"></i> <span style="color: #34d399; font-weight: bold;">3D Game Pro</span>
                        </span>
                        <span class="badge bg-success text-white" style="font-size: 0.6rem;">ACTIVE</span>
                    </a>
                @else
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#payAndPlayModal" class="sidebar-link text-decoration-none d-flex align-items-center justify-content-between p-2 rounded-2" style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3);">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-crown text-success"></i> <span style="color: #34d399; font-weight: bold;">3D Game Pro</span>
                        </span>
                        <span class="badge-pro"><i class="fas fa-lock me-0.5"></i> PRO</span>
                    </a>
                @endif
            </li>

            <!-- 👨‍👩‍👧‍👦 Child Accounts -->
            <li class="{{ request()->is('member/children*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/children') }}" class="sidebar-link text-decoration-none d-flex align-items-center justify-content-between p-2 rounded-2">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fas fa-child text-info"></i> Child Accounts
                    </span>
                    <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 small">+ Create</span>
                </a>
            </li>

            <!-- 💳 Credit Facility -->
            <li class="{{ request()->is('member/wallet*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/wallet') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-credit-card text-warning"></i> My Wallet & ₹5L Credit
                </a>
            </li>

            <!-- 🌐 Tidong Super Hub -->
            <li class="{{ request()->is('member/hub*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/hub') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-qrcode text-primary"></i> Tidong Super Hub
                </a>
            </li>

            <!-- 📷 QR Scanner -->
            <li class="mb-1">
                <a href="javascript:void(0)" onclick="openGlobalQrScanner()" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-camera text-success"></i> QR Code Scanner
                </a>
            </li>

            <!-- 🧾 All Orders -->
            <li class="{{ request()->is('member/orders*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/orders') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-receipt text-warning"></i> All Orders & Tracking
                </a>
            </li>

            <!-- 👤 Profile & Stats -->
            <li class="{{ request()->is('member/profile*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/profile') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-user-circle text-info"></i> My Profile & Stats
                </a>
            </li>

            <!-- 🤝 Affiliates Dashboard -->
            <li class="{{ request()->is('member/referral*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/referral') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-users-cog text-info"></i> Affiliates Dashboard
                </a>
            </li>

            <!-- 🎁 Refer & Earn -->
            <li class="mb-1">
                <a href="#" data-bs-toggle="modal" data-bs-target="#referEarnModal" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-gift text-success"></i> Refer & Earn
                </a>
            </li>

            <!-- 👥 Friend Circle -->
            <li class="{{ request()->is('member/friend*') ? 'active' : '' }} mb-1">
                <a href="#friendCircleSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="sidebar-link text-decoration-none d-flex align-items-center justify-content-between p-2 rounded-2">
                    <span class="d-flex align-items-center gap-2"><i class="fas fa-users text-primary"></i> Friend Circle</span>
                    <i class="fas fa-chevron-down small"></i>
                </a>
                <ul class="collapse list-unstyled ps-3 mt-1" id="friendCircleSubmenu">
                    <li><a href="{{ url('/member/friend?type=family') }}" class="submenu-link small d-block py-1 text-decoration-none"><i class="fas fa-user-friends me-1"></i> Family</a></li>
                    <li><a href="{{ url('/member/friend?type=relative') }}" class="submenu-link small d-block py-1 text-decoration-none"><i class="fas fa-user-tie me-1"></i> Relatives</a></li>
                    <li><a href="{{ url('/member/friend?type=colleague') }}" class="submenu-link small d-block py-1 text-decoration-none"><i class="fas fa-briefcase me-1"></i> Colleagues</a></li>
                </ul>
            </li>

            <!-- 🎴 Visiting Cards -->
            <li class="{{ request()->is('member/cards*') ? 'active' : '' }} mb-1">
                <a href="{{ url('/member/cards') }}" class="sidebar-link text-decoration-none d-flex align-items-center gap-2 p-2 rounded-2">
                    <i class="fas fa-id-card text-info"></i> My Visiting Cards
                </a>
            </li>
        </ul>
    </div>

    <div class="p-3 border-top border-secondary bg-dark" style="height: 70px; flex-shrink: 0;">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 btn-sm py-2 d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Logout Session
            </button>
        </form>
    </div>
</nav>

<!-- 💳 PAY & PLAY PRO YEARLY PASS MODAL (199 T-Coins / Year) -->
<div class="modal fade" id="payAndPlayModal" tabindex="-1" aria-labelledby="payAndPlayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden" style="background: #0f172a; color: #fff;">
            <div class="modal-header border-secondary p-3" style="background: #1e293b;">
                <h5 class="modal-title font-bold text-warning d-flex align-items-center gap-2" id="payAndPlayModalLabel">
                    <i class="fas fa-crown"></i> 3D Game Pro Yearly Pass
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <span class="badge bg-success text-white px-3 py-1 rounded-pill uppercase font-bold" style="letter-spacing: 1px;">
                        Special Launch Offer
                    </span>
                </div>
                <h3 class="fw-bold text-white mb-2">Unlock Continuous World</h3>
                <p class="text-muted small mb-4">
                    Demo game resets every session. Grab the Yearly Pro Pass using T-Coins to save your builds, unlock real brand rewards, and play endlessly for a full year!
                </p>

                <!-- T-Coin Yearly Pricing Box -->
                <div class="card border-warning bg-dark text-white rounded-3 p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <span class="d-block font-bold text-warning"><i class="fas fa-coins me-1 text-warning"></i> Pro Yearly Pass</span>
                            <span class="small text-muted">Full 12 Months Access</span>
                        </div>
                        <div class="text-end">
                            <span class="fs-4 fw-bold text-warning"><i class="fas fa-coins me-1"></i> 199</span>
                            <span class="small text-muted d-block">T-Coins / Year</span>
                        </div>
                    </div>
                </div>

                <!-- Pay Form via T-Coins -->
                <form action="{{ url('/member/game/pay-subscription-tcoins') }}" method="POST">
                    @csrf
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold rounded-pill shadow-sm">
                            <i class="fas fa-bolt me-1"></i> Pay 199 T-Coins & Unlock Yearly Pass
                        </button>
                        <button type="button" class="btn btn-link text-muted btn-sm text-decoration-none" data-bs-dismiss="modal">
                            Continue with Free Demo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

        document.addEventListener('click', function (e) {
            if (sidebar && sidebar.classList.contains('active') && !sidebar.contains(e.target) && toggleBtn && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
                sidebar.classList.remove('show');
            }
        });
    });
</script>