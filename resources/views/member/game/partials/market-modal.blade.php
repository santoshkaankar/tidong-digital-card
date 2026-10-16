<!-- File Path: resources/views/member/game/partials/market-modal.blade.php -->

<!-- Open Market HUD Button -->
<button type="button" class="btn btn-warning fw-bold rounded-pill position-absolute top-0 start-0 m-3 shadow px-4 py-2" style="z-index: 100000;" data-bs-toggle="modal" data-bs-target="#gameMarketModal">
    🏬 Tidong Super Market & Showroom
</button>

<!-- Market Modal Popup -->
<div class="modal fade" id="gameMarketModal" tabindex="-1" aria-labelledby="gameMarketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-white border-warning shadow-lg">
            <div class="modal-header border-secondary d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-warning" id="gameMarketModalLabel">🛒 Tidong Luxury Market & Branded Showroom</h5>
                <!-- Wallet Balance Display (25000 T-Coins Sync) -->
                <div class="bg-secondary px-3 py-1 rounded-pill text-warning fw-bold border border-warning">
                    💰 Wallet: <span id="gameWalletBalance">25000</span> T-Coins
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Main Mode Switch: BUY vs SELL -->
                <ul class="nav nav-pills mb-3 bg-secondary p-1 rounded-pill justify-content-center" id="marketModeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold rounded-pill px-4" id="buy-mode-tab" data-bs-toggle="pill" data-bs-target="#mode-buy" type="button">🛍️ Buy Items (Market)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold rounded-pill px-4 text-white" id="sell-mode-tab" data-bs-toggle="pill" data-bs-target="#mode-sell" type="button" onclick="loadInventoryForSale()">💰 Sell Items (Inventory)</button>
                    </li>
                </ul>

                <!-- TAB CONTENT CONTAINER -->
                <div class="tab-content" id="marketModeContent">
                    
                    <!-- 1. BUY MARKET SECTION -->
                    <div class="tab-pane fade show active" id="mode-buy" role="tabpanel">
                        
                        <!-- Search Bar -->
                        <div class="mb-3">
                            <input type="text" id="marketSearchInput" class="form-control bg-secondary text-white border-0 rounded-pill px-4" placeholder="🔍 Search Gucci, Nike, Ray-Ban, Rolex, BMW, Thar, Villa..." onkeyup="filterMarketItems()">
                        </div>

                        <!-- Sub-categories for Buy -->
                        <ul class="nav nav-tabs mb-3 border-secondary" id="buySubTabs" role="tablist">
                            <li class="nav-item"><button class="nav-link active btn-sm text-warning fw-bold" data-bs-toggle="tab" data-bs-target="#sub-avatar" type="button">👔 Branded Wear & Accessories</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm text-light" data-bs-toggle="tab" data-bs-target="#sub-cars" type="button">🏎️ Cars & Motors</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm text-light" data-bs-toggle="tab" data-bs-target="#sub-house" type="button">🏠 Houses & Rooms</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm text-light" data-bs-toggle="tab" data-bs-target="#sub-decor" type="button">🛋️ Furniture</button></li>
                        </ul>

                        <div class="tab-content">
                            <!-- BRANDED WEAR & ACCESSORIES SHOWROOM -->
                            <div class="tab-pane fade show active" id="sub-avatar">
                                <div class="row g-3 market-item-list" style="max-height: 420px; overflow-y: auto;">
                                    
                                    <!-- BRANDED SHOES (Nike, Adidas, Puma) -->
                                    <div class="col-md-4 market-card-item" data-name="nike air jordan sneakers shoes sports wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>👟 Nike Air Jordan</h5>
                                            <p class="small text-warning fw-bold mb-2">500 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Nike Air Jordan', 500, 'shoes', 0xdc2626)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 market-card-item" data-name="adidas ultraboost sneakers shoes wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>👟 Adidas Ultraboost</h5>
                                            <p class="small text-warning fw-bold mb-2">400 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Adidas Ultraboost', 400, 'shoes', 0x2563eb)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <!-- BRANDED GLASSES (Ray-Ban, Oakley) -->
                                    <div class="col-md-4 market-card-item" data-name="ray-ban aviator sunglasses specs glasses avatar wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🕶️ Ray-Ban Aviators</h5>
                                            <p class="small text-warning fw-bold mb-2">250 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Ray-Ban Aviators', 250, 'glasses', 0x000000)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 market-card-item" data-name="oakley sports sunglasses specs glasses wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🕶️ Oakley Sports Specs</h5>
                                            <p class="small text-warning fw-bold mb-2">300 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Oakley Sports Specs', 300, 'glasses', 0x06b6d4)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <!-- BRANDED BAGS & LUXURY CLOTHES (Gucci, Louis Vuitton) -->
                                    <div class="col-md-4 market-card-item" data-name="gucci luxury leather handbag backpack bag wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>👜 Gucci Leather Bag</h5>
                                            <p class="small text-warning fw-bold mb-2">2,500 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Gucci Leather Bag', 2500, 'bag', 0x8b4513)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 market-card-item" data-name="gucci designer jacket outfit clothes wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🧥 Gucci Designer Jacket</h5>
                                            <p class="small text-warning fw-bold mb-2">3,000 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Gucci Designer Jacket', 3000, 'clothes', 0x15803d)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 market-card-item" data-name="louis vuitton travel duffle bag luggage wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🧳 Louis Vuitton Duffle</h5>
                                            <p class="small text-warning fw-bold mb-2">4,500 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Louis Vuitton Duffle', 4500, 'bag', 0x78350f)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <!-- LUXURY WATCHES (Rolex, Apple Watch) -->
                                    <div class="col-md-4 market-card-item" data-name="apple watch ultra smartwatch wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>⌚ Apple Watch Ultra</h5>
                                            <p class="small text-warning fw-bold mb-2">1,200 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Apple Watch Ultra', 1200, 'watch', 0xf97316)">Buy & Wear</button>
                                        </div>
                                    </div>

                                    <div class="col-md-4 market-card-item" data-name="rolex submariner gold luxury watch wear">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>⌚ Rolex Submariner Gold</h5>
                                            <p class="small text-warning fw-bold mb-2">8,000 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Rolex Submariner', 8000, 'watch', 0xeab308)">Buy & Wear</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- CARS & MOTORS SHOWROOM -->
                            <div class="tab-pane fade" id="sub-cars">
                                <div class="row g-3 market-item-list" style="max-height: 420px; overflow-y: auto;">
                                    <div class="col-md-4 market-card-item" data-name="bicycle cycle eco budget vehicle">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🚲 Eco City Bicycle</h5>
                                            <p class="small text-warning fw-bold mb-2">15 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Eco Bicycle', 15, 'car', 0x00ff00)">Buy & Ride</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 market-card-item" data-name="mahindra thar 4x4 offroad suv vehicle">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🛻 Mahindra Thar 4x4</h5>
                                            <p class="small text-warning fw-bold mb-2">600 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Mahindra Thar', 600, 'car', 0x111111)">Buy & Drive</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 market-card-item" data-name="bmw m5 cs sedan vehicle motors">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🏎️ BMW M5 CS</h5>
                                            <p class="small text-warning fw-bold mb-2">6,000 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('BMW M5', 6000, 'car', 0x3b82f6)">Buy & Drive</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 market-card-item" data-name="lamborghini aventador svj supercar vehicle">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3 h-100">
                                            <h5>🏎️ Lamborghini Aventador</h5>
                                            <p class="small text-warning fw-bold mb-2">18,000 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill mt-auto" onclick="buyItem('Lamborghini Aventador', 18000, 'car', 0xeab308)">Buy & Drive</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- HOUSES -->
                            <div class="tab-pane fade" id="sub-house">
                                <div class="row g-3 market-item-list">
                                    <div class="col-md-6 market-card-item" data-name="modern luxury villa house room">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                            <h5>🏡 Modern Luxury Villa</h5>
                                            <p class="small text-warning fw-bold mb-2">2,500 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyItem('Modern Villa', 2500, 'house', 0xf5f5dc)">Buy House</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FURNITURE -->
                            <div class="tab-pane fade" id="sub-decor">
                                <div class="row g-3 market-item-list">
                                    <div class="col-md-4 market-card-item" data-name="royal sofa couch furniture decor">
                                        <div class="card bg-secondary text-white text-center p-3 border-0 rounded-3">
                                            <h5>🛋️ Royal Sofa</h5>
                                            <p class="small text-warning fw-bold mb-2">150 T-Coins</p>
                                            <button class="btn btn-sm btn-warning fw-bold rounded-pill" onclick="buyItem('Royal Sofa', 150, 'sofa', 0x990000)">Buy Furniture</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- 2. SELL INVENTORY SECTION -->
                    <div class="tab-pane fade" id="mode-sell" role="tabpanel">
                        <div class="p-3 bg-secondary rounded-3">
                            <h6 class="text-warning mb-3">📦 Your Inventory Items (Click to Sell)</h6>
                            <div id="userInventoryList" class="row g-3" style="max-height: 400px; overflow-y: auto;">
                                <p class="text-light small">Loading your owned items...</p>
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
    // Global User Wallet & Inventory
    window.gameWallet = 25000;
    window.userInventory = [];

    function updateWalletUI() {
        const walletEl = document.getElementById('gameWalletBalance');
        if (walletEl) walletEl.innerText = window.gameWallet;
    }

    function filterMarketItems() {
        const query = document.getElementById('marketSearchInput').value.toLowerCase();
        const items = document.querySelectorAll('.market-card-item');
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            item.style.display = name.includes(query) ? 'block' : 'none';
        });
    }

    window.buyItem = function(itemName, price, type, colorHex) {
        if (window.gameWallet < price) {
            alert("❌ Insufficient T-Coins in your wallet! Clean debris or sell items to earn more.");
            return;
        }

        if (confirm(`Do you want to buy "${itemName}" for ${price} T-Coins?`)) {
            window.gameWallet -= price;
            updateWalletUI();

            window.userInventory.push({
                id: Date.now(),
                name: itemName,
                sellPrice: Math.floor(price * 0.7),
                type: type,
                color: colorHex
            });

            if (window.buyAndSpawnItem) {
                window.buyAndSpawnItem(type, colorHex);
            }

            alert(`✅ Congratulations! You bought ${itemName}.`);
        }
    };

    window.loadInventoryForSale = function() {
        const container = document.getElementById('userInventoryList');
        if (!container) return;

        if (window.userInventory.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-light py-4">Your inventory is currently empty! Buy items from the showroom.</div>';
            return;
        }

        let html = '';
        window.userInventory.forEach((item, index) => {
            html += `
                <div class="col-md-4">
                    <div class="card bg-dark text-white border border-warning p-3 text-center rounded-3">
                        <h6>📦 ${item.name}</h6>
                        <p class="small text-success fw-bold mb-2">Sell Price: +${item.sellPrice} T-Coins</p>
                        <button class="btn btn-sm btn-danger fw-bold rounded-pill" onclick="confirmAndSellItem(${index})">Sell Item</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    };

    window.confirmAndSellItem = function(index) {
        const item = window.userInventory[index];
        if (confirm(`❓ Kya aap "${item.name}" ko ${item.sellPrice} T-Coins mein bechna chahte hain?`)) {
            window.gameWallet += item.sellPrice;
            updateWalletUI();

            window.userInventory.splice(index, 1);
            loadInventoryForSale();
            alert(`✅ ${item.name} sold! +${item.sellPrice} T-Coins added to your wallet.`);
        }
    };

    document.addEventListener("DOMContentLoaded", function() {
        updateWalletUI();
    });
</script>