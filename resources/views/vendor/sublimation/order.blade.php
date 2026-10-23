@php
$pageTitle = 'Order · '.$item->order_no;
@endphp
@include('vendor.sublimation.partials.page-start')
<a class="no-print text-sm font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.orders.index', ['business'=>$business->id,'branch'=>$branch->id]) }}">← Order List</a>
@php
$progressSteps = [
  'design_pending' => 'Design', 'approval_pending' => 'Await Approval',
  'approved' => 'Approved', 'in_production' => 'Production', 'ready' => 'Ready',
];
if (($item->fulfillment_method ?? 'pickup') === 'delivery') $progressSteps['dispatched'] = 'Dispatched';
$progressSteps['delivered'] = 'Completed';
$progressKeys = array_keys($progressSteps);
$progressIndex = array_search($item->status, $progressKeys, true);
@endphp
@if($item->status === 'cancelled')
<p class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm font-semibold text-red-800">This job has been cancelled. Its audit history remains available below.</p>
@else
<div class="no-print rounded-xl border bg-white p-4"><h3 class="mb-3 text-sm font-bold">Order Progress</h3>
<ol class="flex flex-wrap gap-2 text-xs">@foreach($progressSteps as $key=>$label)
<li class="rounded-lg border px-3 py-2 {{ $progressIndex !== false && $loop->index <= $progressIndex ? 'border-rose-300 bg-rose-50 font-bold text-rose-900' : 'bg-slate-50 text-slate-500' }}" @if($item->status === $key) aria-current="step" @endif>{{ $label }}</li>
@endforeach</ol></div>
@endif
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
@if($item->approval_note)<p class="text-sm"><strong>Staff-recorded design approval for current revision:</strong> {{ $item->approval_note }}</p>@endif
@if($item->delivery_confirmation_note)<p class="text-sm"><strong>Delivery / pickup confirmation:</strong> {{ $item->delivery_confirmation_note }}</p>@endif
</section>
@if($mayEditContact)
<section class="no-print rounded-2xl border bg-white p-5">
<h3 class="text-lg font-bold">Correct Customer Contact / Delivery Address</h3>
<p class="mt-1 text-xs text-slate-600">Owner-only correction before dispatch. Every change is recorded in order history.</p>
<form method="POST" action="{{ route('vendor.sublimation.orders.contact', ['business'=>$business->id,'order'=>$item->id]) }}" class="mt-3 grid gap-3 md:grid-cols-2">@csrf @method('PATCH')
<label class="text-sm font-semibold">Contact Phone @if(($item->fulfillment_method ?? 'pickup') === 'delivery') (required) @endif<input type="tel" name="customer_mobile" maxlength="25" value="{{ old('customer_mobile', $item->customer_mobile) }}" class="mt-1 w-full rounded-lg border p-3" @if(($item->fulfillment_method ?? 'pickup') === 'delivery') required @endif></label>
@if(($item->fulfillment_method ?? 'pickup') === 'delivery')
<label class="text-sm font-semibold md:col-span-2">Delivery Address (required)<textarea name="delivery_address" maxlength="1000" required rows="2" class="mt-1 w-full rounded-lg border p-3">{{ old('delivery_address', $item->delivery_address) }}</textarea></label>
@endif
<div class="md:col-span-2"><button class="rounded-lg border border-slate-800 px-5 py-2 font-semibold text-slate-800">Save Contact Correction</button></div>
</form></section>
@endif
@if(count($next))
<form method="POST" action="{{ route('vendor.sublimation.orders.status', ['business'=>$business->id,'order'=>$item->id]) }}" class="no-print space-y-4 rounded-2xl border bg-white p-5">@csrf
<h3 class="text-lg font-bold">Update This Order — Design Revision {{ $item->design_revision ?? 1 }}</h3>
<label class="block text-sm font-semibold">Next status<select id="sublimation-next-status" name="status" required class="mt-1 w-full rounded-lg border p-3">
<option value="">Select next step</option>
@foreach($next as $status)<option value="{{ $status }}" @selected(old('status')===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endforeach
</select></label>
@if(in_array('approved',$next,true))<label id="sublimation-approval-wrap" class="block text-sm font-semibold" hidden>Staff-recorded design approval (who approved, when, how; not customer-authenticated proof)<textarea id="sublimation-approval-note" name="approval_note" maxlength="1500" rows="2" class="mt-1 w-full rounded-lg border p-3" placeholder="E.g. Customer confirmed final design in person">{{ old('approval_note') }}</textarea></label>@endif
@if(in_array('dispatched',$next,true))<div id="sublimation-dispatch-wrap" class="hidden grid gap-3 md:grid-cols-2" hidden><label class="text-sm font-semibold">Courier / Rider<input id="sublimation-carrier" name="delivery_carrier" maxlength="120" value="{{ old('delivery_carrier') }}" class="mt-1 w-full rounded-lg border p-3"></label><label class="text-sm font-semibold">Tracking ID (optional)<input name="delivery_tracking" maxlength="120" value="{{ old('delivery_tracking') }}" class="mt-1 w-full rounded-lg border p-3"></label></div>@endif
@if(in_array('delivered',$next,true))<label id="sublimation-delivered-wrap" class="block text-sm font-semibold" hidden>Delivery / Pickup confirmation (recipient and handoff evidence)<textarea id="sublimation-delivered-note" name="delivery_confirmation_note" maxlength="2000" rows="2" class="mt-1 w-full rounded-lg border p-3">{{ old('delivery_confirmation_note') }}</textarea></label>@endif
<label id="sublimation-reason-wrap" class="block text-sm font-semibold" hidden>Reason for cancellation / design rework<textarea id="sublimation-status-note" name="status_note" maxlength="1500" rows="2" class="mt-1 w-full rounded-lg border p-3" placeholder="Explain what changed and any next action">{{ old('status_note') }}</textarea></label>
<button class="rounded-lg bg-slate-900 px-5 py-3 font-semibold text-white">Save Status Change</button>
<script>
(() => {
 const status = document.getElementById('sublimation-next-status'); if (!status) return;
 const fields = [
   ['approved','sublimation-approval-wrap','sublimation-approval-note'],
   ['dispatched','sublimation-dispatch-wrap','sublimation-carrier'],
   ['delivered','sublimation-delivered-wrap','sublimation-delivered-note'],
 ];
 const reasonWrap = document.getElementById('sublimation-reason-wrap');
 const reasonField = document.getElementById('sublimation-status-note');
 function update() {
   for (const [value,wrapId,inputId] of fields) {
     const wrap = document.getElementById(wrapId), field = document.getElementById(inputId);
     if (wrap && field) { const hide = status.value !== value; wrap.hidden = hide; wrap.classList.toggle('hidden', hide); field.required = !hide; }
   }
   const needReason = ['cancelled','design_pending'].includes(status.value);
   reasonWrap.hidden = !needReason; reasonField.required = needReason;
 }
 status.addEventListener('change',update); update();
})();
</script>
</form>
@endif
<section class="no-print rounded-2xl border bg-white p-5"><h3 class="text-lg font-bold">Order History</h3>
<div class="mt-3 space-y-3">@forelse($events as $event)
@php($detail = json_decode($event->new_values ?? '{}',true) ?: [])
@php($before = json_decode($event->old_values ?? '{}',true) ?: [])
<div class="border-l-2 border-blue-600 pl-4 text-sm"><strong>{{ ucwords(str_replace(['sublimation.order.','_','.'],['',' ',' '],$event->action)) }}</strong><p class="text-slate-500">{{ $event->created_at }}</p>
@if(isset($detail['status']))<p>Status: {{ ucwords(str_replace('_',' ',$detail['status'])) }}</p>@endif
@if(isset($detail['design_revision']))<p>Design revision: {{ $detail['design_revision'] }}</p>@endif
@if($event->action === 'sublimation.order.contact_updated')<p>Delivery contact/address corrected by studio owner (details retained in restricted audit).</p>@endif
@if(!empty($detail['approval_note']))<p class="whitespace-pre-line">Staff-recorded approval: {{ $detail['approval_note'] }}</p>@endif
@if(!empty($before['prior_approval_note']))<p class="whitespace-pre-line">Previous revision approval (superseded): {{ $before['prior_approval_note'] }}</p>@endif
@if(!empty($detail['status_note']))<p class="whitespace-pre-line">Reason / rework note: {{ $detail['status_note'] }}</p>@endif</div>
@empty<p class="text-sm text-slate-500">No audit events found.</p>@endforelse</div></section>
<p class="text-xs text-slate-500">Post-dispatch cancellation is not available here; handle shipment exceptions through the authorized Delivery workflow. Manual fulfillment record only; not a courier booking or paid tax invoice.</p>
@include('vendor.sublimation.partials.page-end')
