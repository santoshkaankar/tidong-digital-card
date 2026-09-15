@extends('member.partials.layout')

@section('title', 'Referral & Network - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-sitemap text-primary me-2"></i>My Referral & Network</h4>
            <p class="text-muted small mb-0">Manage your referral link, track your downline team, and view total stage payouts.</p>
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
            <small class="text-success mt-2 fw-semibold" id="copyMsg" style="display: none;">
                <i class="fas fa-check-circle me-1"></i> Link copied to clipboard successfully!
            </small>
        </div>
    </div>

    <!-- 2. Network Statistics Cards (Leg A & Leg B Active/Inactive Breakdown) -->
    <div class="row g-4 mb-4">
        <!-- Leg A (Left Team) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>Leg A (Left Team) Stats
                </h5>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Active IDs (Purchase Done):</span>
                    <span class="fw-bold text-success">{{ $stats['active_a'] }}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Inactive IDs:</span>
                    <span class="fw-bold text-danger">{{ $stats['inactive_a'] }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <span class="fw-bold text-dark">Total Leg A:</span>
                    <span class="fw-bold text-dark">{{ $stats['total_a'] }}</span>
                </div>
            </div>
        </div>

        <!-- Leg B (Right Team) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h5 class="fw-bold text-info mb-3">
                    <i class="fas fa-arrow-right me-2"></i>Leg B (Right Team) Stats
                </h5>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Active IDs (Purchase Done):</span>
                    <span class="fw-bold text-success">{{ $stats['active_b'] }}</span>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-muted">Inactive IDs:</span>
                    <span class="fw-bold text-danger">{{ $stats['inactive_b'] }}</span>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <span class="fw-bold text-dark">Total Leg B:</span>
                    <span class="fw-bold text-dark">{{ $stats['total_b'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grand Total Network Summary Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">Grand Total Network Members</h5>
                <p class="mb-0 text-white-50 small">Combined active and inactive members across both legs</p>
            </div>
            <h2 class="fw-bold mb-0 display-6">{{ $stats['grand_total'] }}</h2>
        </div>
    </div>

    <!-- 3. Sponsor & Account Info Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
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

    <!-- 4. Stage Incentives & Tax Deductions History Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-award text-warning me-2"></i>Stage Incentives & Tax Deductions History</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Stage No</th>
                        <th>Stage Name</th>
                        <th>Gross Amount</th>
                        <th>Admin (10%)</th>
                        <th>TDS</th>
                        <th>Net Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rewards as $reward)
                        <tr>
                            <td>#{{ $reward->stage_no }}</td>
                            <td class="fw-bold">{{ $reward->stage_name }}</td>
                            <td>₹{{ number_format($reward->gross_amount, 2) }}</td>
                            <td class="text-danger">-₹{{ number_format($reward->admin_charge, 2) }}</td>
                            <td class="text-danger">-₹{{ number_format($reward->tds_amount, 2) }}</td>
                            <td class="fw-bold text-success">₹{{ number_format($reward->net_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $reward->status == 'locked' ? 'warning text-dark' : 'success' }}">
                                    {{ ucfirst($reward->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No stages achieved yet. Keep growing your team!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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