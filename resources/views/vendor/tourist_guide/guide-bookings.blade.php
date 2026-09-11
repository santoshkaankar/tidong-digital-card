<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourist Guide Bookings - Partner Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }</style>
</head>
<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold"><i class="fas fa-flag text-danger me-2"></i> Tour Guide Bookings</h4>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
            <h5>No Tour Bookings Scheduled</h5>
            <p class="text-muted small">Tourist booking requests and assigned itineraries will appear here.</p>
        </div>
    </div>
</body>
</html>