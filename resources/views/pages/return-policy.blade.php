<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Policy - Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.7; }
        .policy-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #ef4444; }
        .policy-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #ef4444; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .warning-box { background: #fffbebfb; border: 1px solid #fde68a; border-radius: 12px; padding: 20px; color: #92400e; }
    </style>
    @include('partials.welcome.ai-seo-head')
</head>
<body>

    <div class="policy-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Return Policy</h1>
            <p class="text-light mb-0 small">Operational Guidelines: Tidong Marketing Pvt. Ltd. (Head Office: Agra, UP, India)</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="policy-card shadow-sm">

            <div class="warning-box mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-box-open fs-2 me-3"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Perishable Goods & Prepared Food Policy</h5>
                        <p class="mb-0 small">
                            Due to hygiene and safety reasons, food items and perishable goods delivered via Tidong® are non-returnable once delivered.
                        </p>
                    </div>
                </div>
            </div>

            <h4 class="section-title">1. Return Eligibility</h4>
            <p>
                Returns are only applicable under specific conditions verified at the time of delivery on the <strong>Tidong®</strong> platform:
            </p>
            <ul class="lh-lg">
                <li><strong>Wrong Item Delivered:</strong> Item received does not match the invoice/order confirmation.</li>
                <li><strong>Damaged Packaging or Quality Issues:</strong> Tampered seal or damaged items reported immediately upon delivery with photographic proof.</li>
                <li><strong>Missing Goods:</strong> Partial order items missing from the delivered parcel.</li>
            </ul>

            <h4 class="section-title">2. Non-Returnable Items & Services</h4>
            <ul class="lh-lg">
                <li>Prepared food items, fresh produce, and perishable goods once delivered.</li>
                <li>Completed digital services, taxi rides, or instant bookings.</li>
            </ul>

            <h4 class="section-title">3. How to Report a Return Request</h4>
            <p>If you receive a defective or wrong item, notify support within <strong>2 hours</strong> of delivery:</p>
            <div class="p-3 border rounded-3 bg-light">
                <p class="mb-1"><i class="fas fa-envelope text-danger me-2"></i> <strong>Email:</strong> <a href="mailto:santoshkaankar@gmail.com">santoshkaankar@gmail.com</a></p>
                <p class="mb-0"><i class="fas fa-phone text-success me-2"></i> <strong>WhatsApp Support:</strong> <a href="https://wa.me/919634759912" target="_blank">+91 96347 59912</a></p>
            </div>

            <h4 class="section-title">4. Governing Jurisdiction</h4>
            <p class="small text-muted mb-0">
                All return claims are governed by the laws of India and subject to courts in <strong>Agra, Uttar Pradesh, India</strong>.
            </p>

        </div>
    </div>

    @include('partials.welcome.footer')
</body>
</html>