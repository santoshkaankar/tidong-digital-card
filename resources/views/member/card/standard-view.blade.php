@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <div class="mb-4">
        <a href="{{ route('member.cards.index') }}" class="btn btn-dark btn-sm px-4 fw-bold">🔙 Back to Dashboard</a>
    </div>

    <!-- Credit Card Display Box -->
    <div class="d-flex justify-content-center">
        <div class="card text-white p-3 shadow-lg position-relative d-flex flex-column justify-content-between" style="width: 420px; height: 250px; border-radius: 15px; background: linear-gradient(135deg, #1a1e29 0%, #2c3e50 100%);">
            
            <!-- Top Row: Card Holder Name & Real Database Card Number (Restored larger size) -->
            <div class="d-flex justify-content-between align-items-start">
                <div class="text-start" style="width: 75%;">
                    <h4 class="fw-bold mb-1 text-white text-uppercase" style="letter-spacing: 1px; font-size: 21px;">{{ $card->name }}</h4>
                    <div class="badge bg-warning text-dark font-monospace px-3 py-1 fw-bold text-center mt-1" style="font-size: 15px; letter-spacing: 2px; display: inline-block;">
                        {{ $card->card_no }}
                    </div>
                </div>
                <div>
                    @if(isset($card->show_photo) && $card->show_photo == 1 && !empty($card->photo))
                        <img src="{{ asset('storage/' . $card->photo) }}" class="rounded-circle border border-2 border-warning shadow-sm" width="45" height="45" alt="Photo" style="object-fit: cover;">
                    @endif
                </div>
            </div>

            <!-- Middle Section: Business Name & Tagline (Strictly dependent on show toggles == 1) -->
            <div class="text-center my-auto">
                @if(isset($card->show_business) && $card->show_business == 1 && !empty($card->business_name))
                    <h6 class="mb-0 fw-bold text-warning text-truncate" style="font-size: 16px;">{{ $card->business_name }}</h6>
                @endif
                @if(isset($card->show_tagline) && $card->show_tagline == 1 && !empty($card->tagline))
                    <small class="text-muted d-block text-truncate" style="font-size: 11px;">{{ $card->tagline }}</small>
                @endif
            </div>

            <!-- Contact & Action Icons Row (Bigger icon size) -->
            <div class="d-flex justify-content-center align-items-center gap-3 py-2 px-2 mx-auto rounded" style="background: rgba(255, 255, 255, 0.08); max-width: 95%;">
                @if(!empty($card->phone))
                    <a href="tel:{{ $card->phone }}" class="text-warning text-decoration-none px-1" style="font-size: 22px;" title="Call"><i class="fas fa-phone-alt"></i></a>
                @endif

                @if(!empty($card->whatsapp))
                    <a href="https://wa.me/{{ $card->whatsapp }}" target="_blank" class="text-success text-decoration-none px-1" style="font-size: 22px;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                @endif

                @if(!empty($card->gmail))
                    <a href="mailto:{{ $card->gmail }}" class="text-danger text-decoration-none px-1" style="font-size: 22px;" title="Email"><i class="fas fa-envelope"></i></a>
                @endif

                @if(!empty($card->facebook))
                    <a href="{{ $card->facebook }}" target="_blank" class="text-primary text-decoration-none px-1" style="font-size: 22px;" title="Facebook"><i class="fab fa-facebook"></i></a>
                @endif

                @if(!empty($card->instagram))
                    <a href="{{ $card->instagram }}" target="_blank" class="text-danger text-decoration-none px-1" style="font-size: 22px;" title="Instagram"><i class="fab fa-instagram"></i></a>
                @endif

                @if(!empty($card->website_link))
                    <a href="{{ $card->website_link }}" target="_blank" class="text-info text-decoration-none px-1" style="font-size: 22px;" title="Website"><i class="fas fa-globe"></i></a>
                @endif

                @if(!empty($card->map_location_link))
                    <a href="{{ $card->map_location_link }}" target="_blank" class="text-danger text-decoration-none px-1" style="font-size: 22px;" title="Map Location"><i class="fas fa-map-marker-alt"></i></a>
                @endif
            </div>

            <!-- Bottom Row: Address & Powered By Tidong -->
            <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary" style="font-size: 11px;">
                <div class="text-truncate text-muted" style="max-width: 50%;">
                    @if(isset($card->show_address) && $card->show_address == 1)
                        <span>{{ $card->city ?? '' }}{{ (!empty($card->city) && !empty($card->state)) ? ', ' : '' }}{{ $card->state ?? '' }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-light opacity-75" style="font-size: 11px;">Powered by</span> 
                    <a href="https://tidong.in" target="_blank" class="text-warning text-decoration-none fw-bold ms-1" style="font-size: 14px; letter-spacing: 0.5px;">Tidong</a>
                </div>
            </div>

        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4">
        <a href="{{ route('member.card.standard', $card->id) }}" class="btn btn-primary px-4 fw-bold me-2">⚙️ standard Card</a>
        <a href="https://wa.me/?text={{ urlencode(route('member.card.show', $card->id)) }}" target="_blank" class="btn btn-success px-4 fw-bold">🔗 Share on WhatsApp</a>
    </div>
</div>
@endsection