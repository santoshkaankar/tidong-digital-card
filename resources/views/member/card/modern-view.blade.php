@extends('layouts.app')

@section('content')
<div class="container py-4 text-center">
    <!-- Card Container -->
    <div class="card mx-auto shadow-lg border-0 overflow-hidden position-relative" style="max-width: 450px; border-radius: 20px; background-color: #ffffff;">
        
        <!-- Main Content Wrapper -->
        <div style="position: relative; z-index: 2;">
            
            <!-- Modern Header Banner with Blue Background -->
            <div class="bg-primary pt-4 pb-4 text-white position-relative overflow-hidden" style="min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                
                <!-- Vasudhaiv Kutumbakam Watermark -->
                <div style="position: absolute; top: 12px; left: 0; width: 100%; font-size: 28px; font-weight: 900; color: rgba(255, 255, 255, 0.18); letter-spacing: 6px; text-transform: uppercase; pointer-events: none; z-index: 1; white-space: nowrap;">
                    वसुधैव कुटुम्बकम्
                </div>

                @if(isset($card->photo))
                    <img src="{{ asset('storage/' . $card->photo) }}" class="rounded-circle shadow mx-auto" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff; position: relative; z-index: 2;">
                @else
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center mx-auto shadow" style="width: 90px; height: 90px; font-size: 35px; font-weight: bold; position: relative; z-index: 2;">
                        {{ strtoupper(substr($card->name ?? 'M', 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- White Card Body Area -->
            <div class="card-body px-4 py-4 position-relative" style="background-color: #ffffff;">
                
                <!-- Gandhi Ji Watermark: Opacity kam karke 0.10 ki hai aur mix-blend-mode se background boxes (checkered background) puri tarah hide ho jayenge -->
                <div style="position: absolute; top: -10px; left: 0; width: 100%; height: 100%; background-image: url('{{ asset('gandhiji.png') }}'); background-repeat: no-repeat; background-position: center 30%; background-size: 310px; opacity: 0.10; mix-blend-mode: multiply; pointer-events: none; z-index: 1;"></div>

                <!-- Text & Content (Z-index high rakha hai taaki text clear rahe) -->
                <div style="position: relative; z-index: 2;">
                    <h2 class="fw-bold text-dark mb-1" style="font-size: 28px;">{{ $card->name }}</h2>
                    <p class="text-primary fw-semibold mb-3" style="font-size: 16px;">{{ $card->business_name ?? 'Modern Enterprise' }}</p>

                    <!-- Contact Box with slight transparency -->
                    <div class="p-3 rounded shadow-sm text-start" style="background-color: rgba(255, 255, 255, 0.90);">
                        <p class="mb-2"><a href="tel:{{ $card->phone }}" class="text-secondary text-decoration-none"><strong>📞 Phone:</strong> {{ $card->phone }}</a></p>
                        <p class="mb-2"><a href="https://wa.me/{{ $card->whatsapp }}" target="_blank" class="text-secondary text-decoration-none"><strong>💬 WhatsApp:</strong> {{ $card->whatsapp }}</a></p>
                        <p class="mb-2"><a href="mailto:{{ $card->gmail }}" class="text-secondary text-decoration-none"><strong>📧 Email:</strong> {{ $card->gmail }}</a></p>
                        <p class="mb-0"><a href="https://maps.google.com/?q={{ urlencode($card->city) }}" target="_blank" class="text-secondary text-decoration-none"><strong>📍 Location:</strong> {{ $card->city }}</a></p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="https://tidong.in" target="_blank" style="text-decoration: none; color: #777; font-weight: 500;">
                            Powered by <span style="color: #0d6efd; font-weight: 800; font-size: 16px;">Tidong</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection