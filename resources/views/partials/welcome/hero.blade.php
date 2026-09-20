<section class="hero-section text-center text-lg-start text-white py-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge bg-primary bg-opacity-25 text-primary mb-3 px-3 py-2 rounded-pill fw-bold">🌍 Global Multi-Industry Digital Infrastructure</span>
                <h1 class="display-4 fw-bold mb-4 lh-base">Next-Generation Cross-Border Commerce, Mobility & Digital Identity</h1>
                <p class="lead text-muted mb-5">Empowering international tourists, multi-vendors, corporate fleets, and service providers worldwide. Access instant digital identities, smart retail catalogs, real-time taxi dispatch, forex rates, and multi-stage affiliate rewards in a single unified ecosystem.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    @auth
                        <a href="{{ $dashboardRoute ?? route('member.dashboard') }}" class="btn btn-primary btn-lg shadow rounded-pill px-4">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow rounded-pill px-4">Join Global Platform</a>
                    @endauth
                    <a href="#features" class="btn btn-outline-light btn-lg rounded-pill px-4">Explore Ecosystem</a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 shadow-lg border border-secondary border-opacity-25">
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <span class="badge bg-success p-2 fs-5 rounded-circle shadow-sm" title="Global Connectivity"><i class="fas fa-globe text-white"></i></span>
                        <span class="badge bg-primary p-2 fs-5 rounded-circle shadow-sm" title="Multi-Industry Hub"><i class="fas fa-cubes text-white"></i></span>
                        <span class="badge bg-warning p-2 fs-5 rounded-circle shadow-sm" title="Secure Ecosystem"><i class="fas fa-shield-alt text-white"></i></span>
                    </div>
                    <h4 class="fw-bold text-white mb-2">Tidong® International Hub</h4>
                    <p class="text-light small mb-0">Bridging consumers, vendors, and transport networks across international borders with zero friction.</p>
                </div>
            </div>
        </div>
    </div>
</section>