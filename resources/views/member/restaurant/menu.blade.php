<div class="container py-5">
    <!-- Restaurant Header Info -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-4 bg-white shadow-sm rounded-4 border-0 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Royal Spice Restaurant</h2>
                    <p class="text-muted mb-2"><i class="fas fa-map-marker-alt text-danger me-1"></i> Main Market, Kota | <span class="text-success fw-bold">Open Now</span></p>
                    <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">4.8 <i class="fas fa-star small"></i> (120+ reviews)</span>
                </div>
                <div class="mt-3 mt-md-0">
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-3 fw-bold"><i class="fas fa-motorcycle me-1"></i> Delivery & Dine-In Available</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Items & Cart Layout -->
    <div class="row g-4">
        <!-- Left Side: Menu Dishes -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h4 class="fw-bold text-dark mb-3">Order Menu</h4>
                
                <!-- Dish Item 1 -->
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Dish">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success p-1" style="font-size: 6px;"><i class="fas fa-circle text-white"></i></span>
                                <h6 class="fw-bold text-dark mb-0">Paneer Tikka</h6>
                            </div>
                            <span class="text-muted small">Delicious smoky grilled cottage cheese</span>
                            <h6 class="fw-bold text-success mt-1 mb-0">₹240.00</h6>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-bold">Add +</button>
                    </div>
                </div>

                <!-- Dish Item 2 -->
                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1589302168068-964664d93dc0" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Dish">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success p-1" style="font-size: 6px;"><i class="fas fa-circle text-white"></i></span>
                                <h6 class="fw-bold text-dark mb-0">Veg Biryani</h6>
                            </div>
                            <span class="text-muted small">Aromatic basmati rice cooked with fresh vegetables</span>
                            <h6 class="fw-bold text-success mt-1 mb-0">₹200.00</h6>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-outline-danger btn-sm px-3 rounded-pill fw-bold">Add +</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Cart Box -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 20px;">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-shopping-bag text-danger me-2"></i> Your Order Cart</h5>
                
                <div class="mb-3 border-bottom pb-3">
                    <p class="text-muted small text-center py-3">Your cart is empty. Add dishes to order!</p>
                </div>

                <div class="d-flex justify-content-between fw-bold mb-3">
                    <span>Total Amount:</span>
                    <span class="text-success fs-5">₹0.00</span>
                </div>

                <button class="btn btn-danger w-100 py-3 rounded-xl fw-bold shadow-sm" disabled>
                    PLACE ORDER NOW
                </button>
            </div>
        </div>
    </div>
</div>