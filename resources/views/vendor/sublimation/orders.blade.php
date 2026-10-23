@php($pageTitle = ['design'=>'Design Approvals','production'=>'Production Queue','delivery'=>'Dispatch & Delivery'][$queue] ?? 'Order List & Delivery')
@include('vendor.sublimation.partials.page-start')
<form class="no-print grid gap-3 rounded-2xl border bg-white p-4 md:grid-cols-4" method="GET" action="{{ route('vendor.sublimation.orders.index', $business->id) }}">
<label class="text-sm font-semibold">Branch<select name="branch" class="mt-1 w-full rounded-lg border p-2">@foreach($branches as $option)<option value="{{ $option->id }}" @selected($branch && $branch->id == $option->id)>{{ $option->name }}</option>@endforeach</select></label>
@if(in_array($queue, ['design','production','delivery'], true))<input type="hidden" name="queue" value="{{ $queue }}">@endif
<label class="text-sm font-semibold">Status<select name="status" class="mt-1 w-full rounded-lg border p-2"><option value="">All statuses</option>
@php($queueStatuses = ['design'=>['design_pending','approval_pending'], 'production'=>['approved','in_production'], 'delivery'=>['ready','dispatched','delivered']])
@foreach($statuses as $status)@if(!isset($queueStatuses[$queue]) || in_array($status, $queueStatuses[$queue], true))<option value="{{ $status }}" @selected($status===$status)>{{ ucwords(str_replace('_',' ',$status)) }}</option>@endif @endforeach</select></label>
<label class="text-sm font-semibold">Search<input name="q" maxlength="100" value="{{ $search }}" placeholder="Order no, customer or product" class="mt-1 w-full rounded-lg border p-2"></label>
<div class="flex items-end"><button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Search / Filter</button></div>
</form>
<div class="no-print flex flex-wrap items-center justify-between gap-3"><p class="text-sm text-slate-600">{{ $orders->total() }} matching order(s)</p><a class="rounded-lg bg-rose-700 px-4 py-2 font-semibold text-white" href="{{ route('vendor.sublimation.orders.create', ['business'=>$business->id,'branch'=>$branch?->id]) }}">+ New Studio Job</a></div>
<div class="overflow-x-auto rounded-2xl border bg-white"><table class="w-full text-left text-sm"><thead class="border-b bg-slate-100"><tr><th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Product / Qty</th><th class="p-3">Fulfillment</th><th class="p-3">Status</th><th class="p-3">Quote</th><th class="p-3">Open</th></tr></thead>
<tbody>@forelse($orders as $order)
<tr class="border-b"><td class="p-3"><span class="font-semibold">{{ $order->order_no }}</span><span class="block text-xs text-slate-500">{{ $order->created_at }}</span></td><td class="p-3">{{ $order->customer_name }}</td>
<td class="p-3">{{ $order->product_name_snapshot }} × {{ $order->quantity }}</td>
<td class="p-3">{{ ($order->fulfillment_method ?? 'pickup') === 'delivery' ? 'Home delivery' : 'Pickup' }}</td>
<td class="p-3">{{ ucwords(str_replace('_',' ',$order->status)) }}</td><td class="p-3">₹{{ number_format($order->total_sale_paise/100,2) }}</td>
<td class="p-3"><a class="font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.orders.show',['business'=>$business->id,'order'=>$order->id]) }}">Details →</a></td></tr>
@empty<tr><td colspan="7" class="p-6 text-slate-600">No orders match this filter.</td></tr>@endforelse</tbody></table></div>
{{ $orders->links() }}
@include('vendor.sublimation.partials.page-end')
