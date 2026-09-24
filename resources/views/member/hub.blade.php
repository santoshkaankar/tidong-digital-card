@extends('member.partials.layout')

@section('content')
<div class="container-fluid py-4 px-4">
    
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-qrcode text-primary me-2"></i> Tidong Super Hub Directory
            </h2>
            <p class="text-muted mb-0">Explore all multi-industry digital infrastructure and cross-border services from one centralized location.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="dropdown d-inline-block">
                <button class="btn btn-white border rounded-pill dropdown-toggle fw-semibold shadow-sm px-4 py-2" type="button" data-bs-toggle="dropdown">
                    🌐 Language: <span class="text-uppercase">{{ $currentLang ?? 'en' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                    <li><a class="dropdown-item" href="?lang=en">English</a></li>
                    <li><a class="dropdown-item" href="?lang=hi">Hindi</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Search / Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="fas fa-search"></i></span>
            <input type="text" id="serviceSearchInput" class="form-control border-0 shadow-none" placeholder="Search services (e.g., Restaurant, Hotel, Taxi, Pharmacy)...">
        </div>
    </div>

    <!-- Section 1: Food & Hospitality -->
    <div class="mb-5 service-section">
        <h5 class="fw-bold text-dark mb-3 border-start border-primary border-4 ps-2">Food & Hospitality</h5>
        <div class="row g-4">
            
            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="restaurant food menu">
                <a href="{{ route('vendor.restaurant.menu-card.index') }}" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Restaurant & Food</h6>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;">Active Module</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Manage digital menus, table orders, kitchen tickets, and online food processing.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="catering service">
                <a href="javascript:void(0)" onclick="alert('Catering Service Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#ffedd5; color:#c2410c;">
                                <i class="fas fa-concierge-bell"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Catering Service</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Bulk food orders, event catering menus, and professional catering staff bookings.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="cafe ice cream">
                <a href="javascript:void(0)" onclick="alert('Cafe & Ice Cream Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#fce7f3; color:#db2777;">
                                <i class="fas fa-ice-cream"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Cafe & Ice Cream</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Specialty coffee houses, dessert bars, quick bites, and counter ordering systems.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="bakery cakes">
                <a href="javascript:void(0)" onclick="alert('Bakery & Cake Shop Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Bakery & Cakes</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Custom celebration cakes, fresh bakes, pastries, and daily confectionery items.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="hotel resort stay">
                <a href="javascript:void(0)" onclick="alert('Hotel / Resort Booking Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-hotel"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Hotel & Resort Stay</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Room reservations, luxury property stays, amenities booking, and guest management.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="homestay pg">
                <a href="javascript:void(0)" onclick="alert('Homestay & PG Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#ccfbf1; color:#0f766e;">
                                <i class="fas fa-home"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Homestay & PG</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Verified paying guest accommodations, local family stays, and monthly rentals.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Section 2: Events, Venue & Media -->
    <div class="mb-5 service-section">
        <h5 class="fw-bold text-dark mb-3 border-start border-primary border-4 ps-2">Events, Venue & Media</h5>
        <div class="row g-4">
            
            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="banquet marriage home">
                <a href="javascript:void(0)" onclick="alert('Marriage Home & Banquet Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-archway"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Banquet & Marriage Home</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Grand wedding lawns, party halls, corporate convention booking slots.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="event wedding planner">
                <a href="javascript:void(0)" onclick="alert('Event Planner Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#f3e8ff; color:#7e22ce;">
                                <i class="fas fa-glass-cheers"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Event & Wedding Planner</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Full-scale event production, stage designing, sound coordination, and management.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="tent decoration">
                <a href="javascript:void(0)" onclick="alert('Tent House & Decoration Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-campground"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Tent & Decoration</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Mandaps, floral setups, weather tents, lighting, and seating arrangements.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="photography media">
                <a href="javascript:void(0)" onclick="alert('Photography & Videography Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#e2e8f0; color:#1e293b;">
                                <i class="fas fa-camera-retro"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Photography & Media</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Cinematic wedding shoots, drone coverage, portfolio albums, and studio packages.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Section 3: Transport & Travel -->
    <div class="mb-5 service-section">
        <h5 class="fw-bold text-dark mb-3 border-start border-primary border-4 ps-2">Transport & Travel</h5>
        <div class="row g-4">
            
            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="taxi cab service">
                <a href="javascript:void(0)" onclick="alert('Taxi Service Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-taxi"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Taxi & Cab Service</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Local city rides, outstation cabs, airport transfers, and verified fleet drivers.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="bike scooter rental">
                <a href="javascript:void(0)" onclick="alert('Bike Rental Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#f1f5f9; color:#475569;">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Bike & Scooter Rental</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Self-ride two-wheelers, hourly or daily rental packages with insurance support.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="tourist guides">
                <a href="javascript:void(0)" onclick="alert('Tourist Guides Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Approved Tourist Guides</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Certified historical and cultural tour guides for sightseeing and monuments.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="sightseeing entry tickets">
                <a href="javascript:void(0)" onclick="alert('Sightseeing Passes Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#f3e8ff; color:#7e22ce;">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Sightseeing & Passes</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Fast-track entry passes, heritage monument tickets, and cultural show bookings.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Section 4: Retail & Financial Services -->
    <div class="mb-5 service-section">
        <h5 class="fw-bold text-dark mb-3 border-start border-primary border-4 ps-2">Retail, Health & Financial Services</h5>
        <div class="row g-4">
            
            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="souvenirs emporium handicraft">
                <a href="javascript:void(0)" onclick="alert('Handicraft & Emporium Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Souvenirs & Emporium</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Authentic local handicrafts, traditional art pieces, artifacts, and keepsakes.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="grocery supermarket">
                <a href="javascript:void(0)" onclick="alert('Grocery & Supermarket Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Grocery & Supermarket</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Daily essentials, organic produce, household items, and express home delivery.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="salon spa wellness">
                <a href="javascript:void(0)" onclick="alert('Salon & Spa Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem; background:#fce7f3; color:#db2777;">
                                <i class="fas fa-spa"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Salon & Spa</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Hair styling, therapeutic massages, beauty treatments, and wellness packages.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="medical pharmacy">
                <a href="javascript:void(0)" onclick="alert('Medical Pharmacy Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Medical & Pharmacy</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Prescription medicines, emergency healthcare items, and wellness supplements.</p>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 service-item" data-name="money exchange forex">
                <a href="javascript:void(0)" onclick="alert('Money Exchange Coming Soon')" class="text-decoration-none">
                    <div class="card border-0 rounded-4 p-4 h-100 shadow-sm service-card-hover bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center me-3" style="width:60px; height:60px; font-size:1.75rem;">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Money Exchange (Forex)</h6>
                                <span class="badge bg-warning-subtle text-warning text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;">Coming Soon</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-0">Currency conversion, multi-currency travel cards, and secure remittance services.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center py-4 text-muted small border-top">
        <i class="fas fa-shield-alt text-primary me-1"></i> Powered by <strong>Tidong Marketing Pvt. Ltd</strong> — Global Multi-Industry Digital Infrastructure.
    </div>

</div>

<!-- Realtime Search Script -->
<script>
    document.getElementById('serviceSearchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.service-item');
        
        items.forEach(function(item) {
            let name = item.getAttribute('data-name');
            if (name.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<style>
    .service-card-hover {
        transition: all 0.3s ease;
    }
    .service-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
</style>
@endsection