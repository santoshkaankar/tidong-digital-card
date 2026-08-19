@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header with Back to Dashboard Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">Search Vendors & Services</h3>
            <p class="text-muted small m-0">Members aur vendors ko search karein</p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <form action="{{ route('member.search') }}" method="GET" class="card p-3 mb-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">User Name / Keyword</label>
                <input type="text" name="search_name" value="{{ request('search_name') }}" class="form-control" placeholder="Enter name, card no, mobile...">
            </div>
            <div class="col-md-3">
                <label class="form-label">State</label>
                <select name="state" class="form-select" onchange="this.form.submit()">
                    <option value="">All State</option>
                    @foreach($states as $st)
                        <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">City</label>
                <select name="city" class="form-select">
                    <option value="">All City</option>
                    @foreach($cities as $ct)
                        <option value="{{ $ct }}" {{ request('city') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <!-- Results Section -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if(isset($hasSearched) && $hasSearched)
                @if($results->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Profile Pic</th>
                                    <th>Name</th>
                                    <th>Business Name</th>
                                    <th>City</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $card)
                                    <tr>
                                        <td>
                                            @if(!empty($card->photo))
                                                <img src="{{ asset('storage/' . $card->photo) }}" alt="Profile" width="45" height="45" class="rounded-circle" style="object-fit: cover;">
                                            @else
                                                <img src="{{ asset('default-avatar.png') }}" alt="Profile" width="45" height="45" class="rounded-circle">
                                            @endif
                                        </td>
                                        <td><strong>{{ $card->name }}</strong></td>
                                        <td>{{ $card->business_name ?? 'N/A' }}</td>
                                        <td>{{ $card->city ?? 'N/A' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                View Profile
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted my-4">No matching visiting cards found.</p>
                @endif
            @else
                <p class="text-center text-muted my-4">Please enter a search keyword or select a location to find profiles.</p>
            @endif
        </div>
    </div>
</div>
@endsection