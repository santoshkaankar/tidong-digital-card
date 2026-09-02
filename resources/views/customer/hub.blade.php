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
        
        @if($guestSession->last_table_or_room)
            <div class="mt-3 pt-2 border-top border-white-50 d-flex justify-content-between small">
                <span>Session Location:</span>
                <strong class="text-warning">Table / Room #{{ $guestSession->last_table_or_room }}</strong>
            </div>
        @endif
    </div>

    <!-- Services Grid (Dynamic Hub Menu) -->
    <div class="row g-3">
        
        <!-- 1. Food & Kitchen Service -->
        <div class="col-6">
            <a href="{{ route('vendor.catalogs.index') }}" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-danger-subtle text-danger mx-auto mb-2">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                        {{ \App\Services\TranslationEngineService::get('food_menu', $currentLang) }}
                    </h6>
                </div>
            </a>
        </div>

        <!-- 2. Taxi & Tourister Vehicle Service -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Taxi Service Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-warning-subtle text-warning mx-auto mb-2">
                        <i class="fas fa-taxi"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                        {{ \App\Services\TranslationEngineService::get('taxi_booking', $currentLang) }}
                    </h6>
                </div>
            </a>
        </div>

        <!-- 3. Hotel Room Stay Service -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Hotel Booking Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-success-subtle text-success mx-auto mb-2">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                        {{ \App\Services\TranslationEngineService::get('hotel_booking', $currentLang) }}
                    </h6>
                </div>
            </a>
        </div>

        <!-- 4. Money & Currency Exchange Service -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Money Exchange Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-info-subtle text-info mx-auto mb-2">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                        {{ \App\Services\TranslationEngineService::get('money_exchange', $currentLang) }}
                    </h6>
                </div>
            </a>
        </div>

        <!-- 5. Souvenirs & Local Emporium (NEW) -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Emporium & Handicrafts Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-secondary-subtle text-dark mx-auto mb-2">
                        <i class="fas fa-store"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                        {{ \App\Services\TranslationEngineService::get('souvenir_handicrafts', $currentLang) }}
                    </h6>
                </div>
            </a>
        </div>

        <!-- 6. Approved Tourist Guides (NEW) -->
        <div class="col-6">
            <a href="javascript:void(0)" onclick="alert('Tourist Guides Service Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 h-100 text-center">
                    <div class="icon-box bg-primary-subtle text-primary mx-auto mb-2">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                        {{ \App\Services\TranslationEngineService::get('tourist_guides', $currentLang) }}
                    </h6>
                </div>
            </a>
        </div>

        <!-- 7. Sightseeing & Entry Tickets (NEW) -->
        <div class="col-12">
            <a href="javascript:void(0)" onclick="alert('Sightseeing Passes Coming Soon')" class="text-decoration-none">
                <div class="service-card p-3 text-center d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-purple-subtle text-purple me-3" style="background:#f3e8ff; color:#7e22ce;">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="text-start">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">
                                {{ \App\Services\TranslationEngineService::get('sightseeing_tickets', $currentLang) }}
                            </h6>
                            <small class="text-muted">Book Entry Passes & Shows</small>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted"></i>
                </div>
            </a>
        </div>

    </div>

    <div class="text-center mt-4 text-muted small">
        <i class="fas fa-shield-alt text-primary me-1"></i> Powered by <strong>Tidong Ecosystem</strong>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>