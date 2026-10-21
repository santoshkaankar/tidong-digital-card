@php($pageTitle = 'Studio Reports')
@include('vendor.sublimation.partials.page-start')
@if(!$branch)
<div class="rounded-xl border bg-white p-5">Create a branch to view its reports.</div>
@else
<form class="no-print flex flex-wrap items-end gap-3 rounded-xl border bg-white p-4" method="GET" action="{{ route('vendor.sublimation.reports.index',$business->id) }}">
<label class="text-sm font-semibold">Branch<select name="branch" class="mt-1 block rounded-lg border p-2">@foreach($branches as $option)<option value="{{ $option->id }}" @selected($branch->id==$option->id)>{{ $option->name }}</option>@endforeach</select></label><button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">View Report</button>
</form>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
@foreach([
 ['Products',$products],['Active Products',$activeProducts],['Total Orders',$orders],
 ['Non-cancelled Order Quotes','₹'.number_format($quotedPaise/100,2)],
 ['Estimated Job Costs','₹'.number_format($estimatedCostPaise/100,2)],
 ['Estimated Gross Difference','₹'.number_format(($quotedPaise-$estimatedCostPaise)/100,2)],
 ['Low / Zero Stock Materials',$lowMaterials],
] as [$label,$value])
<div class="rounded-xl border bg-white p-5"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold">{{ $value }}</p></div>
@endforeach
</div>
<p class="rounded-xl border bg-amber-50 p-4 text-sm text-amber-900">All-time branch totals. Order quotes are not payment receipts, revenue recognition, invoices or taxable profit. Cancelled orders are excluded from the quoted-cost summary.</p>
<div class="grid gap-5 lg:grid-cols-2">
<section class="rounded-xl border bg-white p-5"><h2 class="text-xl font-bold">Order Status Breakdown</h2><table class="mt-3 w-full text-sm"><thead><tr class="border-b"><th class="p-2 text-left">Status</th><th class="p-2 text-right">Orders</th></tr></thead><tbody>
@forelse($statuses as $row)<tr class="border-b"><td class="p-2">{{ ucwords(str_replace('_',' ',$row->status)) }}</td><td class="p-2 text-right">{{ $row->total }}</td></tr>
@empty<tr><td class="p-3" colspan="2">No orders yet.</td></tr>@endforelse
</tbody></table></section>
<section class="rounded-xl border bg-white p-5"><h2 class="text-xl font-bold">Recent Orders</h2><table class="mt-3 w-full text-sm"><thead><tr class="border-b"><th class="p-2 text-left">Order</th><th class="p-2">Status</th><th class="p-2 text-right">Quote</th></tr></thead><tbody>
@forelse($recent as $item)<tr class="border-b"><td class="p-2"><a class="font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.orders.show',['business'=>$business->id,'order'=>$item->id]) }}">{{ $item->order_no }}</a></td><td class="p-2">{{ ucwords(str_replace('_',' ',$item->status)) }}</td><td class="p-2 text-right">₹{{ number_format($item->total_sale_paise/100,2) }}</td></tr>
@empty<tr><td class="p-3" colspan="3">No orders yet.</td></tr>@endforelse
</tbody></table></section>
</div>
@endif
@include('vendor.sublimation.partials.page-end')
