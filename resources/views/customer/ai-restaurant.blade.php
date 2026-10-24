@extends('customer.layout')
@section('title', __('TIDONG AI'))
@section('content')
<section class="mx-auto flex h-[calc(100dvh-10rem)] min-h-[420px] max-w-3xl flex-col rounded-2xl border border-slate-200 bg-white shadow-sm">
<header class="flex items-center justify-between border-b border-slate-100 p-4"><h1 class="text-lg font-bold">TIDONG AI</h1><form method="POST" action="{{ route('customer.ai-restaurant.new-chat') }}">@csrf<button type="submit" class="min-h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold">＋ New Chat</button></form></header>
<div id="food-ai-thread" class="min-h-0 flex-1 space-y-4 overflow-y-auto p-4" aria-live="polite">
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
<div class="flex items-end gap-1 rounded-2xl border border-slate-300 px-2 py-1.5 focus-within:border-rose-400">
<button type="button" id="food-ai-location" title="लोकेशन" aria-label="लोकेशन" class="h-8 w-8 shrink-0 rounded-full text-sm" >📍</button>
<label for="food-ai-message" class="sr-only">सवाल लिखें</label>
<textarea id="food-ai-message" name="message" maxlength="400" minlength="2" rows="1" required placeholder="सवाल पूछें…" class="max-h-28 min-h-9 min-w-0 flex-1 resize-none border-0 bg-transparent px-2 py-1.5 text-sm focus:outline-none">{{ old('message', '') }}</textarea>
<button type="button" id="food-ai-mic" title="बोलकर पूछें" aria-label="बोलकर पूछें" class="h-8 w-8 shrink-0 rounded-full text-base" >🎙️</button>
<button type="submit" id="food-ai-send" title="भेजें" aria-label="भेजें" class="h-8 w-8 shrink-0 rounded-full bg-rose-700 text-sm text-white">➤</button>
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
    const form = document.getElementById('food-ai-form');
    const send = document.getElementById('food-ai-send');
    const resizeInput = () => {
        message.style.height = 'auto';
        message.style.height = Math.min(message.scrollHeight, 112) + 'px';
    };
    message.addEventListener('input', resizeInput);
    resizeInput();
    const thread = document.getElementById('food-ai-thread');
    let busy = false;
    const addBubble = (body, who) => {
        const wrapper = document.createElement('div');
        wrapper.className = who === 'user' ? 'flex justify-end' : 'flex justify-start';
        const bubble = document.createElement('p');
        bubble.className = who === 'user'
            ? 'max-w-[85%] whitespace-pre-wrap rounded-2xl bg-rose-700 p-3 text-white'
            : 'max-w-[85%] whitespace-pre-wrap rounded-2xl bg-slate-100 p-3 text-slate-900';
        bubble.textContent = body;
        wrapper.appendChild(bubble);
        thread.appendChild(wrapper);
        thread.scrollTop = thread.scrollHeight;
        return bubble;
    };
    form.addEventListener('submit', async event => {
        if (!window.fetch || !window.FormData) return; // Native form fallback.
        event.preventDefault();
        if (busy) return;
        const question = message.value.trim();
        if (question.length < 2) {
            status.classList.remove('sr-only');
            status.textContent = 'कम से कम दो अक्षरों का सवाल लिखें।';
            message.focus();
            return;
        }
        busy = true;
        send.disabled = true;
        const payload = new FormData(form);
        payload.set('message', question);
        addBubble(question, 'user');
        message.value = '';
        resizeInput();
        const pending = addBubble('जवाब तैयार हो रहा है…', 'assistant');
        status.classList.remove('sr-only');
        status.textContent = 'जवाब तैयार हो रहा है…';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            if (!response.ok || !data || typeof data.answer !== 'string') {
                throw new Error(data && data.message ? data.message : 'जवाब उपलब्ध नहीं है।');
            }
            pending.textContent = data.answer;
            if (window.speechSynthesis) {
                const listen = document.createElement('button');
                listen.type = 'button';
                listen.className = 'min-h-11 min-w-11 rounded-full border border-slate-200';
                listen.setAttribute('aria-label', 'जवाब सुनें');
                listen.textContent = '🔊';
                listen.addEventListener('click', () => {
                    window.speechSynthesis.cancel();
                    const utterance = new SpeechSynthesisUtterance(data.answer);
                    utterance.lang = 'hi-IN';
                    window.speechSynthesis.speak(utterance);
                });
                pending.parentElement.appendChild(listen);
            }
            status.textContent = 'जवाब आ गया।';
        } catch (error) {
            pending.textContent = 'जवाब नहीं मिल पाया। सवाल दोबारा भेज सकते हैं।';
            message.value = question;
            resizeInput();
            status.textContent = 'नेटवर्क या सर्वर की समस्या है। दोबारा भेजें।';
        } finally {
            busy = false;
            send.disabled = false;
            thread.scrollTop = thread.scrollHeight;
        }
    });
    message.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            form.requestSubmit(send);
        }
    });
    locationButton.addEventListener('click', () => {
        if (!navigator.geolocation) { status.textContent = 'यह Browser लोकेशन नहीं दे सकता।'; return; }
        status.textContent = 'लोकेशन की अनुमति माँगी जा रही है…';
        navigator.geolocation.getCurrentPosition(
            pos => { lat.value = pos.coords.latitude; lng.value = pos.coords.longitude; status.textContent = 'लोकेशन मिल गई। अब पूछ सकते हैं।'; },
            () => { lat.value = ''; lng.value = ''; status.textContent = 'लोकेशन की अनुमति नहीं मिली।'; },
            { enableHighAccuracy: false, timeout: 12000, maximumAge: 60000 }
        );
    });
    if (!navigator.geolocation) {
        status.textContent = 'लोकेशन उपलब्ध नहीं है; लिखकर सवाल भेज सकते हैं।';
    }
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
            recognition.onresult = e => { message.value = e.results[0][0].transcript; resizeInput(); message.focus(); status.textContent = 'बात समझने के लिए लिखा गया है। जाँचकर जवाब पाएँ दबाएँ।'; };
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
