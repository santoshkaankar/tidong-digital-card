<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Deductions & Fee Manual - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.7; }
        .guide-header { background: #0f172a; color: #fff; padding: 45px 0; border-bottom: 4px solid #2563eb; }
        .guide-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 35px; margin-bottom: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .step-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
        .badge-fee { background: #ef4444; color: #fff; font-size: 0.85rem; padding: 4px 8px; border-radius: 6px; }
    </style>
</head>
<body>

    <div class="guide-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Platform Deductions & Fee Manual</h1>
            <p class="text-light mb-0 small">Official Fee Transparency for Vendors & Member Stage Deduction Formula</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="guide-content shadow-sm">

            <!-- 1. Vendor Deductions -->
            <h4 class="section-title"><i class="fas fa-store text-primary me-2"></i>1. Vendor Order Deductions & Fees</h4>
            <div class="step-box">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Category</th>
                                <th>Deduction Rate</th>
                                <th>GST Applicability</th>
                                <th>Minimum Charge</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Portal Commission</strong></td>
                                <td>5% per order</td>
                                <td>+ 18% GST</td>
                                <td>₹5.00 per order</td>
                            </tr>
                            <tr>
                                <td><strong>UPI / Gateway Fee</strong></td>
                                <td>2% per order</td>
                                <td>+ 18% GST</td>
                                <td>N/A</td>
                            </tr>
                            <tr>
                                <td><strong>COD Cash Handling</strong></td>
                                <td>1.5% per order</td>
                                <td>+ 18% GST</td>
                                <td>₹2.00 per order</td>
                            </tr>
                            <tr>
                                <td><strong>Daily SaaS Platform Fee</strong></td>
                                <td>₹1.00 / day</td>
                                <td>Included</td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. Customer / Member Wallet & Formula -->
            <h4 class="section-title"><i class="fas fa-wallet text-primary me-2"></i>2. Member 3-Wallet Structure & Stage Deduction Formula</h4>
            <div class="step-box">
                <h6 class="fw-bold text-dark mb-2">A. 3-Wallet System Architecture:</h6>
                <ul class="mb-4">
                    <li><strong>1. T-Coin Wallet:</strong> Account creation par instant <strong>4,540,000.00 T-Coins</strong> (1 T-Coin = ₹1) credit hote hain.</li>
                    <li><strong>2. Non-Withdrawable Wallet (`non_withdrawable_balance`):</strong> Referral network aur stage rewards ka balance. **Daily Stage Deductions** isi wallet se cut-te hain aur negative balance me jaate hain.</li>
                    <li><strong>3. Real Wallet (`real_balance`):</strong> Withdrawable cash wallet. Orders aur referral thresholds poore hone par paisa isme transfer hota hai.</li>
                </ul>

                <h6 class="fw-bold text-dark mb-2">B. Daily Stage Deduction Formula: <code>(14 - Completed Stages) × ₹5 / Day</code></h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Completed Stage</th>
                                <th>Remaining Incomplete Stages</th>
                                <th>Formula Calculation</th>
                                <th>Daily Deduction Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Stage 0 Complete</td>
                                <td>14 Stages Remaining</td>
                                <td>14 × ₹5</td>
                                <td><span class="badge bg-danger">₹70.00 / day</span></td>
                            </tr>
                            <tr>
                                <td>Stage 6 Complete</td>
                                <td>8 Stages Remaining</td>
                                <td>8 × ₹5</td>
                                <td><span class="badge bg-warning text-dark">₹40.00 / day</span></td>
                            </tr>
                            <tr>
                                <td>Stage 14 Complete</td>
                                <td>0 Stages Remaining</td>
                                <td>0 × ₹5</td>
                                <td><span class="badge bg-success">₹0.00 / day</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-danger mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-1"></i> <strong>Negative Balance & Transfer Rule:</strong> Non-Withdrawable Wallet ka balance daily cutting se kitna bhi negative chala jaye, cutting continue rahegi. Jab customer ka naya reward Real Wallet me transfer hoga, toh **pehle Negative Balance automatic deduct hoga**, uske baad bacha hua balance hi Real Wallet me credit hoga.
                </div>
            </div>

        </div>
    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. | Official System Documentation</p>
        </div>
    </footer>

</body>
</html>