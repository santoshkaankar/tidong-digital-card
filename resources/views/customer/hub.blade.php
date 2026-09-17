<!DOCTYPE html>
<html lang="{{ $currentLang }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Super-QR Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .service-card {
            border: none;
            border-radius: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background: #ffffff;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .icon-box {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

<div class="container py-4" style="max-width: 500px;">
    
    <!-- Header & Language Switcher Dropdown -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                <i class="fas fa-qrcode me-1"></i> Tidong Super QR
            </span>
        </div>
        
        <!-- Global Tourist Multi-Language Switcher -->
        <div class="dropdown">
            <button class="btn btn-sm btn-white border rounded-pill dropdown-toggle fw-semibold shadow-sm" type="button" data-bs-toggle="dropdown">
                🌐 {{ \App\Services\TranslationEngineService::$languages[$currentLang] ?? 'Language' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                @foreach(\App\Services\TranslationEngineService::$languages as $code => $name)
                    <li>
                        <a class="dropdown-item @if($currentLang == $code) active fw-bold @endif" href="?lang={{ $code }}">
                            {{ $name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Dynamic Greeting Section -->
    <div class="card border-0 bg-primary text-white rounded-4 p-4 mb-4 shadow-sm">
        <h4 class="fw-bold mb-1">
            {{ \App\Services\TranslationEngineService::get('welcome', $currentLang) }} 👋
        </h4>
        <p class="mb-0 text-white-50 small">
            {{ \App\Services\TranslationEngineService::get('select_service', $currentLang) }}
        </p>
        
        @if(isset($guestSession) && $guestSession->last_table_or_room)
            <div class="mt-3 pt-2 border-top border-white-50 d-flex justify-content-between small">
                <span>Session Location:</span>
                <strong class="text-warning">Table / Room #{{ $guestSession->last_table_or_room }}</strong>
            </div>
        @endif
    </div>

    <!-- Services Grid (Dynamic Hub Menu) -->
    <div class="row g-3">
        
        <!-- 1. Food & Hospitality -->
        <div class="col-6">
            <a href="{{ route('vendor.restaurant.menu-card.index') }}" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-danger-subtle text-danger mx-auto mb-2">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Restaurant & Food</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Catering Service Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-orange-subtle text-orange mx-auto mb-2" style="background:#ffedd5; color:#c2410c;">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Catering Service</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Cafe & Ice Cream Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-pink-subtle text-pink mx-auto mb-2" style="background:#fce7f3; color:#db2777;">
                        <i class="fas fa-ice-cream"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Cafe & Ice Cream</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Bakery & Cake Shop Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-warning-subtle text-warning mx-auto mb-2">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Bakery & Cakes</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Hotel / Resort Booking Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-success-subtle text-success mx-auto mb-2">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Hotel & Resort Stay</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Homestay & PG Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-teal-subtle text-teal mx-auto mb-2" style="background:#ccfbf1; color:#0f766e;">
                        <i class="fas fa-home"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Homestay & PG</h6>
                </div>
            </a>
        </div>

        <!-- 2. Events, Venue & Media -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Marriage Home & Banquet Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-primary-subtle text-primary mx-auto mb-2">
                        <i class="fas fa-archway"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Banquet & Marriage Home</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Event Planner Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-purple-subtle text-purple mx-auto mb-2" style="background:#f3e8ff; color:#7e22ce;">
                        <i class="fas fa-glass-cheers"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Event & Wedding Planner</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Tent House & Decoration Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-info-subtle text-info mx-auto mb-2">
                        <i class="fas fa-campground"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Tent & Decoration</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Photography & Videography Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-dark-subtle text-dark mx-auto mb-2" style="background:#e2e8f0; color:#1e293b;">
                        <i class="fas fa-camera-retro"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Photography & Media</h6>
                </div>
            </a>
        </div>

        <!-- 3. Transport & Travel -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Taxi Service Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-warning-subtle text-warning mx-auto mb-2">
                        <i class="fas fa-taxi"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Taxi & Cab Service</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Bike Rental Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-secondary-subtle text-secondary mx-auto mb-2" style="background:#f1f5f9; color:#475569;">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Bike & Scooter Rental</h6>
                </div>
            </a>
        </div>

        <!-- 4. Retail & Shopping -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Handicraft & Emporium Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-danger-subtle text-danger mx-auto mb-2">
                        <i class="fas fa-store"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Souvenirs & Emporium</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Grocery & Supermarket Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-success-subtle text-success mx-auto mb-2">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Grocery & Supermarket</h6>
                </div>
            </a>
        </div>

        <!-- 5. Health, Wellness & Financial Services -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Salon & Spa Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-pink-subtle text-pink mx-auto mb-2" style="background:#fce7f3; color:#db2777;">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Salon & Spa</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Medical Pharmacy Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-danger-subtle text-danger mx-auto mb-2">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Medical & Pharmacy</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Money Exchange Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-info-subtle text-info mx-auto mb-2">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Money Exchange (Forex)</h6>
                </div>
            </a>
        </div>

        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Tourist Guides Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-primary-subtle text-primary mx-auto mb-2">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">Approved Tourist Guides</h6>
                </div>
            </a>
        </div>

        <!-- Full Width Ticket / Special Service Card -->
        <div class="col-12">
            <a href="javascript:void(0)" onclick="alert('Sightseeing Passes Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 text-center d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-purple-subtle text-purple me-3" style="background:#f3e8ff; color:#7e22ce;">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Sightseeing & Entry Tickets</h6>
                            <small class="text-muted">Book Entry Passes & Shows</small>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
        </div>

    </div>

    <div class="text-center mt-4 text-muted small">
        <i class="fas fa-shield-alt text-primary me-1"></i> Powered by <strong>Tidong Marketing Pvt. Ltd</strong>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>