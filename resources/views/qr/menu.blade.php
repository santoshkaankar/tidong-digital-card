<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Digital Menu — {{ $table->business_name }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 p-4 text-slate-800"><main class="mx-auto max-w-xl space-y-4">
<header class="rounded-xl bg-white p-5"><h1 class="text-2xl font-bold">{{ $table->business_name }}</h1><p>{{ $table->branch_name }} · {{ $table->label }}</p><p class="mt-2 text-sm">{{ $canOrder ? 'Order request available' : 'New orders are temporarily paused.' }}</p></header>
@if(session('success'))<p class="bg-green-50 rounded-xl p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="bg-red-50 rounded-xl p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form id="qr-order-form" class="bg-white rounded-xl p-5 space-y-4" method="post" action="{{ route('qr.menu.submit',$table->qr_token) }}">@csrf
<input type="hidden" name="submission_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}"><input type="hidden" name="confirmed" value="0" disabled>
<h2 class="font-bold text-xl">Menu · Choose Quantity</h2>
@forelse($items as $item)<div class="flex items-center justify-between gap-3 border-b pb-3" data-menu-item data-label="{{ $item->name }}"><div class="flex items-center gap-3">@if($item->photo_path)<img loading="lazy" class="h-16 w-20 rounded-lg object-cover" src="{{ str_starts_with($item->photo_path,'branch-menu/') ? asset('storage/'.$item->photo_path) : asset(ltrim($item->photo_path,'/')) }}" alt="{{ $item->name }}">@endif<div><strong>{{ $item->name }}</strong><p class="text-sm">₹{{ number_format($item->price_paise/100,2) }}</p></div></div><input class="border rounded p-2 w-20 qty" type="number" min="0" max="20" step="1" name="quantities[{{ $item->id }}]" value="0" data-price="{{ $item->price_paise }}" aria-label="Quantity of {{ $item->name }}" {{ $canOrder ? '' : 'disabled' }}></div>
@empty<p>Menu is being prepared.</p>@endforelse
@if($canOrder && $items->isNotEmpty())
<div class="border rounded-lg p-3 space-y-3"><h3 class="font-semibold">Optional voice selection (Hindi / English)</h3><label>Voice language <select id="voice-language" class="border rounded px-2 py-1"><option value="hi-IN">Hindi</option><option value="en-IN">English (India)</option></select></label><button type="button" id="voice-button" class="block rounded border px-3 py-2">🎤 Speak Menu Items</button><p id="voice-result" class="text-sm" aria-live="polite">For example: “दो चाय” or “two tea”. Check quantities after speaking.</p><p class="text-xs">Your browser may use its own speech service. You can always enter quantities manually.</p></div>
<label class="block">Your name / table guest <input class="block border rounded p-2 w-full" name="customer_name" maxlength="80" minlength="2" required autocomplete="name" value="{{ old('customer_name') }}" placeholder="Guest name"></label>
<label class="block">Kitchen note (optional) <textarea class="block border rounded p-2 w-full" name="customer_note" maxlength="500" rows="2">{{ old('customer_note') }}</textarea></label>
<p class="font-semibold" id="total-preview">Estimated Total: ₹0.00</p>
<label class="flex gap-2 items-start"><input type="checkbox" name="confirmed" value="1" required><span>I have checked the items and quantities. Send my request to the restaurant for approval.</span></label>
<button type="submit" class="rounded bg-blue-700 text-white px-5 py-3">Send Order Request</button><p class="text-sm text-slate-500">Not a confirmed Kitchen order. Vendor must accept it. No payment is charged here.</p>
@else<p class="text-sm text-slate-600">Menu browsing only. Ask a waiter if you want to place an order.</p>@endif
</form></main>
<script>
(() => {
 const form=document.getElementById('qr-order-form'); if (!form) return;
 const qtyInputs=[...form.querySelectorAll('.qty')];
 const preview=()=>{let p=qtyInputs.reduce((sum,input)=>sum+Number(input.value||0)*Number(input.dataset.price||0),0);let el=document.getElementById('total-preview');if(el)el.textContent='Estimated Total: ₹'+(p/100).toFixed(2);};
 qtyInputs.forEach(q=>q.addEventListener('input',preview)); preview();
 form.addEventListener('submit',e=>{if(!qtyInputs.some(q=>Number(q.value)>0)){e.preventDefault();alert('Please select at least one menu item.');}});
 const btn=document.getElementById('voice-button'); if(!btn)return;
 const Speech=window.SpeechRecognition||window.webkitSpeechRecognition;
 if(!Speech){btn.disabled=true;document.getElementById('voice-result').textContent='Voice recognition is unavailable in this browser. Please use the quantity boxes.';return;}
 btn.addEventListener('click',()=>{
   const rec=new Speech();rec.lang=document.getElementById('voice-language').value;rec.interimResults=false;rec.maxAlternatives=1;
   document.getElementById('voice-result').textContent='Listening…';
   rec.onresult=event=>{
     const transcript=event.results[0][0].transcript;const normalized=transcript.toLocaleLowerCase('hi-IN');let matched=0;
     const numberWords={'एक':1,'दो':2,'तीन':3,'चार':4,'पाँच':5,'पांच':5,'छः':6,'छह':6,'सात':7,'आठ':8,'नौ':9,'दस':10,'one':1,'two':2,'three':3,'four':4,'five':5,'six':6,'seven':7,'eight':8,'nine':9,'ten':10};
     qtyInputs.forEach(input=>{
       const row=input.closest('[data-menu-item]');let label=(row?.dataset.label||'').toLocaleLowerCase('hi-IN');
       const variants=[label];if(label.includes('chai')||label.includes('tea'))variants.push('चाय','chai','tea');
       if(label.includes('dosa'))variants.push('डोसा');if(label.includes('coffee'))variants.push('कॉफी');
       let hit=variants.filter(Boolean).map(v=>({v,i:normalized.indexOf(v)})).find(p=>p.i!==-1);
       if(!hit)return;
       const before=normalized.slice(Math.max(0,hit.i-45),hit.i).trim();
       const parts=(before.match(/[\p{L}\p{N}]+/gu)||[]).slice(-5).reverse();let qty=1;
       const word=parts.find(p=>/^\d+$/.test(p)||Object.prototype.hasOwnProperty.call(numberWords,p));
       if(word)qty=/^\d+$/.test(word)?Number(word):numberWords[word];
       input.value=String(Math.min(20,Math.max(1,qty)));matched++;
     });
     preview();document.getElementById('voice-result').textContent='Heard: “'+transcript+'”. '+(matched?'Please verify quantities before sending.':'No exact menu names matched; select items manually.');
   };
   rec.onerror=()=>{document.getElementById('voice-result').textContent='Speech recognition failed. You can enter quantities manually.';};
   try{rec.start();}catch(e){document.getElementById('voice-result').textContent='Microphone could not start. Select items manually.';}
 });
})();
</script></body></html>
