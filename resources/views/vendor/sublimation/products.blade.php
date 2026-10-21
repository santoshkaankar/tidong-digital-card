@php($pageTitle = 'Product List & Options')
@include('vendor.sublimation.partials.page-start')
<form class="no-print grid gap-3 rounded-2xl border bg-white p-4 md:grid-cols-4" method="GET" action="{{ route('vendor.sublimation.products.index', $business->id) }}">
<label class="text-sm font-semibold">Branch
<select name="branch" class="mt-1 w-full rounded-lg border p-2">@foreach($branches as $option)<option value="{{ $option->id }}" @selected($branch && $option->id == $branch->id)>{{ $option->name }}</option>@endforeach</select></label>
<label class="text-sm font-semibold">Availability
<select name="state" class="mt-1 w-full rounded-lg border p-2">
<option value="">All products</option><option value="active" @selected(request('state') === 'active')>Active</option><option value="inactive" @selected(request('state') === 'inactive')>Inactive</option>
</select></label>
<label class="text-sm font-semibold">Find product<input name="q" maxlength="100" value="{{ request('q') }}" placeholder="Name" class="mt-1 w-full rounded-lg border p-2"></label>
<div class="flex items-end"><button class="w-full rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Search / Filter</button></div>
</form>
<div class="no-print flex flex-wrap justify-between gap-2"><p class="text-sm text-slate-600">{{ $products->total() }} product(s). Existing seeded products remain unchanged.</p><a class="rounded-lg bg-rose-700 px-4 py-2 font-semibold text-white" href="{{ route('vendor.sublimation.products.create', ['business'=>$business->id,'branch'=>$branch?->id]) }}">+ Add Product</a></div>
<section class="overflow-x-auto rounded-2xl border bg-white"><table class="w-full text-left text-sm"><thead class="border-b bg-slate-100"><tr>
<th class="p-3">Product</th><th class="p-3">Category</th><th class="p-3">Options</th><th class="p-3">Cost</th><th class="p-3">Selling Price</th><th class="p-3">Status</th><th class="p-3">Action</th></tr></thead>
<tbody>
@forelse($products as $product)
@php($options = json_decode($product->option_choices ?? '{}', true) ?: [])
@php($cost = $product->blank_cost_paise+$product->print_cost_paise+$product->packing_cost_paise+$product->other_cost_paise)
<tr class="border-b"><td class="p-3 font-semibold">{{ $product->name }}</td><td class="p-3">{{ $product->category }}</td>
<td class="p-3 text-slate-600">@if (!empty($options['size']))Sizes: {{ implode(', ', $options['size']) }}@endif @if (!empty($options['color']))<span class="block">Colors: {{ implode(', ', $options['color']) }}</span>@endif @if(empty($options['size']) && empty($options['color']))—@endif</td>
<td class="p-3">₹{{ number_format($cost/100,2) }}</td><td class="p-3">₹{{ number_format($product->sale_price_paise/100,2) }}</td>
<td class="p-3">{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
<td class="p-3"><a class="font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.products.show', ['business'=>$business->id,'product'=>$product->id]) }}">View / Edit →</a></td></tr>
@empty<tr><td class="p-6 text-slate-600" colspan="7">No products match your search.</td></tr>@endforelse
</tbody></table></section>
{{ $products->links() }}
@include('vendor.sublimation.partials.page-end')
