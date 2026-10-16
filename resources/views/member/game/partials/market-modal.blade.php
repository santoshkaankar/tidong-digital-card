<!-- File Path: resources/views/member/game/partials/market-modal.blade.php -->
<div class="modal fade" id="marketModal" tabindex="-1" aria-labelledby="marketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-secondary shadow-lg">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="marketModalLabel"><i class="fas fa-store text-info me-2"></i>Tidong City Showroom & Market</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Item 1: Premium Outfit -->
                    <div class="col-md-4">
                        <div class="card bg-secondary text-white border-0 h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div>
                                    <i class="fas fa-tshirt fa-3x text-info mb-3"></i>
                                    <h6 class="fw-bold">Cyber Outfit</h6>
                                    <p class="text-light small">Changes character outfit color instantly.</p>
                                </div>
                                <button class="btn btn-info btn-sm fw-bold text-dark w-100 mt-2" onclick="buyItem(1500, 0x10b981)">
                                    Buy for 1,500 T-Coins
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2: Sports Car -->
                    <div class="col-md-4">
                        <div class="card bg-secondary text-white border-0 h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div>
                                    <i class="fas fa-car fa-3x text-warning mb-3"></i>
                                    <h6 class="fw-bold">Tidong GT Car</h6>
                                    <p class="text-light small">Unlock vehicle driving mode on infinite roads.</p>
                                </div>
                                <button class="btn btn-warning btn-sm fw-bold text-dark w-100 mt-2" onclick="buyItem(10000, 0xf59e0b)">
                                    Buy for 10,000 T-Coins
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3: Luxury Gadget -->
                    <div class="col-md-4">
                        <div class="card bg-secondary text-white border-0 h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-between">
                                <div>
                                    <i class="fas fa-laptop-code fa-3x text-success mb-3"></i>
                                    <h6 class="fw-bold">Dev Gadget Pro</h6>
                                    <p class="text-light small">Boosts earnings & digital card status.</p>
                                </div>
                                <button class="btn btn-success btn-sm fw-bold text-white w-100 mt-2" onclick="buyItem(5000, 0x3b82f6)">
                                    Buy for 5,000 T-Coins
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCoins = 25400;

    function buyItem(price, colorHex) {
        if (currentCoins >= price) {
            currentCoins -= price;
            document.getElementById('wallet-balance').innerText = currentCoins.toLocaleString();
            updatePlayerOutfit(colorHex);
            alert('Purchase successful! Item equipped.');
        } else {
            alert('Insufficient T-Coins in your wallet!');
        }
    }
</script>