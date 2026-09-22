@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h3 class="mb-0">📅 Tiffin Order Calendar View (#{{ $order->id }})</h3>
        <a href="{{ route('vendor.restaurant.proceed-tiffin.list') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Order Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6 col-md-3"><strong>Duration:</strong> <span class="badge bg-info text-dark">{{ strtoupper($order->duration) }}</span></div>
                <div class="col-6 col-md-3"><strong>From:</strong> {{ $order->from_date }}</div>
                <div class="col-6 col-md-3"><strong>To:</strong> {{ $order->to_date }}</div>
                <div class="col-12 col-md-3"><strong>Meals:</strong> 
                    @if(is_array($order->meal_types))
                        {{ implode(', ', $order->meal_types) }}
                    @else
                        {{ $order->meal_types }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section (Date & Meal Wise) -->
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-light">
            <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-filter"></i> Filter Schedule (Date & Meal Wise)</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('vendor.restaurant.proceed-tiffin.show', $order->id) }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold">Filter by Date</label>
                    <input type="date" name="filter_date" class="form-control" value="{{ request('filter_date') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold">Filter by Meal Type</label>
                    <select name="meal_type" class="form-select">
                        <option value="">All Meals</option>
                        <option value="breakfast" {{ request('meal_type') == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                        <option value="lunch" {{ request('meal_type') == 'lunch' ? 'selected' : '' }}>Lunch</option>
                        <option value="snacks" {{ request('meal_type') == 'snacks' ? 'selected' : '' }}>Snacks</option>
                        <option value="dinner" {{ request('meal_type') == 'dinner' ? 'selected' : '' }}>Dinner</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Apply Filter
                    </button>
                    <a href="{{ route('vendor.restaurant.proceed-tiffin.show', $order->id) }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendar Grid Boxes (Fully Mobile Responsive) -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Daily Tiffin Status Schedule</h5>
        </div>
        <div class="card-body">
            @if(count($calendarDates) > 0)
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach($calendarDates as $item)
                        <div class="col">
                            <div class="card h-100 border-start border-primary border-4 shadow-sm">
                                <div class="card-body text-center p-3">
                                    <h6 class="text-muted mb-1">{{ $item['day_name'] }}</h6>
                                    <h5 class="fw-bold text-dark">{{ $item['formatted_date'] }}</h5>
                                    
                                    @if(request('meal_type'))
                                        <div class="badge bg-secondary mb-2">{{ ucfirst(request('meal_type')) }}</div>
                                    @endif

                                    <hr class="my-2">

                                    <!-- Status Badge -->
                                    <div class="mb-3">
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            {{ ucfirst($item['status']) }}
                                        </span>
                                    </div>

                                    <!-- Action Buttons: Done / Cancel -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-success flex-fill" onclick="updateStatus('{{ $item['date'] }}', 'done')">
                                            <i class="fas fa-check"></i> Done
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger flex-fill" onclick="updateStatus('{{ $item['date'] }}', 'cancelled')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">Is date ya filter ke mutabiq koi schedule nahi mila!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateStatus(date, status) {
    alert('Date: ' + date + ' status updated to: ' + status);
}
</script>
@endsection