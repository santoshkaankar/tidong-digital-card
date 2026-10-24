@extends('customer.layout')
@section('title', __('TIDONG AI'))
@section('content')
<section class="mx-auto flex min-h-[70vh] max-w-3xl flex-col rounded-2xl border border-slate-200 bg-white shadow-sm">
<header class="flex items-center justify-between border-b border-slate-100 p-4"><h1 class="text-lg font-bold">TIDONG AI</h1><form method="POST" action="{{ route('customer.ai-restaurant.new-chat') }}">@csrf<button type="submit" class="min-h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold">＋ New Chat</button></form></header>
<div class="flex-1 space-y-4 p-4" aria-live="polite">
@if($answer !== null)
<div class="flex justify-end"><p class="max-w-[85%] whitespace-pre-wrap rounded-2xl bg-rose-700 p-3 text-white">{{ $message }}</p></div>
<div class="flex items-start gap-2"><p id="food-ai-answer" class="max-w-[85%] whitespace-pre-wrap rounded-2xl bg-slate-100 p-3">{{ $answer }}</p><button id="food-ai-speak" type="button" aria-label="जवाब सुनें" class="min-h-11 min-w-11">🔊</button></div>
@endif
@if($errors->any())<p role="alert" class="text-red-700">{{ $errors->first() }}</p>@endif
</div>
<form method="POST" action="{{ route('customer.ai-restaurant.ask') }}" id="food-ai-form" class="border-t border-slate-100 p-3">
@csrf
<input type="hidden" name="latitude" id="food-ai-lat" value="{{ old('latitude', $latitude) }}">
<input type="hidden" name="longitude" id="food-ai-lng" value="{{ old('longitude', $longitude) }}">
<div class="flex items-end gap-2 rounded-2xl border border-slate-300 p-2">
<button type="button" id="food-ai-location" title="लोकेशन" aria-label="लोकेशन" class="min-h-11 min-w-11">📍</button>
<label for="food-ai-message" class="sr-only">सवाल लिखें</label>
<textarea id="food-ai-message" name="message" maxlength="400" minlength="2" rows="1" required placeholder="सवाल पूछें…" class="min-h-11 flex-1 resize-y border-0 p-2 text-base focus:outline-none">{{ old('message', '') }}</textarea>
<button type="button" id="food-ai-mic" title="बोलकर पूछें" aria-label="बोलकर पूछें" class="min-h-11 min-w-11">🎙️</button>
<button type="submit" title="भेजें" aria-label="भेजें" class="min-h-11 min-w-11 rounded-xl bg-rose-700 text-white">➤</button>
</div><p id="food-ai-status" role="status" aria-live="polite" class="sr-only"></p>
</form>
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
