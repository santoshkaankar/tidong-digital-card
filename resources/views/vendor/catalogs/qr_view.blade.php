<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $catalog->address }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .qr-card { background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 400px; width: 100%; padding: 30px; text-align: center; }
        .qr-frame { border: 2px dashed #0d6efd; border-radius: 16px; padding: 15px; display: inline-block; background: #fff; }
    </style>
</head>
<body>

<div class="qr-card mx-3">
    <!-- Top Vendor Name -->
    <h3 class="fw-bold text-dark mb-1">{{ $vendor->name ?? $vendor->business_name ?? 'Vendor' }}</h3>
    
    <!-- Table / Room Address -->
    <div class="mb-3">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6 rounded-pill">
            <i class="bi bi-geo-alt-fill me-1"></i> {{ $catalog->address }}
        </span>
    </div>

    <!-- Center QR Code -->
    <div class="qr-frame my-2">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode(url('/c/' . $catalog->slug)) }}" 
             alt="QR Code" 
             class="img-fluid" 
             style="max-width: 230px; height: auto;">
    </div>
    <p class="text-muted small mt-2 mb-4">Scan to view Digital Menu & Order</p>

    <!-- Share Options -->
    <div class="d-grid gap-2">
        <a href="https://api.whatsapp.com/send?text={{ urlencode('Scan or click to view menu for ' . ($catalog->address) . ': ' . url('/c/' . $catalog->slug)) }}" 
           target="_blank" 
           class="btn btn-success fw-semibold py-2">
            <i class="bi bi-whatsapp me-2"></i> Share via WhatsApp
        </a>

        <div class="row g-2">
            <div class="col-6">
                <button onclick="navigator.clipboard.writeText('{{ url('/c/' . $catalog->slug) }}'); alert('Link copied to clipboard!');" 
                        class="btn btn-outline-secondary w-100 py-2">
                    <i class="bi bi-link-45deg me-1"></i> Copy Link
                </button>
            </div>
            <div class="col-6">
                <button onclick="window.print()" class="btn btn-outline-primary w-100 py-2">
                    <i class="bi bi-printer me-1"></i> Print QR
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>