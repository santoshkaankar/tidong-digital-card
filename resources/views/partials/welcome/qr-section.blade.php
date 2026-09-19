<section class="py-5 bg-white border-top border-bottom">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-5 text-center">
                <div class="p-3 bg-light rounded-4 d-inline-block border shadow-sm">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(route('customer.hub')) }}" alt="Tidong Universal QR Code" class="img-fluid rounded">
                    <p class="mt-2 mb-0 fw-bold text-dark small"><i class="fas fa-camera text-primary me-1"></i> Scan to Access Tidong® Hub</p>
                </div>
            </div>
            <div class="col-md-7 text-center text-md-start">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-2">Universal Smart Access</span>
                <h3 class="fw-bold text-dark mb-3">Scan Any Tidong® QR Code</h3>
                <p class="text-muted mb-4">Users can scan this QR code using any smartphone camera to instantly open the platform, browse local store menus, request taxi rides, check live currency exchange rates, or book local tour guides without downloading any application.</p>
                <a href="{{ route('customer.hub') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-qrcode me-2"></i> Open Smart Hub Directly
                </a>
            </div>
        </div>
    </div>
</section>