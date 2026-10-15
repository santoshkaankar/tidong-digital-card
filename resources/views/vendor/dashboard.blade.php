@extends('layouts.vendor_master')

@section('content')
<div class="container-fluid py-2">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white shadow-sm border-0 rounded-4 p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-1">Welcome back, {{ $user->name }}!</h2>
                        <p class="text-white-50 mb-0">Yeh aapka Central Master Dashboard hai. Yahan se aap apne saare businesses aur outlets manage kar sakte hain.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('vendor.businesses.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 shadow-sm">
                            <i class="fas fa-plus-circle me-2"></i> Add New Business / Outlet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary p-3 rounded-4 fs-4 me-3">
                        <i class="fas fa-store"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Total Businesses</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalBusinesses }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-success-subtle text-success p-3 rounded-4 fs-4 me-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Active Business Session</span>
                        <h6 class="fw-bold mb-0 text-success text-truncate" style="max-width: 200px;">
                            {{ session('active_business_name', 'None Selected') }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="bg-warning-subtle text-warning p-3 rounded-4 fs-4 me-3">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Account Role</span>
                        <h5 class="fw-bold mb-0 text-dark text-uppercase">{{ $user->role }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registered Businesses / Outlets List Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white fw-bold"><i class="fas fa-list-alt me-2"></i> Your Registered Businesses & Outlets</h5>
                    <a href="{{ route('vendor.businesses.index') }}" class="btn btn-sm btn-primary">Manage All</a>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-7">
                                <tr>
                                    <th>Business Name</th>
                                    <th>Type</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($businesses as $biz)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $biz->business_name }}</td>
                                        <td><span class="badge bg-info-subtle text-info border border-info px-2 py-1">{{ ucfirst($biz->business_type) }}</span></td>
                                        <td class="text-muted">{{ $biz->city ?? 'N/A' }}</td>
                                        <td>
                                            @if(session('active_business_id') == $biz->id)
                                                <span class="badge bg-success px-2 py-1">Active Session</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('vendor.businesses.switch', $biz->id) }}" class="btn btn-sm btn-outline-primary px-3 fw-semibold">
                                                <i class="fas fa-exchange-alt me-1"></i> Switch & Open
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                            No business registered yet. Click above to add your first outlet!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection