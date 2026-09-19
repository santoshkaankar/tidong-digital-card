@php
   

    $instructions = [
        ['title' => 'How to Login?', 'desc' => 'Step-by-step guide to securely log in to the portal using your mobile number or email.', 'icon' => 'fa-sign-in-alt', 'route' => 'login'],
        ['title' => 'How to Register?', 'desc' => 'Method to create a new user account and configure your profile details.', 'icon' => 'fa-user-plus', 'route' => 'register'],
        ['title' => 'Create Digital Visiting Card', 'desc' => 'Guide to design an interactive visiting card for your personal or business ID.', 'icon' => 'fa-id-card', 'route' => 'login'],
        ['title' => 'How to Convert T-Coin to Real Money', 'desc' => 'Steps to convert your accumulated T-Coins into real money through platform payouts.', 'icon' => 'fa-coins', 'route' => 'pages.affiliate'],
        ['title' => 'Lucky Draw Bonanza Offer', 'desc' => 'Process to participate in lucky draw bonanza offers and check the winners list.', 'icon' => 'fa-gift', 'route' => 'pages.luckydrow'],
        ['title' => 'Royalty Program & Leadership', 'desc' => 'Steps to achieve royalty levels and unlock leadership perks.', 'icon' => 'fa-crown', 'route' => 'pages.royalty'],
        ['title' => 'Member Guidance Guide', 'desc' => 'Complete handbook for new members to understand all platform features.', 'icon' => 'fa-book-reader', 'route' => 'guidance.member'],
        ['title' => 'Restaurant Menu QR Setup', 'desc' => 'Guide to generate digital menus and table QR codes for your food outlet.', 'icon' => 'fa-utensils', 'route' => 'guidance.restaurant'],
        ['title' => 'Taxi Booking Guide', 'desc' => 'How to manage vendor bookings for local cab and taxi rides.', 'icon' => 'fa-taxi', 'route' => 'vendor.taxi.rides'],
        ['title' => 'Update Forex Rates', 'desc' => 'Information to track and update daily currency exchange rates live.', 'icon' => 'fa-exchange-alt', 'route' => 'vendor.exchange.rates'],
        ['title' => 'Tour Guide Booking', 'desc' => 'How to book local tourism and historical guide services.', 'icon' => 'fa-map-marked-alt', 'route' => 'vendor.guide.bookings'],
        ['title' => 'Smart Hub Access', 'desc' => 'Method to access hub features using universal QR codes without downloading any app.', 'icon' => 'fa-qrcode', 'route' => 'customer.hub'],
        ['title' => 'How to Reset Password?', 'desc' => 'Process to recover your password if you happen to forget it.', 'icon' => 'fa-key', 'route' => 'password.request'],
        ['title' => 'Update Profile Picture', 'desc' => 'Method to edit your account profile photo and personal information.', 'icon' => 'fa-user-edit', 'route' => 'login'],
        ['title' => 'Vendor Store Setup', 'desc' => 'Register your shop or retail business online and list your products.', 'icon' => 'fa-store', 'route' => 'register'],
        ['title' => 'Catalog Pricing (MRP/Sale)', 'desc' => 'Guide to set custom retail sale prices and discounts on your products.', 'icon' => 'fa-tags', 'route' => 'login'],
        ['title' => 'WhatsApp Direct Share', 'desc' => 'Feature to send your card link directly to clients on WhatsApp with a single click.', 'icon' => 'fa-share-square', 'route' => 'login'],
        ['title' => 'Sponsored Ad Space Booking', 'desc' => 'Rules to promote your business ad banner on the welcome page.', 'icon' => 'fa-bullhorn', 'route' => 'login'],
        ['title' => 'Customer Feedback & Support', 'desc' => 'Method to provide suggestions or contact the platform support team.', 'icon' => 'fa-headset', 'route' => 'pages.contact'],
        ['title' => 'Terms & Privacy Guidelines', 'desc' => 'Information regarding company legal terms, conditions, and user privacy policies.', 'icon' => 'fa-shield-alt', 'route' => 'pages.terms'],
        ['title' => 'About Tidong Ecosystem', 'desc' => 'Complete details about the company mission, vision, and digital services.', 'icon' => 'fa-info-circle', 'route' => 'pages.about'],
        ['title' => 'Affiliate Stages Breakdown', 'desc' => 'Complete list of different affiliate levels and their commission milestones.', 'icon' => 'fa-sitemap', 'route' => 'pages.affiliate'],
        ['title' => 'Notification Alerts Setting', 'desc' => 'Settings to receive important platform alerts and updates on your device.', 'icon' => 'fa-bell', 'route' => 'login'],
        ['title' => 'Mobile Responsiveness Guide', 'desc' => 'Tips to use the website perfectly on both smartphones and desktops.', 'icon' => 'fa-mobile-alt', 'route' => 'login'],
        ['title' => 'Data Export & Backup', 'desc' => 'Process to take backups of your business catalogs and customer data.', 'icon' => 'fa-database', 'route' => 'login'],
        ['title' => 'Partner Rewards Claim', 'desc' => 'Claim special partner rewards earned upon completing targets.', 'icon' => 'fa-award', 'route' => 'pages.royalty'],
        ['title' => 'Secure Logout Process', 'desc' => 'Safely log out from your account and close your session.', 'icon' => 'fa-sign-out-alt', 'route' => 'login'],
        ['title' => 'Browser Compatibility', 'desc' => 'Recommended web browsers to use for the best performance.', 'icon' => 'fa-globe', 'route' => 'login'],
        ['title' => 'Quick FAQ Access', 'desc' => 'Find answers to frequently asked questions right here.', 'icon' => 'fa-question-circle', 'route' => 'pages.contact'],
        ['title' => 'Multi-Language Support', 'desc' => 'Facility to explore the platform in different languages.', 'icon' => 'fa-language', 'route' => 'login'],
        ['title' => 'Merchant Verification Guide', 'desc' => 'Steps to get your business account officially verified as a merchant.', 'icon' => 'fa-check-circle', 'route' => 'register'],
        ['title' => 'Discount Coupon Redemption', 'desc' => 'How to apply special discount coupons at participating stores.', 'icon' => 'fa-ticket-alt', 'route' => 'login'],
        ['title' => 'Direct Calling Integration', 'desc' => 'Feature allowing card viewers to place a direct phone call with one click.', 'icon' => 'fa-phone-alt', 'route' => 'login'],
        ['title' => 'Email Inquiry Integration', 'desc' => 'Add a direct email contact link to your digital visiting card.', 'icon' => 'fa-envelope-open', 'route' => 'login'],
        ['title' => 'Social Media Link Setup', 'desc' => 'Attach Facebook, Instagram, LinkedIn, and Twitter profile links to your card.', 'icon' => 'fa-share-alt', 'route' => 'login'],
        ['title' => 'Business Hours Configuration', 'desc' => 'Display your shop or office opening and closing times on your profile.', 'icon' => 'fa-clock', 'route' => 'login'],
        ['title' => 'Location & Address Mapping', 'desc' => 'Embed Google Map location links onto your visiting card or store profile.', 'icon' => 'fa-map-marker-alt', 'route' => 'login'],
        ['title' => 'Account Security Best Practices', 'desc' => 'Important suggestions to keep your password and account secure.', 'icon' => 'fa-lock', 'route' => 'login'],
        ['title' => 'Community Guidelines', 'desc' => 'Rules for polite and professional behavior with other members on the platform.', 'icon' => 'fa-users', 'route' => 'pages.terms'],
        ['title' => 'Future Updates & Roadmap', 'desc' => 'Information about upcoming new features arriving on the Tidong Digital ecosystem.', 'icon' => 'fa-rocket', 'route' => 'pages.about'],
    ];
@endphp


<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold text-uppercase" style="letter-spacing: 1px;">
                <i class="fas fa-book me-1"></i> Knowledge Base & Instructions
            </span>
            <h2 class="fw-bold text-dark mt-2">Platform Instructions & User Guides</h2>
            <p class="text-muted">Sabhi features, login, registration, aur services ko use karne ke liye step-by-step guides (40+ Guides)</p>
        </div>

        <div class="row g-4">
            @foreach($instructions as $item)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border shadow-sm rounded-4 p-3 transition-hover bg-light bg-opacity-25">
                        <div class="card-body">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="fas {{ $item['icon'] }} fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-dark fs-6 mb-2">{{ $item['title'] }}</h5>
                            <p class="text-muted small mb-3">{{ $item['desc'] }}</p>
                            <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}" class="text-primary text-decoration-none fw-semibold small">
                                Read Guide <i class="fas fa-arrow-right ms-1"></i>
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
        transform: translateY(-4px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.075) !important;
        background-color: #fff !important;
    }
</style>