<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Request Branch Edit - {{ $branch->name }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="bg-slate-100 p-4 text-slate-800 sm:p-8"><main class="mx-auto max-w-4xl space-y-5">
<nav aria-label="Branch navigation" class="flex flex-wrap gap-2">
    <a href="{{ route('vendor.home') }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">← Back to Vendor Dashboard</a>
    <a href="{{ route('vendor.branch.licenses.index', $branch->id) }}" class="rounded-lg border bg-white px-4 py-2 text-sm font-semibold text-blue-700">GST / FSSAI / Licences</a>
</nav>
<header class="rounded-xl bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Request Branch Changes</h1>
<p class="text-sm text-slate-600">{{ $branch->name }} · {{ $branch->branch_code }}</p>
<p class="mt-3 text-amber-800">Editing here submits a request. Existing Branch details remain active until Admin approval. Your Branch Code is never changed.</p></header>
@if(session('success')) <p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p> @endif
@if($errors->any()) <div class="rounded-lg bg-red-50 p-4 text-red-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div> @endif
<section class="rounded-xl bg-white p-6 shadow-sm"><h2 class="text-xl font-semibold">Proposed Branch Details</h2>
<form method="POST" action="{{ route('vendor.branch.changes.store', $branch->id) }}" class="mt-4 grid gap-3 md:grid-cols-2" data-address-form data-address-url="{{ route('vendor.address.options') }}">@csrf
<div><label class="block text-sm font-medium">Branch Name</label><input required name="name" value="{{ old('name', $branch->name) }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
<div><label class="block text-sm font-medium">Phone</label><input name="phone" value="{{ old('phone',$branch->phone) }}" maxlength="30" class="mt-1 w-full rounded-lg border p-3"></div>
<div><label class="block text-sm font-medium">Email</label><input name="email" type="email" value="{{ old('email',$branch->email) }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
<div><label class="block text-sm font-medium">Town / City</label><input name="city" value="{{ old('city',$branch->city) }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
<div class="md:col-span-2"><label class="block text-sm font-medium">Street Address</label><input name="address_line" value="{{ old('address_line',$branch->address_line) }}" maxlength="255" class="mt-1 w-full rounded-lg border p-3"></div>
<div class="md:col-span-2 rounded-lg bg-slate-50 p-3 text-sm">Current Postal Address: <strong>{{ $branch->post_office ?: 'Not linked' }}, {{ $branch->district }}, {{ $branch->state }} {{ $branch->pincode }}</strong>. Leave the new Postal Address dropdowns untouched to keep it.</div>
<div><label class="block text-sm font-medium">Change State (optional)</label><select data-address-state class="mt-1 w-full rounded-lg border p-3"><option value="">Loading States...</option></select></div>
<div><label class="block text-sm font-medium">Change District (optional)</label><select data-address-district class="mt-1 w-full rounded-lg border p-3" disabled><option value="">Choose State First</option></select></div>
<div><label class="block text-sm font-medium">New Post Office (optional)</label><select name="india_post_office_id" data-address-office class="mt-1 w-full rounded-lg border p-3" disabled><option value="">Keep Existing Postal Address</option></select></div>
<div><label class="block text-sm font-medium">Selected PIN Code</label><input data-address-pin readonly class="mt-1 w-full rounded-lg border bg-slate-50 p-3" placeholder="Keep existing PIN"></div>
<div><label class="block text-sm font-medium">GPS Latitude (optional)</label><input name="latitude" type="number" step="any" min="6" max="38" value="{{ old('latitude',$branch->latitude) }}" class="mt-1 w-full rounded-lg border p-3"></div>
<div><label class="block text-sm font-medium">GPS Longitude (optional)</label><input name="longitude" type="number" step="any" min="68" max="98" value="{{ old('longitude',$branch->longitude) }}" class="mt-1 w-full rounded-lg border p-3"></div>
<div class="md:col-span-2 text-sm text-slate-600">GPS coordinates are Vendor-provided and are not independently verified. Use the location of the actual outlet, not a Post Office.</div>
<button type="submit" class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white md:col-span-2">Send Changes for Admin Approval</button>
</form></section>
<section class="rounded-xl bg-white p-6 shadow-sm"><h2 class="text-xl font-semibold">Change History</h2>
@forelse($history as $item)
<p class="mt-3 border-t pt-3 text-sm">Request #{{ $item->id }} — <strong>{{ ucfirst($item->status) }}</strong> · {{ $item->created_at->format('d M Y H:i') }}
@if($item->review_reason) · Admin: {{ $item->review_reason }} @endif</p>
@empty <p class="mt-2 text-slate-500">No previous branch change requests.</p> @endforelse
</section>
</main>
<script>
(() => {
const form = document.querySelector('[data-address-form]'); if (!form) return;
const state=form.querySelector('[data-address-state]'), district=form.querySelector('[data-address-district]'), office=form.querySelector('[data-address-office]'),pin=form.querySelector('[data-address-pin]');
const options=async params=>{const url=new URL(form.dataset.addressUrl,location.href); Object.entries(params).forEach(([k,v])=>url.searchParams.set(k,v)); const r=await fetch(url,{credentials:'same-origin',headers:{Accept:'application/json'}}); if(!r.ok) throw Error('Postal directory unavailable'); return (await r.json()).options||[]};
const fill=(el,label,items,isOffice=false)=>{el.replaceChildren(new Option(label,''));items.forEach(x=>{const o=new Option(isOffice?`${x.post_office} · ${x.pincode}`:x,isOffice?String(x.id):x);if(isOffice)o.dataset.pin=x.pincode;el.add(o)});el.disabled=items.length===0};
(async()=>{try{fill(state,'Select State (or keep existing)',await options({level:'states'}))}catch(e){fill(state,'Directory unavailable',[])}})();
state.addEventListener('change',async()=>{fill(district,'Choose District',[]);fill(office,'Keep Existing Postal Address',[]);pin.value='';if(!state.value)return;try{fill(district,'Choose District',await options({level:'districts',state:state.value}))}catch(e){fill(district,'Unable to load Districts',[])}});
district.addEventListener('change',async()=>{fill(office,'Keep Existing Postal Address',[]);pin.value='';if(!district.value)return;try{fill(office,'Choose New Post Office',await options({level:'offices',state:state.value,district:district.value}),true)}catch(e){fill(office,'Unable to load Post Offices',[])}});
office.addEventListener('change',()=>pin.value=office.selectedOptions[0]?.dataset.pin||'');
})();
</script></body></html>
