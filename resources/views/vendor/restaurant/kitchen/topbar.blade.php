<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <!-- Mobile Sidebar Toggle Button -->
        <button type="button" class="btn btn-light d-lg-none rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;" onclick="document.getElementById('restaurantSidebar').classList.toggle('show'); document.getElementById('sidebarBackdrop').classList.toggle('show');">
            <i class="bi bi-list fs-4"></i>
        </button>

        <!-- Back Button -->
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-circle p-2 d-none d-sm-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>

        <div>
            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Live Kitchen Feed</span>
            <h3 class="fw-bold mb-0" style="color: var(--text-main);">Kitchen Display System (KDS)</h3>
            <p class="text-muted small mb-0 d-none d-sm-block">Manage live active orders and update order prep status.</p>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <!-- In-Header Search Bar -->
        <div class="position-relative me-1" style="min-width: 180px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
            <input type="text" class="form-control form-control-sm ps-4" id="kitchenOrderSearch" placeholder="Search KDS orders..." onkeyup="filterKitchenOrders()">
        </div>

        <!-- Global Language Switcher Component -->
        @include('vendor.restaurant.partials.language_switcher')

        <!-- Test Voice Button -->
        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" onclick="testVoice()">
            <i class="bi bi-volume-up-fill"></i> <span class="d-none d-md-inline">Test Voice</span>
        </button>

        <!-- Functional Refresh Button -->
        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> <span class="d-none d-md-inline">Refresh</span>
        </button>
    </div>
</div>

<!-- Simple JavaScript for Instant Local Order Search -->
<script>
function filterKitchenOrders() {
    let input = document.getElementById('kitchenOrderSearch').value.toLowerCase();
    let cards = document.querySelectorAll('.order-card, .kitchen-order-item'); // Adjust class name according to your order cards container
    
    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        if (text.includes(input)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    });
}
</script>