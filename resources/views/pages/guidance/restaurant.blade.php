<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant & Merchant Guidance - Tidong® Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.7; }
        .guide-header { background: #0f172a; color: #fff; padding: 50px 0; border-bottom: 4px solid #2563eb; }
        .guide-content { background: #ffffff; border-radius: 16px; border: 1px solid #cbd5e1; padding: 40px; }
        .section-title { color: #0f172a; border-left: 4px solid #2563eb; padding-left: 12px; margin-top: 20px; font-weight: 700; }
    </style>
</head>
<body>

    <div class="guide-header">
        <div class="container">
            <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm rounded-pill mb-3"><i class="fas fa-arrow-left me-1"></i> Return to Main Platform</a>
            <h1 class="fw-bold mb-2">Restaurant & Merchant Guidance</h1>
            <p class="text-light mb-0 small">Restaurant partners aur commercial merchants ke liye setup aur rules guide</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="guide-content shadow-sm">
            <h4 class="section-title mb-3">Restaurant Partner Rules & Setup:</h4>
            <ul class="lh-lg text-dark fs-6">
                <li>Apne Digital Menu aur Offers portal par list karein.</li>
                <li>Customer T-Coins ke zariye special discounts redemption offer kar sakte hain.</li>
                <li>Real-time order notification aur table status management System Dashboard se handle karein.</li>
                <li>Menu items aur seasonal pricing ko kisi bhi waqt instantly update kar sakte hain.</li>
            </ul>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 text-center">
        <div class="container">
            <p class="small mb-0 text-muted">&copy; {{ date('Y') }} Tidong Marketing Pvt. Ltd. | Registered Office: Agra, UP, India.</p>
        </div>
    </footer>

</body>
</html>