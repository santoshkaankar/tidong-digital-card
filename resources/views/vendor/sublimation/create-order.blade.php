@php($pageTitle = 'New Studio Order')
@include('vendor.sublimation.partials.page-start')
@if(!$branch)
<div class="rounded-xl border bg-white p-5">Create a branch before placing internal orders.</div>
@elseif($products->isEmpty())
<div class="rounded-xl border border-amber-200 bg-amber-50 p-5">No active, priced products for this branch. <a class="font-semibold text-blue-700 underline" href="{{ route('vendor.sublimation.products.index', ['business'=>$business->id,'branch'=>$branch->id]) }}">Open Product List</a> to price and activate one.</div>
@else
<form method="POST" action="{{ route('vendor.sublimation.orders.store', $business->id) }}" class="grid gap-4 rounded-2xl border bg-white p-5 md:grid-cols-2">
@csrf
<input type="hidden" name="branch_id" value="{{ $branch->id }}">
<label class="text-sm font-semibold md:col-span-2">Product<select name="product_id" id="new-order-product" required class="mt-1 w-full rounded-lg border bg-white p-3">
@foreach($products as $product)<option value="{{ $product->id }}" data-choices="{{ $product->option_choices ?? '{}' }}" @selected(old('product_id')==$product->id)>{{ $product->name }} — ₹{{ number_format($product->sale_price_paise/100,2) }} / piece</option>@endforeach</select></label>
<label id="new-order-size-wrap" class="text-sm font-semibold" hidden>Size<select name="size" id="new-order-size" data-old="{{ old('size') }}" class="mt-1 w-full rounded-lg border p-3"></select></label>
<label id="new-order-color-wrap" class="text-sm font-semibold" hidden>Color<select name="color" id="new-order-color" data-old="{{ old('color') }}" class="mt-1 w-full rounded-lg border p-3"></select></label>
<label class="text-sm font-semibold">Customer Name<input name="customer_name" required maxlength="160" value="{{ old('customer_name') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Contact Phone (optional)<input name="customer_mobile" maxlength="25" value="{{ old('customer_mobile') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Quantity<input name="quantity" type="number" min="1" max="10000" required value="{{ old('quantity',1) }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold">Due Date<input name="due_date" type="date" value="{{ old('due_date') }}" class="mt-1 w-full rounded-lg border p-3"></label>
<label class="text-sm font-semibold md:col-span-2">Design Instructions<textarea name="design_instructions" maxlength="3000" rows="4" class="mt-1 w-full rounded-lg border p-3" placeholder="Custom photo, name, text, finish...">{{ old('design_instructions') }}</textarea></label>
<p class="text-xs text-slate-500 md:col-span-2">Manual internal studio order. For home delivery customers, use the customer order request flow; do not assume this creates a courier booking.</p>
<div class="md:col-span-2"><button class="rounded-lg bg-rose-700 px-5 py-3 font-bold text-white">Create Studio Order</button></div>
</form>
<script>
(() => {
 const product = document.getElementById('new-order-product'); if (!product) return;
 function refresh() {
   let choices={}; try { choices=JSON.parse(product.selectedOptions[0]?.dataset.choices||'{}'); } catch (_) {}
   for (const key of ['size','color']) {
     const field=document.getElementById('new-order-'+key), wrap=document.getElementById('new-order-'+key+'-wrap');
     const values=Array.isArray(choices[key])?choices[key]:[];
     field.replaceChildren(); field.disabled=!values.length; field.required=!!values.length; wrap.hidden=!values.length;
     if (!values.length) continue;
     field.add(new Option('Choose '+key,''));
     for (const value of values) field.add(new Option(value,value));
     if (values.includes(field.dataset.old)) field.value=field.dataset.old;
   }
 }
 product.addEventListener('change', () => {for(const key of ['size','color']) document.getElementById('new-order-'+key).dataset.old=''; refresh();});
 refresh();
})();
</script>
@endif
@include('vendor.sublimation.partials.page-end')
