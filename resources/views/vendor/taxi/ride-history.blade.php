<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ride History - Tidong Partner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-history text-primary me-2"></i>Completed & Cancelled Rides</h3>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Booking ID</th>
                                <th>Pickup Location</th>
                                <th>Date & Time</th>
                                <th>Total Fare</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rides ?? [] as $ride)
                                <tr>
                                    <td class="fw-bold">{{ $ride->booking_number }}</td>
                                    <td>{{ $ride->pickup_location }}</td>
                                    <td>{{ $ride->pickup_datetime }}</td>
                                    <td>₹{{ number_format($ride->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $ride->booking_status == 'completed' ? 'bg-success' : 'bg-danger' }}">
                                            {{ strtoupper($ride->booking_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vendor.taxi.success', $ride->id) }}" class="btn btn-sm btn-light border">Invoice</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No ride history records available.</td>
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