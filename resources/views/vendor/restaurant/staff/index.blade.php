@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Restaurant Staff & Sub-Users Management</h2>
            <p class="text-muted">Create and manage sub-users (Manager, Cashier, Kitchen, Waiter) for your restaurant operations.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Add Staff Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add New Staff Member</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vendor.restaurant.staff.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="staff@restaurant.com" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="manager">Sub-Manager</option>
                            <option value="cashier">Cashier</option>
                            <option value="kitchen">Kitchen (KDS)</option>
                            <option value="waiter">Waiter</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">Save Staff</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff List Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Existing Staff List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role / Access</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffs as $index => $staff)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>
                                @if($staff->role == 'manager')
                                    <span class="badge bg-danger">Sub-Manager</span>
                                @elseif($staff->role == 'cashier')
                                    <span class="badge bg-warning text-dark">Cashier</span>
                                @elseif($staff->role == 'kitchen')
                                    <span class="badge bg-info text-dark">Kitchen (KDS)</span>
                                @else
                                    <span class="badge bg-primary">Waiter</span>
                                @endif
                            </td>
                            <td>{{ $staff->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No staff members added yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection