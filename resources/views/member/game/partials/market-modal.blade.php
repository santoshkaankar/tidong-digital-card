<!-- File Path: resources/views/member/game/partials/market-modal.blade.php -->

<!-- Open Market HUD Button -->
<button type="button" class="btn btn-primary fw-bold rounded-pill position-absolute bottom-0 start-0 m-3 shadow z-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#gameMarketModal">
    🛒 Market & Wardrobe
</button>

<!-- Market Modal Popup -->
<div class="modal fade" id="gameMarketModal" tabindex="-1" aria-labelledby="gameMarketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-warning">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold text-warning" id="gameMarketModalLabel">🏪 Tidong Game Market & Kabadiwala</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Nav Tabs -->
                <ul class="nav nav-pills mb-3 border-bottom border-secondary pb-2" id="marketTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active btn-sm fw-bold me-2" id="avatar-tab" data-bs-toggle="pill" data-bs-target="#tab-avatar" type="button">👕 Avatar & Clothes</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm fw-bold me-2" id="tools-tab" data-bs-toggle="pill" data-bs-target="#tab-tools" type="button">🧹 Tools & Machines</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link btn-sm fw-bold" id="waste-tab" data-bs-toggle="pill" data-bs-target="#tab-waste" type="button">📦 Waste Generator Items</button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="marketTabsContent">
                    
                    <!-- 1. Avatar Customization -->
                    <div class="tab-pane fade show active" id="tab-avatar" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h6>🕶️ Cool Sunglasses</h6>
                                    <p class="small text-warning fw-bold mb-2">50 T-Coins</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="alert('Bought Sunglasses!')">Buy & Wear</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h6>👕 Pro Player Jacket</h6>
                                    <p class="small text-warning fw-bold mb-2">120 T-Coins</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="alert('Bought Pro Jacket!')">Buy & Wear</button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                    <h6>🧢 Gamer Cap</h6>
                                    <p class="small text-warning fw-bold mb-2">30 T-Coins</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="alert('Bought Cap!')">Buy & Wear</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Cleaning Tools -->
                    <div class="tab-pane fade" id="tab-tools" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-secondary text-white p-3 border-0 rounded-3">
                                    <h6>🧹 Super Vacuum Blower</h6>
                                    <p class="small text-light">Cleans 3 trash items at once automatically.</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="alert('Vacuum Machine Active!')">Buy for 200 T-Coins</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-secondary text-white p-3 border-0 rounded-3">
                                    <h6>🗑️ Smart Eco-Dustbin</h6>
                                    <p class="small text-light">Earn double T-Coins per collected waste item.</p>
                                    <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="alert('Dustbin Upgraded!')">Buy for 150 T-Coins</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Trash Generator Items (Realistic Triggers) -->
                    <div class="tab-pane fade" id="tab-waste" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-secondary text-white p-3 border-0 rounded-3">
                                    <h6>🥤 Snack & Food Order</h6>
                                    <p class="small text-light">Generates box/wrapper waste on field to clean for XP.</p>
                                    <button class="btn btn-sm btn-danger fw-bold rounded-pill" onclick="if(window.spawnSingleTrash) { window.spawnSingleTrash(); window.spawnSingleTrash(); alert('Snack box trash dropped on ground!'); }">Order Snack (Creates Waste)</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-secondary text-white p-3 border-0 rounded-3">
                                    <h6>🚗 Delivery Vehicle Tour</h6>
                                    <p class="small text-light">Drives through field and drops debris items naturally.</p>
                                    <button class="btn btn-sm btn-danger fw-bold rounded-pill" onclick="if(window.spawnSingleTrash) { window.spawnSingleTrash(); window.spawnSingleTrash(); window.spawnSingleTrash(); alert('Vehicle passed and generated trash!'); }">Run Vehicle Tour</button>
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