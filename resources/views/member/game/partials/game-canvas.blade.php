<!-- File Path: resources/views/member/game/partials/game-canvas.blade.php -->
<div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-warning mb-1">
                <i class="fas fa-cube me-2"></i> Tidong 3D Game Pro World - Vast Field
            </h4>
            <p class="text-muted small mb-0">Continuous Pro World — Open ground environment active.</p>
        </div>
        <div>
            <span class="badge bg-success px-3 py-2 rounded-pill">
                <i class="fas fa-shield-alt me-1"></i> Pro Pass Active
            </span>
        </div>
    </div>

    <!-- Include the Ground / Field Part -->
    @include('member.game.partials.vast-ground')

    <!-- Game Controls / Toolbar -->
    <div class="row mt-3 g-3">
        <div class="col-md-8">
            <div class="d-flex gap-2">
                <button class="btn btn-warning text-dark fw-bold px-4 rounded-pill shadow-sm">
                    <i class="fas fa-arrows-alt me-1"></i> Explore Field
                </button>
                <button class="btn btn-outline-light fw-bold px-4 rounded-pill">
                    <i class="fas fa-camera me-1"></i> Reset Camera
                </button>
            </div>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="text-muted small">Environment: <strong class="text-success">Open Grassland Field</strong></span>
        </div>
    </div>
</div>