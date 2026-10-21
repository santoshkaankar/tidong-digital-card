@php($pageTitle = 'Create Item')
@include('vendor.sublimation.partials.page-start')
@if(!$branch)
<div class="rounded-xl border bg-white p-5">Create a business branch before adding products.</div>
@else
<form class="grid gap-4 rounded-2xl border bg-white p-5 md:grid-cols-2" method="POST" action="{{ route('vendor.sublimation.products.store',$business->id) }}">
@csrf
<label class="text-sm font-semibold md:col-span-2">Business Branch
<select name="branch_id" required class="mt-1 w-full rounded-lg border p-3">@foreach($branches as $option)<option value="{{ $option->id }}" @selected(old('branch_id', $branch->id)==$option->id)>{{ $option->name }}</option>@endforeach</select></label>
<label class="text-sm font-semibold md:col-span-2">Product Name<input name="name" required maxlength="160" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border p-3" placeholder="Personalized Photo Mug"></label>
<label class="text-sm font-semibold">Category<select name="category" class="mt-1 w-full rounded-lg border p-3">
@foreach(['Mug','T-Shirt','Cushion','Keychain','Bottle','Photo Frame','Mouse Pad','Coaster','Tote Bag','Puzzle','Other'] as $category)
<option value="{{ $category }}" @selected(old('category')===$category)>{{ $category }}</option>@endforeach</select></label>
@foreach(['blank_cost'=>'Blank Material Cost (₹)','print_cost'=>'Print Cost (₹)','packing_cost'=>'Packing Cost (₹)','other_cost'=>'Press / Other Cost (₹)','sale_price'=>'Selling Price (₹)'] as $field=>$label)
<label class="text-sm font-semibold">{{ $label }}<input name="{{ $field }}" type="number" min="0" max="1000000" step="0.01" required value="{{ old($field,'0.00') }}" class="mt-1 w-full rounded-lg border p-3"></label>
@endforeach
<label class="text-sm font-semibold">Available Sizes <span class="font-normal text-slate-500">(comma-separated)</span><input name="size_options" maxlength="600" value="{{ old('size_options') }}" class="mt-1 w-full rounded-lg border p-3" placeholder="S, M, L"></label>
<label class="text-sm font-semibold">Available Colors <span class="font-normal text-slate-500">(comma-separated)</span><input name="color_options" maxlength="600" value="{{ old('color_options') }}" class="mt-1 w-full rounded-lg border p-3" placeholder="White, Black"></label>
<label class="text-sm font-semibold md:col-span-2">Production Notes<textarea name="production_notes" maxlength="2000" rows="4" class="mt-1 w-full rounded-lg border p-3">{{ old('production_notes') }}</textarea></label>
<p class="text-xs text-slate-500 md:col-span-2">Size/color options share the base price. Set an accurate selling price before creating orders.</p>
<div class="md:col-span-2 flex flex-wrap gap-3"><button class="rounded-lg bg-rose-700 px-5 py-3 font-bold text-white">Save Product</button><a class="rounded-lg border px-5 py-3 font-semibold" href="{{ route('vendor.sublimation.products.index', ['business'=>$business->id,'branch'=>$branch->id]) }}">Product List</a></div>
</form>
@endif
@include('vendor.sublimation.partials.page-end')
