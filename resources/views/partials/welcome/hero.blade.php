<section class="hero-section text-center text-lg-start text-white py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-primary bg-opacity-25 text-primary mb-3 px-3 py-2 rounded-pill fw-bold">✨ Built For Everyone & Every Business</span>
                <h1 class="display-4 fw-bold mb-4 lh-base">Your Interactive Digital Identity & Business Catalogs, Shared in One Click</h1>
                <p class="lead text-muted mb-5">Create your stunning personal visiting card or business profile. Share it instantly with anyone—allowing them to chat on WhatsApp, call, or browse your product catalogs with a single tap.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    @auth
                        <a href="{{ $dashboardRoute ?? route('member.dashboard') }}" class="btn btn-primary btn-lg shadow rounded-pill px-4">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow rounded-pill px-4">Create Your Card Now</a>
                    @endauth
                    <a href="#features" class="btn btn-outline-light btn-lg rounded-pill px-4">Explore Features</a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 shadow-lg border border-secondary border-opacity-25">
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <a href="https://wa.me/919634759912" target="_blank" class="action-icon" title="Chat on WhatsApp">
                            <span class="badge bg-success p-2 fs-5 rounded-circle shadow-sm"><i class="fab fa-whatsapp text-white"></i></span>
                        </a>
                        <a href="tel:9634759912" class="action-icon" title="Call Now">
                            <span class="badge bg-primary p-2 fs-5 rounded-circle shadow-sm"><i class="fas fa-phone text-white"></i></span>
                        </a>
                        <a href="mailto:santoshkaankar@gmail.com" class="action-icon" title="Send Email">
                            <span class="badge bg-danger p-2 fs-5 rounded-circle shadow-sm"><i class="fas fa-envelope text-white"></i></span>
                        </a>
                    </div>
                    <h4 class="fw-bold text-white mb-2">One-Tap Direct Connection</h4>
                    <p class="text-light small mb-0">No apps required for viewers. They click WhatsApp icon and land straight into your WhatsApp chat instantly!</p>
                </div>
            </div>
        </div>
    </div>
</section>