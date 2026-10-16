<!-- File Path: resources/views/member/children/index.blade.php -->
@extends('member.partials.layout')

@section('title', 'Manage Child Sub-Accounts - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4" style="background: #f8fafc; min-height: 85vh;">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-child text-info"></i> Family & Child Sub-Accounts
            </h2>
            <p class="text-muted small">Create child accounts with real credentials and affiliate network positioning. Earnings remain reserved until age 18, managed via mini pocket-money wallets.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> Please check all required fields and try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Create Child Form Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
        <h4 class="fw-bold text-dark mb-3 fs-5"><i class="fas fa-user-plus text-primary me-2"></i> Register New Child / Family Member</h4>
        
        <form method="POST" action="{{ route('member.children.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Affiliate Network Position</label>
                    <select name="position" required class="form-select rounded-3">
                        <option value="left">Left Leg</option>
                        <option value="right">Right Leg</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Child Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control rounded-3" placeholder="e.g. Gourang Sharma">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required class="form-control rounded-3" placeholder="Unique username">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Email Address (Real)</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="form-control rounded-3" placeholder="child@example.com">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Password</label>
                    <input type="password" name="password" required class="form-control rounded-3" placeholder="••••••••">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-secondary">Date of Birth (DOB for 18+ Rule)</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" required class="form-control rounded-3">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-primary text-white fw-bold rounded-pill shadow-sm border-0">
                    <i class="fas fa-save me-1"></i> Save Child Account & Link Network
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Children List Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h4 class="fw-bold text-dark mb-3 fs-5"><i class="fas fa-users text-success me-2"></i> Registered Child Sub-Accounts</h4>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-3 rounded-start">Name</th>
                        <th class="py-3 px-3">Username / Email</th>
                        <th class="py-3 px-3">Position</th>
                        <th class="py-3 px-3">DOB & Age Status</th>
                        <th class="py-3 px-3 rounded-end">Mini Wallet (Pocket Money)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($children as $child)
                        @php
                            $age = $child->dob ? \Carbon\Carbon::parse($child->dob)->age : 0;
                        @endphp
                        <tr>
                            <td class="px-3 fw-bold text-dark">{{ $child->name }}</td>
                            <td class="px-3">
                                <span class="d-block text-dark">@<span>{{ $child->username }}</span></span>
                                <span class="small text-muted">{{ $child->email }}</span>
                            </td>
                            <td class="px-3">
                                <span class="badge bg-info text-dark uppercase">{{ $child->position ?? 'N/A' }} Leg</span>
                            </td>
                            <td class="px-3">
                                <span class="d-block small fw-semibold">{{ $child->dob }}</span>
                                @if($age >= 18)
                                    <span class="badge bg-success">Adult (Unlocked)</span>
                                @else
                                    <span class="badge bg-warning text-dark">Minor (Age: {{ $age }}) - Reserved</span>
                                @endif
                            </td>
                            <td class="px-3">
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">
                                    <i class="fas fa-wallet me-1"></i> Mini Wallet Active
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-child fa-2x mb-2 d-block text-secondary"></i>
                                No child accounts registered yet. Use the form above to add accounts.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection