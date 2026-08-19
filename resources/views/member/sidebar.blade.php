<!-- Sidebar Menu Component -->
<nav id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-layer-group"></i> Tidong® Portal
    </div>
    <ul class="list-unstyled components">
        <li class="{{ request()->is('member/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/member/dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        </li>

        <!-- My Profile & Master Details -->
        <li class="{{ request()->is('member/profile*') ? 'active' : '' }}">
            <a href="{{ url('/member/profile') }}"><i class="fas fa-user-circle"></i> My Profile & Stats</a>
        </li>

        <li class="{{ request()->is('member/search*') ? 'active' : '' }}">
            <a href="#"><i class="fas fa-search"></i> Advanced Search</a>
        </li>
        <li class="{{ request()->is('member/wallet*') ? 'active' : '' }}">
            <a href="{{ url('/member/wallet') }}"><i class="fas fa-wallet text-warning"></i> My Wallet</a>
        </li>

        <!-- Friend Circle with Submenu -->
        <li class="{{ request()->is('member/friend*') ? 'active' : '' }}">
            <a href="#friendCircleSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->is('member/friend*') ? 'true' : 'false' }}" class="dropdown-toggle">
                <i class="fas fa-users"></i> Friend Circle
            </a>
            <ul class="collapse list-unstyled {{ request()->is('member/friend*') ? 'show' : '' }}" id="friendCircleSubmenu">
                <li>
                    <a href="{{ url('/member/friend?type=friend') }}" class="ps-4">
                        <i class="fas fa-user-friends me-1"></i> Family
                    </a>
                </li>
                <li>
                    <a href="{{ url('/member/friend?type=friend') }}" class="ps-4">
                        <i class="fas fa-user-friends me-1"></i> Cousins
                    </a>
                </li>
                <li>
                    <a href="{{ url('/member/friend?type=friend') }}" class="ps-4">
                        <i class="fas fa-user-friends me-1"></i> Real Friends
                    </a>
                </li>
                
                <li>
                    <a href="{{ url('/member/friend?type=relative') }}" class="ps-4">
                        <i class="fas fa-user-tie me-1"></i> Relatives
                    </a>
                </li>
                <li>
                    <a href="{{ url('/member/friend?type=colleague') }}" class="ps-4">
                        <i class="fas fa-briefcase me-1"></i> Colleagues
                    </a>
                </li>
                <li>
                    <a href="{{ url('/member/friend?type=cousin') }}" class="ps-4">
                        <i class="fas fa-child me-1"></i> Friends
                    </a>
                </li>
            </ul>
        </li>

        <!-- Direct Create Card Link -->
        <li class="{{ request()->is('member/card/create') ? 'active' : '' }}">
            <a href="{{ url('/member/card/create') }}">
                <i class="fas fa-plus-circle text-success"></i> Create Card
            </a>
        </li>

        <!-- My Visiting Cards -->
        <li class="{{ request()->is('member/cards*') ? 'active' : '' }}">
            <a href="{{ url('/member/cards') }}"><i class="fas fa-id-card"></i> My Visiting Cards</a>
        </li>

        <!-- My Orders -->
        <li class="{{ request()->is('member/orders*') ? 'active' : '' }}">
            <a href="{{ url('/member/orders') }}"><i class="fas fa-receipt"></i> My Orders</a>
        </li>

        <!-- Logout Button -->
        <li class="mt-4 px-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm py-2">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</nav>