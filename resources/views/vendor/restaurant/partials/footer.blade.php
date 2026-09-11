<footer class="app-footer py-3 px-4 mt-auto border-top bg-white">
    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3 small text-muted">
        <div>
            &copy; 2022 &ndash; {{ date('Y') }} <strong class="text-primary">Tidong Marketing Pvt Ltd</strong>. All rights reserved.
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('pages.about') }}" class="text-muted text-decoration-none">About Us</a>
            <a href="{{ route('pages.terms') }}" class="text-muted text-decoration-none">Terms</a>
            <a href="{{ route('pages.privacy') }}" class="text-muted text-decoration-none">Privacy</a>
            <a href="{{ route('pages.contact') }}" class="text-muted text-decoration-none">Contact</a>
        </div>
    </div>
</footer>

<!-- JS Scripts & Mobile Sidebar Logic -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("restaurantSidebar");
    const backdrop = document.getElementById("restaurantSidebarBackdrop");
    const toggleBtn = document.getElementById("restaurantSidebarToggle");
    const closeBtn = document.getElementById("closeRestaurantSidebar");

    function openSidebar() {
        if (sidebar) sidebar.classList.add("show");
        if (backdrop) backdrop.classList.add("show");
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove("show");
        if (backdrop) backdrop.classList.remove("show");
    }

    if (toggleBtn) toggleBtn.addEventListener("click", openSidebar);
    if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
    if (backdrop) backdrop.addEventListener("click", closeSidebar);
});
</script>