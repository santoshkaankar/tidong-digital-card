<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royalty Program & Leadership Rewards - Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.7; }
        .royalty-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #2563eb; }
        .royalty-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 40px; margin-bottom: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .benefit-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; height: 100%; transition: 0.3s; }
        .benefit-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    </style>
    @include('partials.ai-seo-head')
</head>
<body>

    <div class="royalty-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Royalty Program & Leadership Rewards</h1>
            <p class="text-light mb-0 small">Executive milestones, corporate revenue shares, and performance incentives for top-tier leaders on the Tidong® Platform.</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="royalty-content shadow-sm">

            <!-- Executive Summary Card -->
            <div class="row align-items-center mb-5 bg-light p-4 rounded-4 border">
                <div class="col-lg-8">
                    <span class="badge bg-primary mb-2">Elite Leadership Qualification</span>
                    <h3 class="fw-bold text-dark mb-3">Unlock Recurring Corporate Royalty Benefits</h3>
                    <p class="text-muted mb-0">Upon successful qualification and progression beyond foundational affiliate and leadership tiers, members unlock exclusive corporate royalty benefits designed to support sustained network expansion and reward top-tier leadership excellence across international operations.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <div class="p-3 bg-white rounded-3 border shadow-sm d-inline-block text-center">
                        <span class="d-block small text-muted text-uppercase fw-bold">Estimated Royalty Income</span>
                        <span class="text-success fs-4 fw-bold">₹200,000 / Month*</span>
                    </div>
                </div>
            </div>

            <!-- Program Features Grid -->
            <h4 class="section-title">Royalty Structure & Milestone Criteria</h4>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="benefit-card text-center">
                        <i class="fas fa-award text-primary fa-2x mb-3"></i>
                        <h5 class="fw-bold text-dark">Executive Milestone</h5>
                        <p class="small text-muted mb-0">Achieve advanced structural milestones across your primary network legs to qualify for tier-based corporate profit sharing.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card text-center">
                        <i class="fas fa-chart-line text-success fa-2x mb-3"></i>
                        <h5 class="fw-bold text-dark">Recurring Revenue Share</h5>
                        <p class="small text-muted mb-0">Enjoy monthly recurring payouts drawn from platform transactional growth and global business expansion initiatives.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card text-center">
                        <i class="fas fa-shield-alt text-warning fa-2x mb-3"></i>
                        <h5 class="fw-bold text-dark">Transparent Compliance</h5>
                        <p class="small text-muted mb-0">All royalty disbursements are processed securely with standard administrative and tax deductions in accordance with guidelines.</p>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions Note -->
            <h4 class="section-title">Program Terms & Eligibility</h4>
            <div class="p-4 bg-white border rounded-3">
                <ul class="lh-lg text-dark mb-0">
                    <li>Royalty qualification requires active participation, maintenance of active shopping/transaction thresholds, and team leadership credentials.</li>
                    <li>Payout frequencies and milestone calculations are managed automatically through your dashboard account metrics.</li>
                    <li>For specific tier progression details and customized queries, reach out to official support via your member panel or WhatsApp helpline.</li>
                </ul>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. | Registered Office: Agra, UP, India.</p>
        </div>
    </footer>

</body>
</html>