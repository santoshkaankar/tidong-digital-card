@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header Section with Switch Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h3 class="mb-0">🔍 Advanced Tiffin Order Schedule & Filter</h3>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('vendor.restaurant.proceed-tiffin.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Proceed New Tiffin
            </a>
            <a href="{{ route('vendor.restaurant.proceed-tiffin.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list"></i> Saved List
            </a>
        </div>
    </div>

    <!-- Advanced Multi-Filter Box -->
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Multi-Parameter Filter Dashboard (Pending Orders)</h5>
        </div>
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('vendor.restaurant.proceed-tiffin.schedule') }}" class="row g-3 align-items-end">
                
                <div class="col-12 col-md-2">
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold">Filter by Meal Type</label>
                    <select name="meal_type" class="form-select">
                        <option value="all" {{ request('meal_type') == 'all' ? 'selected' : '' }}>All Meals (Auto-arranged)</option>
                        <option value="breakfast" {{ request('meal_type') == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                        <option value="lunch" {{ request('meal_type') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                        <option value="snacks" {{ request('meal_type') == 'snacks' ? 'selected' : '' }}>Snacks</option>
                        <option value="dinner" {{ request('meal_type') == 'dinner' ? 'selected' : '' }}>Dinner</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-bold">Search Pending Order / Name</label>
                    <select name="search_query" class="form-select">
                        <option value="">-- All Pending Orders --</option>
                        @if(isset($pendingOrders))
                            @foreach($pendingOrders as $pOrder)
                                <option value="{{ $pOrder->id }}" {{ request('search_query') == $pOrder->id ? 'selected' : '' }}>
                                    #{{ $pOrder->id }} - {{ $pOrder->customer_name }} ({{ $pOrder->customer_mobile }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill">
                        <i class="fas fa-search"></i> Apply
                    </button>
                    <a href="{{ route('vendor.restaurant.proceed-tiffin.schedule') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rendered Orders & Schedule Boxes -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Filtered Pending Orders Result</h5>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                    @foreach($orders as $order)
                        <div class="col">
                            <div class="card h-100 border-start border-success border-4 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">Order ID: #{{ $order->id }}</span>
                                        <span class="badge bg-info text-dark">Duration: {{ strtoupper($order->duration) }}</span>
                                    </div>
                                    
                                    @if(!empty($order->customer_name))
                                        <p class="mb-1 fw-bold text-dark"><i class="fas fa-user"></i> {{ $order->customer_name }}</p>
                                    @endif

                                    <p class="mb-1 text-muted small"><i class="far fa-calendar-alt"></i> {{ $order->from_date }} To {{ $order->to_date }}</p>
                                    
                                    <hr class="my-2">

                                    <div class="mb-2">
                                        <strong>Meal Types:</strong><br>
                                        @if(is_array($order->meal_types))
                                            @foreach($order->meal_types as $meal)
                                                <span class="badge bg-secondary me-1 mb-1">{{ ucfirst($meal) }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-secondary">{{ $order->meal_types }}</span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <strong>Catalogs IDs:</strong> 
                                        <span class="text-danger fw-bold">
                                            @if(is_array($order->selected_catalogs))
                                                {{ implode(', ', $order->selected_catalogs) }}
                                            @else
                                                {{ $order->selected_catalogs }}
                                            @endif
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('vendor.restaurant.proceed-tiffin.show', $order->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-calendar-day"></i> View Daily Calendar Boxes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p class="mb-0">In filters ke mutabiq koi pending order nahi mila!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection