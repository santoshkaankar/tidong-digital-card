@php($pageTitle = 'Order · '.$item->order_no)
@include('vendor.sublimation.partials.page-start')
<a class="no-print text-sm font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.orders.index', ['business'=>$business->id,'branch'=>$branch->id]) }}">← Order List</a>
<section class="print-card space-y-4 rounded-2xl border bg-white p-5">
<div class="flex flex-wrap items-center justify-between gap-3"><div><h2 class="text-2xl font-bold">{{ $item->order_no }}</h2><p class="text-sm text-slate-500">Placed: {{ $item->created_at }}</p></div>
<div class="flex items-center gap-3"><span class="rounded-lg bg-rose-50 px-3 py-2 font-semibold text-rose-800">{{ ucwords(str_replace('_',' ',$item->status)) }}</span><button type="button" onclick="window.print()" class="no-print rounded-lg border px-3 py-2 text-sm">Print Job Card</button></div></div>
<div class="grid gap-5 md:grid-cols-2"><div class="space-y-1 text-sm"><h3 class="font-bold">Customer &amp; Item</h3><p><strong>Name:</strong> {{ $item->customer_name }}</p><p><strong>Phone:</strong> {{ $item->customer_mobile ?: 'Not provided' }}</p>
<p><strong>Product:</strong> {{ $item->product_name_snapshot }}</p><p><strong>Quantity:</strong> {{ $item->quantity }}</p>
@foreach($choices as $key=>$value)<p><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</p>@endforeach
@if($item->due_date)<p><strong>Due:</strong> {{ $item->due_date }}</p>@endif
</div><div class="space-y-1 text-sm"><h3 class="font-bold">Quote &amp; Fulfillment</h3><p><strong>Per piece:</strong> ₹{{ number_format($item->unit_sale_paise/100,2) }}</p>
<p><strong>Total quote:</strong> ₹{{ number_format($item->total_sale_paise/100,2) }}</p>
<p><strong>Recorded cost:</strong> ₹{{ number_format($item->total_cost_paise/100,2) }}</p>
<p><strong>Method:</strong> {{ ($item->fulfillment_method ?? 'pickup') === 'delivery' ? 'Home Delivery' : 'Studio Pickup' }}</p>
@if($item->delivery_address)<p class="whitespace-pre-line"><strong>Address:</strong> {{ $item->delivery_address }}</p>@endif
@if($item->delivery_carrier)<p><strong>Carrier / rider:</strong> {{ $item->delivery_carrier }}</p>@endif
@if($item->delivery_tracking)<p><strong>Tracking:</strong> {{ $item->delivery_tracking }}</p>@endif
@if($item->dispatched_at)<p><strong>Dispatched:</strong> {{ $item->dispatched_at }}</p>@endif
@if($item->delivered_at)<p><strong>Completed:</strong> {{ $item->delivered_at }}</p>@endif
</div></div>
@if($item->design_instructions)<div class="rounded-lg bg-slate-50 p-3 text-sm"><strong>Print instructions:</strong><p class="mt-1 whitespace-pre-line">{{ $item->design_instructions }}</p></div>@endif
@if($item->approval_note)<p class="text-sm"><strong>Design approval record:</strong> {{ $item->approval_note }}</p>@endif
@if($item->delivery_confirmation_note)<p class="text-sm"><strong>Delivery / pickup confirmation:</strong> {{ $item->delivery_confirmation_note }}</p>@endif
</section>
@if(count($next))
<form method="POST" action="{{ route('vendor.sublimation.orders.status', ['business'=>$business->id,'order'=>$item->id]) }}" class="no-print space-y-4 rounded-2xl border bg-white p-5">@csrf
<h3 class="text-lg font-bold">Update This Order</h3>
<label class="block text-sm font-semibold">Next status<select name="status" required class="mt-1 w-full rounded-lg border p-3">
@foreach($next as $status)<option value="{{ $status }}" @selected(old('status')===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endforeach
</select></label>
@if(in_array('approved',$next,true))<label class="block text-sm font-semibold">Design Approval Evidence (required when approving)<textarea name="approval_note" maxlength="1500" rows="2" class="mt-1 w-full rounded-lg border p-3">{{ old('approval_note') }}</textarea></label>@endif
@if(in_array('dispatched',$next,true))<div class="grid gap-3 md:grid-cols-2"><label class="text-sm font-semibold">Courier / Rider (required when dispatching)<input name="delivery_carrier" maxlength="120" value="{{ old('delivery_carrier') }}" class="mt-1 w-full rounded-lg border p-3"></label><label class="text-sm font-semibold">Tracking ID (optional)<input name="delivery_tracking" maxlength="120" value="{{ old('delivery_tracking') }}" class="mt-1 w-full rounded-lg border p-3"></label></div>@endif
@if(in_array('delivered',$next,true))<label class="block text-sm font-semibold">Delivery / Pickup Confirmation (required when completing)<textarea name="delivery_confirmation_note" maxlength="2000" rows="2" class="mt-1 w-full rounded-lg border p-3">{{ old('delivery_confirmation_note') }}</textarea></label>@endif
<button class="rounded-lg bg-slate-900 px-5 py-3 font-semibold text-white">Save Status Change</button>
</form>
@endif
<section class="no-print rounded-2xl border bg-white p-5"><h3 class="text-lg font-bold">Order History</h3>
<div class="mt-3 space-y-3">@forelse($events as $event)
@php($detail = json_decode($event->new_values ?? '{}',true) ?: [])
<div class="border-l-2 border-blue-600 pl-4 text-sm"><strong>{{ ucwords(str_replace(['sublimation.order.','_','.'],['',' ',' '],$event->action)) }}</strong><p class="text-slate-500">{{ $event->created_at }}</p>
@if(isset($detail['status']))<p>Status: {{ ucwords(str_replace('_',' ',$detail['status'])) }}</p>@endif</div>
@empty<p class="text-sm text-slate-500">No audit events found.</p>@endforelse</div></section>
<p class="text-xs text-slate-500">Manual fulfillment record only; not a courier booking or paid tax invoice.</p>
@include('vendor.sublimation.partials.page-end')
