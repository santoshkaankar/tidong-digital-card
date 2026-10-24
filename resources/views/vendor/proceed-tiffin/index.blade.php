<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Proceed Tiffin & Everyday Thali · {{ $outlet->business_name }}</title>
@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 text-slate-900">
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row">
@include('vendor.partials.sidebar')
<main class="w-full min-w-0 flex-1 space-y-5">
<header class="rounded-2xl bg-white p-5 shadow-sm">
<p class="text-xs font-bold uppercase tracking-wide text-teal-700">Food · Outlet workspace</p>
<h1 class="mt-1 text-2xl font-bold">Proceed Tiffin &amp; Everyday Thali</h1>
<p class="mt-2 text-sm text-slate-600">{{ $outlet->business_name }} · {{ $outlet->name }} · Today: {{ $today }}</p>
<p class="mt-2 text-sm text-slate-600"><strong>Day</strong> creates date-specific Tiffin. <strong>Everyday</strong> creates a recurring Thali offer. Both use only Single Items selected by this outlet.</p>
</header>
@if($errors->any())<section role="alert" class="rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-900"><strong>Unable to save:</strong><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></section>@endif
@if(session('success'))<p role="status" class="rounded-xl border border-teal-300 bg-teal-50 p-4 text-teal-900">{{ session('success') }}</p>@endif
@if($outlet->vendor_status!=='approved'||$outlet->business_status!=='approved'||$outlet->branch_status!=='active')
<p class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm">This outlet is not approved/active. Changes and orders remain blocked.</p>
@endif
<section id="tiffin-items" class="rounded-2xl bg-white p-5 shadow-sm">
<h2 class="text-xl font-bold">Single Item List</h2>
<div class="mt-3 overflow-x-auto"><table class="w-full min-w-[450px] text-left text-sm"><thead><tr class="border-b"><th class="p-2">Item</th><th class="p-2">MRP</th><th class="p-2">Sale</th><th class="p-2">Source</th></tr></thead>
<tbody>@forelse($items as $item)<tr class="border-b"><td class="p-2">{{ $item->name }}</td>
<td class="p-2">₹{{ number_format($item->mrp_paise/100,2) }}</td>
<td class="p-2">₹{{ number_format($item->sale_price_paise/100,2) }}</td>
<td class="p-2">{{ $item->global_menu_item_id?'Global':'Custom' }}</td></tr>
@empty<tr><td colspan="4" class="p-3 text-slate-500">No item selected yet. Select Global Items below.</td></tr>@endforelse</tbody></table></div>
<div class="mt-5 grid gap-5 lg:grid-cols-2">
<form method="POST" action="{{ route('vendor.proceed-tiffin.items.global',['business'=>$outlet->business_id,'branch'=>$outlet->id]) }}" class="space-y-3 rounded-xl border p-4">@csrf
<h3 class="font-bold">Select from Global Item Master</h3>
<label class="block text-sm font-semibold">Single Item<select required name="global_menu_item_id" class="mt-1 w-full rounded-lg border p-3"><option value="">Choose Global Item</option>
@foreach($catalog as $global)<option value="{{ $global->id }}">{{ $global->name }} · {{ $global->category }} · MRP ₹{{ number_format($global->mrp_paise/100,2) }} / Sale ₹{{ number_format(($global->sale_price_paise?:$global->mrp_paise)/100,2) }}</option>@endforeach
</select></label>
<div class="grid grid-cols-2 gap-3"><label class="block text-sm font-semibold">Outlet MRP ₹<input name="mrp" inputmode="decimal" placeholder="Use global" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="block text-sm font-semibold">Outlet Sale ₹<input name="sale_price" inputmode="decimal" placeholder="Use global" class="mt-1 w-full rounded-lg border p-3"></label></div>
<button class="rounded-lg bg-teal-700 px-4 py-3 font-bold text-white">Select Item for Outlet</button>
</form>
<form method="POST" action="{{ route('vendor.proceed-tiffin.items.custom',['business'=>$outlet->business_id,'branch'=>$outlet->id]) }}" class="space-y-3 rounded-xl border p-4">@csrf
<h3 class="font-bold">Create Custom Single Item</h3>
<label class="block text-sm font-semibold">Name<input name="name" required maxlength="150" class="mt-1 w-full rounded-lg border p-3"></label>
<div class="grid grid-cols-2 gap-3"><label class="block text-sm font-semibold">MRP ₹<input name="mrp" required inputmode="decimal" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="block text-sm font-semibold">Sale ₹<input name="sale_price" inputmode="decimal" placeholder="Same as MRP" class="mt-1 w-full rounded-lg border p-3"></label></div>
<button class="rounded-lg bg-slate-800 px-4 py-3 font-bold text-white">Create Outlet Item</button>
</form>
</div></section>
<section id="tiffin-meals" class="rounded-2xl bg-white p-5 shadow-sm">
<h2 class="text-xl font-bold">Prepare Tiffin / Thali</h2>
<p class="mt-1 text-sm text-slate-600">You choose individual items and their quantities. MRP total is calculated on the server. Sale price remains editable. Published meals cannot be changed in this version.</p>
<form method="POST" action="{{ route('vendor.proceed-tiffin.meals.save',['business'=>$outlet->business_id,'branch'=>$outlet->id]) }}" class="mt-4 space-y-4">@csrf
<div class="grid gap-3 sm:grid-cols-2">
<label class="text-sm font-semibold">Schedule<select required id="schedule_scope" name="schedule_scope" class="mt-1 block w-full rounded-lg border p-3">
<option value="day" @selected(old('schedule_scope')!=='everyday')>Day — Tiffin</option>
<option value="everyday" @selected(old('schedule_scope')==='everyday')>Everyday — Thali</option>
</select></label>
<label id="date-label" class="text-sm font-semibold">Tiffin Date<input type="date" id="service_date" name="service_date" min="{{ $today }}" value="{{ old('service_date',$today) }}" class="mt-1 block w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Meal Slot<select name="meal_type" required class="mt-1 block w-full rounded-lg border p-3">@foreach($mealTypes as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></label>
<label class="text-sm font-semibold">Tiffin / Thali Name<input name="name" required maxlength="150" value="{{ old('name') }}" placeholder="For example: Lunch Special" class="mt-1 block w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Sale Price ₹ (Optional)<input name="sale_price" inputmode="decimal" placeholder="Default: item MRP total" class="mt-1 block w-full rounded-lg border p-3"></label>
</div>
<h3 class="font-semibold">Select Items and Quantity</h3>
<div class="grid gap-2 sm:grid-cols-2">
@forelse($items->where('is_active',true)->values() as $item)
<label class="flex items-center justify-between gap-2 rounded-lg border p-3 text-sm"><span>{{ $item->name }} · ₹{{ number_format($item->mrp_paise/100,2) }}</span>
<input type="hidden" name="components[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
<input type="number" name="components[{{ $loop->index }}][quantity]" min="0" max="100" value="0" aria-label="Quantity for {{ $item->name }}" class="w-20 rounded-lg border p-2"></label>
@empty<p class="text-sm text-amber-700">Select at least one Single Item to prepare a meal.</p>@endforelse
</div>
<button type="submit" class="rounded-lg bg-slate-900 px-5 py-3 font-bold text-white">Save Draft</button></form>
</section>
<section class="rounded-2xl bg-white p-5 shadow-sm"><h2 class="text-xl font-bold">Day Tiffin &amp; Everyday Thali Schedule</h2>
<div class="mt-4 space-y-3">@forelse($meals as $meal)
<div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border p-3 text-sm">
<div><strong>{{ $meal->name }}</strong><p class="text-slate-600">{{ $meal->schedule_scope==='everyday'?'Everyday Thali':'Day Tiffin · '.$meal->service_date }} / {{ $mealTypes[$meal->meal_type]??$meal->meal_type }} / {{ ucfirst($meal->status) }}</p>
<p>MRP ₹{{ number_format($meal->mrp_total_paise/100,2) }} · Sale ₹{{ number_format($meal->sale_price_paise/100,2) }}</p></div>
@if($meal->status==='draft')
<form method="POST" action="{{ route('vendor.proceed-tiffin.meals.publish',['business'=>$outlet->business_id,'branch'=>$outlet->id,'meal'=>$meal->id]) }}">@csrf<button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Publish</button></form>
@endif</div>
@empty<p class="text-slate-500">No meals prepared.</p>@endforelse</div>
</section>
<section id="tiffin-orders" class="rounded-2xl bg-white p-5 shadow-sm"><h2 class="text-xl font-bold">Today's Counter Menu</h2><p class="text-sm text-slate-600">Day Tiffin is offered only on the scheduled date. Everyday Thali is listed daily until disabled in a future management workflow.</p>
<div class="mt-4 grid gap-4 lg:grid-cols-2">
@forelse($todayOffers as $offer)
<form method="POST" action="{{ route('vendor.proceed-tiffin.orders.store',['business'=>$outlet->business_id,'branch'=>$outlet->id,'meal'=>$offer->id]) }}" class="rounded-xl border p-4 space-y-3">@csrf
<h3 class="font-bold">{{ $offer->name }}</h3><p class="text-sm text-slate-600">{{ $offer->schedule_scope==='everyday'?'Everyday Thali':'Day Tiffin' }} · ₹{{ number_format($offer->sale_price_paise/100,2) }}</p>
<label class="block text-sm">Quantity<input required type="number" name="quantity" min="1" max="50" value="1" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="block text-sm">Customer Name (Optional)<input name="customer_name" maxlength="100" class="mt-1 w-full rounded-lg border p-3"></label>
<button class="rounded-lg bg-teal-700 px-4 py-3 font-bold text-white">Place Unpaid Counter Order</button></form>
@empty<p class="text-slate-500">No published Tiffin or Thali for today.</p>@endforelse
</div>
<h3 class="mt-6 font-bold">Recent Order List</h3><div class="mt-2 overflow-x-auto"><table class="w-full min-w-[550px] text-left text-sm">
<thead><tr class="border-b"><th class="p-2">Order</th><th class="p-2">Meal</th><th class="p-2">Qty</th><th class="p-2">Total</th><th class="p-2">Payment</th></tr></thead><tbody>
@forelse($orders as $order)<tr class="border-b"><td class="p-2">{{ $order->order_number }}</td><td class="p-2">{{ $order->meal_name_snapshot }}</td><td class="p-2">{{ $order->quantity }}</td><td class="p-2">₹{{ number_format($order->total_paise/100,2) }}</td><td class="p-2">{{ strtoupper($order->payment_status) }}</td></tr>
@empty<tr><td colspan="5" class="p-3 text-slate-500">No counter orders yet.</td></tr>@endforelse</tbody></table></div>
</section>
</main></div>
<script>
(function(){
const scope=document.getElementById('schedule_scope');
const label=document.getElementById('date-label');
const date=document.getElementById('service_date');
function sync(){const everyday=scope.value==='everyday';label.hidden=everyday;date.disabled=everyday;date.required=!everyday;}
if(scope&&label&&date){scope.addEventListener('change',sync);sync();}
})();
</script>
</body></html>