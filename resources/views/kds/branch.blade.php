<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Kitchen · {{ $branchRecord->name }}</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 p-4 sm:p-8"><main class="mx-auto max-w-6xl space-y-5"><header class="rounded-xl bg-white p-6 flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-bold">Kitchen · {{ $branchRecord->name }}</h1><p class="text-slate-600">{{ $branchRecord->business_name }} · Refresh to see new Orders</p></div><div class="flex gap-3"><a class="rounded-lg border px-4 py-2" href="{{ route('pos.branches.show', $branchRecord->id) }}">Counter POS</a><a class="rounded-lg border px-4 py-2" href="{{ route('kds.branches.show', $branchRecord->id) }}">Refresh</a></div></header>
@if(session('success'))<p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
@forelse($orders as $order)
<article class="rounded-xl bg-white p-5 shadow-sm space-y-3"><h2 class="font-bold">{{ $order->order_number }}</h2>
<p class="font-semibold">{{ ucfirst($order->status) }} · {{ $order->dining_table_label ?: 'Counter' }} · {{ ucfirst($order->order_origin) }}</p>
<p class="text-xs text-slate-500">{{ $order->placed_at }} · Payment: {{ $order->payment_status }}</p>
@php $orderLines = $items->get($order->id, collect()); $totalQuantity = $orderLines->sum(fn ($item) => (int) $item->quantity); @endphp
<p class="text-sm font-semibold">Current order: {{ $orderLines->count() }} item lines · {{ $totalQuantity }} total units</p>
<ul class="list-disc pl-5 text-sm">@foreach($orderLines as $item)<li>{{ $item->quantity }} × {{ $item->name_snapshot }}</li>@endforeach</ul>
@if($order->customer_note)<p class="rounded-lg bg-amber-50 p-3 text-sm">Note: {{ $order->customer_note }}</p>@endif
@php $next = ['received'=>'preparing','preparing'=>'ready','ready'=>'served'][$order->status] ?? null; @endphp
@if($next)<form method="POST" action="{{ route('kds.orders.status', $order->id) }}">@csrf<input type="hidden" name="status" value="{{ $next }}"><button class="rounded-lg bg-blue-700 px-5 py-2 text-white">Mark {{ ucfirst($next) }}</button></form>@endif
</article>
@empty<div class="rounded-xl bg-white p-6 text-slate-500">No active Kitchen Orders.</div>@endforelse</div>
<p class="text-center text-xs text-slate-500" role="note">AI can make mistake. KDS shows recorded order quantities; separate added batches and partially prepared quantities are not tracked here yet.</p>
</main></body></html>
