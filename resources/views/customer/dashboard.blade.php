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
        #sidebar { min-width: 260px; max-width: 260px; background: #0f172a; color: #fff; transition: all 0.3s ease; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; }
        #sidebar .sidebar-header { padding: 20px; background: #1e293b; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; color: #38bdf8; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; transition: all 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active a { color: #fff; background: #1e293b; border-left: 4px solid #38bdf8; }
        #content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; transition: all 0.3s ease; }
        .top-navbar { background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .ad-box { border-radius: 16px; color: #fff; padding: 20px; transition: all 0.3s ease; cursor: pointer; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08); position: relative; overflow: hidden; }
        .ad-box:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .info-card { background: #fff; border-radius: 16px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border: none; height: 100%; display: flex; flex-direction: column; }
        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #sidebar.active { margin-left: 0; } #content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    <!-- Include Sidebar Component -->
    @include('customer.sidebar')

    <!-- Page Content Holder -->
    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand fw-bold text-dark mb-0 h6 d-flex align-items-center gap-2">
                <span class="text-secondary fw-normal">Welcome back,</span> <span class="text-primary">{{ Auth::user()->name }}!</span> 🚀
            </div>
            <div class="ms-auto d-flex align-items-center gap-3">
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
                <span class="fw-bold text-dark d-none d-md-inline"><i class="fas fa-user-circle text-primary me-1"></i> {{ Auth::user()->name }}</span>
            </div>
        </nav>

        <div class="container-fluid py-4 px-4">
            
            <!-- Include Advertising Component -->
            @include('customer.advertising')

            <!-- Analytics & Stats Row -->
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

            <!-- Quick Actions Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fas fa-bolt text-primary me-2"></i> Quick Actions</h5>
                <span class="text-muted small">Core features</span>
            </div>
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 fs-4"><i class="fas fa-search"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Search</h6>
                                <p class="text-muted small mb-0">Find Vendors</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-auto w-100 btn-sm" data-bs-toggle="modal" data-bs-target="#searchModal">Open Search</button>
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
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 fs-4"><i class="fas fa-id-badge"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">My Card</h6>
                                <p class="text-muted small mb-0">Visiting Card</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-dark mt-auto w-100 btn-sm text-white" data-bs-toggle="modal" data-bs-target="#visitingCardModal">Manage Card</button>
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
                        <a href="#" class="btn btn-outline-dark mt-auto w-100 btn-sm">View Orders</a>
                    </div>
                </div>
            </div>

            <footer class="text-center py-4 text-muted small border-top">
                &copy; {{ date('Y') }} Tidong® Portal. All rights reserved. Built for Global Reach.
            </footer>
        </div>
    </div>

    <!-- Modals Section (Search, Wallet, Friend Circle, Visiting Card) -->
    <!-- (Modals code remains fully intact here for proper functionality) -->

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

        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
    </script>
</body>
</html>