<!-- File Path: resources/views/member/partials/advertising.blade.php -->

<!-- MAIN BANNER SLIDER (Rotates every 6 seconds) -->
<div class="row mb-4">
    <div class="col-12">
        <div id="mainAdSlider" class="carousel slide carousel-fade shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#mainAdSlider" data-bs-slide-to="3"></button>
            </div>
            
            <div class="carousel-inner">
                <!-- 1. Restaurants & Food -->
                <div class="carousel-item active text-white p-4 p-md-5" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); min-height: 180px;">
                    <span class="badge bg-light text-danger fw-bold mb-2"><i class="bi bi-shop me-1"></i> RESTAURANTS & FOOD</span>
                    <h3 class="fw-bold">Explore Top Nearby Restaurants!</h3>
                    <p class="mb-3 text-white-50">Dine-in, Tiffin, Street Food & Custom menus around your location.</p>
                    <a href="{{ Route::has('member.restaurant') ? route('member.restaurant') : url('/member/restaurant') }}" class="btn btn-light btn-sm fw-bold px-3 py-2 text-danger shadow-sm">View Restaurants</a>
                </div>

                <!-- 2. Delivery & Express Services -->
                <div class="carousel-item text-white p-4 p-md-5" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); min-height: 180px;">
                    <span class="badge bg-light text-warning fw-bold mb-2"><i class="bi bi-truck me-1"></i> DELIVERY SERVICES</span>
                    <h3 class="fw-bold">Fast Doorstep Delivery Services</h3>
                    <p class="mb-3 text-white-50">Instant parcel, food, grocery & document pickup and delivery partners.</p>
                    <a href="{{ Route::has('member.delivery') ? route('member.delivery') : url('/member/delivery') }}" class="btn btn-light btn-sm fw-bold px-3 py-2 text-dark shadow-sm">Book Express Delivery</a>
                </div>

                <!-- 3. Transport & Taxi Rides -->
                <div class="carousel-item text-white p-4 p-md-5" style="background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); min-height: 180px;">
                    <span class="badge bg-warning text-dark fw-bold mb-2"><i class="bi bi-taxi-front-fill me-1"></i> TAXI & RENTALS</span>
                    <h3 class="fw-bold">Book Taxi, Cabs & Bike Rentals</h3>
                    <p class="mb-3 text-white-50">Verified local cabs, outstation taxis & daily self-drive bike rentals.</p>
                    <a href="{{ Route::has('member.taxi') ? route('member.taxi') : url('/member/taxi') }}" class="btn btn-light btn-sm fw-bold px-3 py-2 text-primary shadow-sm">Book Rides Now</a>
                </div>

                <!-- 4. Events & Wedding Planning -->
                <div class="carousel-item text-white p-4 p-md-5" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); min-height: 180px;">
                    <span class="badge bg-light text-primary fw-bold mb-2"><i class="bi bi-balloon-heart me-1"></i> EVENTS & VENUES</span>
                    <h3 class="fw-bold">Banquet Halls, DJ & Event Planners</h3>
                    <p class="mb-3 text-white-50">Book Marriage Homes, Tent Decoration, Photographers & DJ Systems easily.</p>
                    <a href="{{ Route::has('member.events') ? route('member.events') : url('/member/events') }}" class="btn btn-light btn-sm fw-bold px-3 py-2 text-primary shadow-sm">Explore Event Vendors</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ALL BUSINESSES ROTATING CARDS (Changes every 6 seconds) -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-dark m-0"><i class="fas fa-ad text-danger me-2"></i> Promotional Offers & Partner Services</h5>
    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 small"><i class="bi bi-clock-history me-1"></i> Updates every 6s</span>
</div>

