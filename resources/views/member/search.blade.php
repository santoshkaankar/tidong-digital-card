<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Search - Tidong® Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        #sidebar { min-width: 260px; max-width: 260px; background: #0f172a; color: #fff; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; }
        #sidebar .sidebar-header { padding: 20px; background: #1e293b; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; color: #38bdf8; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; transition: all 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active a { color: #fff; background: #1e293b; border-left: 4px solid #38bdf8; }
        #content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; }
        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>
    @include('member.sidebar')

    <div id="content">
        <nav class="navbar navbar-light bg-white px-4 py-3 shadow-sm">
            <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left me-1"></i> Dashboard</a>
            <span class="fw-bold text-dark">Advanced Search Studio</span>
        </nav>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h3 class="fw-bold text-dark mb-3"><i class="fas fa-search text-primary me-2"></i> Search Vendors & Services</h3>
                        <p class="text-muted">Find local businesses, services, and digital cards instantly.</p>
                        
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" placeholder="Search by name, area, or service...">
                            <button class="btn btn-primary px-4" type="button">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>