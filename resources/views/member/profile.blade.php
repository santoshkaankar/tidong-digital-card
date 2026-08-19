<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile & Stats - Tidong® Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; background: #0f172a; color: #fff; transition: all 0.3s ease; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; }
        #sidebar .sidebar-header { padding: 20px; background: #1e293b; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; color: #38bdf8; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; transition: all 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active a { color: #fff; background: #1e293b; border-left: 4px solid #38bdf8; }
        #content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; transition: all 0.3s ease; }
        .top-navbar { background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        
        /* YouTube Style Cover & Profile Layout */
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

        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #sidebar.active { margin-left: 0; } #content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    @include('member.sidebar')

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand fw-bold text-dark mb-0 h6">
                My Profile & Stats
            </div>
            <div class="ms-auto d-flex gap-2">
                <!-- Profile Setting Button Added -->
                <a href="#" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-cog me-1"></i> Profile Setting
                </a>
                <a href="{{ route('member.profile.edit') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-edit me-1"></i> Edit Master Profile
                </a>
                <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </nav>

        <div class="container-fluid py-4 px-4">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    
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
                                        @if(isset($card) && $card->profile_photo)
                                            <img src="{{ asset('storage/' . $card->profile_photo) }}" alt="Profile Photo" class="avatar-img">
                                        @else
                                            <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Default Avatar" class="avatar-img">
                                        @endif
                                        <div class="avatar-upload-overlay" data-bs-toggle="modal" data-bs-target="#updateAvatarModal">
                                            <i class="fas fa-camera"></i> Change
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <h3 class="fw-bold text-dark mb-1">{{ $card->name ?? Auth::user()->name }} @if(isset($card->nickname) && $card->nickname) <span class="text-muted fs-6">({{ $card->nickname }})</span> @endif</h3>
                                        <p class="text-muted mb-0 small"><i class="fas fa-envelope me-1"></i> {{ $card->gmail ?? Auth::user()->email }}</p>
                                    </div>
                                </div>

                                <div class="mb-2 mt-3 mt-md-0">
                                    <span class="badge text-white px-3 py-2 rounded-pill fw-bold" style="background-color: #28a745;">
                                        <i class="fas fa-check-circle me-1"></i> Active Member
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Style Stats Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 rounded-4">
                                <p class="text-muted mb-1 small">Profile Views</p>
                                <h4 class="fw-bold mb-0">1,245</h4>
                                <small class="text-success"><i class="fas fa-arrow-up"></i> 12% this week</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 rounded-4">
                                <p class="text-muted mb-1 small">Card Shares</p>
                                <h4 class="fw-bold mb-0">348</h4>
                                <small class="text-success"><i class="fas fa-share-alt"></i> WhatsApp & Social</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card p-3 shadow-sm border-0 rounded-4">
                                <p class="text-muted mb-1 small">Wallet Balance</p>
                                <h4 class="fw-bold mb-0 text-success">₹2,450.00</h4>
                                <small class="text-primary">Manage Wallet →</small>
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
                                <a href="#" class="btn btn-outline-primary btn-sm w-100">View All Friends</a>
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
                                <a href="#" class="btn btn-warning btn-sm w-100 text-white">Manage My Card</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <footer class="text-center py-4 text-muted small border-top mt-5">
            &copy; {{ date('Y') }} Tidong® Portal. All rights reserved.
        </footer>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
    </script>
</body>
</html>