<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow text-center p-4">
                <h2 class="text-success fw-bold">Booking Confirmed!</h2>
                <p class="text-muted">Booking No: <strong>{{ $booking->booking_number }}</strong></p>
                <hr>

                <div class="text-start mb-3">
                    <p><strong>Pickup:</strong> {{ $booking->pickup_location }}</p>
                    <p><strong>Date/Time:</strong> {{ $booking->pickup_datetime }}</p>
                    <p><strong>Vehicle:</strong> {{ $booking->taxi->vehicle_name }} ({{ $booking->taxi->vehicle_number }})</p>
                </div>

                <div class="card bg-light text-start p-3 mb-3">
                    <h6 class="fw-bold border-bottom pb-2">Sightseeing Route</h6>
                    <ol class="mb-0 ps-3">
                        @foreach($booking->stops as $stop)
                            <li>{{ $stop->spot->name }}</li>
                        @endforeach
                    </ol>
                </div>

                <!-- Fare Breakdown -->
                <div class="table-responsive">
                    <table class="table table-sm text-start">
                        <tr><td>Base Fare:</td><td class="text-end">₹{{ number_format($booking->base_fare, 2) }}</td></tr>
                        <tr><td>Distance Fare:</td><td class="text-end">₹{{ number_format($booking->distance_fare, 2) }}</td></tr>
                        <tr><td>Toll Charges:</td><td class="text-end">₹{{ number_format($booking->toll_charges, 2) }}</td></tr>
                        <tr><td>Tip Amount:</td><td class="text-end">₹{{ number_format($booking->tip_amount, 2) }}</td></tr>
                        <tr class="fw-bold table-active"><td>Total Fare:</td><td class="text-end">₹{{ number_format($booking->grand_total, 2) }}</td></tr>
                    </table>
                </div>

                <!-- Add Tip Form -->
                <form action="{{ route('vendor.taxi.updateCharges', $booking->id) }}" method="POST" class="mt-3">
                    @csrf
                    <label class="form-label fw-bold">Add Tip / Driver Appreciation (Optional)</label>
                    <div class="input-group">
                        <input type="number" name="tip_amount" class="form-control" placeholder="Amount in ₹" value="{{ $booking->tip_amount > 0 ? $booking->tip_amount : '' }}">
                        <button type="submit" class="btn btn-outline-success">Update Tip</button>
                    </div>
                </form>

                <a href="{{ route('taxi.index') }}" class="btn btn-primary mt-4 w-100">Back to Home</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>