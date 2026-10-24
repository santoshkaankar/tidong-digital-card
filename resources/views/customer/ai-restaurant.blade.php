@extends('customer.layout')
@section('title', __('TIDONG AI'))
@section('content')
<section class="mx-auto max-w-4xl space-y-5" aria-labelledby="food-ai-title">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <span class="text-xs font-bold uppercase tracking-wider text-rose-700">{{ __('TIDONG AI') }}</span>
        <h1 id="food-ai-title" class="mt-2 text-2xl font-bold text-slate-900">{{ __('बोलकर रेस्टोरेंट और मेन्यू पूछें') }}</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('हिन्दी में बोलिए या लिखिए। लोकेशन केवल आपकी अनुमति से मिलेगी। अभी यह रेस्टोरेंट और मेन्यू की जानकारी देता है; अपने आप ऑर्डर या भुगतान नहीं करता।') }}</p>
        <form method="POST" action="{{ route('customer.ai-restaurant.ask') }}" id="food-ai-form" class="mt-5 space-y-3">
            @csrf
            <input type="hidden" name="latitude" id="food-ai-lat" value="{{ old('latitude', $latitude) }}">
            <input type="hidden" name="longitude" id="food-ai-lng" value="{{ old('longitude', $longitude) }}">
            <label class="block text-sm font-semibold text-slate-800" for="food-ai-message">{{ __('आप क्या खाना चाहते हैं?') }}</label>
            <textarea id="food-ai-message" name="message" maxlength="400" minlength="2" rows="3" required
                class="w-full rounded-xl border border-slate-300 p-3 text-base focus:border-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-200"
                placeholder="उदाहरण: मेरे पास पनीर और रोटी किस रेस्टोरेंट में मिलेगी?">{{ old('message', $message) }}</textarea>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="food-ai-location" class="min-h-11 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900">{{ __('📍 मेरी लोकेशन लें') }}</button>
                <button type="button" id="food-ai-mic" class="min-h-11 rounded-xl border border-rose-300 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-800">{{ __('🎙️ बोलकर पूछें') }}</button>
                <button type="submit" class="min-h-11 rounded-xl bg-rose-700 px-5 py-2 font-semibold text-white hover:bg-rose-800">{{ __('जवाब पाएँ') }}</button>
            </div>
            <p id="food-ai-status" class="text-sm text-slate-600" aria-live="polite">{{ $latitude !== null ? __('लोकेशन चुनी गई है।') : __('लोकेशन अभी साझा नहीं की गई है।') }}</p>
        </form>
    </div>
    @if($answer !== null)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-live="polite">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold">{{ __('TIDONG AI का जवाब') }}</h2>
                <button type="button" id="food-ai-speak" class="min-h-11 rounded-lg border border-slate-300 px-3 text-sm font-semibold">{{ __('🔊 जवाब सुनें') }}</button>
            </div>
            <p id="food-ai-answer" class="mt-3 whitespace-pre-wrap text-base leading-7">{{ $answer }}</p>
        </div>
    @endif
    @if(count($restaurants))
        <h2 class="text-xl font-bold text-slate-900">{{ __('आपकी लोकेशन के पास उपलब्ध रेस्टोरेंट') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($restaurants as $restaurant)
                <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="font-bold text-slate-900">{{ $restaurant['restaurant'] }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $restaurant['outlet'] }} · {{ $restaurant['distance_km'] }} km</p>
                    <div class="mt-3 space-y-1.5">
                        @forelse($restaurant['menu'] as $item)
                            <div class="flex justify-between gap-2 border-b border-slate-100 py-1 text-sm"><span>{{ $item['name'] }}</span><strong class="whitespace-nowrap">₹{{ $item['price_rupees'] }}</strong></div>
                        @empty
                            <p class="text-sm text-slate-500">{{ __('अभी मेन्यू प्रकाशित नहीं है।') }}</p>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </div>
    @endif
    <p class="text-sm text-slate-600">{{ __('ऑर्डर करने से पहले उपलब्धता, कुल राशि, डिलीवरी और भुगतान अलग Checkout में पुष्टि करना आवश्यक होगा।') }}</p>
</section>
<script>
(() => {
    'use strict';
    const status = document.getElementById('food-ai-status');
    const message = document.getElementById('food-ai-message');
    const lat = document.getElementById('food-ai-lat');
    const lng = document.getElementById('food-ai-lng');
    const locationButton = document.getElementById('food-ai-location');
    locationButton.addEventListener('click', () => {
        if (!navigator.geolocation) { status.textContent = 'यह Browser लोकेशन नहीं दे सकता।'; return; }
        status.textContent = 'लोकेशन की अनुमति माँगी जा रही है…';
        navigator.geolocation.getCurrentPosition(
            pos => { lat.value = pos.coords.latitude; lng.value = pos.coords.longitude; status.textContent = 'लोकेशन मिल गई। अब पूछ सकते हैं।'; },
            () => { lat.value = ''; lng.value = ''; status.textContent = 'लोकेशन की अनुमति नहीं मिली।'; },
            { enableHighAccuracy: false, timeout: 12000, maximumAge: 60000 }
        );
    });
    const VoiceRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    const mic = document.getElementById('food-ai-mic');
    if (!VoiceRecognition) {
        mic.disabled = true;
        mic.textContent = '🎙️ इस Browser में उपलब्ध नहीं';
        mic.title = 'लिखकर पूछना उपलब्ध है';
    } else {
        mic.addEventListener('click', () => {
            const recognition = new VoiceRecognition();
            recognition.lang = 'hi-IN';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;
            recognition.onresult = e => { message.value = e.results[0][0].transcript; status.textContent = 'बात समझने के लिए लिखा गया है। जाँचकर जवाब पाएँ दबाएँ।'; };
            recognition.onerror = () => { status.textContent = 'आवाज़ साफ नहीं मिली। कृपया लिखकर पूछें।'; };
            try { recognition.start(); status.textContent = 'बोलिए, मैं सुन रहा हूँ…'; } catch (_) { status.textContent = 'माइक्रोफोन शुरू नहीं हुआ।'; }
        });
    }
    const speak = document.getElementById('food-ai-speak');
    if (speak) speak.addEventListener('click', () => {
        if (!window.speechSynthesis) { status.textContent = 'बोलकर जवाब इस Browser में उपलब्ध नहीं है।'; return; }
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(document.getElementById('food-ai-answer').textContent);
        utterance.lang = 'hi-IN';
        utterance.rate = 0.95;
        window.speechSynthesis.speak(utterance);
    });
})();
</script>
@endsection
