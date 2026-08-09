<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Global Item & Item Category - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-box { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.04); margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-dark"><i class="fas fa-layer-group text-primary me-2"></i> Item Categories & Global Items Master</h3>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- SECTION 1: Create Item Category Master -->
                <div class="card-box">
                    <h5 class="fw-bold text-secondary mb-3"><i class="fas fa-folder-plus me-2"></i> Step 1: Create New Item Category</h5>
                    <form action="{{ route('admin.item.category.store') }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Enter Item Category (e.g. Beverages, Fast Food, Snacks)" required>
                            <button type="submit" class="btn btn-dark"><i class="fas fa-plus me-1"></i> Add Item Category</button>
                        </div>
                    </form>
                </div>

                <!-- SECTION 2: Create Global Item using Item Categories -->
                <div class="card-box">
                    <h5 class="fw-bold text-secondary mb-3"><i class="fas fa-plus-circle me-2"></i> Step 2: Create Global Item Under Item Category</h5>
                    <form action="{{ route('admin.global.item.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Item Category</label>
                            <select name="category" class="form-select" required>
                                <option value="">-- Choose Item Category --</option>
                                @foreach($itemCategories ?? [] as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-danger">Agar item category list mein nahi hai, toh pehle upar "Step 1" se add karein.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Name</label>
                            <input type="text" name="item_name" class="form-control" placeholder="e.g. Burger, Cold Coffee" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Picture</label>
                            <input type="file" name="item_pic" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" placeholder="0.00" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Write item details..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="fas fa-save me-1"></i> Save Global Item
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>