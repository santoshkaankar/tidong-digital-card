<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Categories — Restaurant Hub</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4f46e5;
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
            width: 100%;
        }

        /* Sidebar overlapping fix */
        .sidebar-area {
            width: 260px;
            flex-shrink: 0;
        }

        .main-viewport {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            width: calc(100% - 260px);
        }

        .table-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .custom-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
        }

        .custom-table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            color: var(--text-main);
            font-weight: 500;
        }

        @media (max-width: 991.98px) {
            .sidebar-area { width: 0; }
            .main-viewport { width: 100%; padding: 1rem !important; }
            .header-actions { width: 100%; }
            .header-actions .btn { flex: 1; text-align: center; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Sidebar Wrapper -->
    <div class="sidebar-area">
        @include('vendor.restaurant.partials.sidebar')
    </div>

    <div class="main-viewport p-3 p-md-4 p-lg-5">
        
        <!-- Header Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                    GLOBAL CATEGORIES
                </span>
                <h2 class="fw-bold mt-2 mb-0">Food Categories</h2>
                <p class="text-muted small mb-0">Select or assign categories from the global dropdown for your menu.</p>
            </div>

            <!-- Header Action Buttons -->
            <div class="d-flex flex-wrap gap-2 header-actions align-items-center">
                @if(Route::has('vendor.restaurant.dashboard'))
                    <a href="{{ route('vendor.restaurant.dashboard') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-1">
                        <i class="bi bi-arrow-left me-1"></i>
                        <span>Dashboard</span>
                    </a>
                @endif

                <button class="btn btn-primary rounded-3 px-4 py-2 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#selectCategoryModal">
                    <i class="bi bi-plus-lg fs-6"></i>
                    <span>Select / Add Category</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Selected Categories Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table custom-table mb-0 align-middle" style="min-width: 600px;">
                    <thead>
                        <tr>
                            <th width="80">#</th>
                            <th>Category Name</th>
                            <th>Status</th>
                            <th class="text-end" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $key => $category)
                            <tr>
                                <td class="fw-bold text-muted">{{ $key + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-tag-fill text-primary opacity-75"></i>
                                        <span class="fw-semibold">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 rounded-2 fw-semibold">
                                        Active
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('vendor.restaurant.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Remove this category from your list?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-2 p-1.5" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-tags display-4 text-muted opacity-25 d-block mb-3"></i>
                                        <h5 class="fw-bold text-dark mb-1">No categories selected yet</h5>
                                        <p class="text-muted small mb-3">Click below to select categories from the global system dropdown.</p>
                                        <button class="btn btn-primary btn-sm rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#selectCategoryModal">
                                            <i class="bi bi-plus-lg me-1"></i> Select Category
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Category Dropdown Modal -->
<div class="modal fade" id="selectCategoryModal" tabindex="-1" aria-labelledby="selectCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold" id="selectCategoryModalLabel">
                    <i class="bi bi-tags-fill text-primary me-2"></i>Select Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('vendor.restaurant.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Global Category Choose Karein</label>
                        <select name="global_category_id" class="form-select form-select-lg rounded-3 fs-6" required>
                            <option value="" selected disabled>-- Dropdown se Category Select Karein --</option>
                            @if(isset($globalCategories))
                                @foreach($globalCategories as $globalCat)
                                    <option value="{{ $globalCat->id }}">{{ $globalCat->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="form-text">Yeh category aapke restaurant menu me apply ho jayegi.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4 border-top-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-semibold">
                        <i class="bi bi-check2 me-1"></i> Add Category
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>