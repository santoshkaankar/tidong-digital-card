<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Tidong® Global Corporate Offices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; }
        .office-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 25px; transition: 0.3s; }
        .office-card:hover { border-color: #2563eb; transform: translateY(-4px); }
    </style>
</head>
<body>

    <div class="bg-dark text-white py-5">
        <div class="container text-center">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold">Contact Global Support & Corporate HQ</h1>
            <p class="text-light small">Connecting Merchants, Fleet Partners, and Global Visitors</p>
        </div>
    </div>

    <div class="container py-5">
        
        <!-- Corporate Locations Grid -->
        <h3 class="fw-bold text-dark text-center mb-4">Our Key Operational Hubs</h3>
        <div class="row g-4 mb-5">
            
            <!-- Registered Head Office -->
            <div class="col-md-4">
                <div class="office-card h-100 shadow-sm">
                    <span class="badge bg-primary mb-3">Registered Head Office</span>
                    <h5 class="fw-bold text-dark"><i class="fas fa-landmark text-primary me-2"></i>Agra HQ</h5>
                    <p class="text-muted small mb-2"><strong>Entity:</strong> Tidong Marketing Pvt. Ltd.</p>
                    <p class="text-muted small mb-0">Agra, Uttar Pradesh, India</p>
                    <hr>
                    <span class="badge bg-danger bg-opacity-10 text-danger small">Primary Legal Jurisdiction</span>
                </div>
            </div>

            <!-- Corporate Office -->
            <div class="col-md-4">
                <div class="office-card h-100 shadow-sm">
                    <span class="badge bg-dark mb-3">Corporate Operations</span>
                    <h5 class="fw-bold text-dark"><i class="fas fa-building text-dark me-2"></i>Delhi Corporate Office</h5>
                    <p class="text-muted small mb-2">International Business Expansion & Strategic Partnerships</p>
                    <p class="text-muted small mb-0">New Delhi, NCR, India</p>
                </div>
            </div>

            <!-- Regional Hub -->
            <div class="col-md-4">
                <div class="office-card h-100 shadow-sm">
                    <span class="badge bg-info mb-3">Regional Operations</span>
                    <h5 class="fw-bold text-dark"><i class="fas fa-map-marker-alt text-info me-2"></i>Kota Office</h5>
                    <p class="text-muted small mb-2">Regional Onboarding & Technical Operations</p>
                    <p class="text-muted small mb-0">Kota, Rajasthan, India</p>
                </div>
            </div>

        </div>

        <!-- Direct Contact Card -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm text-center">
                    <h4 class="fw-bold text-dark mb-4"><i class="fas fa-headset text-primary me-2"></i>Direct Merchant & User Support</h4>
                    <p class="mb-3"><i class="fas fa-envelope text-danger me-2"></i> <strong>Email:</strong> santoshkaankar@gmail.com</p>
                    <p class="mb-4"><i class="fas fa-phone text-success me-2"></i> <strong>Support Hotline:</strong> +91 96347 59912</p>
                    <a href="https://wa.me/919634759912" target="_blank" class="btn btn-success btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fab fa-whatsapp me-2"></i> Connect on Official WhatsApp
                    </a>
                </div>
            </div>
        </div>

    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>