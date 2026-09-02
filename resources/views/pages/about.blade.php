<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Tidong® Global Digital Ecosystem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; }
        .hero-banner { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; padding: 70px 0; }
        .feature-box { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 25px; transition: 0.3s; }
        .feature-box:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>

    <div class="hero-banner">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-4"><i class="fas fa-arrow-left me-1"></i> Back to Platform</a>
            <span class="badge bg-primary px-3 py-2 rounded-pill d-block w-fit mb-3">Global Business Engine</span>
            <h1 class="display-4 fw-bold mb-3">Empowering Global Digital Commerce & Mobility</h1>
            <p class="lead text-light max-w-800">Tidong® is a Next-Generation Multi-Industry Marketplace & Digital Identity Platform bridging tourists, consumers, vendors, taxi fleets, forex dealers, and service providers worldwide.</p>
        </div>
    </div>

    <div class="container py-5">
        
        <!-- Corporate Profile -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm h-100">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-globe text-primary me-2"></i> Our Vision & Architecture</h3>
                    <p><strong>Tidong®</strong> (Registered Trademark) is an advanced international digital infrastructure operated under <strong>Tidong Marketing Pvt. Ltd.</strong> Built to replace fragmented local apps, Tidong® serves as a unified cross-border ecosystem enabling seamless instant connections, dynamic multilingual catalogs, real-time transportation bookings, currency rate exchanges, and licensed tourist guide engagements.</p>
                    <p>Designed with zero-friction user engagement, the platform allows global travelers and local consumers to access dynamic digital catalogs, place real-time orders, and initiate multi-currency service interactions in their preferred native language without requiring compulsory mobile application installations.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
                    <h4 class="fw-bold text-dark mb-3"><i class="fas fa-building text-primary me-2"></i> Global Operations</h4>
                    <hr>
                    <div class="mb-3">
                        <h6 class="fw-bold mb-1 text-primary"><i class="fas fa-landmark me-2"></i> Registered Head Office</h6>
                        <p class="small text-muted mb-0">Tidong Marketing Pvt. Ltd.<br>Agra, Uttar Pradesh, India</p>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold mb-1 text-primary"><i class="fas fa-city me-2"></i> Corporate Office</h6>
                        <p class="small text-muted mb-0">New Delhi, NCR, India</p>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-primary"><i class="fas fa-map-marker-alt me-2"></i> Regional Operational Hub</h6>
                        <p class="small text-muted mb-0">Kota, Rajasthan, India</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ecosystem Grid -->
        <h3 class="fw-bold text-dark text-center mb-4">Integrated Platform Modules</h3>
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="feature-box h-100">
                    <i class="fas fa-id-card fa-2x text-primary mb-3"></i>
                    <h5 class="fw-bold">Dynamic Digital Identity</h5>
                    <p class="small text-muted">Interactive multi-language digital profile cards with instant WhatsApp integration, live route maps, and tap-to-call actions.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box h-100">
                    <i class="fas fa-utensils fa-2x text-danger mb-3"></i>
                    <h5 class="fw-bold">Smart Retail & Dining</h5>
                    <p class="small text-muted">Real-time QR menu and catalog management with dynamic multi-currency display, inventory switches, and automated ordering.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box h-100">
                    <i class="fas fa-taxi fa-2x text-warning mb-3"></i>
                    <h5 class="fw-bold">International Taxi & Fleet</h5>
                    <p class="small text-muted">Direct passenger-to-driver dispatch system with real-time status switches and transparent regional trip routing.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box h-100">
                    <i class="fas fa-coins fa-2x text-success mb-3"></i>
                    <h5 class="fw-bold">Forex & Money Exchange</h5>
                    <p class="small text-muted">Live currency buying and selling rates publication portal connecting licensed exchangers with global travelers.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box h-100">
                    <i class="fas fa-map-marked-alt fa-2x text-info mb-3"></i>
                    <h5 class="fw-bold">Tourist Guide Management</h5>
                    <p class="small text-muted">Verified multilingual tour guide scheduling and booking marketplace for heritage and commercial tourism destinations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box h-100">
                    <i class="fas fa-qrcode fa-2x text-dark mb-3"></i>
                    <h5 class="fw-bold">Universal Super-QR</h5>
                    <p class="small text-muted">A single cross-platform fingerprint-enabled QR technology routing visitors instantly to localized regional services.</p>
                </div>
            </div>
        </div>

    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. Tidong® is a Registered Trademark. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>