<div class="row g-3 mb-4" id="dynamicAdCards"></div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // All Business & Partner Services List
    const adServices = [
        // 1. Food & Hospitality
        { title: "Restaurants & Dining", icon: "bi-shop", color: "bg-danger", url: "/member/restaurant", desc: "Dine-in, KDS & Street Food" },
        { title: "Catering Services", icon: "bi-egg-fried", color: "bg-danger text-white", style: "background-color: #dc2626;", url: "/member/catering", desc: "Event & Bulk Food Catering" },
        { title: "Cafe & Ice Cream", icon: "bi-cup-hot-fill", color: "bg-warning text-dark", url: "/member/cafe", desc: "Coffee, Shakes & Desserts" },
        { title: "Bakery & Cake Shop", icon: "bi-cake2-fill", color: "bg-danger text-white", style: "background-color: #e11d48;", url: "/member/bakery", desc: "Custom Cakes & Fresh Bakes" },
        { title: "Hotels & Guest House", icon: "bi-building", color: "bg-success", url: "/member/hotel", desc: "Resorts & Hourly Stays" },
        { title: "Homestay & PG", icon: "bi-house-heart-fill", color: "bg-success text-white", style: "background-color: #059669;", url: "/member/homestay", desc: "Student & Tourist PG Rooms" },

        // 2. Delivery & Courier
        { title: "Delivery Services", icon: "bi-truck", color: "bg-warning text-dark", url: "/member/delivery", desc: "Parcel, Food & Express Pickup" },

        // 3. Transport & Travel
        { title: "Taxi & Cab Services", icon: "bi-taxi-front-fill", color: "bg-primary", url: "/member/taxi", desc: "Local & Outstation Cab Booking" },
        { title: "Bike & Scooter Rental", icon: "bi-bicycle", color: "bg-info text-dark", url: "/member/bike-rental", desc: "Self-Drive Two Wheeler Rides" },
        { title: "Tourist Guides", icon: "bi-person-badge-fill", color: "bg-primary text-white", style: "background-color: #2563eb;", url: "/member/tourist-guide", desc: "Verified Local City Guides" },
        { title: "Travel Agencies", icon: "bi-compass-fill", color: "bg-info text-dark", url: "/member/travel-agency", desc: "Tour Packages & Bus Tickets" },

        // 4. Events, Venue & Media
        { title: "Marriage Home & Banquets", icon: "bi-bank2", color: "bg-purple text-white", style: "background-color: #7c3aed;", url: "/member/marriage-home", desc: "Gardens & Banquet Halls" },
        { title: "Event & Wedding Planners", icon: "bi-calendar-event-fill", color: "bg-purple text-white", style: "background-color: #9333ea;", url: "/member/event-planner", desc: "Complete Wedding Management" },
        { title: "Tent House & Decor", icon: "bi-stars", color: "bg-danger text-white", style: "background-color: #be123c;", url: "/member/tent-decor", desc: "Stage Lighting & Flower Decor" },
        { title: "Photography & Video", icon: "bi-camera-reels-fill", color: "bg-dark text-white", url: "/member/photography", desc: "Pre-wedding & Cinematic Shoot" },
        { title: "DJ & Sound System", icon: "bi-disc-fill", color: "bg-dark text-white", style: "background-color: #111827;", url: "/member/dj-sound", desc: "Sound, Stage & Lighting Setup" },

        // 5. Shopping & E-Commerce
        { title: "Shopping & Mega Stores", icon: "bi-bag-check-fill", color: "bg-success text-white", style: "background-color: #16a34a;", url: "/member/shopping", desc: "Fashion, Grocery & Multi-stores" },

        // 6. Health, Wellness & Beauty
        { title: "Salon, Spa & Parlours", icon: "bi-scissors", color: "bg-purple text-white", style: "background-color: #a855f7;", url: "/member/salon", desc: "Grooming & Hair Styling" },
        { title: "Gym & Fitness Centers", icon: "bi-activity", color: "bg-danger text-white", style: "background-color: #b91c1c;", url: "/member/gym", desc: "Personal Trainers & Yoga Studios" },
        { title: "Medical & Pharmacy", icon: "bi-capsule", color: "bg-primary text-white", style: "background-color: #0d6efd;", url: "/member/medical", desc: "24x7 Medicines & Health Needs" },

        // 7. Financial & Professional Services
        { title: "Money Exchange (Forex)", icon: "bi-currency-exchange", color: "bg-success text-white", style: "background-color: #15803d;", url: "/member/money-exchange", desc: "Currency Exchange & Forex" },
        { title: "Real Estate & Dealers", icon: "bi-house-check-fill", color: "bg-secondary text-white", url: "/member/real-estate", desc: "Buy, Sell & Rent Properties" },
        { title: "Coaching & Institutes", icon: "bi-journal-bookmark-fill", color: "bg-primary text-white", style: "background-color: #1d4ed8;", url: "/member/coaching", desc: "Tuitions & Skill Development" },
        { title: "Local Support Services", icon: "bi-tools", color: "bg-warning text-dark", url: "/member/local-services", desc: "Electricians, Plumbers & Repairs" }
    ];

    let currentIndex = 0;
    const container = document.getElementById("dynamicAdCards");

    function renderCards() {
        if (!container) return;

        let html = '';
        for (let i = 0; i < 4; i++) {
            let item = adServices[(currentIndex + i) % adServices.length];
            html += `
                <div class="col-12 col-sm-6 col-md-3">
                    <a href="${item.url}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-3 h-100 p-3 card-fade-in" style="transition: all 0.4s ease;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="badge ${item.color || ''} p-3 rounded-3" style="${item.style || ''}">
                                    <i class="bi ${item.icon} fs-4 text-white"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark text-truncate">${item.title}</h6>
                                    <small class="text-muted d-block text-truncate" style="font-size: 11px;">${item.desc}</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            `;
        }
        container.innerHTML = html;
        currentIndex = (currentIndex + 4) % adServices.length;
    }

    // Initial Render
    renderCards();

    // Rotate cards every 6000ms (12 seconds)
    setInterval(renderCards, 12000);
});
</script>

<style>
.card-fade-in {
    animation: fadeIn 0.6s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0.3; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>