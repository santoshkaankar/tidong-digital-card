<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dining Tables & QR Codes — Restaurant Hub</title>
    
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --bg-canvas: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            overflow-x: hidden;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-viewport {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease-in-out;
        }

        .table-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
            border-color: var(--primary-color);
        }

        .table-icon-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.5rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Restaurant Sidebar Partial -->
    @include('vendor.restaurant.partials.sidebar')

    <div class="main-viewport p-4 p-lg-5">
        
        <!-- Header Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                    FLOOR & TABLES
                </span>
                <h2 class="fw-bold mt-2 mb-0">Dining Tables & QR Codes</h2>
                <p class="text-muted small mb-0">Manage floor tables, seating capacity, and generate dynamic QR codes.</p>
            </div>
            <div>
                <button class="btn btn-primary px-4 py-2.5 rounded-3 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addTableModal">
                    <i class="bi bi-plus-lg"></i> Add New Table
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Table Grid Cards -->
        <div class="row g-4">
            @forelse($tables as $table)
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="content-card table-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="table-icon-avatar">
                                <i class="bi bi-grid-3x3-gap-fill"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">T-{{ $table->table_number }}</h4>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-people me-1"></i> {{ $table->seating_capacity }} Persons Capacity
                            </p>
                            
                            @if($table->status == 'available')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                                    Available
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                                    {{ ucfirst($table->status) }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-outline-dark btn-sm w-100 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-qr-code"></i> View QR Token
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="content-card p-5">
                        <i class="bi bi-grid-3x3-gap display-4 text-muted opacity-25 d-block mb-3"></i>
                        <h5 class="fw-bold text-dark mb-1">No Tables Created Yet</h5>
                        <p class="text-muted small mb-3">Start by adding your restaurant dining tables to generate QR codes.</p>
                        <button class="btn btn-primary btn-sm px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addTableModal">
                            <i class="bi bi-plus-lg me-1"></i> Add First Table
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('vendor.restaurant.tables.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Add Dining Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Table Number / Name</label>
                        <input type="text" name="table_number" class="form-control rounded-3 p-2.5" required placeholder="e.g. 01, T-02, VIP-1">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Seating Capacity</label>
                        <input type="number" min="1" name="seating_capacity" class="form-control rounded-3 p-2.5" value="4" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Create Table</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>