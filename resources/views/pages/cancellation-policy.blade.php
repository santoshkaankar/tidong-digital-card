<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellation Policy - Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.7; }
        .policy-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #ef4444; }
        .policy-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #ef4444; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .highlight-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px; color: #991b1b; }
    </style>
    @include('partials.welcome.ai-seo-head')
</head>
<body>

    <div class="policy-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Cancellation Policy</h1>
            <p class="text-light mb-0 small">Operational Guidelines: Tidong Marketing Pvt. Ltd. (Head Office: Agra, UP, India)</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="policy-card shadow-sm">

            <div class="highlight-box mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-ban fs-2 me-3"></i>
                    <div>
                        <h5 class="fw-bold mb-1">Standard Cancellation Charge</h5>
                        <p class="mb-0 small">
                            A mandatory <strong>5% Cancellation Charge</strong> applies on user-initiated cancellations to cover administrative and processing fees.
                        </p>
                    </div>
                </div>
            </div>

            <h4 class="section-title">1. Order Cancellation Terms</h4>
            <p>
                This Cancellation Policy applies to all orders and service requests placed on the <strong>Tidong®</strong> platform operated by <strong>Tidong Marketing Pvt. Ltd.</strong>
            </p>

            <h4 class="section-title">2. User-Initiated Cancellations</h4>
            <ul class="lh-lg">
                <li>
                    <strong>Standard Deduction:</strong> If a user cancels an order after placement, a flat <strong>5% cancellation charge</strong> of the total order value will be deducted before issuing a refund.
                </li>
                <li>
                    <strong>Partner/Merchant Specific Rules:</strong> Additional charges may apply if food preparation has already begun or if a courier/ride partner has been dispatched prior to cancellation.
                </li>
            </ul>

            <h4 class="section-title">3. Merchant or System Cancellations</h4>
            <p>
                If an order is canceled by the restaurant, vendor, or platform due to stock unavailability or operational issues, a <strong>100% full refund</strong> will be initiated without any 5% deduction.
            </p>

            <h4 class="section-title">4. Assistance & Support</h4>
            <p>For immediate cancellation help, reach out to our team:</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <i class="fas fa-envelope text-danger me-2"></i> <strong>Email:</strong> <a href="mailto:santoshkaankar@gmail.com" class="text-decoration-none">santoshkaankar@gmail.com</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 border rounded-3 bg-light">
                        <i class="fas fa-phone text-success me-2"></i> <strong>WhatsApp / Helpline:</strong> <a href="https://wa.me/919634759912" target="_blank" class="text-decoration-none">+91 96347 59912</a>
                    </div>
                </div>
            </div>

            <h4 class="section-title">5. Governing Jurisdiction</h4>
            <p class="small text-muted mb-0">
                All disputes related to cancellations are subject to the exclusive jurisdiction of courts in <strong>Agra, Uttar Pradesh, India</strong>.
            </p>

        </div>
    </div>

    @include('partials.welcome.footer')
</body>
</html>