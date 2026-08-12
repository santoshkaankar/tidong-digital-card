<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $card->name }} - Digital Card</title>
    <!-- Bootstrap 5 & FontAwesome for Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-container { max-width: 480px; margin: 30px auto; background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); position: relative; margin-bottom: 80px; }
        .header-bg { background: linear-gradient(135deg, #0d6efd, #0dcaf0); padding: 30px 20px; text-align: center; color: white; }
        
        /* Strict CSS for Perfect Round Profile Image */
        .profile-img { 
            width: 110px !important; 
            height: 110px !important; 
            min-width: 110px !important;
            min-height: 110px !important;
            max-width: 110px !important;
            max-height: 110px !important;
            border-radius: 50% !important; 
            border: 4px solid #fff !important; 
            object-fit: cover !important; 
            object-position: center !important;
            margin-bottom: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); 
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .action-btn { border-radius: 50px; font-weight: 600; padding: 10px 20px; transition: all 0.3s; }
        .action-btn:hover { transform: translateY(-2px); }
        
        /* Floating Share/Save Bar */
        .floating-actions {
            background: #ffffff;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 480px;
            margin: 0 auto;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <div class="card-container">
        <!-- Top Profile Section -->
        <div class="header-bg">
            @if($card->photo)
                <img src="{{ asset('storage/' . $card->photo) }}" alt="Profile Photo" class="profile-img">
            @else
                <img src="https://via.placeholder.com/110" alt="Default" class="profile-img">
            @endif
            
            <!-- Name -->
            <h2 class="fw-bold mb-1">{{ $card->name }}</h2>

            <!-- Business Name & Nick Name -->
            <p class="mb-1 text-white">
                <i class="fas fa-briefcase"></i> {{ $card->business_name }} 
                @if($card->owner_name) 
                    <br><small class="bg-white text-primary px-2 py-1 rounded-pill mt-1 d-inline-block fw-bold" style="font-size: 0.8rem;">
                        {{ $card->owner_name }}
                    </small> 
                @endif
            </p>

            @if($card->tagline)
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill mt-2">{{ $card->tagline }}</span>
            @endif
        </div>

        <div class="p-4">
            <!-- Quick Action Buttons -->
            <div class="row text-center g-2 mb-4">
                @if($card->phone)
                <div class="col-4">
                    <a href="tel:{{ $card->phone }}" class="btn btn-outline-primary w-100 action-btn py-2">
                        <i class="fas fa-phone-alt fa-lg d-block mb-1"></i> Call
                    </a>
                </div>
                @endif

                @if($card->whatsapp)
                <div class="col-4">
                    <a href="https://wa.me/{{ $card->whatsapp }}" target="_blank" class="btn btn-outline-success w-100 action-btn py-2">
                        <i class="fab fa-whatsapp fa-lg d-block mb-1"></i> WhatsApp
                    </a>
                </div>
                @endif

                @if($card->gmail || $card->other_email)
                <div class="col-4">
                    <a href="mailto:{{ $card->gmail ?? $card->other_email }}" class="btn btn-outline-danger w-100 action-btn py-2">
                        <i class="fas fa-envelope fa-lg d-block mb-1"></i> Email
                    </a>
                </div>
                @endif
            </div>

            <!-- About Us Section -->
            @if($card->about_us)
            <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2 fw-bold"><i class="fas fa-info-circle"></i> About Us</h5>
                <p class="text-muted small">{{ $card->about_us }}</p>
            </div>
            @endif

            <!-- Products / Services Section -->
            @if($card->services_or_products)
            <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2 fw-bold"><i class="fas fa-box-open"></i> Products & Services</h5>
                <p class="text-muted small">{{ $card->services_or_products }}</p>
            </div>
            @endif

            <!-- Social Media Links -->
            @if($card->facebook || $card->instagram || $card->youtube || $card->website_link)
            <div class="mb-4 text-center">
                <h6 class="text-secondary fw-bold mb-3">Connect With Us Online</h6>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    @if($card->facebook)<a href="{{ $card->facebook }}" target="_blank" class="btn btn-primary btn-sm rounded-circle p-2" style="width:40px;height:40px;"><i class="fab fa-facebook-f pt-1"></i></a>@endif
                    @if($card->instagram)<a href="{{ $card->instagram }}" target="_blank" class="btn btn-danger btn-sm rounded-circle p-2" style="width:40px;height:40px;"><i class="fab fa-instagram pt-1"></i></a>@endif
                    @if($card->youtube)<a href="{{ $card->youtube }}" target="_blank" class="btn btn-dark btn-sm rounded-circle p-2" style="width:40px;height:40px;"><i class="fab fa-youtube pt-1"></i></a>@endif
                    @if($card->website_link)<a href="{{ $card->website_link }}" target="_blank" class="btn btn-success btn-sm rounded-circle p-2" style="width:40px;height:40px;"><i class="fas fa-globe pt-1"></i></a>@endif
                </div>
            </div>
            @endif

            <!-- Payment QR & UPI Section -->
            @if($card->qr_code || $card->upi_id)
            <div class="card bg-light border-0 p-3 mb-3 text-center rounded-4">
                <h6 class="text-primary fw-bold mb-2"><i class="fas fa-qrcode"></i> Scan & Pay</h6>
                @if($card->qr_code)
                    <img src="{{ asset('storage/' . $card->qr_code) }}" alt="QR Code" class="img-fluid rounded mx-auto mb-2" style="max-height: 150px;">
                @endif
                @if($card->upi_id)
                    <p class="mb-0 small fw-bold text-dark">UPI ID: {{ $card->upi_id }}</p>
                @endif
            </div>
            @endif

            <!-- Address & Location -->
            @if($card->address || $card->city)
            <div class="mb-3 text-center text-muted small">
                <p class="mb-1"><i class="fas fa-map-marker-alt text-danger"></i> 
                    {{ $card->address }}@if($card->address), @endif {{ $card->city }} @if($card->pincode)- {{ $card->pincode }}@endif @if($card->state)({{ $card->state }})@endif
                </p>
                @if($card->map_location_link)
                    <a href="{{ $card->map_location_link }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill mt-1">Open in Google Maps</a>
                @endif
            </div>
            @endif

        </div>

        <div class="bg-dark text-white text-center py-2 small">
            <span>Powered by Tidong Digital Card 🚀</span>
        </div>
    </div>

    <!-- Sticky Share & Save Action Bar -->
    <div class="floating-actions p-3 text-center">
        <div class="row g-2">
            <div class="col-6">
                <button onclick="shareCard()" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">
                    <i class="fas fa-share-alt me-1"></i> Share Card
                </button>
            </div>
            <div class="col-6">
                <button onclick="saveContact()" class="btn btn-success w-100 rounded-pill py-2 fw-bold">
                    <i class="fas fa-user-plus me-1"></i> Save Contact
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript for Share and vCard Download -->
    <script>
        function shareCard() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $card->business_name }}',
                    text: 'Check out the digital visiting card of {{ $card->name }} ({{ $card->business_name }})',
                    url: window.location.href,
                }).catch((error) => console.log('Sharing failed', error));
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Card link copied to clipboard!');
            }
        }

        function saveContact() {
            let vCardData = "BEGIN:VCARD\n" +
                            "VERSION:3.0\n" +
                            "FN:{{ $card->name }}\n" +
                            "ORG:{{ $card->business_name }}\n" +
                            "TEL;TYPE=WORK,VOICE:{{ $card->phone }}\n" +
                            "EMAIL:{{ $card->gmail ?? $card->other_email }}\n" +
                            "URL:{{ $card->website_link }}\n" +
                            "ADR:;;{{ $card->address }};{{ $card->city }};{{ $card->state }};{{ $card->pincode }}\n" +
                            "END:VCARD";

            let blob = new Blob([vCardData], { type: "text/vcard;charset=utf-8" });
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = url;
            a.download = "{{ Str::slug($card->name) }}.vcf";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
</body>
</html>