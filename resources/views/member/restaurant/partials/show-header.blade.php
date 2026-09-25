<!-- Header Action Buttons -->
<div class="mb-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
    <a href="{{ route('member.restaurant.index') }}" class="btn btn-sm btn-light border text-muted rounded-3">
        <i class="fas fa-arrow-left me-1"></i> Back to Restaurants
    </a>
    <button class="btn btn-primary btn-sm btn-md-md rounded-pill px-3 px-md-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#tiffinBookingModal">
        <i class="fas fa-calendar-alt me-1"></i> Pre-Book Tiffin (1D / 1W / 1M)
    </button>
</div>

<!-- Restaurant Info Card -->
<div class="restaurant-header p-3 p-md-4 mb-4 shadow-sm">
    <div class="row align-items-center g-3">
        <div class="col-12 col-md-8">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fw-bold fs-3 flex-shrink-0" style="width: 55px; height: 55px;">
                    {{ strtoupper(substr($restaurant->name ?? 'R', 0, 1)) }}
                </div>
                <div>
                    <h4 class="fw-bold text-dark mb-1 fs-5 fs-md-4">{{ $restaurant->name ?? 'Restaurant Name' }}</h4>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                        {{ $restaurant->city ?? 'Kota' }}, {{ $restaurant->state ?? 'Rajasthan' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 text-start text-md-end">
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                <i class="fas fa-check-circle me-1"></i> Accepting Orders
            </span>
        </div>
    </div>
</div>