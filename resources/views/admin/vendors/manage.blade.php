<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor 360° Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        #sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: #1e293b; color: #fff; overflow-y: auto; z-index: 1000; }
        #sidebar .brand { font-size: 1.1rem; padding: 15px; background: #0f172a; text-align: center; font-weight: bold; border-bottom: 1px solid #334155; }
        #sidebar .nav-link { color: #94a3b8; padding: 7px 12px; margin: 2px 8px; border-radius: 6px; font-size: 0.88rem; }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: #2563eb; }
        #main-content { margin-left: 250px; padding: 30px; }
        @media (max-width: 768px) {
            #sidebar { width: 100%; height: auto; position: relative; }
            #main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Include -->
    @include('admin.layouts.sidebar')

    <!-- Main Content Area -->
    <div id="main-content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-4"><i class="fas fa-store me-2"></i> 360° Vendor & Business Management</h3>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Universal Search Box for Vendors -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <form action="{{ route('admin.vendors.manage') }}" method="GET">
                        <label class="form-label fw-semibold">Search Vendor/Business by Email ID or Mobile Number</label>
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Enter vendor email or mobile..." value="{{ $query ?? '' }}" required>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fas fa-search me-1"></i> Search / Open Vendor Hub
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Vendor Hub (Appears after search) -->
            @if(isset($query))
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            @if($vendor)
                                <i class="fas fa-store-alt me-2 text-info"></i> Managing Vendor: {{ $vendor->name }} ({{ $vendor->email }})
                            @else
                                <i class="fas fa-user-plus me-2 text-warning"></i> New Vendor Setup (Auto-Register)
                            @endif
                        </h5>
                        @if($vendor)
                            <span class="badge bg-{{ $vendor->status == 'approved' ? 'success' : 'warning' }} fs-6 text-capitalize">
                                Status: {{ $vendor->status ?? 'Pending' }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="card-body p-4">
                        @if($vendor)
                            <!-- NAVIGATION TABS FOR VENDOR MODULES -->
                            <ul class="nav nav-tabs mb-4" id="vendorTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-semibold" id="v-profile-tab" data-bs-toggle="tab" data-bs-target="#v-profile" type="button" role="tab">
                                        <i class="fas fa-user-tie me-1"></i> Business Profile
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="v-products-tab" data-bs-toggle="tab" data-bs-target="#v-products" type="button" role="tab">
                                        <i class="fas fa-boxes me-1"></i> Products & Items
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="v-wallet-tab" data-bs-toggle="tab" data-bs-target="#v-wallet" type="button" role="tab">
                                        <i class="fas fa-wallet me-1"></i> Earnings & Payouts
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-semibold" id="v-orders-tab" data-bs-toggle="tab" data-bs-target="#v-orders" type="button" role="tab">
                                        <i class="fas fa-clipboard-list me-1"></i> Store Orders
                                    </button>
                                </li>
                            </ul>
                        @endif

                        <!-- TAB CONTENTS -->
                        <div class="tab-content" id="vendorTabContent">
                            
                            <!-- 1. VENDOR PROFILE TAB -->
                            <div class="tab-pane fade show active" id="v-profile" role="tabpanel">
                                <form action="{{ route('admin.vendors.save') }}" method="POST">
                                    @csrf
                                    <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-1"></i> Vendor & Store Credentials</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Owner Name *</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $vendor->name ?? '') }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Email Address *</label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $vendor->email ?? $query) }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Mobile Number *</label>
                                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $vendor->mobile ?? (is_numeric($query) ? $query : '')) }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Business Type / Category</label>
                                            <input type="text" name="business_type" class="form-control" value="{{ old('business_type', $vendor->business_type ?? '') }}" placeholder="e.g. Retail, Service, Food">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Approval Status</label>
                                            <select name="status" class="form-select">
                                                <option value="approved" {{ (isset($vendor) && $vendor->status == 'approved') ? 'selected' : '' }}>Approved</option>
                                                <option value="pending" {{ (isset($vendor) && $vendor->status == 'pending') ? 'selected' : '' }}>Pending Approval</option>
                                                <option value="rejected" {{ (isset($vendor) && $vendor->status == 'rejected') ? 'selected' : '' }}>Rejected / Blocked</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Set / Reset Password</label>
                                            <input type="text" name="password" class="form-control" placeholder="Leave blank to keep old">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-success px-4 fw-semibold">
                                            <i class="fas fa-save me-1"></i> Save Vendor Details
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @if($vendor)
                                <!-- 2. PRODUCTS & ITEMS TAB -->
                                <div class="tab-pane fade" id="v-products" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="text-dark mb-0">Vendor Catalog Items</h5>
                                        <span class="text-muted small">Manage items listed by this business</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No custom items found for this vendor.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- 3. WALLET & PAYOUTS TAB -->
                                <div class="tab-pane fade" id="v-wallet" role="tabpanel">
                                    <h5 class="text-dark mb-3">Vendor Earnings & Balance</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="card p-3 bg-white border shadow-sm">
                                                <span class="text-muted small">Available Payout Balance</span>
                                                <h3 class="fw-bold text-success mt-1">₹ 0.00</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card p-3 bg-white border shadow-sm">
                                                <label class="form-label fw-semibold">Adjust Vendor Wallet</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" placeholder="Enter amount">
                                                    <button class="btn btn-outline-success">Add Credit</button>
                                                    <button class="btn btn-outline-danger">Deduct</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. STORE ORDERS TAB -->
                                <div class="tab-pane fade" id="v-orders" role="tabpanel">
                                    <h5 class="text-dark mb-3">Orders Received by Vendor</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Total Amount</th>
                                                    <th>Order Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No orders received yet.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>