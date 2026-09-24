<div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card restaurant-card h-100 p-3">
        <div class="card-body p-1 d-flex flex-column justify-content-between">
            
            <div>
                <!-- Header Info -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rest-avatar">
                        {{ strtoupper(substr($restaurant->name ?? 'R', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $restaurant->name ?? 'Restaurant Name' }}</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0 small" style="font-size: 0.7rem;">
                            ● Active
                        </span>
                    </div>
                </div>

                <!-- Location & Details -->
                <div class="p-2.5 bg-light rounded-3 mb-3 text-muted small border border-light-subtle">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-secondary me-2"></i>
                        <span>{{ $restaurant->city ?? 'Kota' }}, {{ $restaurant->state ?? 'Rajasthan' }}</span>
                    </div>
                    @if(isset($restaurant->food_type))
                        <div class="mt-2">
                            @if(in_array($restaurant->food_type, ['pure_veg', 'veg']))
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-leaf me-1"></i> Pure Veg</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-drumstick-bite me-1"></i> Veg / Non-Veg</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- View Button -->
            <div>
                <a href="{{ route('member.restaurant.show', $restaurant->id) }}" class="btn btn-outline-primary w-100 rounded-3 fw-medium py-1.5 fs-6">
                    View Menu
                </a>
            </div>

        </div>
    </div>
</div>