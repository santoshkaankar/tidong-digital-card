@extends('member.partials.layout')

@section('title', 'Search Vendors & Services - Tidong® Portal')

@section('content')
<div class="container-fluid py-4 px-4">
    <!-- Header with Back to Dashboard Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0 text-dark"><i class="fas fa-search text-primary me-2"></i> Search Vendors & Services</h3>
            <p class="text-muted small m-0">Members, restaurants, taxis, aur emporiums ko category wise search karein</p>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form with Category Filter -->
    <form action="{{ route('member.search') }}" method="GET" class="card p-4 mb-4 shadow-sm border-0 rounded-4">
        <div class="row g-3">
            <!-- Category Filter Dropdown -->
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Category Filter</label>
                <select name="category" class="form-select rounded-3">
                    <option value="">🔍 All Categories</option>
                    <option value="member" {{ request('category') == 'member' ? 'selected' : '' }}>👤 Members</option>
                    <option value="restaurant" {{ request('category') == 'restaurant' ? 'selected' : '' }}>🍽️ Restaurants</option>
                    <option value="taxi" {{ request('category') == 'taxi' ? 'selected' : '' }}>🚕 Taxi & Transport</option>
                    <option value="emporium" {{ request('category') == 'emporium' ? 'selected' : '' }}>🛍️ Emporium & Shops</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">User Name / Keyword</label>
                <input type="text" name="search_name" value="{{ request('search_name') }}" class="form-control rounded-3" placeholder="Enter name, card no, mobile...">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary">State</label>
                <select name="state" class="form-select rounded-3" onchange="this.form.submit()">
                    <option value="">All State</option>
                    @foreach($states as $st)
                        <option value="{{ $st }}" {{ request('state') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-secondary">City</label>
                <select name="city" class="form-select rounded-3">
                    <option value="">All City</option>
                    @foreach($cities as $ct)
                        <option value="{{ $ct }}" {{ request('city') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-bold">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
        </div>
    </form>

    <!-- Results Section -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
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
                                                <img src="{{ asset('storage/' . $card->photo) }}" alt="Profile" width="45" height="45" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                            @else
                                                <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Profile" width="45" height="45" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                            @endif
                                        </td>
                                        <td><strong>{{ $card->name }}</strong></td>
                                        <td>{{ $card->business_name ?? 'N/A' }}</td>
                                        <td>{{ $card->city ?? 'N/A' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                View Profile
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open text-muted fs-1 mb-3"></i>
                        <p class="text-muted m-0">No matching visiting cards or services found.</p>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search-location text-primary fs-1 mb-3"></i>
                    <p class="text-muted m-0">Please select a category or enter search keywords to find profiles.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection