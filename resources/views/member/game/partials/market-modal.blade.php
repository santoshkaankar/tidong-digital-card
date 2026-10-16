<!-- File Path: resources/views/member/game/partials/market-modal.blade.php -->
<button type="button" class="btn btn-warning fw-bold rounded-pill position-absolute top-0 start-0 m-3 shadow px-4 py-2" style="z-index: 100000;" data-bs-toggle="modal" data-bs-target="#gameMarketModal">
    🏬 Tidong Super Market & Wallet
</button>

<div class="modal fade" id="gameMarketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-white border-warning shadow-lg">
            <div class="modal-header border-secondary d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-warning">🛒 Tidong Database Market</h5>
                <div class="bg-secondary px-3 py-1 rounded-pill text-warning fw-bold border border-warning">
                    💰 Wallet: <span id="gameWalletBalance">25000</span> T-Coins
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <ul class="nav nav-pills mb-3 bg-secondary p-1 rounded-pill justify-content-center" role="tablist">
                    <li class="nav-item"><button class="nav-link active fw-bold rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#mode-buy" type="button">🛍️ Buy Market</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold rounded-pill px-4 text-white" data-bs-toggle="pill" data-bs-target="#mode-sell" type="button" onclick="loadInventoryForSale()">💰 Sell Inventory</button></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="mode-buy" role="tabpanel">
                        
                        <div class="mb-3">
                            <input type="text" id="marketSearchInput" class="form-control bg-secondary text-white border-0 rounded-pill px-4" placeholder="🔍 Search Phones, Cars, Nike, Grocery..." onkeyup="filterMarketItems()">
                        </div>

                        <ul class="nav nav-tabs mb-3 border-secondary" role="tablist">
                            <li class="nav-item"><button class="nav-link active btn-sm text-warning fw-bold" data-bs-toggle="tab" data-bs-target="#sub-phones" type="button">📱 Phones</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm text-light" data-bs-toggle="tab" data-bs-target="#sub-cars" type="button">🏎️ Cars</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm text-light" data-bs-toggle="tab" data-bs-target="#sub-clothing" type="button">👔 Clothing</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm text-light" data-bs-toggle="tab" data-bs-target="#sub-grocery" type="button">🍎 Grocery</button></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="sub-phones">@include('member.game.partials.market-phones')</div>
                            <div class="tab-pane fade" id="sub-cars">@include('member.game.partials.market-cars')</div>
                            <div class="tab-pane fade" id="sub-clothing">@include('member.game.partials.market-clothing')</div>
                            <div class="tab-pane fade" id="sub-grocery">@include('member.game.partials.market-grocery')</div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="mode-sell" role="tabpanel">
                        <div class="p-3 bg-secondary rounded-3">
                            <h6 class="text-warning mb-3">📦 Your Database Inventory (Click to Sell)</h6>
                            <div id="userInventoryList" class="row g-3" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-light small">Loading inventory...</p>
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