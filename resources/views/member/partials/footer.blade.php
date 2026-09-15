<!-- File Path: resources/views/member/partials/footer.blade.php -->

<footer class="app-footer py-3 px-4 mt-auto border-top" style="background: var(--card-bg); border-color: var(--border-color) !important;">
    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        
        <!-- Left: Pure SVG Full Tidong Logo -->
        <div class="d-flex align-items-center gap-2 text-muted small">
            @if(View::exists('partials.logos.full'))
                @include('partials.logos.full', ['iconWidth' => '28', 'iconHeight' => '30'])
            @endif
            
            <span class="ms-2">
                &copy; 2022 &ndash; {{ date('Y') }} 
                <strong style="color: var(--primary-color);">Tidong Marketing Pvt Ltd</strong>. All rights reserved.
            </span>
        </div>

        <!-- Right: Links & Feedback Button -->
        <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 small">
            <a href="{{ route('pages.about') }}" class="text-muted text-decoration-none hover-primary">About Us</a>
            <span class="text-muted opacity-50">&bull;</span>

            <a href="{{ route('pages.terms') }}" class="text-muted text-decoration-none hover-primary">Terms & Conditions</a>
            <span class="text-muted opacity-50">&bull;</span>

            <a href="{{ route('pages.privacy') }}" class="text-muted text-decoration-none hover-primary">Privacy Policy</a>
            <span class="text-muted opacity-50">&bull;</span>

            <a href="{{ route('pages.contact') }}" class="text-muted text-decoration-none hover-primary">Contact Us</a>
            <span class="text-muted opacity-50">&bull;</span>

            <button type="button" class="btn btn-link p-0 text-muted text-decoration-none hover-primary small border-0" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                <i class="bi bi-star-fill text-warning me-1"></i> Rate Us / Suggestion
            </button>
        </div>
    </div>
</footer>