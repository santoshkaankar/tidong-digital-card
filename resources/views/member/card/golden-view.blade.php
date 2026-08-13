@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <div class="card mx-auto shadow-lg p-4 text-dark" style="max-width: 450px; background: linear-gradient(135deg, #f7e98e, #d4af37, #aa771c); border-radius: 15px;">
        @if(isset($card->photo))
            <img src="{{ asset('storage/' . $card->photo) }}" class="rounded-circle mx-auto mb-3 shadow" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
        @endif
        <h2 class="fw-bold text-uppercase mb-1" style="text-shadow: 1px 1px 2px rgba(255,255,255,0.6);">{{ $card->name }}</h2>
        <h5 class="fw-light mb-3 text-dark">{{ $card->business_name ?? 'Luxury Enterprise' }}</h5>
        <hr style="border-top: 1px solid #443300; width: 50%; margin: 10px auto;">
        <p class="mb-1"><strong>📞 Phone:</strong> {{ $card->phone ?? 'N/A' }}</p>
        <p class="mb-1"><strong>💬 WhatsApp:</strong> {{ $card->whatsapp ?? 'N/A' }}</p>
        <p class="mb-0"><strong>🌐 Website:</strong> {{ $card->website_link ?? 'N/A' }}</p>
    </div>
</div>
@endsection