<!-- File Path: resources/views/member/partials/tidong-hub.blade.php -->

<style>
.tidong-atm-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0284c7 100%);
    border-radius: 18px;
    padding: 18px;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.atm-chip {
    width: 40px;
    height: 28px;
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    border-radius: 5px;
    border: 1px solid #d97706;
    display: inline-block;
}

.atm-service-btn {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff !important;
    padding: 8px 10px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
}

.atm-service-btn:hover {
    background: #ffffff;
    color: #0f172a !important;
}
</style>

<div class="row mb-3">
    <div class="col-12">
        <div class="tidong-atm-card">
            
            <!-- Card Header -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="atm-chip"></div>
                    <i class="fas fa-wifi text-white-50" style="transform: rotate(90deg); font-size: 0.9rem;"></i>
                </div>
                <div class="text-end">
                    <span class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-1" style="font-size: 0.6rem;">EXPRESS HUB</span>
                    <h6 class="fw-bold m-0 text-white mt-1" style="letter-spacing: 0.5px;">
                        Tidong Services Pass
                    </h6>
                </div>
            </div>

            <!-- Card Subtitle -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <small class="text-white-50" style="font-size: 0.7rem;">
                    <i class="fas fa-hand-pointer me-1 text-warning"></i> Tap any service below to open
                </small>
                <span class="text-white-50 fw-mono" style="font-size: 0.75rem; letter-spacing: 2px;">•••• 8890</span>
            </div>

            <!-- Service Buttons -->
            <div class="row g-2 mb-2">
                <div class="col-6 col-md-3">
                    <a href="{{ url('/member/restaurant') }}" class="atm-service-btn justify-content-center">
                        <i class="fas fa-utensils text-warning"></i>
                        <span>Restaurant</span>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="{{ url('/member/shopping') }}" class="atm-service-btn justify-content-center">
                        <i class="fas fa-shopping-basket text-info"></i>
                        <span>Grocery</span>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="{{ url('/member/taxi') }}" class="atm-service-btn justify-content-center">
                        <i class="fas fa-taxi text-warning"></i>
                        <span>Taxi & Cab</span>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="{{ url('/member/delivery') }}" class="atm-service-btn justify-content-center">
                        <i class="fas fa-truck-fast text-success"></i>
                        <span>Delivery</span>
                    </a>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="pt-2 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center">
                <span class="text-white-50 fw-semibold" style="font-size: 0.7rem;">Tidong® Smart Access</span>
                <a href="{{ url('/member/tidong-super-hub') }}" class="btn btn-xs btn-light rounded-pill px-3 fw-bold text-dark shadow-sm" style="font-size: 0.75rem;">
                    Open Full Hub <i class="fas fa-arrow-right ms-1 text-primary"></i>
                </a>
            </div>

        </div>
    </div>
</div>