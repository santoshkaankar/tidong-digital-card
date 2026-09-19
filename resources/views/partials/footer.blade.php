<footer class="text-white pt-5 pb-3 border-top" style="background-color: #1a1d20; border-color: #2c3034 !important;">
    <div class="container py-4">
        <div class="row g-4 justify-content-between">
            
            <!-- Column 1: Brand & T-Coin Badge -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="d-flex align-items-center gap-2 mb-3">
                    @include('partials.logos.full', ['iconWidth' => '32', 'iconHeight' => '32'])
                    <span class="fw-bold fs-4 text-white">Tidong® Digital</span>
                </div>
                <p class="text-muted small mb-4">
                    Empowering digital growth, smart visiting cards, affiliate rewards, and seamless local business integration.
                </p>
                <div class="d-inline-flex align-items-center bg-dark border border-secondary rounded-pill px-3 py-2 text-warning fw-bold small shadow-sm">
                    <i class="fas fa-coins me-2 text-warning"></i> 1 T-Coin = 1 INR
                </div>
            </div>

            <!-- Column 2: Program & Guidance Links -->
            <div class="col-lg-4 col-md-6 mb-3">
                <h6 class="text-uppercase fw-bold text-warning mb-3" style="letter-spacing: 0.5px;">
                    <i class="fas fa-sitemap me-2"></i> Program & Guidance
                </h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li>
                        <a href="{{ route('pages.affiliate') ?? '#' }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Affiliate Program & Stages
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.luckydrow') ?? '#' }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Lucky Draw Bonanza Offer
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.royalty') ?? '#' }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Royalty Program & Leadership
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.guidance.member') ?? '#' }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Member Guidance Guide
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.guidance.restaurant') ?? '#' }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Restaurant & Partner Guidance
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Company & Legal Links -->
            <div class="col-lg-4 col-md-6 mb-3">
                <h6 class="text-uppercase fw-bold text-warning mb-3" style="letter-spacing: 0.5px;">
                    <i class="fas fa-shield-alt me-2"></i> Company & Legal
                </h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li>
                        <a href="{{ route('pages.about') }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> About Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.terms') }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Terms & Conditions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.privacy') }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.contact') }}" class="text-muted text-decoration-none hover-light">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Contact Us
                        </a>
                    </li>
                    <li>
                        <button type="button" class="btn btn-link p-0 text-muted text-decoration-none hover-light small border-0 text-start" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                            <i class="fas fa-chevron-right text-warning small me-2"></i> Feedback & FAQs / Suggestions
                        </button>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Copyright Row -->
        <hr class="border-secondary my-4 opacity-25">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted small gap-2">
            <div>
                &copy; 2022 &ndash; {{ date('Y') }} <strong class="text-light">Tidong Marketing Pvt Ltd</strong>. All rights reserved.
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('pages.privacy') }}" class="text-muted text-decoration-none">Privacy</a>
                <span>&bull;</span>
                <a href="{{ route('pages.terms') }}" class="text-muted text-decoration-none">Terms</a>
                <span>&bull;</span>
                <a href="{{ route('pages.contact') }}" class="text-muted text-decoration-none">Support</a>
            </div>
        </div>
    </div>
</footer>