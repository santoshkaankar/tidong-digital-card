<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Orders - Tidong Partner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-list-alt text-success me-2"></i>All Taxi & Sightseeing Bookings</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('taxi.index') }}" class="btn btn-warning btn-sm fw-bold"><i class="fas fa-plus me-1"></i> New Booking</a>
                <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Booking ID</th>
                                <th>Pickup Location</th>
                                <th>Pickup Date/Time</th>
                                <th>Grand Total</th>
                                <th>Booking Status</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings ?? [] as $booking)
                                <tr>
                                    <td class="fw-bold">{{ $booking->booking_number }}</td>
                                    <td>{{ $booking->pickup_location }}</td>
                                    <td>{{ $booking->pickup_datetime }}</td>
                                    <td>₹{{ number_format($booking->grand_total, 2) }}</td>
                                    <td><span class="badge bg-info text-dark">{{ strtoupper($booking->booking_status) }}</span></td>
                                    <td><span class="badge bg-secondary">{{ strtoupper($booking->payment_status ?? 'pending') }}</span></td>
                                    <td>
                                        <a href="{{ route('vendor.taxi.success', $booking->id) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye me-1"></i> Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No booking orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>