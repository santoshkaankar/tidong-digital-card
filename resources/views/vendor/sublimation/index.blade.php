@php($pageTitle = 'Sublimation Dashboard')
@include('vendor.sublimation.partials.page-start')
@if(!$branch)
<div class="rounded-xl border bg-white p-6">Create a branch for this business first. <a class="font-semibold text-blue-700 underline" href="{{ route('vendor.home') }}">Vendor Dashboard</a></div>
@else
<form method="GET" action="{{ route('vendor.sublimation.index', $business->id) }}" class="no-print flex flex-wrap items-end gap-3 rounded-xl border bg-white p-4">
<label class="text-sm font-semibold">Working Branch
<select name="branch" class="mt-1 block rounded-lg border bg-white p-2">@foreach($branches as $option)<option value="{{ $option->id }}" @selected($branch->id == $option->id)>{{ $option->name }}</option>@endforeach</select>
</label><button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Switch Branch</button>
</form>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
@foreach([
 ['Total Products', $stats['products']], ['Active Products', $stats['active_products']],
 ['Total Orders', $stats['orders']], ['Awaiting Design', $stats['awaiting_design']],
 ['In Production', $stats['in_production']], ['Ready / Dispatch', $stats['ready_dispatch']],
 ['Delivered Orders', $stats['delivered']], ['Open Order Quotes', '₹'.number_format($stats['open_quote_paise']/100,2)],
] as [$label,$value])
<div class="rounded-xl border bg-white p-5"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold">{{ $value }}</p></div>
@endforeach
</div>
<div class="rounded-xl border bg-white p-5">
<div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-xl font-bold">Recent Orders</h2>
<a class="text-sm font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.orders.index', ['business'=>$business->id,'branch'=>$branch->id]) }}">Open Order List →</a></div>
<div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b bg-slate-50"><th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Status</th><th class="p-3">Amount</th></tr></thead><tbody>
@forelse($recentOrders as $order)
<tr class="border-b"><td class="p-3"><a class="font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.orders.show',['business'=>$business->id,'order'=>$order->id]) }}">{{ $order->order_no }}</a></td><td class="p-3">{{ $order->customer_name }}</td><td class="p-3">{{ ucwords(str_replace('_',' ',$order->status)) }}</td><td class="p-3">₹{{ number_format($order->total_sale_paise/100,2) }}</td></tr>
@empty<tr><td class="p-5 text-slate-500" colspan="4">No orders yet.</td></tr>@endforelse
</tbody></table></div>
</div>
<p class="text-xs text-slate-500">This dashboard shows statistics only. Use the sidebar for Create Item, Item List, Orders, Stock and Reports. Quotes are not collected payments.</p>
@endif
@include('vendor.sublimation.partials.page-end')
