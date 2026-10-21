<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Table Requests</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 text-slate-800 p-5"><main class="mx-auto max-w-5xl space-y-5">
<header class="bg-white rounded-xl p-5 flex flex-wrap justify-between gap-2 items-center"><div><h1 class="text-2xl font-bold">QR Order Requests</h1><p>{{ $branchRecord->business_name }} · {{ $branchRecord->name }}</p></div><div class="flex gap-2"><a class="border px-3 py-2 rounded" href="{{ route('pos.branches.show',$branchRecord->id) }}">POS</a><a class="border px-3 py-2 rounded" href="{{ route('pos.qr.requests',$branchRecord->id) }}">Refresh</a></div></header>
@if(session('success'))<p class="bg-green-50 rounded p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="bg-red-50 rounded p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<p class="text-sm">New QR requests require Vendor approval before a Kitchen order is created. Guest prices are checked again on approval. Orders remain UNPAID.</p>
<section class="space-y-4">@forelse($requests as $r)<article class="bg-white rounded-xl shadow-sm p-5 space-y-3"><div class="flex flex-wrap justify-between gap-2"><h2 class="font-bold">Request #{{ $r->id }} · {{ $r->table_label }} · {{ $r->customer_name }}</h2><span class="uppercase text-sm font-semibold">{{ $r->status }}</span></div>
<p class="text-sm">{{ $r->created_at }} · Quote ₹{{ number_format($r->quoted_total_paise/100,2) }}</p>
<ul class="list-disc pl-5">@foreach($items->get($r->id,collect()) as $item)<li>{{ $item->quantity }} × {{ $item->name_snapshot }} (₹{{ number_format($item->line_total_paise/100,2) }})</li>@endforeach</ul>
@if($r->customer_note)<p class="bg-amber-50 p-2">Note: {{ $r->customer_note }}</p>@endif
@if($r->status==='pending')<div class="flex flex-wrap gap-3 items-end"><form method="post" action="{{ route('pos.qr.accept',$r->id) }}">@csrf<button class="rounded bg-green-700 text-white px-4 py-2">Accept → Send to Kitchen</button></form><form method="post" action="{{ route('pos.qr.reject',$r->id) }}" class="flex gap-2">@csrf<input name="reason" class="border rounded px-2 py-1" placeholder="Reason for rejection" required minlength="5" maxlength="500"><button class="rounded bg-red-700 text-white px-4 py-2">Reject</button></form></div>
@elseif($r->status==='accepted')<p class="text-sm">POS Order #{{ $r->accepted_order_id }} · Unpaid</p>
@elseif($r->rejection_reason)<p class="text-sm">Rejected: {{ $r->rejection_reason }}</p>@endif</article>
@empty<p class="bg-white p-5 rounded-xl">No QR Requests yet.</p>@endforelse</section></main></body></html>
