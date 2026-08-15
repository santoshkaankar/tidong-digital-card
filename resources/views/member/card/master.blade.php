<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->business_name }} - {{ $card->name }}</title>
    
    <!-- Icons & Styling -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/card-themes.css') }}">
</head>
<body>

    @php
        $themeClass = !empty($card->design_type) ? $card->design_type : 'modern';
    @endphp

    <div class="card-wrapper theme-{{ $themeClass }}">
        <div class="card-container">
            
            <!-- Avatar / Photo -->
            @if($card->photo)
                <img src="{{ asset('storage/' . $card->photo) }}" alt="{{ $card->name }}" class="card-avatar">
            @else
                <div class="card-avatar-placeholder">
                    {{ strtoupper(substr($card->name, 0, 1)) }}
                </div>
            @endif

            <!-- Header Info -->
            <h2 class="card-name">{{ $card->name }}</h2>
            <p class="card-business">{{ $card->business_name }}</p>
            @if($card->tagline)
                <p class="card-tagline">"{{ $card->tagline }}"</p>
            @endif

            <!-- Quick Action Buttons -->
            <div class="action-buttons">
                @if($card->phone)
                    <a href="tel:{{ $card->phone }}" class="action-btn">
                        <i class="fas fa-phone"></i> Call
                    </a>
                @endif

                @if($card->whatsapp)
                    <a href="https://wa.me/91{{ $card->whatsapp }}" target="_blank" class="action-btn">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                @endif

                @if($card->gmail)
                    <a href="mailto:{{ $card->gmail }}" class="action-btn">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                @endif

                @if($card->map_location_link)
                    <a href="{{ $card->map_location_link }}" target="_blank" class="action-btn">
                        <i class="fas fa-location-dot"></i> Map
                    </a>
                @endif
            </div>

            <!-- About Us -->
            @if($card->about_us)
                <div class="detail-block">
                    <h5><i class="fas fa-user-check me-1"></i> About Us</h5>
                    <p>{{ $card->about_us }}</p>
                </div>
            @endif

            <!-- Services / Products -->
            @if($card->services_or_products)
                <div class="detail-block">
                    <h5><i class="fas fa-briefcase me-1"></i> Services / Products</h5>
                    <p>{{ $card->services_or_products }}</p>
                </div>
            @endif

            <!-- Address -->
            @if($card->address || $card->city)
                <div class="detail-block">
                    <h5><i class="fas fa-map-marker-alt me-1"></i> Address</h5>
                    <p>
                        {{ $card->address ? $card->address . ', ' : '' }}
                        {{ $card->area ? $card->area . ', ' : '' }}
                        {{ $card->city }} - {{ $card->pincode }}, {{ $card->state }}
                    </p>
                </div>
            @endif

            <!-- Payment Details & QR Code -->
            @if($card->qr_code || $card->upi_id)
                <div class="card-payment-section">
                    <h5><i class="fas fa-qrcode me-1"></i> Instant Payment</h5>
                    @if($card->qr_code)
                        <img src="{{ asset('storage/' . $card->qr_code) }}" class="qr-code-img" alt="UPI QR Code">
                    @endif
                    @if($card->upi_id)
                        <p class="upi-text">UPI: {{ $card->upi_id }}</p>
                    @endif
                </div>
            @endif

        </div>
    </div>

</body>
</html>