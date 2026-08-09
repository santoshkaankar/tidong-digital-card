<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Digital Menu QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('business.dashboard') }}"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </nav>

    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 text-center shadow-sm border-0 rounded-4 bg-white">
                    <h3 class="fw-bold mb-3"><i class="fas fa-qrcode text-success me-2"></i> Your Digital Menu QR Code</h3>
                    <p class="text-muted small">Scan this QR code using a phone camera to view your live digital menu and place orders.</p>
                    
                    <div class="my-3 p-3 bg-light border rounded-3 d-inline-block shadow-sm">
                        {!! $qrcode !!}
                    </div>

                    <p class="mt-3 text-break small"><strong>Menu Link:</strong> <a href="{{ $menuUrl }}" target="_blank">{{ $menuUrl }}</a></p>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ $menuUrl }}" target="_blank" class="btn btn-primary"><i class="fas fa-external-link-alt me-1"></i> Preview Menu</a>
                        <button onclick="window.print();" class="btn btn-outline-dark"><i class="fas fa-print me-1"></i> Print / Save QR Code</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>