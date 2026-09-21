<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Digital</title>
    
    <!-- Bootstrap & FontAwesome Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Navbar Partial -->
    @include('partials.welcome.navbar')

    <!-- Hero Section -->
    @include('partials.welcome.hero')

    <!-- Quick Services Menu Grid -->
    @include('partials.welcome.services')

    <!-- Main Ad Platform Carousel -->
    @include('partials.welcome.carousel-ads')

    <!-- Universal QR Scan Section -->
    @include('partials.welcome.qr-section')

    <!-- Features Section -->
    @include('partials.welcome.features')

    <!-- Instructions & Guides Cards Grid -->
    @include('partials.welcome.instructions-grid')

    <!-- Sponsored Ads & Partner Offers Grid (Sahi naam yahan hai) -->
    @include('partials.welcome.sponsored-ads')

   

    <!-- Modals -->
    @include('partials.welcome.modals')

    <!-- Footer -->
    @include('partials.welcome.footer')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Page Specific Scripts -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Carousel initialization
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

        // Random Ad Box Shuffler
        let container = document.getElementById('homeRandomAdContainer');
        if(container) {
            let boxes = Array.from(container.getElementsByClassName('ad-box'));
            boxes.sort(() => Math.random() - 0.5);
            boxes.forEach(box => container.appendChild(box));
        }
    });
    </script>
</body>
</html>