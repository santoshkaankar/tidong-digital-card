<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Privacy Policy - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.7; }
        .privacy-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #10b981; }
        .privacy-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 40px; }
    </style>
</head>
<body>

    <div class="privacy-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Global Privacy Policy & Data Security</h1>
            <p class="text-light mb-0 small">Compliance Framework: Tidong Marketing Pvt. Ltd. (Head Office: Agra, UP, India)</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="privacy-card shadow-sm">
            <h4 class="fw-bold text-dark mb-3">1. Data Privacy Commitment</h4>
            <p><strong>Tidong Marketing Pvt. Ltd.</strong> ("Tidong®") is committed to protecting the privacy and personal data of global travelers, users, retail customers, and business vendors accessing our international multi-service platform.</p>

            <h4 class="fw-bold text-dark mb-3 mt-4">2. Information Collection & Usage</h4>
            <p>To deliver seamless zero-download web services, dynamic ordering, and direct vendor dispatching, Tidong® collects:</p>
            <ul>
                <li><strong>Identity Details:</strong> User name, contact numbers, and email addresses provided during registration or guest ordering.</li>
                <li><strong>Technical Fingerprinting:</strong> Device fingerprint hashes, browser details, IP address logs, and localized preferences to manage temporary scan sessions without forcing invasive app installations.</li>
                <li><strong>Business Records:</strong> Vendor store listings, inventory items, taxi duty statuses, and currency rate updates.</li>
            </ul>

            <h4 class="fw-bold text-dark mb-3 mt-4">3. Data Sharing & Third-Party Dispatch</h4>
            <p>Tidong® does not sell or lease user data to marketing brokers. Necessary booking data (e.g., customer location or order details) is shared strictly with the selected independent vendor (restaurant, driver, forex dealer, or guide) to fulfill requested transactions.</p>

            <h4 class="fw-bold text-dark mb-3 mt-4">4. Legal Jurisdiction</h4>
            <p>All privacy disputes or data protection claims shall be governed exclusively under Indian Data Protection laws and fall under the legal jurisdiction of the Courts in <strong>Agra, Uttar Pradesh, India</strong>.</p>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>