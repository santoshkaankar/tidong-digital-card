<!-- File Path: resources/views/member/game/partials/market-modal.blade.php -->

<!-- Open Market HUD Button -->
<button type="button" class="btn btn-primary fw-bold rounded-pill position-absolute bottom-0 start-0 m-3 shadow z-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#gameMarketModal">
    🏬 Tidong Super Market & Showroom
</button>

<!-- Market Modal Popup -->
<div class="modal fade" id="gameMarketModal" tabindex="-1" aria-labelledby="gameMarketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-white border-warning shadow-lg">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-warning" id="gameMarketModalLabel">🏎️ Tidong Luxury Market & Motors</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Search Bar for Quick Item Lookup -->
                <div class="mb-3 position-relative">
                    <input type="text" id="marketSearchInput" class="form-control bg-secondary text-white border-0 rounded-pill px-4" placeholder="🔍 Search Cars, Houses, Sofa, Beds, Clothes..." onkeyup="filterMarketItems()">
                </div>

                <!-- Nav Tabs -->
                <ul class="nav nav-pills mb-3 border-bottom border-secondary pb-2" id="marketTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active btn-sm fw-bold me-2" id="cars-tab" data-bs-toggle="pill" data-bs-target="#tab-cars" type="button">🚗 Cars & Motors</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm fw-bold me-2" id="house-tab" data-bs-toggle="pill" data-bs-target="#tab-house" type="button">🏠 Houses & Rooms</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm fw-bold me-2" id="decor-tab" data-bs-toggle="pill" data-bs-target="#tab-decor" type="button">🛋️ Furniture & Decor</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm fw-bold" id="avatar-tab" data-bs-toggle="pill" data-bs-target="#tab-avatar" type="button">👕 Avatar Wardrobe</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="marketTabsContent">
                    
                    <!-- 1. Cars & Motors Showroom -->
                    <div class="tab-pane fade show active" id="tab-cars" role="tabpanel">
                        <div class="row g-3 market-item-list">
                            <div class="col-md-4 market-card-item" data-name="red sports car gtr vehicle">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h5>🏎️ Red Sports Car</h5>
                                    <p class="small text-warning fw-bold mb-2">500 T-Coins / ₹49 Direct</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyAndSpawnItem('car', 0xff0000)">Buy & Drive</button>
                                </div>
                            </div>
                            <div class="col-md-4 market-card-item" data-name="yellow luxury suv truck vehicle">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h5>🛻 Yellow SUV Truck</h5>
                                    <p class="small text-warning fw-bold mb-2">750 T-Coins / ₹79 Direct</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyAndSpawnItem('car', 0xffd700)">Buy & Drive</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Houses & Rooms -->
                    <div class="tab-pane fade" id="tab-house" role="tabpanel">
                        <div class="row g-3 market-item-list">
                            <div class="col-md-6 market-card-item" data-name="modern luxury villa house room">
                                <div class="card bg-secondary text-white p-3 border-0 rounded-3">
                                    <h5>🏡 Modern Luxury Villa</h5>
                                    <p class="small text-light">3D Room setup on ground with interior decor slots.</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyAndSpawnItem('house', 0xffffff)">Buy House (1000 T-Coins)</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Furniture & Decor -->
                    <div class="tab-pane fade" id="tab-decor" role="tabpanel">
                        <div class="row g-3 market-item-list">
                            <div class="col-md-4 market-card-item" data-name="luxury sofa couch furniture">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h5>🛋️ Royal Red Sofa</h5>
                                    <p class="small text-warning fw-bold mb-2">150 T-Coins</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyAndSpawnItem('sofa', 0x990000)">Place in House</button>
                                </div>
                            </div>
                            <div class="col-md-4 market-card-item" data-name="wooden dining table furniture">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h5>🪑 Wooden Table</h5>
                                    <p class="small text-warning fw-bold mb-2">100 T-Coins</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyAndSpawnItem('table', 0x8b4513)">Place in House</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Wardrobe & Accessories -->
                    <div class="tab-pane fade" id="tab-avatar" role="tabpanel">
                        <div class="row g-3 market-item-list">
                            <div class="col-md-4 market-card-item" data-name="cool sunglasses glasses">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h5>🕶️ Cool Sunglasses</h5>
                                    <p class="small text-warning fw-bold mb-2">50 T-Coins</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="alert('Wearing Sunglasses!')">Equip Avatar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Close Market</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Search Filter Function for Market Items
    function filterMarketItems() {
        const query = document.getElementById('marketSearchInput').value.toLowerCase();
        const items = document.querySelectorAll('.market-card-item');
        
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>