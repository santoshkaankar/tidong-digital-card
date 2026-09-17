<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Program, Stages & Royalty Structure - Tidong® International Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.7; }
        .aff-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #2563eb; }
        .aff-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 30px; font-weight: 700; }
        .rule-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; height: 100%; }
    </style>
</head>
<body>

    <div class="aff-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Affiliate Program, Stage Structure & Royalty</h1>
            <p class="text-light mb-0 small">Comprehensive Overview of All 14 Stages, Terms & Conditions, Shopping Limits, Deductions, and Royalty Income Structure</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="aff-content shadow-sm">

            <!-- Program Rules Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="rule-card text-center">
                        <i class="fas fa-gift text-success fs-1 mb-2"></i>
                        <h6 class="fw-bold text-dark">Registration Bonus</h6>
                        <p class="small text-muted mb-0">Receive an instant registration bonus of <strong>4,540,000 T-Coins</strong> (1 T-Coin = ₹1) upon sign-up.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="rule-card text-center">
                        <i class="fas fa-shopping-cart text-primary fs-1 mb-2"></i>
                        <h6 class="fw-bold text-dark">₹25,000 Shopping Limit</h6>
                        <p class="small text-muted mb-0">Stage clearance requires a minimum cumulative shopping threshold of ₹25,000 by self and team members.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="rule-card text-center">
                        <i class="fas fa-clock text-warning fs-1 mb-2"></i>
                        <h6 class="fw-bold text-dark">Daily T-Coin Deduction</h6>
                        <p class="small text-muted mb-0">Calculated as: 14 minus completed stages equals daily deduction. Faster stage completion minimizes daily deductions.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="rule-card text-center">
                        <i class="fas fa-file-invoice-dollar text-danger fs-1 mb-2"></i>
                        <h6 class="fw-bold text-dark">TDS & Admin Deductions</h6>
                        <p class="small text-muted mb-0">Incentive disbursements are subject to a 10% Administrative Charge plus 5% TDS (with PAN verification) or 20% TDS (without PAN).</p>
                    </div>
                </div>
            </div>

            <!-- Stages Table -->
            <h4 class="section-title mb-3">All 14 Affiliate Stages</h4>
            <div class="table-responsive rounded-3 border mb-5">
                <table class="table table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Stage</th>
                            <th class="text-start">Designation</th>
                            <th>Leg A Req.</th>
                            <th>Leg B Req.</th>
                            <th>Gross Incentive</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stages = DB::table('affiliate_stages')->orderBy('stage_no', 'asc')->get();
                        @endphp
                        @forelse($stages as $stg)
                        <tr>
                            <td><span class="badge bg-primary">Stage {{ $stg->stage_no }}</span></td>
                            <td class="fw-bold text-start">{{ $stg->stage_name }}</td>
                            <td>{{ number_format($stg->leg_a_count) }}</td>
                            <td>{{ number_format($stg->leg_b_count) }}</td>
                            <td class="text-success fw-bold">₹ {{ number_format($stg->incentive_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-muted py-4">No affiliate stages found in the system.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Royalty Income Section -->
            <h4 class="section-title mb-3">Royalty Program Structure</h4>
            <div class="row g-4">
                <div class="col-12">
                    <div class="rule-card bg-light border-primary p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-award text-primary fs-1 me-3"></i>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Executive Stage & Leadership Royalty Qualification</h5>
                                <p class="small text-muted mb-0">Achieve elite milestones to unlock recurring corporate revenue shares and specialized performance bonuses.</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="text-dark mb-2">Upon successful qualification and progression beyond the foundational leadership tiers, members unlock exclusive corporate royalty benefits designed to support sustained network expansion and reward top-tier leadership excellence across international operations.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-success fs-6 px-4 py-3 shadow-sm">Royalty Income: ₹200,000 Per Month*</span>
                            </div>
                        </div>
                    </div>
                </div>
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