<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Order Deductions & Fee Policy - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.7; }
        .guide-header { background: #0f172a; color: #fff; padding: 45px 0; border-bottom: 4px solid #2563eb; }
        .guide-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 35px; margin-bottom: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 30px; font-weight: 700; margin-bottom: 15px; }
        .step-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="guide-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Vendor Order Deductions & Fee Policy</h1>
            <p class="text-light mb-0 small">Aapke har order aur wallet par lagne waale sabhi charges ki poori jaankari</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="guide-content shadow-sm">

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 bg-light">
                        <small class="text-muted d-block">Portal Commission</small>
                        <h4 class="text-primary font-weight-bold my-1">5% <span class="fs-6 text-dark">(Min ₹5)</span></h4>
                        <small class="text-muted">+ 18% GST</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 bg-light">
                        <small class="text-muted d-block">UPI / PG Charge</small>
                        <h4 class="text-primary font-weight-bold my-1">2%</h4>
                        <small class="text-muted">+ 18% GST</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 bg-light">
                        <small class="text-muted d-block">COD Handling Fee</small>
                        <h4 class="text-primary font-weight-bold my-1">1.5% <span class="fs-6 text-dark">(Min ₹2)</span></h4>
                        <small class="text-muted">+ 18% GST</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center p-3 bg-light">
                        <small class="text-muted d-block">Daily SaaS Charge</small>
                        <h4 class="text-primary font-weight-bold my-1">₹1 / Day</h4>
                        <small class="text-muted">Auto-debited at 00:00</small>
                    </div>
                </div>
            </div>

            <!-- Charges Breakdown Table -->
            <h4 class="section-title"><i class="fas fa-list-check text-primary me-2"></i>Charges Breakdown & Rules</h4>
            <div class="step-box">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Charge Type</th>
                                <th>Applicable Rate</th>
                                <th>GST Rate</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Portal Commission</strong></td>
                                <td>5% (Minimum ₹5 per order)</td>
                                <td>18% GST</td>
                                <td>Platform service and ordering infrastructure fee.</td>
                            </tr>
                            <tr>
                                <td><strong>UPI / Payment Gateway Charge</strong></td>
                                <td>2% of Order Amount</td>
                                <td>18% GST</td>
                                <td>Online payment gateway processing charge (Cards, UPI, NetBanking).</td>
                            </tr>
                            <tr>
                                <td><strong>COD / Cash Handling Charge</strong></td>
                                <td>1.5% of Order Amount (Min ₹2)</td>
                                <td>18% GST</td>
                                <td>Applied when order is paid via Cash on Delivery.</td>
                            </tr>
                            <tr>
                                <td><strong>Daily SaaS Platform Fee</strong></td>
                                <td>₹1.00 / Day</td>
                                <td>Included</td>
                                <td>Nightly maintenance fee debited automatically from vendor wallet.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calculation Example -->
            <h4 class="section-title"><i class="fas fa-calculator text-primary me-2"></i>Example: ₹500 Order Calculation</h4>
            <div class="step-box">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush border rounded">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Order Total</span>
                                <strong>₹500.00</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-danger">
                                <span>Portal Commission (5%)</span>
                                <span>- ₹25.00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-danger">
                                <span>GST on Commission (18%)</span>
                                <span>- ₹4.50</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-danger">
                                <span>UPI PG Charge (2%)</span>
                                <span>- ₹10.00</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-danger">
                                <span>GST on PG Charge (18%)</span>
                                <span>- ₹1.80</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-center mt-3 mt-md-0">
                        <div class="p-4 bg-success text-white rounded-3 shadow-sm">
                            <small class="text-uppercase fw-bold d-block mb-1">Vendor Final Payout</small>
                            <h2 class="fw-bold mb-0">₹458.70</h2>
                            <span class="badge bg-danger mt-2">Total Deductions: ₹41.30</span>
                        </div>
                    </div>
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