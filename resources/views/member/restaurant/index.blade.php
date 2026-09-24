<div class="container py-4">
    <!-- Header & Search Bar -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold text-dark mb-3"><i class="fas fa-utensils text-danger me-2"></i> Restaurants & Food Hub</h3>
            <div class="input-group shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0 ps-4"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="restaurantSearch" class="form-control border-0 py-3" placeholder="Search restaurants, cuisines, or dishes...">
                <button class="btn btn-danger px-4 fw-bold" type="button">Search</button>
            </div>
        </div>
    </div>

    <!-- Veg / Non-Veg / Egg & Nearby Filters -->
    <div class="row mb-4">
        <div class="col-md-12 d-flex flex-wrap gap-2 align-items-center">
            <span class="fw-bold text-secondary me-2">Filters:</span>
            <button class="btn btn-outline-success rounded-pill px-3 active filter-btn" data-filter="all">All</button>
            <button class="btn btn-outline-success rounded-pill px-3 filter-btn" data-filter="veg"><i class="fas fa-circle text-success small me-1"></i> Pure Veg</button>
            <button class="btn btn-outline-danger rounded-pill px-3 filter-btn" data-filter="nonveg"><i class="fas fa-circle text-danger small me-1"></i> Non-Veg</button>
            <button class="btn btn-outline-warning rounded-pill px-3 filter-btn" data-filter="egg"><i class="fas fa-circle text-warning small me-1"></i> Egg Items</button>
            <button class="btn btn-outline-dark rounded-pill px-3 ms-auto"><i class="fas fa-map-marker-alt text-danger me-1"></i> Nearby Location</button>
        </div>
    </div>

    <!-- Restaurant Cards Grid -->
    <div class="row g-4" id="restaurantGrid">
        <!-- Dynamic Loop of Restaurants will come here -->
        <div class="col-xl-3 col-lg-4 col-md-6 restaurant-item" data-type="veg">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4" class="card-img-top" alt="Restaurant" style="height: 180px; object-fit: cover;">
                    <span class="badge bg-success position-absolute top-0 end-0 m-3 px-2 py-1">4.5 <i class="fas fa-star small"></i></span>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold text-dark mb-1">Royal Spice Restaurant</h5>
                    <p class="text-muted small mb-2">North Indian, Chinese, Fast Food</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-success fw-bold small"><i class="fas fa-clock me-1"></i> 25-30 mins</span>
                        <a href="#" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">View Menu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>