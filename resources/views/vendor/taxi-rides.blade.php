<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxi Active Rides - Partner Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }</style>
</head>
<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold"><i class="fas fa-route text-warning me-2"></i> Active & Upcoming Rides</h4>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4 text-center">
                <i class="fas fa-taxi fa-3x text-muted mb-3"></i>
                <h5>No Active Ride Requests Right Now</h5>
                <p class="text-muted small">New passenger requests for taxi pickups will appear here in real-time.</p>
            </div>
        </div>
    </div>
</body>
</html>