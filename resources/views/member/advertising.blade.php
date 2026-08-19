<!-- Advertising & Banners Component -->
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

<!-- Top Ads Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark m-0"><i class="fas fa-ad text-danger me-2"></i> Featured Ads & Promotions</h5>
    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 small">Live Ads</span>
</div>
<div class="row g-4 mb-4" id="topAdContainer"></div>

<!-- Bottom Ads Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark m-0"><i class="fas fa-bullhorn text-warning me-2"></i> Sponsored Banners</h5>
    <span class="badge bg-warning bg-opacity-10 text-dark px-2 py-1 small">Promoted</span>
</div>
<div class="row g-4 mb-5" id="bottomAdContainer"></div>
