@extends('member.partials.layout')

@section('title', 'Referral & Network - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-sitemap text-primary me-2"></i>My Referral & Network</h4>
            <p class="text-muted small mb-0">Manage your referral link, track your downline team, and view total sales volume.</p>
        </div>
    </div>

    <!-- 1. Referral Link Sharing Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-2"><i class="fas fa-link text-primary me-2"></i>Your Referral Link</h6>
            <p class="text-muted small mb-3">Share this link with your friends and network to grow your downline team.</p>
            <div class="input-group">
                <input type="text" class="form-control rounded-start-3 bg-light" id="referralLink" value="{{ route('register', ['ref' => Auth::user()->referral_id]) }}" readonly>
                <button class="btn btn-primary px-4 rounded-end-3 fw-semibold" type="button" onclick="copyReferralLink()">
                    <i class="fas fa-copy me-1"></i> Copy Link
                </button>
            </div>
            <!-- Yahan se d-block hata diya hai taaki style="display: none;" properly kaam kare -->
            <small class="text-success mt-2 fw-semibold" id="copyMsg" style="display: none;">
                <i class="fas fa-check-circle me-1"></i> Link copied to clipboard successfully!
            </small>
        </div>
    </div>

    <!-- 2. Network Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Left Team Count -->
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-success p-3 bg-white rounded-4 shadow-sm h-100">
                <span class="text-muted small fw-semibold">Left Team Count</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">{{ Auth::user()->left_count ?? 0 }}</h3>
                <small class="text-success mt-2 d-block"><i class="fas fa-users me-1"></i> Active Members</small>
            </div>
        </div>

        <!-- Right Team Count -->
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-info p-3 bg-white rounded-4 shadow-sm h-100">
                <span class="text-muted small fw-semibold">Right Team Count</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">{{ Auth::user()->right_count ?? 0 }}</h3>
                <small class="text-info mt-2 d-block"><i class="fas fa-users me-1"></i> Active Members</small>
            </div>
        </div>

        <!-- Total Downline -->
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-warning p-3 bg-white rounded-4 shadow-sm h-100">
                <span class="text-muted small fw-semibold">Total Downline</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">
                    {{ (Auth::user()->left_count ?? 0) + (Auth::user()->right_count ?? 0) }}
                </h3>
                <small class="text-warning mt-2 d-block"><i class="fas fa-network-wired me-1"></i> Combined Team</small>
            </div>
        </div>

        <!-- Total Business / Sales -->
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-danger p-3 bg-white rounded-4 shadow-sm h-100">
                <span class="text-muted small fw-semibold">Total Business / Sales</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">₹ {{ number_format(Auth::user()->total_business ?? 0, 2) }}</h3>
                <small class="text-danger mt-2 d-block"><i class="fas fa-rupee-sign me-1"></i> Team Volume</small>
            </div>
        </div>
    </div>

    <!-- 3. Sponsor & Account Info Section -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold text-dark m-0"><i class="fas fa-user-shield text-dark me-2"></i>Sponsor & Upline Details</h6>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block mb-1">Your Referral ID</span>
                        <h6 class="fw-bold text-primary mb-0 font-monospace">{{ Auth::user()->referral_id ?? 'N/A' }}</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block mb-1">Sponsor ID (Upline)</span>
                        <h6 class="fw-bold text-dark mb-0 font-monospace">{{ Auth::user()->sponsor_id ?? 'N/A' }}</h6>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block mb-1">Network Position</span>
                        <h6 class="fw-bold text-success mb-0 text-uppercase">{{ Auth::user()->position ?? 'N/A' }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function copyReferralLink() {
    var copyText = document.getElementById("referralLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999); 
    navigator.clipboard.writeText(copyText.value);
    
    var msg = document.getElementById("copyMsg");
    msg.style.display = "block"; // Click karne par show hoga
    
    setTimeout(function() {
        msg.style.display = "none"; // 3 second baad gayab ho jayega
    }, 3000);
}
</script>
@endpush