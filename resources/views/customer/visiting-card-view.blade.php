<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->business_name ?? 'Digital Visiting Card' }} - Tidong® Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #e2e8f0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        .mobile-container {
            max-width: 420px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        .atm-card-wrapper { padding: 20px; background: #f8fafc; }
        .atm-card {
            width: 100%;
            aspect-ratio: 1.586;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: 16px;
            padding: 20px;
            color: #fff;
            position: relative;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .atm-card::after {
            content: ''; position: absolute; top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: linear-gradient(to bottom right, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 40%, rgba(255,255,255,0) 100%);
            transform: rotate(30deg); pointer-events: none;
        }
        .card-logo { width: 50px; height: 50px; border-radius: 10px; object-fit: cover; background: #fff; padding: 2px; }
        .chip-icon { width: 35px; opacity: 0.8; }
        .action-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; padding: 20px; text-align: center; }
        .action-btn { 
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-decoration: none; color: #475569; font-size: 0.8rem; font-weight: 600; gap: 8px;
        }
        .action-btn .icon-circle {
            width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s;
        }
        .action-btn:hover .icon-circle { transform: translateY(-3px); }
        .bg-call { background: #3b82f6; }
        .bg-whatsapp { background: #22c55e; }
        .bg-email { background: #ef4444; }
        .bg-map { background: #f59e0b; }
        .section-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 15px; padding: 0 20px; border-left: 4px solid #38bdf8; margin-left: 20px; }
        .info-list { padding: 0 20px; list-style: none; margin: 0; }
        .info-list li { display: flex; align-items: center; gap: 15px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 0.95rem; }
        .info-list li:last-child { border-bottom: none; }
        .info-list i { color: #38bdf8; font-size: 1.1rem; width: 20px; text-align: center; }
        .qr-section { background: #f8fafc; padding: 30px 20px; text-align: center; border-radius: 0 0 24px 24px; }
        .qr-image { width: 150px; height: 150px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 4px solid #fff; }
    </style>
</head>
<body>

    <div class="container pb-5">
        <div class="text-center mt-3 mb-2 d-print-none">
            <a href="{{ route('customer.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="mobile-container">
            <div class="atm-card-wrapper">
                <div class="atm-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-0 text-white" style="letter-spacing: 1px;">{{ $card->business_name }}</h5>
                            @if($card->tagline)
                                <small class="text-white-50" style="font-size: 0.7rem;">{{ $card->tagline }}</small>
                            @endif
                        </div>
                        @if(!empty($card->photo))
                            <img src="{{ asset('storage/' . $card->photo) }}" class="card-logo shadow-sm" alt="Logo">
                        @else
                            <div class="card-logo d-flex align-items-center justify-content-center text-dark bg-light fw-bold">
                                {{ substr($card->business_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="my-auto">
                        <svg class="chip-icon mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><line x1="9" y1="4" x2="9" y2="20"></line><line x1="15" y1="4" x2="15" y2="20"></line><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line></svg>
                        <h4 class="mb-0 fw-bold" style="letter-spacing: 2px; font-family: monospace;">
                            {{ $card->phone }}
                        </h4>
                    </div>

                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <small class="text-white-50 text-uppercase" style="font-size: 0.6rem;">Card Holder</small>
                            <h6 class="mb-0 text-white text-uppercase fw-bold">{{ $card->name }}</h6>
                            @if($card->owner_name)
                                <small class="text-white-50">({{ $card->owner_name }})</small>
                            @endif
                        </div>
                        <div>
                            <i class="fas fa-wifi" style="transform: rotate(90deg); opacity: 0.7;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-grid">
                @if($card->phone)
                <a href="tel:{{ $card->phone }}" class="action-btn">
                    <div class="icon-circle bg-call"><i class="fas fa-phone-alt"></i></div>
                    Call
                </a>
                @endif
                @if($card->whatsapp)
                <a href="https://wa.me/{{ $card->whatsapp }}?text=Hello%20{{ urlencode($card->name) }}" class="action-btn" target="_blank">
                    <div class="icon-circle bg-whatsapp"><i class="fab fa-whatsapp"></i></div>
                    WhatsApp
                </a>
                @endif
                @if($card->gmail || $card->other_email)
                <a href="mailto:{{ $card->gmail ?? $card->other_email }}" class="action-btn">
                    <div class="icon-circle bg-email"><i class="fas fa-envelope"></i></div>
                    Email
                </a>
                @endif
                @if($card->map_location_link)
                <a href="{{ $card->map_location_link }}" class="action-btn" target="_blank">
                    <div class="icon-circle bg-map"><i class="fas fa-map-marker-alt"></i></div>
                    Location
                </a>
                @endif
            </div>

            <hr class="mx-4 my-2 text-muted">

            <div class="section-title mt-4">Contact Details</div>
            <ul class="info-list">
                @if($card->alt_phone)
                <li>
                    <i class="fas fa-mobile-alt"></i>
                    <div>
                        <a href="tel:{{ $card->alt_phone }}" class="text-decoration-none text-dark">{{ $card->alt_phone }}</a>
                        <div class="small text-muted">Alternate Mobile</div>
                    </div>
                </li>
                @endif
                @if($card->address || $card->city)
                <li>
                    <i class="fas fa-building"></i>
                    <div>
                        {{ $card->address }}<br>
                        @if($card->area){{ $card->area }}, @endif
                        @if($card->city){{ $card->city }} - @endif
                        @if($card->pincode){{ $card->pincode }}@endif<br>
                        @if($card->state){{ $card->state }}@endif
                    </div>
                </li>
                @endif
            </ul>

            @if($card->about_us || $card->services_or_products)
                <div class="section-title mt-4">Business Details</div>
                <div class="px-4 pb-3">
                    @if($card->about_us)
                        <h6 class="fw-bold mb-1 small text-dark">About Us</h6>
                        <p class="text-muted small mb-3">{{ $card->about_us }}</p>
                    @endif
                    @if($card->services_or_products)
                        <h6 class="fw-bold mb-1 small text-dark">Our Services / Products</h6>
                        <p class="text-muted small mb-0">{{ $card->services_or_products }}</p>
                    @endif
                </div>
            @endif

            @if($card->qr_code || $card->upi_id)
                <div class="qr-section mt-3">
                    <h5 class="fw-bold text-dark mb-3">Scan & Pay</h5>
                    @if($card->qr_code)
                        <img src="{{ asset('storage/' . $card->qr_code) }}" class="qr-image mb-3" alt="Payment QR Code">
                    @endif
                    @if($card->upi_id)
                        <div class="fw-bold text-primary mb-3">UPI ID: {{ $card->upi_id }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>