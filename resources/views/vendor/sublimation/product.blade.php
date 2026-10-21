@php($pageTitle = 'Individual Product · '.$item->name)
@include('vendor.sublimation.partials.page-start')
<div class="no-print"><a class="text-sm font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.products.index', ['business'=>$business->id,'branch'=>$branch->id]) }}">← All Products</a></div>
@php($totalCost = $item->blank_cost_paise+$item->print_cost_paise+$item->packing_cost_paise+$item->other_cost_paise)
<div class="grid gap-4 md:grid-cols-3">
<div class="rounded-xl border bg-white p-4"><p class="text-sm text-slate-500">Status</p><p class="mt-1 text-xl font-bold">{{ $item->is_active ? 'Active' : 'Inactive' }}</p></div>
<div class="rounded-xl border bg-white p-4"><p class="text-sm text-slate-500">Cost / Piece</p><p class="mt-1 text-xl font-bold">₹{{ number_format($totalCost/100,2) }}</p></div>
<div class="rounded-xl border bg-white p-4"><p class="text-sm text-slate-500">Sale / Piece</p><p class="mt-1 text-xl font-bold">₹{{ number_format($item->sale_price_paise/100,2) }}</p></div>
</div>
<form method="POST" action="{{ route('vendor.sublimation.products.update', ['business'=>$business->id,'product'=>$item->id]) }}" class="grid gap-4 rounded-2xl border bg-white p-5 md:grid-cols-2">
@csrf @method('PATCH')
<input type="hidden" name="branch_id" value="{{ $branch->id }}">
<label class="text-sm font-semibold md:col-span-2">Product Name<input name="name" required maxlength="160" value="{{ old('name',$item->name) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Category<select name="category" class="mt-1 w-full rounded-lg border p-3">
@foreach(['Mug','T-Shirt','Cushion','Keychain','Bottle','Photo Frame','Mouse Pad','Coaster','Tote Bag','Puzzle','Other'] as $category)
<option value="{{ $category }}" @selected(old('category',$item->category) === $category)>{{ $category }}</option>@endforeach
</select></label>
<label class="text-sm font-semibold">Blank Cost (₹)<input type="number" step=".01" min="0" name="blank_cost" required value="{{ old('blank_cost',number_format($item->blank_cost_paise/100,2,'.','')) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Print Cost (₹)<input type="number" step=".01" min="0" name="print_cost" required value="{{ old('print_cost',number_format($item->print_cost_paise/100,2,'.','')) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Packing Cost (₹)<input type="number" step=".01" min="0" name="packing_cost" required value="{{ old('packing_cost',number_format($item->packing_cost_paise/100,2,'.','')) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Press / Other (₹)<input type="number" step=".01" min="0" name="other_cost" required value="{{ old('other_cost',number_format($item->other_cost_paise/100,2,'.','')) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Sale Price / Piece (₹)<input type="number" step=".01" min="0" name="sale_price" required value="{{ old('sale_price',number_format($item->sale_price_paise/100,2,'.','')) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Size Options (comma-separated)<input name="size_options" maxlength="600" value="{{ old('size_options',implode(', ', $choices['size'] ?? [])) }}" placeholder="e.g. S, M, L, XL" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Color Options (comma-separated)<input name="color_options" maxlength="600" value="{{ old('color_options',implode(', ', $choices['color'] ?? [])) }}" placeholder="e.g. White, Black" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold md:col-span-2">Production Notes<textarea name="production_notes" maxlength="2000" rows="3" class="mt-1 w-full rounded-lg border p-3">{{ old('production_notes',$item->production_notes) }}</textarea></label>
<label class="flex gap-2 text-sm font-semibold md:col-span-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$item->is_active))> Active and orderable when business &amp; branch accept orders</label>
<p class="text-xs text-amber-800 md:col-span-2">Size/color options share one base price. No automatic surcharges or stock guarantees; confirm custom requirements with the customer. Existing order snapshots do not change.</p>
<button class="rounded-lg bg-rose-700 px-5 py-3 font-semibold text-white md:col-span-2">Save Product &amp; Options</button>
</form>
@include('vendor.sublimation.partials.page-end')
