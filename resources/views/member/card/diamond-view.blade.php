@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <div class="card mx-auto shadow-lg p-4 text-white" style="max-width: 450px; background: #121212; border-top: 6px solid #00d2ff; border-radius: 8px;">
        @if(isset($card->photo))
            <img src="{{ asset('storage/' . $card->photo) }}" class="rounded-circle mx-auto mb-3 shadow" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #00d2ff;">
        @endif
        <h2 class="fw-bold mb-1" style="color: #00d2ff;">{{ $card->name }}</h2>
        <p class="text-light mb-3">{{ $card->business_name ?? 'Diamond Solutions' }}</p>
        <div class="text-start bg-dark p-3 rounded border border-secondary">
            <p class="mb-1"><strong>📞 Phone:</strong> {{ $card->phone ?? 'N/A' }}</p>
            <p class="mb-1"><strong>💳 UPI ID:</strong> {{ $card->upi_id ?? 'N/A' }}</p>
            <p class="mb-0"><strong>📍 Location:</strong> {{ $card->city ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@endsection