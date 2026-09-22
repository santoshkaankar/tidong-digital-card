@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid px-2 px-md-4 py-3">
    <!-- Header Section with Switch Buttons -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h3 class="mb-0">📦 Saved Proceed Tiffin List</h3>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('vendor.restaurant.proceed-tiffin.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Proceed New Tiffin
            </a>
            <a href="{{ route('vendor.restaurant.proceed-tiffin.schedule') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-calendar-alt"></i> Schedule & Filter
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Responsive Table Card -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Duration</th>
                            <th>Date Range</th>
                            <th>Meal Types</th>
                            <th>Saved Catalogs</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($savedOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($order->duration) }}</span></td>
                                <td>{{ $order->from_date }} to {{ $order->to_date }}</td>
                                <td>
                                    @if(is_array($order->meal_types))
                                        {{ implode(', ', $order->meal_types) }}
                                    @else
                                        {{ $order->meal_types }}
                                    @endif
                                </td>
                                <td>
                                    @if(is_array($order->selected_catalogs))
                                        <span class="text-primary fw-bold">IDs: {{ implode(', ', $order->selected_catalogs) }}</span>
                                    @else
                                        {{ $order->selected_catalogs }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('vendor.restaurant.proceed-tiffin.show', $order->id) }}" class="btn btn-sm btn-info text-white" title="View Calendar">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Koi tiffin order save nahi mila!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection