@extends('member.partials.layout')

@section('title', 'User Dashboard - Tidong®')

@push('styles')
<style>
    /* YouTube Style Cover & Profile Layout Styles */
    .profile-container { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; overflow: hidden; }
    .banner-wrapper { position: relative; width: 100%; height: 200px; background: #e2e8f0; }
    .banner-img { width: 100%; height: 100%; object-fit: cover; }
    .banner-upload-btn { position: absolute; bottom: 15px; right: 15px; background: rgba(0, 0, 0, 0.7); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: 0.2s; }
    .banner-upload-btn:hover { background: rgba(0, 0, 0, 0.9); }
    
    .channel-header-section { padding: 0 30px 25px 30px; position: relative; }
    .avatar-wrapper { position: relative; width: 120px; height: 120px; margin-top: -60px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); background: #fff; overflow: hidden; display: inline-block; }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-upload-overlay { position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0,0,0,0.6); color: white; text-align: center; font-size: 11px; padding: 4px 0; cursor: pointer; opacity: 0; transition: 0.2s; }
    .avatar-wrapper:hover .avatar-upload-overlay { opacity: 1; }

    @media (max-width: 768px) {
        .banner-wrapper { height: 140px; }
        .avatar-wrapper { width: 90px; height: 90px; margin-top: -45px; }
        .channel-header-section { padding: 0 15px 20px 15px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">

    <!-- 1. YouTube Style Cover & Profile Section (Top of Dashboard) -->
    <div class="profile-container mb-4">
        <div class="banner-wrapper">
            @if(isset($card) && $card->banner_image)
                <img src="{{ asset('storage/' . $card->banner_image) }}" alt="Channel Banner" class="banner-img">
            @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white fw-bold small">
                    <i class="fas fa-image me-2"></i> Click 'Change Banner' to Upload Cover Photo
                </div>
            @endif
            <button class="banner-upload-btn" data-bs-toggle="modal" data-bs-target="#updateBannerModal">
                <i class="fas fa-camera me-1"></i> <span class="d-none d-sm-inline">Change</span> Banner
            </button>
        </div>

        <div class="channel-header-section">
            <div class="d-flex flex-wrap justify-content-between align-items-end">
                <div class="d-flex align-items-end gap-3 gap-md-4">
                    <div class="avatar-wrapper">
                        @if(isset($card) && $card->profile_photo)
                            <img src="{{ asset('storage/' . $card->profile_photo) }}" alt="Profile Photo" class="avatar-img">
                        @else
                            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Default Avatar" class="avatar-img">
                        @endif
                        <div class="avatar-upload-overlay" data-bs-toggle="modal" data-bs-target="#updateAvatarModal">
                            <i class="fas fa-camera"></i> Change
                        </div>
                    </div>
                    
                    <div class="mb-1">
                        <h4 class="fw-bold text-dark mb-1">{{ $card->name ?? Auth::user()->name }} @if(isset($card->nickname) && $card->nickname) <span class="text-muted fs-6">({{ $card->nickname }})</span> @endif</h4>
                        <p class="text-muted mb-0 small"><i class="fas fa-envelope me-1"></i> {{ $card->gmail ?? Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="mb-1 mt-3 mt-md-0 d-flex gap-2">
                    <a href="{{ route('member.profile.edit') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                        <i class="fas fa-user-edit me-1"></i> Edit Profile
                    </a>
                    <span class="badge text-white px-3 py-2 rounded-pill fw-bold d-flex align-items-center" style="background-color: #28a745;">
                        <i class="fas fa-check-circle me-1"></i> Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Top Action Buttons Row -->
    <div class="row g-2 mb-4">
        <div class="col-md-4 col-12">
            <a href="{{ route('member.profile.edit') }}" class="btn btn-primary w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm mobile-card-action-btn rounded-4">
                <i class="fas fa-user-gear fs-5"></i>
                <span>Configure / User Details Form</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ url('/member/card/create') }}" class="btn btn-info text-white w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm mobile-card-action-btn rounded-4">
                <i class="fas fa-plus-circle fs-5"></i>
                <span>Create / Edit Card</span>
            </a>
        </div>
        <div class="col-md-4 col-6">
            <a href="{{ url('/member/cards') }}" class="btn btn-dark w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm mobile-card-action-btn rounded-4">
                <i class="fas fa-id-card fs-5 text-warning"></i>
                <span>View Cards List</span>
            </a>
        </div>
    </div>
    
    <!-- 3. Include Advertising Component -->
    @include('member.partials.advertising')

    <!-- 4. Analytics & Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-primary">
                <span class="text-muted small fw-semibold">Profile Views</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">1,245</h3>
                <small class="text-success mt-2"><i class="fas fa-arrow-up"></i> +12% this week</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-success">
                <span class="text-muted small fw-semibold">Card Shares</span>
                <h3 class="fw-bold text-dark mt-1 mb-0">348</h3>
                <small class="text-success mt-2"><i class="fab fa-whatsapp"></i> WhatsApp & Social</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card border-start border-4 border-warning">
                <span class="text-muted small fw-semibold">Wallet Balance</span>
                <h5 class="fw-bold text-success mt-1 mb-0">₹2,450.00</h5>
                <a href="#walletModal" data-bs-toggle="modal" class="small text-primary mt-2 text-decoration-none fw-semibold">Manage Wallet <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 text-white shadow-sm rounded-4 border-0 h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.65rem;">GOLD PLAN</span>
                    <small class="text-white-50" style="font-size: 0.75rem;">Active</small>
                </div>
                <h6 class="fw-bold mb-1">Expires: 240 Days</h6>
                <a href="#" class="btn btn-outline-light btn-sm w-15 py-0 mt-1" style="font-size: 0.75rem;">Upgrade</a>
            </div>
        </div>
    </div>

    <!-- 5. Quick Actions Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark m-0"><i class="fas fa-bolt text-primary me-2"></i> Quick Actions</h5>
        <span class="text-muted small">Core features</span>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="info-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4"><i class="fas fa-user-gear"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Configure Details</h6>
                        <p class="text-muted small mb-0">Edit Profile & Info</p>
                    </div>
                </div>
                <a href="{{ url('/member/configure') }}" class="btn btn-primary mt-auto w-100 btn-sm">Configure Form</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4"><i class="fas fa-wallet"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Wallet</h6>
                        <p class="text-muted small mb-0">Balance & Cash</p>
                    </div>
                </div>
                <button type="button" class="btn btn-warning mt-auto w-100 btn-sm text-white" data-bs-toggle="modal" data-bs-target="#walletModal">Check Wallet</button>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-secondary bg-opacity-10 text-dark p-3 rounded-3 fs-4"><i class="fas fa-id-badge"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">My Card</h6>
                        <p class="text-muted small mb-0">Visiting Card</p>
                    </div>
                </div>
                <a href="{{ url('/member/cards') }}" class="btn btn-dark mt-auto w-100 btn-sm text-white">Manage Card</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4"><i class="fas fa-history"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Orders</h6>
                        <p class="text-muted small mb-0">Order History</p>
                    </div>
                </div>
                <a href="{{ url('/member/orders') }}" class="btn btn-outline-dark mt-auto w-100 btn-sm">View Orders</a>
            </div>
        </div>
    </div>

</div>

<!-- Modal for Profile Photo Update -->
<div class="modal fade" id="updateAvatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="fw-bold modal-title">Update Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Choose Profile Image</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Upload Photo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Banner Image Update -->
<div class="modal fade" id="updateBannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="fw-bold modal-title">Update Cover Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Choose Banner Image (YouTube Style)</label>
                        <input type="file" name="banner_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Upload Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const adsData = [
        { t: "Mega Store Sale", s: "Up to 50% Off Today", icon: "fas fa-tag", g: "linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)", label: "SPONSORED" },
        { t: "Local Vendors", s: "Explore New Shops", icon: "fas fa-store", g: "linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)", label: "FEATURED" },
        { t: "Trending Services", s: "Top Rated in Area", icon: "fas fa-fire", g: "linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)", label: "ADVERTISEMENT" },
        { t: "Premium Access", s: "Upgrade Your Plan", icon: "fas fa-crown", g: "linear-gradient(135deg, #f6d365 0%, #fda085 100%)", label: "PROMO" },
        { t: "Refer & Earn", s: "Get Instant Cash", icon: "fas fa-gift", g: "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)", label: "SPECIAL OFFER" },
        { t: "Advertise With Us", s: "Reach Local Audience", icon: "fas fa-bullhorn", g: "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)", label: "PARTNER" },
        { t: "Exclusive Deals", s: "Save Big This Week", icon: "fas fa-percentage", g: "linear-gradient(135deg, #fa709a 0%, #fee140 100%)", label: "LIMITED TIME" },
        { t: "Customer Support", s: "24/7 Assistance", icon: "fas fa-headset", g: "linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)", label: "HELP DESK" }
    ];

    function loadAds() {
        const topContainer = document.getElementById('topAdContainer');
        const bottomContainer = document.getElementById('bottomAdContainer');
        if(!topContainer || !bottomContainer) return;
        
        adsData.slice(0, 4).forEach(a => {
            topContainer.innerHTML += `
                <div class="col-md-3">
                    <div class="ad-box" style="background: ${a.g};">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-white bg-opacity-25 text-white" style="font-size: 0.65rem;">${a.label}</span>
                            <i class="${a.icon} fs-5 text-white-50"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-white">${a.t}</h6>
                        <p class="small text-white-50 mb-0">${a.s}</p>
                    </div>
                </div>
            `;
        });
        
        adsData.slice(4, 8).forEach(a => {
            bottomContainer.innerHTML += `
                <div class="col-md-3">
                    <div class="ad-box" style="background: ${a.g};">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-white bg-opacity-25 text-white" style="font-size: 0.65rem;">${a.label}</span>
                            <p class="small text-white-50 mb-0">${a.label}</p>
                        </div>
                        <h6 class="fw-bold mb-1 text-white">${a.t}</h6>
                        <p class="small text-white-50 mb-0">${a.s}</p>
                    </div>
                </div>
            `;
        });
    }
    loadAds();
</script>
@endpush