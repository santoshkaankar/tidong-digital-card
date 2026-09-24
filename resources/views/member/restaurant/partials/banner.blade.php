<div class="food-hero-banner p-4 mb-4 shadow-sm position-relative overflow-hidden">
    <div class="row align-items-center">
        <div class="col-md-8">
            <span class="badge bg-white text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-2 small fw-semibold">
                <i class="fas fa-utensils me-1"></i> Food & Dining Directory
            </span>
            <h2 class="fw-bold mb-1 text-dark">Restaurant & Food Hub</h2>
            <p class="mb-0 text-secondary small">Explore the best restaurants and dining spots near you.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="bg-white p-3 rounded-3 d-inline-block text-center border shadow-sm">
                <span class="d-block fs-4 fw-bold text-dark">
                    {{ method_exists($restaurants, 'total') ? $restaurants->total() : $restaurants->count() }}
                </span>
                <span class="small text-muted" style="font-size: 0.75rem;">Total Restaurants</span>
            </div>
        </div>
    </div>
</div>