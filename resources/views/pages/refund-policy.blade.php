<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Policy - Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.7; }
        .policy-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #ef4444; }
        .policy-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #ef4444; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .info-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; color: #1e40af; }
    </style>
    @include('partials.welcome.ai-seo-head')
</head>
<body>

    <div class="policy-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Refund Policy</h1>
            <p class="text-light mb-0 small">Operational Guidelines: Tidong Marketing Pvt. Ltd. (Head Office: Agra, UP, India)</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="policy-card shadow-sm">

            <div class="info-box mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clock fs-2 me-3"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Processing Window: 7 to 14 Business Days</h5>
                        <p class="mb-0 small">
                            All approved refunds are credited back to the original source payment account within <strong>7 to 14 business days</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <h4 class="section-title">1. Refund Eligibility & Rules</h4>
            <p>
                Refunds are processed by <strong>Tidong Marketing Pvt. Ltd.</strong> under the following conditions:
            </p>
            <ul class="lh-lg">
                <li>Approved cancellation request (subject to a standard 5% cancellation charge deduction for user-initiated cancellations).</li>
                <li>Full refund for merchant-initiated cancellations or failed transaction attempts.</li>
                <li>Verified return claims for missing, wrong, or damaged item deliveries.</li>
            </ul>

            <h4 class="section-title">2. Mode of Refund</h4>
            <p>
                Refunds will be credited directly back to the original payment method used during checkout (UPI, Net Banking, Credit/Debit Card, or Digital Wallet). No cash refunds will be provided.
            </p>

            <h4 class="section-title">3. Tracking & Assistance</h4>
            <p>If you haven't received your refund after 14 business days, please contact customer support with your Order ID:</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <i class="fas fa-envelope text-danger me-2"></i> <strong>Support Email:</strong><br>
                        <a href="mailto:santoshkaankar@gmail.com" class="text-decoration-none">santoshkaankar@gmail.com</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <i class="fas fa-phone text-success me-2"></i> <strong>Helpline / WhatsApp:</strong><br>
                        <a href="https://wa.me/919634759912" target="_blank" class="text-decoration-none">+91 96347 59912</a>
                    </div>
                </div>
            </div>

            <h4 class="section-title">4. Governing Jurisdiction</h4>
            <p class="small text-muted mb-0">
                All refund claims are governed under Indian laws and subject to exclusive legal jurisdiction at <strong>Agra, Uttar Pradesh, India</strong>.
            </p>

        </div>
    </div>

    @include('partials.welcome.footer')
</body>
</html>