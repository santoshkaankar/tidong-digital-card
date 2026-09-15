<nav class="top-navbar">
    <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
        <i class="fas fa-bars"></i>
    </button>
    <div class="navbar-brand fw-bold text-dark mb-0 h6 d-flex align-items-center gap-2">
        <i class="fas fa-id-card text-primary"></i> Digital Visiting Card Configuration
        @if(isset($card) && $card->card_no)
            <span class="badge bg-success ms-2">Card No: {{ $card->card_no }}</span>
        @endif
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa-home me-1"></i> Dashboard
        </a>
    </div>
</nav>