<div class="d-inline-flex align-items-center gap-2 {{ $class ?? '' }}">
    <!-- Icon Component Include -->
    @include('partials.logos.icon', ['width' => $iconWidth ?? '30', 'height' => $iconHeight ?? '32'])
    
    <!-- Tidong Text & Registered Symbol -->
    <span class="fw-bold fs-4 tracking-tight d-inline-flex align-items-start" style="color: var(--text-main); font-family: 'Plus Jakarta Sans', sans-serif;">
        tidong
        <span style="font-size: 0.55em; font-weight: 600; margin-left: 2px; position: relative; top: -2px;">®</span>
    </span>
</div>