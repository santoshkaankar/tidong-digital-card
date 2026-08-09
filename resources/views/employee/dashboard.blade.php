<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Tidong®</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background: #1e293b; color: #fff; padding: 20px; }
        .sidebar a { color: #94a3b8; text-decoration: none; display: block; padding: 10px 15px; border-radius: 8px; margin-bottom: 5px; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background: #2563eb; }
        .main-content { margin-left: 260px; padding: 30px; }
        .card-box { background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); margin-bottom: 20px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="fw-bold mb-4"><i class="fas fa-user-tie me-2 text-primary"></i> Employee Panel</h4>
        <ul class="list-unstyled">
            <li><a href="{{ route('employee.dashboard') }}" class="active"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-tasks me-2"></i> Assigned Tasks</a></li>
            <li><a href="#"><i class="fas fa-clipboard-list me-2"></i> Order Management</a></li>
            <li class="mt-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark">Employee Dashboard 👋</h3>
            <span class="badge bg-success">Active Staff</span>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card-box text-center">
                    <h6 class="text-muted text-uppercase small">Pending Tasks</h6>
                    <h3 class="fw-bold text-warning mt-2">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-box text-center">
                    <h6 class="text-muted text-uppercase small">Completed Orders</h6>
                    <h3 class="fw-bold text-success mt-2">0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-box text-center">
                    <h6 class="text-muted text-uppercase small">Today's Attendance</h6>
                    <h3 class="fw-bold text-primary mt-2">Present</h3>
                </div>
            </div>
        </div>

        <div class="card-box">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Welcome, {{ Auth::user()->name }}</h5>
            <p class="text-muted">Aap yahan se apne daily assigned tasks aur customer orders track kar sakte hain. System puri tarah se active hai.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>