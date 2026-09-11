<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - Tidong Partner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-4">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-wallet text-warning me-2"></i>Payments & Fare Settlement</h3>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Booking ID</th>
                                <th>Base & Distance Fare</th>
                                <th>Tolls & Tips</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments ?? [] as $payment)
                                <tr>
                                    <td class="fw-bold">{{ $payment->booking_number }}</td>
                                    <td>₹{{ number_format(($payment->base_fare ?? 0) + ($payment->distance_fare ?? 0), 2) }}</td>
                                    <td>₹{{ number_format(($payment->toll_charges ?? 0) + ($payment->tip_amount ?? 0), 2) }}</td>
                                    <td class="fw-bold">₹{{ number_format($payment->grand_total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $payment->payment_status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ strtoupper($payment->payment_status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vendor.taxi.success', $payment->id) }}" class="btn btn-sm btn-outline-dark"><i class="fas fa-receipt me-1"></i> Fare Breakup</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No payment records found.</td>
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