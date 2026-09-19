@php
    $instructions = [
        ['title' => 'Login Kaise Karein?', 'desc' => 'Apne registered mobile number ya email se secure login karne ka step-by-step tareeqa.', 'icon' => 'bi-box-arrow-in-right', 'route' => 'login'],
        ['title' => 'Register Kaise Karein?', 'desc' => 'Naya account banane aur apni profile setup karne ki poori jankari.', 'icon' => 'bi-person-plus', 'route' => 'register'],
        ['title' => 'Visiting Card Banayein', 'desc' => 'Apna interactive digital visiting card sirf 2 minute me design karein.', 'icon' => 'bi-card-heading', 'route' => 'pages.about'],
        ['title' => 'T-Coins Kaise Earn Karein?', 'desc' => 'Affiliate aur referrals ke zariye T-Coins kamane ke niyam aur tarike.', 'icon' => 'bi-coin', 'route' => 'pages.affiliate'],
        ['title' => 'Lucky Draw Me Participate Karein', 'desc' => 'Bonanza offers aur lucky draw me kaise hissa lein, uski puri guide.', 'icon' => 'bi-gift', 'route' => 'pages.luckydrow'],
        ['title' => 'Royalty Program Join Karein', 'desc' => 'Leadership levels aur royalty benefits hasil karne ki puri process.', 'icon' => 'bi-award', 'route' => 'pages.royalty'],
        ['title' => 'Restaurant Menu Setup', 'desc' => 'Apne outlet ya restaurant ka digital menu aur QR code kaise generate karein.', 'icon' => 'bi-shop', 'route' => 'pages.guidance.restaurant'],
        ['title' => 'Member Guidance Guide', 'desc' => 'Members ke liye platform ko use karne ke sabhi zaroori nirdesh.', 'icon' => 'bi-book', 'route' => 'pages.guidance.member'],
        // Aap yahan aise hi aur bhi 40-50 cards add kar sakte hain...
    ];
@endphp

<section class="py-5 bg-light border-top">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Platform Instructions & User Guides</h2>
            <p class="text-muted">Sabhi features, login, registration aur services ko use karne ke liye step-by-step guides</p>
        </div>

        <div class="row g-4">
            @foreach($instructions as $item)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-3 transition-hover">
                        <div class="card-body">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="bi {{ $item['icon'] }} fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-dark fs-6 mb-2">{{ $item['title'] }}</h5>
                            <p class="text-muted small mb-3">{{ $item['desc'] }}</p>
                            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" class="text-primary text-decoration-none fw-semibold small">
                                Read Guide <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-4px;);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.075) !important;
    }
</style>