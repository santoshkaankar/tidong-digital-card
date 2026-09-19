<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container text-center text-md-start">
        <div class="row g-4">
            <!-- Column 1: Brand & Value Rule -->
            <div class="col-md-4 col-lg-4">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-layer-group me-2"></i>Tidong<span class="text-white">®</span> Digital
                </h5>
                <p class="small text-light lh-base mb-3 opacity-75">
                    Interactive digital visiting cards, multi-stage affiliate rewards system, and smart digital catalogs for businesses.
                </p>
                <div class="badge bg-success bg-opacity-25 text-success px-3 py-2 rounded-pill fw-bold border border-success border-opacity-25">
                    <i class="fas fa-coins text-warning me-1"></i> 1 T-Coin = ₹1 INR
                </div>
            </div>

            <!-- Column 2: Affiliate & Guidance Pages -->
            <div class="col-md-4 col-lg-4">
                <h6 class="fw-bold text-warning text-uppercase mb-3" style="letter-spacing: 1px;">
                    <i class="fas fa-project-diagram me-1"></i> Program & Guidance
                </h6>
                <ul class="list-unstyled small mb-0 lh-lg">
                    <li class="mb-1">
                        <a href="{{ route('pages.affiliate') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Affiliate Program & Stages
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('pages.luckydrow') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Lucky Draw Bonanza Offer
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('pages.royalty') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Royalty Program & Leadership
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('guidance.member') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Member Guidance Guide
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('guidance.restaurant') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Restaurant & Partner Guidance
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Company & Legal Pages -->
            <div class="col-md-4 col-lg-4">
                <h6 class="fw-bold text-warning text-uppercase mb-3" style="letter-spacing: 1px;">
                    <i class="fas fa-shield-alt me-1"></i> Company & Legal
                </h6>
                <ul class="list-unstyled small mb-0 lh-lg">
                    <li class="mb-1">
                        <a href="{{ route('pages.about') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> About Us
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('pages.terms') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Terms & Conditions
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('pages.privacy') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Privacy Policy
                        </a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('pages.contact') }}" class="text-light text-decoration-none opacity-75">
                            <i class="fas fa-chevron-right fs-6 me-1 text-primary"></i> Contact Us
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <!-- Bottom Copyright & Powered By Section -->
        <div class="row align-items-center small text-light opacity-75">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <p class="mb-1">&copy; 2022 - {{ date('Y') }} All rights reserved.</p>
                <p class="mb-0">Tidong® is a registered trademark.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Powered by <span class="text-white fw-semibold">Tidong Marketing Pvt. Ltd.</span></p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var homeCarouselEl = document.getElementById('homeAdCarousel');
        if(homeCarouselEl) {
            var homeCarousel = new bootstrap.Carousel(homeCarouselEl, {
                interval: 4000,
                ride: 'carousel'
            });

            let savedSlide = localStorage.getItem('homeActiveAdSlideIndex');
            if (savedSlide !== null) {
                homeCarousel.to(parseInt(savedSlide));
            }

            homeCarouselEl.addEventListener('slid.bs.carousel', function (e) {
                localStorage.setItem('homeActiveAdSlideIndex', e.to);
            });
        }

        let container = document.getElementById('homeRandomAdContainer');
        if(container) {
            let boxes = Array.from(container.getElementsByClassName('ad-box'));
            boxes.sort(() => Math.random() - 0.5);
            boxes.forEach(box => container.appendChild(box));
        }
    });
</script>