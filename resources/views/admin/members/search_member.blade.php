@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0"><i class="fas fa-users-search me-2"></i> Search Registered Members</h3>
            <p class="text-muted small m-0">Filter aur manage karein sabhi existing members ko</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <form action="{{ route('admin.members.search_member') }}" method="GET" class="card p-3 mb-4 shadow-sm border-0">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Member Name / Keyword</label>
                <input type="text" name="search_name" value="{{ request('search_name') }}" class="form-control" placeholder="Search by name, email, mobile...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">State</label>
                <select name="state" class="form-select" onchange="this.form.submit()">
                    <option value="">All States</option>
                    @foreach($states as $st)
                        <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">City</label>
                <select name="city" class="form-select">
                    <option value="">All Cities</option>
                    @foreach($cities as $ct)
                        <option value="{{ $ct }}" {{ request('city') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="fas fa-search me-1"></i> Search User
                </button>
            </div>
        </div>
    </form>

    <!-- Results Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if(isset($hasSearched) && $hasSearched)
                @if($results->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Email & Mobile</th>
                                    <th>City / State</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $user)
                                    <tr>
                                        <td>
                                            @if(!empty($user->photo))
                                                <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile" width="45" height="45" class="rounded-circle" style="object-fit: cover;">
                                            @else
                                                <img src="{{ asset('default-avatar.png') }}" alt="Profile" width="45" height="45" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>
                                            <div class="small">{{ $user->email }}</div>
                                            <div class="text-muted small">{{ $user->mobile ?? 'N/A' }}</div>
                                        </td>
                                        <td>{{ $user->city ?? 'N/A' }}, {{ $user->state ?? '' }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.manage', ['query' => $user->email]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog me-1"></i> Manage Config
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted my-4">No matching members found.</p>
                @endif
            @else
                <p class="text-center text-muted my-4">Search criteria select karke members find karein.</p>
            @endif
        </div>
    </div>
</div>
@endsection