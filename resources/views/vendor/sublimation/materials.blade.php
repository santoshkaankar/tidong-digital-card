@php($pageTitle = 'Materials & Stock')
@include('vendor.sublimation.partials.page-start')
@if(!$branch)
<div class="rounded-xl border bg-white p-5">Create a branch to manage its material stock.</div>
@else
<div class="no-print flex flex-wrap items-end gap-3 rounded-xl border bg-white p-4">
<form method="GET" action="{{ route('vendor.sublimation.materials.index',$business->id) }}" class="flex flex-wrap items-end gap-3"><label class="text-sm font-semibold">Branch<select name="branch" class="mt-1 block rounded-lg border p-2">@foreach($branches as $option)<option value="{{ $option->id }}" @selected($option->id==$branch->id)>{{ $option->name }}</option>@endforeach</select></label><button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Switch Branch</button></form>
</div>
<p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Material balances are recorded manually by the vendor. Production orders do not automatically deduct stock; record usage with Stock Out.</p>
<section class="rounded-2xl border bg-white p-5"><h2 class="text-xl font-bold">Add Material</h2>
<form method="POST" action="{{ route('vendor.sublimation.materials.store', $business->id) }}" class="mt-4 grid gap-3 md:grid-cols-5">@csrf
<input type="hidden" name="branch" value="{{ $branch->id }}">
<label class="text-sm font-semibold md:col-span-2">Material Name<input name="name" required maxlength="160" placeholder="Blank ceramic mugs" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">SKU (optional)<input name="sku" maxlength="80" value="{{ old('sku') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Unit<select name="unit" class="mt-1 w-full rounded-lg border p-3">@foreach(['pcs','sheets','ml','meters','grams','packs'] as $unit)<option value="{{ $unit }}" @selected(old('unit')===$unit)>{{ $unit }}</option>@endforeach</select></label>
<label class="text-sm font-semibold">Low Stock Level<input name="low_stock_level" inputmode="decimal" required value="{{ old('low_stock_level','0') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<button class="rounded-lg bg-rose-700 px-5 py-3 font-semibold text-white md:col-span-5">Create Material</button>
</form></section>
<section class="overflow-x-auto rounded-2xl border bg-white"><table class="w-full text-left text-sm"><thead class="bg-slate-100"><tr><th class="p-3">Material</th><th class="p-3">Available</th><th class="p-3">Low Limit</th><th class="p-3">Stock Adjustment</th></tr></thead><tbody>
@forelse($materials as $material)
<tr class="border-b"><td class="p-3"><strong>{{ $material->name }}</strong><div class="text-xs text-slate-500">{{ $material->sku ?? 'No SKU' }} · {{ $material->unit }}</div></td>
<td class="p-3 font-semibold {{ (float)$material->on_hand <= (float)$material->low_stock_level ? 'text-rose-700' : 'text-emerald-700' }}">{{ number_format((float)$material->on_hand,3) }} {{ $material->unit }}</td>
<td class="p-3">{{ number_format((float)$material->low_stock_level,3) }}</td>
<td class="p-3"><form class="flex flex-wrap gap-2" method="POST" action="{{ route('vendor.sublimation.materials.movement',['business'=>$business->id,'material'=>$material->id]) }}">@csrf
<input name="branch" type="hidden" value="{{ $branch->id }}">
<select name="direction" aria-label="Stock movement direction" class="rounded-lg border p-2"><option value="in">Stock In</option><option value="out">Stock Out</option></select>
<input name="quantity" inputmode="decimal" placeholder="Quantity" required class="w-24 rounded-lg border p-2" aria-label="Quantity">
<input name="reason" maxlength="300" required placeholder="Reason / reference" class="min-w-40 flex-1 rounded-lg border p-2" aria-label="Reason">
<button class="rounded-lg bg-slate-900 px-3 py-2 text-white">Save</button></form></td></tr>
@empty<tr><td class="p-5 text-slate-500" colspan="4">No materials for this branch yet.</td></tr>@endforelse</tbody></table></section>
{{ $materials->links() }}
<section class="rounded-2xl border bg-white p-5"><h2 class="text-xl font-bold">Recent Stock Movements</h2>
<div class="mt-3 overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">Time</th><th class="p-2">Material</th><th class="p-2">Type</th><th class="p-2">Quantity</th><th class="p-2">Reason</th></tr></thead><tbody>
@forelse($recent as $movement)<tr class="border-b"><td class="p-2">{{ $movement->created_at }}</td><td class="p-2">{{ $movement->material_name }}</td><td class="p-2">{{ ucfirst($movement->direction) }}</td><td class="p-2">{{ $movement->quantity }}</td><td class="p-2">{{ $movement->reason }}</td></tr>
@empty<tr><td colspan="5" class="p-4 text-slate-500">No movements yet.</td></tr>@endforelse</tbody></table></div></section>
@endif
@include('vendor.sublimation.partials.page-end')
