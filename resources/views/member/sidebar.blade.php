<!-- Sidebar Menu Component -->
<nav id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-layer-group"></i> Tidong® Portal
    </div>
    <ul class="list-unstyled components">
        <li class="{{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
            <a href="{{ route('member.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li class="{{ request()->routeIs('member.search') ? 'active' : '' }}">
            <a href="{{ route('member.search') }}"><i class="fas fa-search"></i> Advanced Search</a>
        </li>
        <li>
            <a href="#"><i class="fas fa-wallet text-warning"></i> My Wallet</a>
        </li>
        <li>
            <a href="#"><i class="fas fa-users"></i> Friend Circle</a>
        </li>
        <li class="{{ request()->routeIs('member.card.create') ? 'active' : '' }}">
            <a href="{{ route('member.card.create') }}"><i class="fas fa-id-badge"></i> Card Detail</a>
        </li>
        <li class="{{ request()->routeIs('member.card.customize*') ? 'active' : '' }}">
    <a href="{{ route('member.card.customize', 1) }}"><i class="fas fa-plus-circle text-success"></i> Create Card</a>
</li>
        <li class="{{ request()->routeIs('member.cards.index') ? 'active' : '' }}">
            <a href="{{ route('member.cards.index') }}"><i class="fas fa-id-card"></i> My Visiting Cards</a>
        </li>
        <li>
            <a href="#"><i class="fas fa-user-circle"></i> My Profile & Stats</a>
        </li>
        <li>
            <a href="#"><i class="fas fa-receipt"></i> My Orders</a>
        </li>
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