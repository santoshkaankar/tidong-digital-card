<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Kitchen · {{ $branchRecord->name }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 p-4 sm:p-8"><main class="mx-auto max-w-6xl space-y-5"><header class="rounded-xl bg-white p-6 flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-bold">Kitchen · {{ $branchRecord->name }}</h1><p class="text-slate-600">{{ $branchRecord->business_name }} · Refresh to see new Orders</p></div><div class="flex gap-3"><a class="rounded-lg border px-4 py-2" href="{{ route('pos.branches.show', $branchRecord->id) }}">Counter POS</a><a class="rounded-lg border px-4 py-2" href="{{ route('kds.branches.show', $branchRecord->id) }}">Refresh</a></div></header>
@if(session('success'))<p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
@forelse($orders as $order)
<article class="rounded-xl bg-white p-5 shadow-sm space-y-3"><h2 class="font-bold">{{ $order->order_number }}</h2>
<p class="font-semibold">{{ ucfirst($order->status) }} · {{ $order->dining_table_label ?: 'Counter' }} · {{ ucfirst($order->order_origin) }}</p>
<p class="text-xs text-slate-500">{{ $order->placed_at }} · Payment: {{ $order->payment_status }}</p>
<ul class="space-y-2 text-sm">
@foreach($items->get($order->id, collect()) as $item)
<li class="rounded-lg bg-slate-50 px-3 py-2">{{ $item->name_snapshot }}
<span class="block text-xs text-slate-600">कुल {{ $item->quantity }} · अतिरिक्त {{ $item->added_quantity }} · तैयार {{ $item->ready_quantity }} · परोसे {{ $item->served_quantity }}</span></li>
@endforeach
</ul>
@if($freeAiEnabled)
<form method="POST" action="{{ route('kds.orders.free-ask', $order->id) }}" class="rounded-xl border border-slate-200 p-3 space-y-2">
@csrf
<label class="block font-semibold text-sm" for="free-kds-{{ $order->id }}">TIDONG AI · Free Kitchen</label>
<input id="free-kds-{{ $order->id }}" name="question" required minlength="2" maxlength="300" class="w-full rounded-lg border p-2" placeholder="टेबल 5 की कितनी चपाती पहुँची?">
<button class="rounded-lg bg-slate-900 px-4 py-2 text-white font-semibold text-sm" type="submit">पूछें · API ₹0</button>
<p class="text-xs text-slate-500">यह Free Mode केवल रिकॉर्ड देखकर जवाब देता है। आवाज़ से ऑर्डर अपने आप नहीं बदलेगा।</p>
</form>
@if(($kdsReply = session('free_kds_reply')) && ($kdsReply['order_id'] ?? null) == $order->id)
<p class="rounded-lg bg-blue-50 p-3 text-sm" role="status">{{ $kdsReply['reply'] }}</p>
@endif
@endif
@if($order->customer_note)<p class="rounded-lg bg-amber-50 p-3 text-sm">Note: {{ $order->customer_note }}</p>@endif
@php $next = ['received'=>'preparing','preparing'=>'ready','ready'=>'served'][$order->status] ?? null; @endphp
@if($next)<form method="POST" action="{{ route('kds.orders.status', $order->id) }}">@csrf<input type="hidden" name="status" value="{{ $next }}"><button class="rounded-lg bg-blue-700 px-5 py-2 text-white">Mark {{ ucfirst($next) }}</button></form>@endif
</article>
@empty<div class="rounded-xl bg-white p-6 text-slate-500">No active Kitchen Orders.</div>@endforelse</div>
</main></body></html>
