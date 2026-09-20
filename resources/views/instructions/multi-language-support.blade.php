@extends('layouts.app')

@section('content')
@include('partials.seo-schema')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="display-4 text-primary mb-3"><i class="fas fa-language"></i></div>
                        <h2 class="fw-bold text-dark">Multi-Language Support</h2>
                        <p class="text-muted">Facility to explore the platform in different languages.</p>
                    </div>
                    <hr class="my-4">
                    <div class="content-body text-secondary lh-lg">
                        <h5 class="fw-bold text-dark mb-3">Step-by-Step Guide</h5>
                        <p>Yahan par aap is topic se related poori detail aur steps add kar sakte hain. Yeh ek default template hai jise aap baad me apne hisab se update kar lenge.</p>
                        <ol class="ps-3">
                            <li class="mb-2">Portal par visit karein aur apne account me login karein.</li>
                            <li class="mb-2">Apne dashboard ya relevant section par navigate karein.</li>
                            <li class="mb-2">Diye gaye instructions ko follow karte hue process complete karein.</li>
                        </ol>
                    </div>
                    <div class="mt-5 text-center">
                        <a href="{{ route('login') }}" class="btn btn-primary px-4 rounded-pill">
                            <i class="fas fa-arrow-right me-2"></i> Proceed / Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection