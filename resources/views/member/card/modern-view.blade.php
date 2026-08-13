@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <div class="card mx-auto shadow-lg border-0 overflow-hidden" style="max-width: 450px; border-radius: 20px;">
        <!-- Modern Header Banner -->
        <div class="bg-primary py-4 text-white position-relative">
            @if(isset($card->photo))
                <img src="{{ asset('storage/' . $card->photo) }}" class="rounded-circle shadow mx-auto mt-2" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
            @else
                <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mx-auto shadow" style="width: 90px; height: 90px; font-size: 35px; font-weight: bold;">
                    {{ strtoupper(substr($card->name ?? 'M', 0, 1)) }}
                </div>
            @endif
        </div>

        <div class="card-body bg-light px-4 py-4">
            <h3 class="fw-bold text-dark mb-1">{{ $card->name }}</h3>
            <p class="text-primary fw-semibold mb-3">{{ $card->business_name ?? 'Modern Enterprise' }}</p>

            <div class="bg-white p-3 rounded shadow-sm text-start">
                <p class="mb-2 text-secondary"><strong>📞 Phone:</strong> {{ $card->phone ?? 'N/A' }}</p>
                <p class="mb-2 text-secondary"><strong>💬 WhatsApp:</strong> {{ $card->whatsapp ?? 'N/A' }}</p>
                <p class="mb-2 text-secondary"><strong>📧 Email:</strong> {{ $card->gmail ?? 'N/A' }}</p>
                <p class="mb-0 text-secondary"><strong>📍 Location:</strong> {{ $card->city ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection