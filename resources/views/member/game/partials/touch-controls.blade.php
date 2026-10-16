<!-- File Path: resources/views/member/game/partials/touch-controls.blade.php -->
<div class="card bg-dark border-secondary mt-3 shadow-sm">
    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        
        <!-- Directional Controls -->
        <div class="btn-group shadow-sm" role="group">
            <button type="button" class="btn btn-outline-info px-3 py-2 fw-bold" onclick="movePlayer('left')"><i class="fas fa-chevron-left"></i></button>
            <button type="button" class="btn btn-outline-info px-3 py-2 fw-bold" onclick="movePlayer('forward')"><i class="fas fa-chevron-up"></i> Up</button>
            <button type="button" class="btn btn-outline-info px-3 py-2 fw-bold" onclick="movePlayer('backward')"><i class="fas fa-chevron-down"></i> Down</button>
            <button type="button" class="btn btn-outline-info px-3 py-2 fw-bold" onclick="movePlayer('right')"><i class="fas fa-chevron-right"></i></button>
        </div>

        <!-- Action / Showroom Button -->
        <div class="d-flex gap-2">
            <button class="btn btn-warning fw-bold px-3 py-2 shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#marketModal">
                <i class="fas fa-shopping-bag me-1"></i> Open Store
            </button>
        </div>

    </div>
</div>