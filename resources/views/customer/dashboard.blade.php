<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        
        /* Sidebar Styling for Desktop */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: #0f172a;
            color: #fff;
            transition: all 0.3s ease;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #1e293b;
            font-size: 1.25rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #38bdf8;
        }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s;
        }
        #sidebar ul li a:hover, #sidebar ul li.active a {
            color: #fff;
            background: #1e293b;
            border-left: 4px solid #38bdf8;
        }
        
        /* Main Content Area */
        #content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Vibrant Ad-Box Style */
        .ad-box {
            border-radius: 16px;
            color: #fff;
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        .ad-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Info Card Style */
        .info-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: none;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 992px) {
            #sidebar { margin-left: -260px; }
            #sidebar.active { margin-left: 0; }
            #content { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Menu -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-layer-group"></i> Tidong® Portal
        </div>
        <ul class="list-unstyled components">
            <li class="active">
                <a href="#"><i class="fas fa-home"></i> Dashboard</a>
            </li>
            <li>
                <a href="#searchModal" data-bs-toggle="modal"><i class="fas fa-search"></i> Advanced Search</a>
            </li>
            <li>
                <a href="#walletModal" data-bs-toggle="modal"><i class="fas fa-wallet text-warning"></i> My Wallet</a>
            </li>
            <li>
                <a href="#friendCircleModal" data-bs-toggle="modal"><i class="fas fa-users"></i> Friend Circle</a>
            </li>
            <li>
                <a href="#visitingCardModal" data-bs-toggle="modal"><i class="fas fa-id-badge"></i> Digital Visiting Card</a>
            </li>
            <li>
                <a href="#myCardsModal" data-bs-toggle="modal"><i class="fas fa-id-card"></i> My Visiting Cards</a>
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

    <!-- Page Content Holder -->
    <div id="content">
        <!-- Top Navbar with Welcome Message & Notification -->
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand fw-bold text-dark mb-0 h6 d-flex align-items-center gap-2">
                <span class="text-secondary fw-normal">Welcome back,</span> <span class="text-primary">{{ Auth::user()->name }}!</span> 🚀
            </div>
            <div class="ms-auto d-flex align-items-center gap-3">
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-2" style="width: 280px;">
                        <li><h6 class="dropdown-header fw-bold">Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small text-wrap rounded-2" href="#">🎉 Welcome to Tidong International Platform!</a></li>
                        <li><a class="dropdown-item small text-wrap rounded-2 mt-1" href="#">💼 Your digital card was viewed 14 times today.</a></li>
                    </ul>
                </div>

                <!-- User Profile Name -->
                <span class="fw-bold text-dark d-none d-md-inline"><i class="fas fa-user-circle text-primary me-1"></i> {{ Auth::user()->name }}</span>
            </div>
        </nav>

        <!-- Main Body Content -->
        <div class="container-fluid py-4 px-4">
            
            <!-- 1. SLIDING AD BANNER (Moved to Top) -->
            <div class="row mb-4">
                <div class="col-12">
                    <div id="mainAdSlider" class="carousel slide shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active text-white p-4 p-md-5" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); min-height: 180px;">
                                <span class="badge bg-warning text-dark mb-2">FEATURED BANNER 1</span>
                                <h3 class="fw-bold">Digital Visiting Cards & Business Catalogs!</h3>
                                <p class="mb-3 text-white-50">Create your professional online identity in less than 2 minutes and share anywhere.</p>
                                <a href="#visitingCardModal" data-bs-toggle="modal" class="btn btn-light btn-sm fw-bold px-3 py-2 text-primary">Create Card Now</a>
                            </div>
                            <div class="carousel-item text-white p-4 p-md-5" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); min-height: 180px;">
                                <span class="badge bg-warning text-dark mb-2">FEATURED BANNER 2</span>
                                <h3 class="fw-bold">Connect via Local Friend Circle</h3>
                                <p class="mb-3 text-white-50">Build a network of professional friends and share local market updates easily.</p>
                                <a href="#friendCircleModal" data-bs-toggle="modal" class="btn btn-light btn-sm fw-bold px-3 py-2 text-success">Join Circle</a>
                            </div>
                            <div class="carousel-item text-white p-4 p-md-5" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); min-height: 180px;">
                                <span class="badge bg-dark text-white mb-2">FEATURED BANNER 3</span>
                                <h3 class="fw-bold">Advanced Vendor & Member Search</h3>
                                <p class="mb-3 text-white-50">Find best local stores, services, and members around your city instantly.</p>
                                <a href="#searchModal" data-bs-toggle="modal" class="btn btn-light btn-sm fw-bold px-3 py-2 text-warning">Start Searching</a>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#mainAdSlider" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#mainAdSlider" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. ANALYTICS & MEMBERSHIP STATS ROW (Card moved next to counts) -->
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

            <!-- TOP ADVERTISEMENT SECTION (4 Boxes) -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fas fa-ad text-danger me-2"></i> Featured Ads & Promotions</h5>
                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 small">Live Ads</span>
            </div>
            <div class="row g-4 mb-4" id="topAdContainer">
                <!-- Auto-loaded via script -->
            </div>

            <!-- QUICK ACTIONS SECTION -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fas fa-bolt text-primary me-2"></i> Quick Actions</h5>
                <span class="text-muted small">Core features</span>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Search</h6>
                                <p class="text-muted small mb-0">Find Vendors</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-auto w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#searchModal">
                            Open Search
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Wallet</h6>
                                <p class="text-muted small mb-0">Balance & Cash</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning mt-auto w-100 btn-sm text-white" data-bs-toggle="modal" data-bs-target="#walletModal">
                            Check Wallet
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4">
                                <i class="fas fa-id-badge"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">My Card</h6>
                                <p class="text-muted small mb-0">Visiting Card</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-dark mt-auto w-100 btn-sm text-white" data-bs-toggle="modal" data-bs-target="#visitingCardModal">
                            Manage Card
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 fs-4">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Orders</h6>
                                <p class="text-muted small mb-0">Order History</p>
                            </div>
                        </div>
                        <a href="#" class="btn btn-outline-dark mt-auto w-100 btn-sm">View Orders</a>
                    </div>
                </div>
            </div>

            <!-- BOTTOM ADVERTISEMENT SECTION (4 Boxes) -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fas fa-bullhorn text-warning me-2"></i> Sponsored Banners</h5>
                <span class="badge bg-warning bg-opacity-10 text-dark px-2 py-1 small">Promoted</span>
            </div>
            <div class="row g-4 mb-5" id="bottomAdContainer">
                <!-- Auto-loaded via script -->
            </div>

            <!-- Footer -->
            <footer class="text-center py-4 text-muted small border-top">
                &copy; {{ date('Y') }} Tidong® Portal. All rights reserved. Built for Global Reach.
            </footer>

        </div>
    </div>

    <!-- Floating WhatsApp Support Button -->
    <a href="https://api.whatsapp.com/send?phone=910000000000&text=Hello%20Tidong%20Support,%20I%20need%20help!" target="_blank" class="position-fixed bottom-0 end-0 m-4 bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 55px; height: 55px; z-index: 999; font-size: 28px; text-decoration: none;">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- 🔍 SEARCH MODAL -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-search text-primary me-2"></i> Advanced Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="GET">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Search By Name <span class="text-muted fw-normal">(Person or Business)</span></label>
                            <input type="text" name="search_name" class="form-control" placeholder="Enter name to search...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Search For</label>
                            <select id="searchType" name="search_type" class="form-select" onchange="toggleCategoryFilter(this)">
                                <option value="all" selected>Auto All (Members & Vendors)</option>
                                <option value="member">Member</option>
                                <option value="vendor">Vendor / Business</option>
                            </select>
                        </div>
                        <div class="mb-3" id="vendorCategoryBox" style="display: none;">
                            <label class="form-label small fw-semibold">Vendor Category</label>
                            <select name="vendor_category" class="form-select">
                                <option value="all" selected>-- All Categories --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Area Name <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="area_name" class="form-control" placeholder="e.g. Downtown, Main Road">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            <i class="fas fa-search me-1"></i> Search Results
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 💰 WALLET MODAL -->
    <div class="modal fade" id="walletModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-wallet text-warning me-2"></i> My Wallet & Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="p-4 bg-light rounded-4 mb-3">
                        <span class="text-muted small">Available Balance</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">₹2,450.00</h3>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success w-50 py-2 fw-bold"><i class="fas fa-plus-circle me-1"></i> Add Money</button>
                        <button class="btn btn-outline-dark w-50 py-2 fw-bold"><i class="fas fa-history me-1"></i> History</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FRIEND CIRCLE MODAL -->
    <div class="modal fade" id="friendCircleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Friend Circle Network</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4 text-muted">
                    <i class="fas fa-user-friends fa-2x mb-2 text-secondary"></i>
                    <p class="small mb-0">Your friend circle is empty. Connect with other members!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 🆔 VISITING CARD MODAL -->
    <div class="modal fade" id="visitingCardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-id-badge text-warning me-2"></i> Digital Visiting Card Studio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="small text-muted text-center mb-4">Choose how you want to create your digital visiting card:</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-4 border rounded-4 text-center bg-light shadow-sm" style="cursor: pointer; transition: 0.3s;" onmouseover="this.style.borderColor='#3b82f6'" onmouseout="this.style.borderColor='#dee2e6'">
                                <div class="text-primary fs-2 mb-2"><i class="fas fa-layer-group"></i></div>
                                <h6 class="fw-bold">Pre-Designed Template</h6>
                                <p class="text-muted small mb-3">Choose from professionally styled ready-to-use templates.</p>
                                <button class="btn btn-outline-primary btn-sm w-100">Select Template</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 border rounded-4 text-center bg-light shadow-sm" style="cursor: pointer; transition: 0.3s;" onmouseover="this.style.borderColor='#10b981'" onmouseout="this.style.borderColor='#dee2e6'">
                                <div class="text-success fs-2 mb-2"><i class="fas fa-wand-magic-sparkles"></i></div>
                                <h6 class="fw-bold">AI Generated Card</h6>
                                <p class="text-muted small mb-3">Let AI auto-design a custom card based on your business details.</p>
                                <button class="btn btn-outline-success btn-sm w-100">Generate with AI</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📋 MY VISITING CARDS LIST & WHATSAPP SHARE MODAL -->
    <div class="modal fade" id="myCardsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="fw-bold text-dark"><i class="fas fa-id-card text-success me-2"></i> My Saved Digital Cards & WhatsApp Share</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <!-- Card 1 -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Pre-Designed</span>
                                    <small class="text-muted">Saved Today</small>
                                </div>
                                <div class="p-3 rounded-3 text-white mb-3" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
                                    <h6 class="fw-bold mb-1">{{ Auth::user()->name }}'s Business</h6>
                                    <p class="small text-white-50 mb-0">Tidong Verified Vendor</p>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <button class="btn btn-outline-dark btn-sm w-50"><i class="fas fa-eye me-1"></i> View</button>
                                    <a href="https://api.whatsapp.com/send?text=Check%20out%20my%20digital%20visiting%20card%20on%20Tidong!" target="_blank" class="btn btn-success btn-sm w-50"><i class="fab fa-whatsapp me-1"></i> Share</a>
                                </div>
                            </div>
                        </div>
                        <!-- Card 2 -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-light h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-success bg-opacity-10 text-success">AI Generated</span>
                                    <small class="text-muted">Saved Recent</small>
                                </div>
                                <div class="p-3 rounded-3 text-white mb-3" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                                    <h6 class="fw-bold mb-1">{{ Auth::user()->name }} Services</h6>
                                    <p class="small text-white-50 mb-0">AI Custom Template</p>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <button class="btn btn-outline-dark btn-sm w-50"><i class="fas fa-eye me-1"></i> View</button>
                                    <a href="https://api.whatsapp.com/send?text=Check%20out%20my%20AI%20visiting%20card%20on%20Tidong!" target="_blank" class="btn btn-success btn-sm w-50"><i class="fab fa-whatsapp me-1"></i> Share</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & Custom Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                                <i class="${a.icon} fs-5 text-white-50"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-white">${a.t}</h6>
                            <p class="small text-white-50 mb-0">${a.s}</p>
                        </div>
                    </div>
                `;
            });
        }
        loadAds();

        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });

        function toggleCategoryFilter(select) {
            const box = document.getElementById('vendorCategoryBox');
            box.style.display = (select.value === 'vendor') ? 'block' : 'none';
        }
    </script>
</body>
</html>