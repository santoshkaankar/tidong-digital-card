<!-- Mobile Categories Horizontal Scroll -->
<div class="d-lg-none mb-3">
    <label class="form-label fw-bold text-dark small mb-2">Categories:</label>
    <div class="nav nav-pills flex-nowrap mobile-category-scroll gap-2" role="tablist">
        <button class="nav-link active text-nowrap rounded-pill px-3 py-1.5 small" data-bs-toggle="pill" data-bs-target="#cat-all">
            <i class="fas fa-utensils me-1"></i> All Items & Thalis
        </button>
        @foreach($categories as $category)
            <button class="nav-link text-nowrap rounded-pill px-3 py-1.5 small" data-bs-toggle="pill" data-bs-target="#cat-{{ $category->id }}">
                {{ $category->name }}
            </button>
        @endforeach
        <button class="nav-link text-nowrap rounded-pill px-3 py-1.5 small" data-bs-toggle="pill" data-bs-target="#cat-tiffin">
            <i class="fas fa-box me-1"></i> Today's Tiffin ({{ $todayDay }})
        </button>
    </div>
</div>

<!-- Desktop Categories Sidebar -->
<div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white d-none d-lg-block">
    <h6 class="fw-bold text-dark mb-3">Categories</h6>
    <div class="nav flex-column nav-pills gap-1" id="v-pills-tab">
        <button class="nav-link active text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-all">
            <i class="fas fa-utensils me-2"></i> All Items & Thalis
        </button>
        @foreach($categories as $category)
            <button class="nav-link text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-{{ $category->id }}">
                {{ $category->name }}
            </button>
        @endforeach
        <button class="nav-link text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-tiffin">
            <i class="fas fa-box me-2"></i> Today's Tiffin ({{ $todayDay }})
        </button>
    </div>
</div>