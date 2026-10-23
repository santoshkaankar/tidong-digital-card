<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $item->name }} · TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-50 text-slate-900"><main class="mx-auto max-w-3xl space-y-5 px-4 py-8">
<header class="flex flex-wrap items-center justify-between gap-3"><a class="font-semibold text-blue-700 underline" href="{{ route('sublimation.shop', ['business' => $item->business_id]) }}">← This Studio</a>@auth<a class="font-semibold text-blue-700 underline" href="{{ route('customer.sublimation.orders') }}">My Orders</a>@endauth</header>
<section class="rounded-2xl border bg-white p-6"><p class="text-xs font-bold uppercase text-rose-700">{{ $item->category }} · {{ $item->business_name }}</p><h1 class="mt-2 text-3xl font-bold">{{ $item->name }}</h1>
<p class="mt-2 text-sm text-slate-600">Studio branch: {{ $item->branch_name }}</p><p class="mt-3 text-2xl font-bold">₹{{ number_format($item->sale_price_paise/100,2) }} <span class="text-sm font-normal text-slate-600">per piece, excluding unconfirmed delivery charges</span></p>
<p class="mt-3 text-sm text-slate-600">This is an order request. The studio confirms the print design and fulfillment separately; no online payment collected.</p>
</section>
@if($errors->any())<div role="alert" class="rounded-xl bg-red-50 p-4 text-red-800"><strong>Check your order:</strong><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@auth
<form method="POST" action="{{ route('sublimation.orders.place') }}" class="grid gap-4 rounded-2xl border bg-white p-5 sm:grid-cols-2">@csrf<input type="hidden" name="request_token" value="{{ old('request_token', (string) \Illuminate\Support\Str::uuid()) }}"><input type="hidden" name="product_id" value="{{ $item->id }}">
<label class="text-sm font-semibold">Customer Name<input name="customer_name" required maxlength="160" value="{{ old('customer_name',auth()->user()->name) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Mobile Number<input name="customer_mobile" required minlength="8" maxlength="25" value="{{ old('customer_mobile') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Quantity<input name="quantity" type="number" min="1" max="100" required value="{{ old('quantity',1) }}" class="mt-1 w-full rounded-lg border p-3"></label>
@foreach(['size'=>'Size','color'=>'Color'] as $key=>$label)
@if(!empty($choices[$key]))<label class="text-sm font-semibold">{{ $label }}<select name="{{ $key }}" required class="mt-1 w-full rounded-lg border p-3"><option value="">Choose {{ strtolower($label) }}</option>
@foreach($choices[$key] as $choice)<option value="{{ $choice }}" @selected(old($key)===$choice)>{{ $choice }}</option>@endforeach</select></label>@endif
@endforeach
<label class="text-sm font-semibold sm:col-span-2">Personalized Name, Photo / Print Instructions<textarea name="design_instructions" maxlength="3000" rows="3" class="mt-1 w-full rounded-lg border p-3" placeholder="Name, message, image instructions; studio will request artwork separately">{{ old('design_instructions') }}</textarea></label>
<label class="text-sm font-semibold">Fulfillment<select id="customer-sublimation-fulfillment" name="fulfillment_method" required class="mt-1 w-full rounded-lg border p-3"><option value="pickup" @selected(old('fulfillment_method','pickup')==='pickup')>Studio Pickup</option><option value="delivery" @selected(old('fulfillment_method')==='delivery')>Home Delivery (to be confirmed)</option></select></label>
@if(isset($savedAddresses) && $savedAddresses->isNotEmpty())
<label id="customer-sublimation-saved-wrap" class="text-sm font-semibold sm:col-span-2">Saved delivery address
<select id="customer-sublimation-saved-address" name="delivery_address_id" class="mt-1 w-full rounded-lg border bg-white p-3">
<option value="">Enter another address below</option>
@foreach($savedAddresses as $address)
<option value="{{ $address->id }}" @selected((string)old('delivery_address_id', $address->is_default ? $address->id : '') === (string)$address->id)>{{ $address->label }} — {{ $address->address_line_1 }}, {{ $address->city }} ({{ $address->postal_code }})</option>
@endforeach
</select></label>
@endif
<label id="customer-sublimation-address-wrap" class="text-sm font-semibold sm:col-span-2">Delivery address (when selecting home delivery)
<textarea id="customer-sublimation-address" name="delivery_address" maxlength="1000" rows="2" class="mt-1 w-full rounded-lg border p-3" placeholder="Fill this when you do not select a saved address">{{ old('delivery_address') }}</textarea>
<span class="block pt-1 text-xs text-slate-500">A saved address takes priority when selected. Pickup orders do not require an address.</span></label>
<p class="text-sm font-semibold sm:col-span-2">Estimated product quote: <span id="customer-sublimation-quote" data-unit="{{ $item->sale_price_paise }}">₹{{ number_format($item->sale_price_paise/100,2) }}</span></p>
<p class="text-xs text-slate-600 sm:col-span-2">Size and color choices use the product's base price. Delivery charges, custom availability and any additional charges must be separately confirmed. No payment is collected here.</p>
<button id="sublimation-customer-submit" class="rounded-lg bg-rose-700 px-4 py-3 font-bold text-white sm:col-span-2">Place Order Request</button>
</form>
<script>
(() => {
 const fulfillment = document.getElementById('customer-sublimation-fulfillment');
 const address = document.getElementById('customer-sublimation-address');
 const addressWrap = document.getElementById('customer-sublimation-address-wrap');
 const savedWrap = document.getElementById('customer-sublimation-saved-wrap');
 const savedSelect = document.getElementById('customer-sublimation-saved-address');
 const quantity = document.querySelector('input[name="quantity"]');
 const quote = document.getElementById('customer-sublimation-quote');
 if (!fulfillment || !address || !addressWrap || !quantity || !quote) return;
 function refresh() {
   const delivery = fulfillment.value === 'delivery';
   if (savedWrap) savedWrap.hidden = !delivery;
   const needsManualAddress = delivery && !(savedSelect && savedSelect.value);
   addressWrap.hidden = !needsManualAddress;
   address.required = needsManualAddress;
   const q = Number(quantity.value);
   const paisa = Number(quote.dataset.unit);
   if (Number.isInteger(q) && q >= 1 && q <= 100 && Number.isInteger(paisa)) {
     quote.textContent = '₹' + ((paisa*q)/100).toFixed(2);
   } else quote.textContent = 'Enter a valid quantity';
 }
 document.getElementById('sublimation-customer-submit')?.closest('form')?.addEventListener('submit', (event) => { const button = document.getElementById('sublimation-customer-submit'); if (button.dataset.sent) { event.preventDefault(); return; } button.dataset.sent='1'; button.disabled=true; });
 fulfillment.addEventListener('change',refresh);
 savedSelect?.addEventListener('change',refresh);
 quantity.addEventListener('input',refresh);
 refresh();
})();
</script>
@else
<a href="{{ route('login') }}" class="inline-block rounded-lg bg-rose-700 px-5 py-3 font-bold text-white">Login to Order</a>
@endauth
</main></body></html>
