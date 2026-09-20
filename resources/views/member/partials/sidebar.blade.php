<!-- Sidebar Menu Component -->
<nav id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-layer-group"></i> Tidong® Portal
    </div>
    <ul class="list-unstyled components">
        <li class="{{ request()->is('member/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/member/dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
        </li>

        <!-- 1. Tidong Super Hub Menu Link -->
        <li class="{{ request()->is('hub') ? 'active' : '' }}">
            <a href="{{ url('/hub') }}"><i class="fas fa-qrcode text-primary"></i> Tidong Super Hub</a>
        </li>

        <!-- 2. QR Scanner Menu Link -->
        <li>
            <a href="{{ url('/member/dashboard#qr-section') }}"><i class="fas fa-camera text-success"></i> QR Code Scanner</a>
        </li>

        <!-- My Profile & Master Details -->
        <li class="{{ request()->is('member/profile*') ? 'active' : '' }}">
            <a href="{{ url('/member/profile') }}"><i class="fas fa-user-circle"></i> My Profile & Stats</a>
        </li>

        <!-- Advanced Search with Active Route -->
        <li class="{{ request()->is('member/search*') ? 'active' : '' }}">
            <a href="{{ route('member.search') }}"><i class="fas fa-search"></i> Advanced Search</a>
        </li>

        <!-- Affiliates Dashboard Menu -->
        <li class="{{ request()->is('member/referral*') ? 'active' : '' }}">
            <a href="{{ url('/member/referral') }}"><i class="fas fa-users-cog text-info"></i> Affiliates</a>
        </li>

        <!-- Refer & Earn Modal Trigger Button -->
        <li>
            <a href="#" data-bs-toggle="modal" data-bs-target="#referEarnModal">
                <i class="fas fa-gift text-success"></i> Refer & Earn
            </a>
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

<!-- Refer & Earn Share Modal Popup -->
<div class="modal fade" id="referEarnModal" tabindex="-1" aria-labelledby="referEarnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="referEarnModalLabel">
                    <i class="fas fa-gift text-success me-2"></i>Refer & Earn
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Share your referral link instantly with your network via social media or messaging!</p>
                
                <!-- Referral Link Input -->
                <div class="input-group mb-4">
                    <input type="text" class="form-control rounded-start-3 bg-light" id="modalReferralLink" value="{{ route('register', ['ref' => Auth::user()->referral_id ?? '']) }}" readonly>
                    <button class="btn btn-primary px-3 rounded-end-3 fw-semibold" type="button" onclick="copyModalReferralLink()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <small class="text-success mb-3 d-block fw-semibold" id="modalCopyMsg" style="display: none;">
                    <i class="fas fa-check-circle me-1"></i> Link copied to clipboard!
                </small>

                <!-- Social & Messaging Share Buttons Grid -->
                <div class="row g-2">
                    <!-- WhatsApp Share -->
                    <div class="col-4">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode('Join Tidong Portal using my referral link: ' . route('register', ['ref' => Auth::user()->referral_id ?? ''])) }}" target="_blank" class="btn btn-success w-100 btn-sm py-2 fw-semibold d-flex align-items-center justify-content-center">
                            <i class="fab fa-whatsapp me-1 fs-5"></i> WhatsApp
                        </a>
                    </div>
                    <!-- Facebook Share -->
                    <div class="col-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('register', ['ref' => Auth::user()->referral_id ?? ''])) }}" target="_blank" class="btn btn-primary w-100 btn-sm py-2 fw-semibold d-flex align-items-center justify-content-center" style="background-color: #1877f2; border-color: #1877f2;">
                            <i class="fab fa-facebook-f me-1"></i> Facebook
                        </a>
                    </div>
                    <!-- Twitter / X Share -->
                    <div class="col-4">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('register', ['ref' => Auth::user()->referral_id ?? ''])) }}&text={{ urlencode('Join Tidong Portal using my referral link!') }}" target="_blank" class="btn btn-dark w-100 btn-sm py-2 fw-semibold d-flex align-items-center justify-content-center" style="background-color: #000000; border-color: #000000;">
                            <i class="fab fa-x-twitter me-1"></i> Twitter
                        </a>
                    </div>
                    <!-- Instagram -->
                    <div class="col-4">
                        <button type="button" onclick="copyModalReferralLink(); alert('Link copied! You can now paste it in your Instagram bio or story.');" class="btn w-100 btn-sm py-2 fw-semibold d-flex align-items-center justify-content-center text-white" style="background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%,#d6249f 60%,#285AEB 90%);">
                            <i class="fab fa-instagram me-1"></i> Instagram
                        </button>
                    </div>
                    <!-- Email Share -->
                    <div class="col-4">
                        <a href="mailto:?subject=Join Tidong Portal&body={{ urlencode('Hey, join Tidong Portal using my referral link: ' . route('register', ['ref' => Auth::user()->referral_id ?? ''])) }}" class="btn btn-danger w-100 btn-sm py-2 fw-semibold d-flex align-items-center justify-content-center">
                            <i class="fas fa-envelope me-1"></i> Email
                        </a>
                    </div>
                    <!-- SMS / Message Share -->
                    <div class="col-4">
                        <a href="sms:?body={{ urlencode('Join Tidong Portal using my referral link: ' . route('register', ['ref' => Auth::user()->referral_id ?? ''])) }}" class="btn btn-secondary w-100 btn-sm py-2 fw-semibold d-flex align-items-center justify-content-center">
                            <i class="fas fa-sms me-1"></i> SMS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyModalReferralLink() {
    var copyText = document.getElementById("modalReferralLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999); 
    navigator.clipboard.writeText(copyText.value);
    
    var msg = document.getElementById("modalCopyMsg");
    msg.style.display = "block";
    setTimeout(function() {
        msg.style.display = "none";
    }, 3000);
}
</script>
@endpush