<!-- File Path: resources/views/member/partials/tidong-hub.blade.php -->

<!-- TIDONG SUPER HUB COMPACT STRIP BUTTON -->
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ Route::has('member.hub') ? route('member.hub') : url('/member/tidong-super-hub') }}" 
           class="btn btn-primary w-100 py-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center justify-content-between text-white fw-bold text-decoration-none"
           style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border: none;">
            
            <div class="d-flex align-items-center">
                <i class="bi bi-grid-fill me-2 fs-5"></i>
                <span class="fs-6">Tidong Super Hub</span>
            </div>
            
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fs-7 shadow-sm">
                Tap to explore services <i class="bi bi-arrow-right ms-1"></i>
            </span>
        </a>
    </div>
</div>