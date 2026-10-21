<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Order {{ $item->order_no }} · TIDONG</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-50 text-slate-900"><main class="mx-auto max-w-3xl space-y-5 px-4 py-8"><a href="{{ route('customer.sublimation.orders') }}" class="font-semibold text-blue-700 underline">← My Orders</a>
<section class="space-y-4 rounded-2xl border bg-white p-6"><div class="flex flex-wrap items-center justify-between gap-2"><div><p class="text-xs text-rose-700">Order Reference</p><h1 class="text-2xl font-bold">{{ $item->order_no }}</h1></div>
<span class="rounded-lg bg-slate-100 p-2 font-semibold">{{ ucwords(str_replace('_',' ',$item->status)) }}</span></div>
<p><strong>Product:</strong> {{ $item->product_name_snapshot }} × {{ $item->quantity }}</p>
@foreach($choices as $key=>$value)<p><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>@endforeach
@if($item->design_instructions)<p class="whitespace-pre-line"><strong>Personalization:</strong> {{ $item->design_instructions }}</p>@endif
<p><strong>Total product quote:</strong> ₹{{ number_format($item->total_sale_paise/100,2) }}</p>
<p><strong>Fulfillment:</strong> {{ ($item->fulfillment_method ?? 'pickup')==='delivery' ? 'Home Delivery' : 'Studio Pickup' }}</p>
@if($item->delivery_address)<p class="whitespace-pre-line"><strong>Address:</strong> {{ $item->delivery_address }}</p>@endif
@if($item->status==='approval_pending')<p class="rounded-lg bg-amber-50 p-3 text-sm">Studio is waiting for design approval. Contact the studio to confirm your print proof.</p>@endif
@if($item->dispatched_at)<p><strong>Dispatched:</strong> {{ $item->dispatched_at }}</p>@endif
@if($item->delivery_carrier)<p><strong>Carrier / rider:</strong> {{ $item->delivery_carrier }}</p>@endif
@if($item->delivery_tracking)<p><strong>Tracking reference:</strong> {{ $item->delivery_tracking }}</p>@endif
@if($item->delivered_at)<p class="font-semibold text-green-800">Completed: {{ $item->delivered_at }}</p>@endif
@if($item->status==='cancelled')<p class="font-semibold text-red-700">This order was cancelled.</p>@endif
<p class="text-xs text-slate-600">Order records are shown for information. No online payment, delivery charge or GST tax invoice has been processed on this page.</p></section></main></body></html>
