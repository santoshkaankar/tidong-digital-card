@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <div class="card mx-auto shadow-lg p-4" style="max-width: 450px; border: 4px solid #333; font-family: 'Georgia', serif; background: #fffdf9;">
        @if(isset($card->photo))
            <img src="{{ asset('storage/' . $card->photo) }}" class="rounded-circle mx-auto mb-3" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #333;">
        @endif
        <h2 class="fw-bold text-uppercase" style="letter-spacing: 1px;">{{ $card->name }}</h2>
        <p class="text-muted fst-italic mb-2">{{ $card->business_name ?? 'Business Tagline' }}</p>
        <hr style="width: 60%; margin: 15px auto; border-top: 2px solid #333;">
        <p class="mb-1"><strong>📞 Phone:</strong> {{ $card->phone ?? 'N/A' }}</p>
        <p class="mb-1"><strong>📧 Email:</strong> {{ $card->gmail ?? 'N/A' }}</p>
        <p class="mb-0"><strong>📍 Location:</strong> {{ $card->city ?? 'N/A' }}</p>
    </div>
</div>
@endsection