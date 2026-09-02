<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Exchange Rates - Partner Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }</style>
</head>
<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold"><i class="fas fa-coins text-success me-2"></i> Forex Currency Exchange Rates</h4>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-4">
            <h5 class="fw-bold mb-3">Today Buying & Selling Rates (INR)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Currency</th>
                            <th>Code</th>
                            <th>Buy Rate (₹)</th>
                            <th>Sell Rate (₹)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-dollar-sign text-primary me-2"></i> US Dollar</td>
                            <td>USD</td>
                            <td><input type="number" class="form-control form-control-sm" value="82.50"></td>
                            <td><input type="number" class="form-control form-control-sm" value="83.80"></td>
                            <td><button class="btn btn-sm btn-success">Update</button></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-euro-sign text-warning me-2"></i> Euro</td>
                            <td>EUR</td>
                            <td><input type="number" class="form-control form-control-sm" value="89.20"></td>
                            <td><input type="number" class="form-control form-control-sm" value="90.50"></td>
                            <td><button class="btn btn-sm btn-success">Update</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>