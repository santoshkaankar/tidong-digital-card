@extends('layouts.vendor_master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Employee Management</h4>
            <small class="text-muted">Manage staff & assign dynamic module permissions per outlet</small>
        </div>
        <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="fas fa-user-plus me-1"></i> Add New Employee
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Employee Name</th>
                            <th>Outlet / Business</th>
                            <th>Designation</th>
                            <th>Phone / Email</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $emp->name }}</td>
                                <td><span class="badge bg-info text-dark">{{ $emp->business->business_name ?? 'N/A' }}</span></td>
                                <td>{{ $emp->designation }}</td>
                                <td>
                                    <div><small class="fw-semibold">{{ $emp->email }}</small></div>
                                    <small class="text-muted">{{ $emp->phone ?? 'No Phone' }}</small>
                                </td>
                                <td>
                                    @if($emp->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form method="POST" action="{{ route('vendor.employees.toggle', $emp->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $emp->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $emp->is_active ? 'Disable Access' : 'Enable Access' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No employees registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Employee -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('vendor.employees.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-shield me-2"></i> Register New Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Business / Outlet</label>
                            <select name="business_id" class="form-select" required>
                                <option value="">-- Select Outlet --</option>
                                @foreach($businesses as $biz)
                                    <option value="{{ $biz->id }}">{{ $biz->business_name }} ({{ ucfirst($biz->business_type) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Rahul Sharma">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email (Login Username)</label>
                            <input type="email" name="email" class="form-control" required placeholder="rahul@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="******">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mobile Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="10-digit number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Designation / Role</label>
                            <select name="designation" class="form-select" required>
                                <option value="Manager">Manager</option>
                                <option value="Cashier / POS Operator">Cashier / POS Operator</option>
                                <option value="Kitchen Staff / Chef">Kitchen Staff / Chef</option>
                                <option value="Waiter / Server">Waiter / Server</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-2"><i class="fas fa-toggle-on me-1 text-primary"></i> Outlet Field Access Toggles</label>
                            <div class="row g-2 bg-light p-3 rounded-3 border">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="kds" id="perm_kds" checked>
                                        <label class="form-check-label fw-medium" for="perm_kds">Live Kitchen (KDS)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="pos" id="perm_pos" checked>
                                        <label class="form-check-label fw-medium" for="perm_pos">POS / Counter Billing</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="orders" id="perm_orders" checked>
                                        <label class="form-check-label fw-medium" for="perm_orders">Orders History</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="categories" id="perm_categories">
                                        <label class="form-check-label fw-medium" for="perm_categories">Select Category</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="tiffin_items" id="perm_tiffin_items">
                                        <label class="form-check-label fw-medium" for="perm_tiffin_items">Thali / Tiffin Items</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="items" id="perm_items">
                                        <label class="form-check-label fw-medium" for="perm_items">Select Items / Menu</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="tiffin_menu" id="perm_tiffin_menu">
                                        <label class="form-check-label fw-medium" for="perm_tiffin_menu">Create Tiffin Menu</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="proceed_tiffin" id="perm_proceed_tiffin">
                                        <label class="form-check-label fw-medium" for="perm_proceed_tiffin">Proceed Tiffin Order</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Employee & Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection