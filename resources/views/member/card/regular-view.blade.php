@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="text-center mb-3">
        <a href="{{ route('member.cards.index') }}" class="btn btn-dark btn-sm">← Back to Dashboard</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <!-- Realistic ATM/Credit Card Box -->
            <div id="visitingCard" class="card text-white shadow-lg p-3 rounded-4 position-relative overflow-hidden mx-auto" style="width: 100%; max-width: 380px; height: 220px; background: linear-gradient(135deg, #1f2937 0%, #111827 50%, #030712 100%); border: 1px solid rgba(255,255,255,0.15);">
                
                <!-- Top Row: Tidong Brand, Contactless Symbol & Profile Photo -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <a href="https://tidong.in" target="_blank" class="text-decoration-none text-warning fw-bold" style="font-size: 16px; letter-spacing: 1px;">
                            Tidong 🚀
                        </a>
                        <span class="ms-2 text-secondary" style="font-size: 12px;" title="Contactless">📶</span>
                    </div>

                    @if($card->photo)
                        <img src="{{ asset('storage/' . $card->photo) }}" alt="Photo" class="rounded-circle border border-light shadow-sm" style="width: 38px; height: 38px; object-fit: cover;">
                    @endif
                </div>

                <!-- EMV Chip & Card Holder Name -->
                <div class="d-flex align-items-center my-1">
                    <!-- Simulated ATM Chip -->
                    <div class="rounded bg-warning bg-opacity-75 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 28px; border: 1px solid #b45309;">
                        <div style="width: 100%; height: 1px; background: #78350f;"></div>
                    </div>
                    <div>
                        <div class="text-secondary text-uppercase" style="font-size: 9px; letter-spacing: 1px;">CARD HOLDER</div>
                        <h6 class="fw-bold text-white mb-0 text-truncate" style="max-width: 220px; font-size: 14px;">{{ $card->name }}</h6>
                    </div>
                </div>

                <!-- Middle Section: Unique Card Number -->
                <div class="my-2 text-center">
                    <div class="text-warning fw-bold" style="font-size: 18px; letter-spacing: 2.5px; font-family: monospace;">
                        {{ $card->card_no }}
                    </div>
                </div>

                <!-- Bottom Section: City & Action Icons -->
                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top border-secondary border-opacity-25">
                    <small class="text-secondary text-uppercase" style="font-size: 10px; letter-spacing: 1px;">{{ $card->city ?? 'INDIA' }}</small>

                    <!-- Action Icons -->
                    <div class="d-flex gap-1 align-items-center">
                        <a href="tel:{{ $card->phone }}" class="text-white bg-primary rounded-circle d-flex align-items-center justify-content-center text-decoration-none shadow-sm" style="width: 26px; height: 26px;" title="Call: {{ $card->phone }}">
                            <span style="font-size: 10px;">📞</span>
                        </a>
                        <a href="https://wa.me/{{ $card->whatsapp ?? $card->phone }}" target="_blank" class="text-white bg-success rounded-circle d-flex align-items-center justify-content-center text-decoration-none shadow-sm" style="width: 26px; height: 26px;" title="WhatsApp">
                            <span style="font-size: 10px;">💬</span>
                        </a>
                        <a href="mailto:{{ $card->gmail }}" class="text-white bg-danger rounded-circle d-flex align-items-center justify-content-center text-decoration-none shadow-sm" style="width: 26px; height: 26px;" title="Email: {{ $card->gmail }}">
                            <span style="font-size: 10px;">📧</span>
                        </a>
                        <a href="{{ $card->map_location_link ?? '#' }}" target="_blank" class="text-white bg-warning rounded-circle d-flex align-items-center justify-content-center text-decoration-none shadow-sm" style="width: 26px; height: 26px;" title="Location">
                            <span style="font-size: 10px;">📍</span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Download & Share Buttons -->
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button onclick="window.print()" class="btn btn-outline-primary btn-sm px-4 fw-bold">
                    📥 Download / Print Card
                </button>
                <a href="https://api.whatsapp.com/send?text={{ urlencode('Check out my Digital Card - Unique No: ' . $card->card_no . ' | View here: ' . url()->current()) }}" target="_blank" class="btn btn-success btn-sm px-4 fw-bold">
                    🔗 Share on WhatsApp
                </a>
            </div>

        </div>
    </div>
</div>
@endsection