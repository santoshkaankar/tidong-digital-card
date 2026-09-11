<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-arrow-left fs-5"></i>
        </a>
        <div>
            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Live Kitchen Feed</span>
            <h3 class="fw-bold mb-0" style="color: var(--text-main);">Kitchen Display System (KDS)</h3>
            <p class="text-muted small mb-0">Manage live active orders and update order prep status.</p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <!-- Global Language Switcher Component -->
        @include('partials.language_switcher')

        <!-- Test Voice Button -->
        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" onclick="testVoice()">
            <i class="bi bi-volume-up-fill"></i> Test Voice
        </button>

        <!-- Functional Refresh Button -->
        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1" onclick="window.location.reload();">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>