@extends('member.partials.layout')

@section('title', 'My Profile & Stats - Tidong® Portal')

@push('styles')
<style>
    .profile-container { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; overflow: hidden; }
    .banner-wrapper { position: relative; width: 100%; height: 220px; background: #e2e8f0; }
    .banner-img { width: 100%; height: 100%; object-fit: cover; }
    .banner-upload-btn { position: absolute; bottom: 15px; right: 15px; background: rgba(0, 0, 0, 0.7); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: 0.2s; }
    .banner-upload-btn:hover { background: rgba(0, 0, 0, 0.9); }
    
    .channel-header-section { padding: 0 30px 30px 30px; position: relative; }
    .avatar-wrapper { position: relative; width: 130px; height: 130px; margin-top: -65px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); background: #fff; overflow: hidden; display: inline-block; }
    .avatar-img { width: 100%; height: 100%; object-fit: cover; }
    .avatar-upload-overlay { position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0,0,0,0.6); color: white; text-align: center; font-size: 11px; padding: 4px 0; cursor: pointer; opacity: 0; transition: 0.2s; }
    .avatar-wrapper:hover .avatar-upload-overlay { opacity: 1; }

    /* Autocomplete Dropdown Custom Style */
    .pincode-dropdown { position: absolute; z-index: 1050; width: 100%; max-height: 200px; overflow-y: auto; background: #fff; border: 1px solid #ced4da; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; }
    .pincode-dropdown-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f1f1f1; font-size: 0.85rem; }
    .pincode-dropdown-item:hover { background: #0d6efd; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <!-- Page Title & Top Actions Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <h4 class="fw-bold text-dark m-0"><i class="fas fa-id-badge text-primary me-2"></i> My Profile & Stats</h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editMasterProfileModal">
                        <i class="fas fa-user-edit me-1"></i> Edit Master Profile & KYC
                    </button>
                    <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- YouTube Style Cover & Profile Section -->
            <div class="profile-container mb-4">
                <div class="banner-wrapper">
                    @if(isset($card) && $card->banner_image)
                        <img src="{{ asset('storage/' . $card->banner_image) }}" alt="Channel Banner" class="banner-img">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-secondary text-white fw-bold">
                            <i class="fas fa-image me-2"></i> Click 'Change Banner' to Upload Cover Photo
                        </div>
                    @endif
                    <button class="banner-upload-btn" data-bs-toggle="modal" data-bs-target="#updateBannerModal">
                        <i class="fas fa-camera me-1"></i> Change Banner
                    </button>
                </div>

                <div class="channel-header-section">
                    <div class="d-flex flex-wrap justify-content-between align-items-end">
                        <div class="d-flex align-items-end gap-4">
                            <div class="avatar-wrapper">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile Photo" class="avatar-img">
                                @elseif(isset($card) && $card->profile_photo)
                                    <img src="{{ asset('storage/' . $card->profile_photo) }}" alt="Profile Photo" class="avatar-img">
                                @else
                                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Default Avatar" class="avatar-img">
                                @endif
                                <div class="avatar-upload-overlay" data-bs-toggle="modal" data-bs-target="#updateAvatarModal">
                                    <i class="fas fa-camera"></i> Change
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <h3 class="fw-bold text-dark mb-1">
                                    {{ Auth::user()->name }} 
                                    @if(isset($card->nickname) && $card->nickname) <span class="text-muted fs-6">({{ $card->nickname }})</span> @endif
                                </h3>
                                <p class="text-muted mb-0 small"><i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <div class="mb-2 mt-3 mt-md-0 d-flex gap-2">
                            <span class="badge bg-{{ Auth::user()->kyc_status == 'approved' ? 'success' : (Auth::user()->kyc_status == 'pending' ? 'warning' : 'secondary') }} px-3 py-2 rounded-pill fw-bold">
                                <i class="fas fa-shield-alt me-1"></i> KYC: {{ ucfirst(Auth::user()->kyc_status ?? 'Unverified') }}
                            </span>
                            <span class="badge text-white px-3 py-2 rounded-pill fw-bold" style="background-color: #28a745;">
                                <i class="fas fa-check-circle me-1"></i> Active Member
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 rounded-4">
                        <p class="text-muted mb-1 small">Profile Views</p>
                        <h4 class="fw-bold mb-0">{{ number_format($profileViews ?? 1245) }}</h4>
                        <small class="text-success"><i class="fas fa-arrow-up"></i> 12% this week</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 rounded-4">
                        <p class="text-muted mb-1 small">Card Shares</p>
                        <h4 class="fw-bold mb-0">{{ number_format($cardShares ?? 348) }}</h4>
                        <small class="text-success"><i class="fas fa-share-alt"></i> WhatsApp & Social</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 rounded-4">
                        <p class="text-muted mb-1 small">Wallet Balance</p>
                        <h4 class="fw-bold mb-0 text-success">₹ {{ number_format($walletBalance ?? 0, 2) }}</h4>
                        <a href="{{ route('member.wallet') }}" class="small text-primary text-decoration-none">Manage Wallet →</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 shadow-sm border-0 rounded-4 bg-dark text-white">
                        <p class="text-white-50 mb-1 small">Gold Plan</p>
                        <h5 class="fw-bold mb-0">Expires: 240 Days</h5>
                        <small class="text-warning">Active Plan</small>
                    </div>
                </div>
            </div>

            <!-- Details Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card p-4 shadow-sm border-0 rounded-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-id-card text-warning me-2"></i> KYC Verification</h6>
                            <span class="badge bg-{{ Auth::user()->pan_number ? 'success' : 'danger' }} small">
                                {{ Auth::user()->pan_number ? '5% TDS' : '20% TDS' }}
                            </span>
                        </div>
                        <ul class="list-unstyled small mb-0 lh-lg">
                            <li><strong>PAN Card:</strong> {{ Auth::user()->pan_number ?? 'Not Updated' }}</li>
                            <li><strong>Aadhaar Card:</strong> {{ Auth::user()->aadhaar_number ? 'XXXX-XXXX-' . substr(Auth::user()->aadhaar_number, -4) : 'Not Updated' }}</li>
                            <li><strong>Doc Verification:</strong> {{ ucfirst(Auth::user()->kyc_status ?? 'Pending') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-4 shadow-sm border-0 rounded-4 h-100">
                        <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-university text-primary me-2"></i> Bank Payout Account</h6>
                        <ul class="list-unstyled small mb-0 lh-lg">
                            <li><strong>Holder:</strong> {{ Auth::user()->account_holder_name ?? 'Not Updated' }}</li>
                            <li><strong>Bank:</strong> {{ Auth::user()->bank_name ?? 'Not Updated' }}</li>
                            <li><strong>Account No:</strong> {{ Auth::user()->account_number ? 'XXXX' . substr(Auth::user()->account_number, -4) : 'Not Updated' }}</li>
                            <li><strong>IFSC Code:</strong> {{ Auth::user()->ifsc_code ?? 'Not Updated' }}</li>
                            <li><strong>UPI ID:</strong> {{ Auth::user()->upi_id ?? 'N/A' }}</li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-4 shadow-sm border-0 rounded-4 h-100">
                        <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-map-marker-alt text-danger me-2"></i> Address Information</h6>
                        <p class="small text-muted mb-2">
                            {{ Auth::user()->address ?? 'No address provided yet.' }}
                        </p>
                        <ul class="list-unstyled small mb-0 lh-lg">
                            <li><strong>Area:</strong> {{ Auth::user()->area ?? 'N/A' }}</li>
                            <li><strong>City:</strong> {{ Auth::user()->city ?? 'N/A' }}</li>
                            <li><strong>State:</strong> {{ Auth::user()->state ?? 'N/A' }}</li>
                            <li><strong>Pincode:</strong> {{ Auth::user()->pincode ?? 'N/A' }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Friends & Business Status Section -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm border-0 rounded-4 h-100">
                        <h5 class="fw-bold mb-3"><i class="fas fa-users text-primary me-2"></i> Friend Circle</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light p-3 rounded-circle me-3"><i class="fas fa-user-friends text-secondary"></i></div>
                            <div>
                                <h6 class="mb-0">Total Connections</h6>
                                <p class="text-muted mb-0 small">Manage your network and friend circle</p>
                            </div>
                        </div>
                        <a href="{{ route('member.friend.index', ['type' => 'all']) }}" class="btn btn-outline-primary btn-sm w-100">View All Friends</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 shadow-sm border-0 rounded-4 h-100">
                        <h5 class="fw-bold mb-3"><i class="fas fa-briefcase text-warning me-2"></i> Business Status</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light p-3 rounded-circle me-3"><i class="fas fa-id-card text-warning"></i></div>
                            <div>
                                <h6 class="mb-0">Visiting Card</h6>
                                <p class="text-muted mb-0 small">Active & Visible to Public</p>
                            </div>
                        </div>
                        <a href="{{ route('member.card.configure') }}" class="btn btn-warning btn-sm w-100 text-white">Manage My Card</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal for Edit Master Profile & KYC -->
<div class="modal fade" id="editMasterProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="fw-bold modal-title"><i class="fas fa-user-edit text-primary me-2"></i>Edit Master Profile, KYC & Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- 1. Personal Info --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user me-1"></i> Personal Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" value="{{ Auth::user()->mobile }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select Gender</option>
                                <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ Auth::user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ Auth::user()->dob }}">
                        </div>
                    </div>

                    {{-- 2. Bank Details --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-university me-1"></i> Bank Account (For Payouts)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Account Holder Name</label>
                            <input type="text" name="account_holder_name" class="form-control" value="{{ Auth::user()->account_holder_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ Auth::user()->bank_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ Auth::user()->account_number }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control text-uppercase" value="{{ Auth::user()->ifsc_code }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">UPI ID</label>
                            <input type="text" name="upi_id" class="form-control" value="{{ Auth::user()->upi_id }}">
                        </div>
                    </div>

                    {{-- 3. KYC Documents --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-file-contract me-1"></i> KYC Documents (PAN & Aadhaar)</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">PAN Number (Applies 5% TDS)</label>
                            <input type="text" name="pan_number" class="form-control text-uppercase" value="{{ Auth::user()->pan_number }}" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Upload PAN Image</label>
                            <input type="file" name="pan_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Aadhaar Number</label>
                            <input type="text" name="aadhaar_number" class="form-control" value="{{ Auth::user()->aadhaar_number }}" maxlength="12">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Aadhaar Front</label>
                            <input type="file" name="aadhaar_front_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Aadhaar Back</label>
                            <input type="file" name="aadhaar_back_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    {{-- 4. Address Details with Live Dropdown Search --}}
                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-map-marker-alt me-1"></i> Address Details</h6>
                    
                    {{-- Search Input Bar --}}
                    <div class="position-relative mb-3">
                        <label class="form-label small fw-bold text-success"><i class="fas fa-search me-1"></i> Search Pincode / Area / City / State</label>
                        <input type="text" id="pincodeSearchInput" class="form-control border-success" placeholder="Type Pincode, Area or City (e.g. 504273, Adilabad, Kothimir)..." autocomplete="off">
                        <div id="pincodeDropdown" class="pincode-dropdown"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Address (House / Street)</label>
                            <input type="text" name="address" class="form-control" value="{{ Auth::user()->address }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Area</label>
                            <input type="text" name="area" id="inputArea" class="form-control" value="{{ Auth::user()->area }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">City / District</label>
                            <input type="text" name="city" id="inputCity" class="form-control" value="{{ Auth::user()->city }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">State</label>
                            <input type="text" name="state" id="inputState" class="form-control" value="{{ Auth::user()->state }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Pincode</label>
                            <input type="text" name="pincode" id="inputPincode" class="form-control" value="{{ Auth::user()->pincode }}" maxlength="10">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">Save All Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modals for Photos --}}
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
                    <input type="file" name="profile_photo" class="form-control" accept="image/*" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Upload Photo</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    <input type="file" name="banner_image" class="form-control" accept="image/*" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Upload Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Dynamic Address Search Script --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('pincodeSearchInput');
    const dropdown = document.getElementById('pincodeDropdown');
    const inputArea = document.getElementById('inputArea');
    const inputCity = document.getElementById('inputCity');
    const inputState = document.getElementById('inputState');
    const inputPincode = document.getElementById('inputPincode');

    let debounceTimer;

    function fetchPincodes(query) {
        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        fetch(`{{ route('member.pincode.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                dropdown.innerHTML = '';
                if (data.length === 0) {
                    dropdown.style.display = 'none';
                    return;
                }

                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'pincode-dropdown-item';
                    div.innerHTML = `<strong>${item.pincode}</strong> - ${item.area}, ${item.city}, ${item.state}`;
                    
                    div.addEventListener('click', function () {
                        inputArea.value = item.area || '';
                        inputCity.value = item.city || '';
                        inputState.value = item.state || '';
                        inputPincode.value = item.pincode || '';
                        
                        searchInput.value = `${item.pincode} - ${item.area}, ${item.city}`;
                        dropdown.style.display = 'none';
                    });

                    dropdown.appendChild(div);
                });

                dropdown.style.display = 'block';
            })
            .catch(error => console.error('Pincode Search Error:', error));
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchPincodes(this.value.trim()), 300);
    });

    [inputPincode, inputArea, inputCity].forEach(input => {
        if (input) {
            input.addEventListener('input', function () {
                if (this.value.trim().length >= 3) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => fetchPincodes(this.value.trim()), 300);
                }
            });
        }
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection