<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Grocery Workspace · TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800">
@php
    $money = fn($paise) => '₹'.number_format(((int)$paise) / 100, 2);
    $qtyFormat = fn($qty) => number_format(((int)$qty) / 1000, 3);
    $link = fn($branchId) => route('vendor.grocery.index', ['business' => $business->id, 'branch' => $branchId]);
@endphp
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row xl:p-6">
    @include('vendor.partials.sidebar')
    <main class="w-full min-w-0 flex-1 space-y-6">
        <header class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-widest text-emerald-300">TIDONG Grocery</p>
            <h1 class="mt-1 text-3xl font-black">{{ $business->name }}</h1>
            <p class="mt-2 text-sm text-slate-300">Branch-level product, pricing, inventory and billing tools · Business: {{ ucfirst($business->status) }}</p>
            <a class="mt-3 inline-block rounded border border-white/40 px-4 py-2 text-sm" href="{{ route('vendor.business.workspace', $business->id) }}">Business Overview</a>
        </header>
        @if(session('success'))<p role="status" class="rounded-lg bg-emerald-50 p-4 text-emerald-900">{{ session('success') }}</p>@endif
        @if($errors->any())<div role="alert" class="rounded-lg bg-rose-50 p-4 text-rose-900"><h2 class="font-semibold">Please check your entries</h2>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold">Select Branch</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @forelse($branches as $option)
                    <a href="{{ $link($option->id) }}" class="rounded border px-3 py-2 text-sm {{ $branch && (int)$option->id === (int)$branch->id ? 'border-emerald-700 bg-emerald-700 text-white' : 'bg-white' }}">{{ $option->name }}</a>
                @empty
                    <p>Add a branch in Vendor Dashboard before setting up Grocery.</p>
                @endforelse
            </div>
        </section>
        @if($branch)
            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Grocery workspace overview">
                <div class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Catalogue products</p><p class="mt-1 text-3xl font-extrabold text-emerald-900">{{ $products->count() }}</p></div>
                <div class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Active listings</p><p class="mt-1 text-3xl font-extrabold text-emerald-900">{{ $products->where('is_active', true)->count() }}</p></div>
                <div class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Recent counter sales</p><p class="mt-1 text-3xl font-extrabold text-emerald-900">{{ $sales->count() }}</p></div>
                <div class="rounded-2xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Online order management</p><a class="mt-2 inline-block rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white" href="{{ route('vendor.grocery.online.index', ['business' => $business->id, 'branch' => $branch->id]) }}">{{ $flags['online_orders'] ? 'Review Online Orders →' : 'Disabled by Settings' }}</a></div>
            </section>
            @if($flags['online_store'])
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-5"><div><strong class="text-emerald-900">Customer Shopping Website</strong><p class="text-sm text-emerald-700">Your branch catalogue is available for browsing (subject to stock and business approval).</p></div><a class="rounded-lg bg-emerald-700 px-5 py-3 font-semibold text-white" target="_blank" rel="noopener noreferrer" href="{{ route('grocery.shop.catalog', [$business->id, $branch->id]) }}">Preview Online Store ↗</a></div>
            @endif
            <section class="rounded-xl bg-white p-5 shadow-sm" id="grocery-settings">
                <h2 class="text-lg font-bold">Branch Options</h2>
                <p class="text-sm text-slate-500">You can disable operations in this branch. Global Admin OFF always wins.</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach(\App\Services\Grocery\GroceryFeatures::LABELS as $feature => $label)
                        @if($feature !== 'module')
                            @php($effective = (bool)($flags[$feature] ?? false))
                            <div class="rounded-lg border p-3">
                                <p class="font-medium">{{ $label }}</p>
                                <p class="text-xs {{ $effective ? 'text-emerald-700' : 'text-rose-700' }}">Effective: {{ $effective ? 'ON' : 'OFF' }}</p>
                                <form method="POST" action="{{ route('vendor.grocery.branch-settings', $business->id) }}" class="mt-2 flex gap-2">@csrf
                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                    <input type="hidden" name="feature" value="{{ $feature }}">
                                    <select name="enabled" aria-label="Branch {{ $label }} preference" class="rounded border p-2 text-sm"><option value="1" @selected(!isset($branchPreferences[$feature]) || (bool)$branchPreferences[$feature])>Allow if Admin ON</option><option value="0" @selected(isset($branchPreferences[$feature]) && !(bool)$branchPreferences[$feature])>Disable for Branch</option></select>
                                    <button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Save</button>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            </section>
            @if($flags['catalogue'] && $flags['pricing'])
                <section class="rounded-xl bg-white p-5 shadow-sm" id="grocery-products">
                    <h2 class="text-lg font-bold">Add Grocery Product</h2>
                    <p class="mt-1 text-sm text-slate-500">New products remain inactive until manually activated.</p>
                    <form method="POST" action="{{ route('vendor.grocery.products.store', $business->id) }}" class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">@csrf
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <label class="text-sm">SKU *<input required maxlength="64" name="sku" value="{{ old('sku') }}" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Barcode<input maxlength="80" name="barcode" value="{{ old('barcode') }}" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Product Name *<input required maxlength="180" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Category *<input required maxlength="100" name="category" value="{{ old('category') }}" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Unit *<select name="unit" class="mt-1 w-full rounded border p-2">@foreach(['pcs','pack','kg','g','l','ml'] as $unit)<option value="{{ $unit }}">{{ $unit }}</option>@endforeach</select></label>
                        <label class="text-sm">HSN<input name="hsn_code" maxlength="12" value="{{ old('hsn_code') }}" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">GST (%) *<input name="gst_rate" type="number" step="0.01" min="0" max="40" value="{{ old('gst_rate', '0') }}" required class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Tax Mode<select name="tax_inclusive" class="mt-1 w-full rounded border p-2"><option value="1">Tax Inclusive</option><option value="0">Tax Exclusive</option></select></label>
                        <label class="text-sm">Offline MRP (₹) *<input name="offline_mrp" type="number" step="0.01" min="0.01" required class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Offline Sale (₹) *<input name="offline_sale" type="number" step="0.01" min="0.01" required class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-sm">Online MRP (₹)<input name="online_mrp" type="number" step="0.01" min="0.01" class="mt-1 w-full rounded border p-2" placeholder="Default offline MRP"></label>
                        <label class="text-sm">Online Sale (₹)<input name="online_sale" type="number" step="0.01" min="0.01" class="mt-1 w-full rounded border p-2" placeholder="Default offline Sale"></label>
                        <button class="rounded bg-emerald-700 px-5 py-3 font-semibold text-white sm:col-span-2 xl:col-span-4">Create Inactive Product</button>
                    </form>
                </section>
            @endif
            <section class="rounded-xl bg-white p-5 shadow-sm" id="grocery-inventory">
                <h2 class="text-lg font-bold">Products & Branch Inventory</h2>
                <div class="mt-3 space-y-4">
                    @forelse($products as $product)
                        <div class="rounded-lg border p-4">
                            <div class="flex flex-wrap justify-between gap-2"><div><p class="font-bold">{{ $product->name }}</p><p class="text-xs text-slate-500">{{ $product->sku }} · {{ $product->category }} · {{ $product->unit }} · {{ $product->is_active ? 'Active' : 'Inactive' }}</p></div><p class="font-semibold">{{ $money($product->offline_sale_paise) }} <span class="text-xs font-normal text-slate-500">MRP {{ $money($product->offline_mrp_paise) }} · Save {{ $money(max(0, $product->offline_mrp_paise - $product->offline_sale_paise)) }} ({{ $product->offline_mrp_paise ? round(max(0, $product->offline_mrp_paise - $product->offline_sale_paise) * 100 / $product->offline_mrp_paise, 1) : 0 }}%)</span></p></div>
                            @if($flags['catalogue'])
                                <form method="POST" action="{{ route('vendor.grocery.products.availability', ['business' => $business->id, 'product' => $product->id]) }}" class="mt-3 inline-flex items-center gap-2">@csrf @method('PATCH')
                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}"><input type="hidden" name="active" value="{{ $product->is_active ? 0 : 1 }}">
                                    <button class="rounded border px-3 py-2 text-sm">{{ $product->is_active ? 'Deactivate Product' : 'Activate Product' }}</button>
                                </form>
                            @endif
                            @if($flags['pricing'])
                                <form method="POST" action="{{ route('vendor.grocery.products.price', ['business' => $business->id, 'product' => $product->id]) }}" class="mt-3 grid gap-2 sm:grid-cols-5">@csrf @method('PATCH')
                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                    <label class="text-xs">Offline MRP<input name="offline_mrp" value="{{ \App\Services\Grocery\GroceryMath::formatPaise($product->offline_mrp_paise) }}" required class="mt-1 w-full rounded border p-2"></label>
                                    <label class="text-xs">Offline Sale<input name="offline_sale" value="{{ \App\Services\Grocery\GroceryMath::formatPaise($product->offline_sale_paise) }}" required class="mt-1 w-full rounded border p-2"></label>
                                    <label class="text-xs">Online MRP<input name="online_mrp" value="{{ $product->online_mrp_paise === null ? '' : \App\Services\Grocery\GroceryMath::formatPaise($product->online_mrp_paise) }}" class="mt-1 w-full rounded border p-2"></label>
                                    <label class="text-xs">Online Sale<input name="online_sale" value="{{ $product->online_sale_paise === null ? '' : \App\Services\Grocery\GroceryMath::formatPaise($product->online_sale_paise) }}" class="mt-1 w-full rounded border p-2"></label>
                                    <button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Update Prices</button>
                                </form>
                            @endif
                            @if($flags['catalogue'])
                            <details class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50/50 p-3"><summary class="cursor-pointer font-semibold text-emerald-900">Storefront Photo, Brand & Description</summary>
                                <form method="POST" action="{{ route('vendor.grocery.products.presentation', ['business' => $business->id, 'product' => $product->id]) }}" class="mt-3 grid gap-2 sm:grid-cols-2">@csrf @method('PATCH')
                                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                    <label class="text-sm">Brand<input name="brand" maxlength="100" value="{{ $product->brand }}" class="mt-1 w-full rounded border p-2"></label>
                                    <label class="text-sm">Image HTTPS URL<input type="url" name="image_url" maxlength="1500" value="{{ $product->image_url }}" placeholder="https://..." class="mt-1 w-full rounded border p-2"></label>
                                    <label class="text-sm sm:col-span-2">Short Product Description<textarea name="description" maxlength="2000" rows="2" class="mt-1 w-full rounded border p-2">{{ $product->description }}</textarea></label>
                                    <label class="flex items-center gap-2 text-sm"><input type="hidden" name="is_featured" value="0"><input type="checkbox" name="is_featured" value="1" @checked($product->is_featured)> Feature on shop homepage</label>
                                    <button class="rounded bg-emerald-800 px-3 py-2 text-white">Save Storefront Details</button>
                                </form>
                            </details>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No products in this branch yet.</p>
                    @endforelse
                </div>
                @if($flags['inventory'] && $products->isNotEmpty())
                    <h3 class="mt-5 font-semibold">Receive Stock / Lot</h3>
                    <form method="POST" action="{{ route('vendor.grocery.stock.receive', $business->id) }}" class="mt-2 grid gap-3 sm:grid-cols-5">@csrf
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <label class="text-xs">Product<select name="product_id" required class="mt-1 w-full rounded border p-2">@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }} · {{ $p->sku }}</option>@endforeach</select></label>
                        <label class="text-xs">Batch / Lot Code<input required name="lot_code" maxlength="80" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-xs">Quantity<input required name="quantity" type="number" min="0.001" step="0.001" class="mt-1 w-full rounded border p-2"></label>
                        <label class="text-xs">Expiry Date<input name="expiry_date" type="date" class="mt-1 w-full rounded border p-2"></label>
                        <button class="self-end rounded bg-blue-700 px-4 py-2 text-white">Receive Stock</button>
                    </form>
                @endif
                @if($lots->isNotEmpty())
                    <h3 class="mt-5 font-semibold">Recent Lots</h3>
                    <div class="mt-2 overflow-x-auto"><table class="min-w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">Product ID</th><th class="p-2">Lot</th><th class="p-2">Available</th><th class="p-2">Expiry</th></tr></thead><tbody>@foreach($lots as $lot)<tr class="border-b"><td class="p-2">{{ $lot->product_id }}</td><td class="p-2">{{ $lot->lot_code }}</td><td class="p-2">{{ $qtyFormat($lot->available_qty_milli) }}</td><td class="p-2">{{ $lot->expiry_date ?? 'No expiry recorded' }}</td></tr>@endforeach</tbody></table></div>
                @endif
            </section>
            <section class="rounded-xl bg-white p-5 shadow-sm" id="grocery-billing">
                <h2 class="text-lg font-bold">Counter Sales & Records</h2>
                <p class="mt-1 text-sm text-slate-500">Counter sales create immutable price/GST snapshots and stock movements. No payment is collected; customer online requests are managed separately.</p>
                @if($flags['counter_sales'] && $flags['inventory'] && $flags['pricing'] && $branch->state && $business->status === 'approved' && $business->is_accepting_orders && $branch->is_accepting_orders && $branch->status === 'active')
                    <form method="POST" action="{{ route('vendor.grocery.sales.store', $business->id) }}" class="mt-4 space-y-3" id="grocery-pos-form">@csrf
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <label class="block text-sm font-semibold">Supply State<input required maxlength="100" name="supply_state" value="{{ $branch->state }}" class="mt-1 w-full rounded border p-3"></label>
                        <div class="flex flex-wrap items-end gap-2"><label class="min-w-0 flex-1 text-sm">Scan Barcode or Enter SKU<input type="text" id="grocery-pos-scanner" class="mt-1 w-full rounded border p-3" placeholder="Scan an item and press Enter" autocomplete="off"></label><button id="grocery-pos-scan" type="button" class="rounded border border-emerald-700 p-3 font-semibold text-emerald-900">Add Scanned Item</button></div>
                        <p id="grocery-pos-hint" role="status" class="text-xs text-slate-600">Scanner acts like a keyboard. All prices and stock will be rechecked on submission.</p>
                        <div id="grocery-pos-lines" class="space-y-2">
                            <div class="grid gap-2 rounded-lg border bg-slate-50 p-3 sm:grid-cols-[1fr_130px]">
                                <label class="text-sm">Product<select required name="items[0][product_id]" class="grocery-pos-product mt-1 w-full rounded border p-2">@foreach($products as $p)@if($p->is_active)<option value="{{ $p->id }}" data-barcode="{{ $p->barcode }}" data-sku="{{ $p->sku }}">{{ $p->name }} · {{ $p->sku }}</option>@endif @endforeach</select></label>
                                <label class="text-sm">Quantity<input required type="number" step="0.001" min="0.001" name="items[0][quantity]" value="1" class="mt-1 w-full rounded border p-2"></label>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2"><button type="button" id="grocery-pos-add" class="rounded border border-emerald-700 px-4 py-2 font-bold text-emerald-800">+ Add Another Item</button><button type="submit" class="rounded bg-emerald-700 px-5 py-3 font-bold text-white">Record Multi-Item Counter Sale</button></div>
                    </form>
                    <template id="grocery-pos-line-template">
                        <div class="grid gap-2 rounded-lg border bg-slate-50 p-3 sm:grid-cols-[1fr_130px_auto]">
                            <label class="text-sm">Product<select required name="items[__INDEX__][product_id]" class="grocery-pos-product mt-1 w-full rounded border p-2">@foreach($products as $p)@if($p->is_active)<option value="{{ $p->id }}" data-barcode="{{ $p->barcode }}" data-sku="{{ $p->sku }}">{{ $p->name }} · {{ $p->sku }}</option>@endif @endforeach</select></label>
                            <label class="text-sm">Quantity<input required type="number" step="0.001" min="0.001" name="items[__INDEX__][quantity]" value="1" class="mt-1 w-full rounded border p-2"></label>
                            <button type="button" class="grocery-pos-remove self-end rounded border border-rose-300 px-3 py-2 text-rose-800">Remove</button>
                        </div>
                    </template>
                    <script>
                    (() => {
                      const rows=document.getElementById('grocery-pos-lines');
                      const template=document.getElementById('grocery-pos-line-template');
                      const add=document.getElementById('grocery-pos-add');
                      const scanner=document.getElementById('grocery-pos-scanner');
                      const hint=document.getElementById('grocery-pos-hint');
                      let nextIndex=1;
                      function addLine(productId=null){
                        if(rows.children.length>=30){hint.textContent='Maximum 30 products per bill.';return;}
                        const wrap=document.createElement('div');
                        wrap.innerHTML=template.innerHTML.replaceAll('__INDEX__',String(nextIndex++));
                        const row=wrap.firstElementChild;
                        rows.appendChild(row);
                        row.querySelector('.grocery-pos-remove').addEventListener('click',()=>row.remove());
                        if(productId)row.querySelector('select').value=productId;
                      }
                      add.addEventListener('click',()=>addLine());
                      function scan(){
                        const code=scanner.value.trim().toLowerCase();
                        if(!code)return;
                        const options=Array.from(document.querySelectorAll('#grocery-pos-lines .grocery-pos-product option'));
                        const match=options.find(o=>(o.dataset.barcode||'').toLowerCase()===code || (o.dataset.sku||'').toLowerCase()===code);
                        if(!match){hint.textContent='No active product matches the scanned code.';return;}
                        const existing=Array.from(rows.querySelectorAll('select')).find(s=>s.value===match.value);
                        if(existing){const qty=existing.closest('div').querySelector('input[name$="[quantity]"]');qty.value=String((Number(qty.value)||0)+1);}
                        else addLine(match.value);
                        scanner.value='';hint.textContent='Product added. Verify quantities before billing.';scanner.focus();
                      }
                      document.getElementById('grocery-pos-scan').addEventListener('click',scan);
                      scanner.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();scan();}});
                    })();
                    </script>
                    <p class="mt-2 text-xs text-slate-500">Multi-product Counter Billing supports up to 30 distinct products per bill. Payment capture is not connected.</p>
                @else
                    <p class="mt-3 rounded bg-amber-50 p-3 text-sm text-amber-900">Counter billing is unavailable. Check feature switches, approved Business/active Branch, Accepting Orders, and Branch State.</p>
                @endif
                @if($flags['reports'])
                <h3 class="mt-5 font-semibold">Recent Sales</h3>
                <div class="mt-2 overflow-x-auto"><table class="min-w-full text-left text-sm"><thead><tr class="border-b"><th class="p-2">Sale</th><th class="p-2">Status</th><th class="p-2">GST</th><th class="p-2">Total</th><th class="p-2">Action</th></tr></thead><tbody>
                @forelse($sales as $sale)
                    <tr class="border-b"><td class="p-2">{{ $sale->sale_number }}</td><td class="p-2">{{ ucfirst($sale->status) }}</td><td class="p-2">{{ $money($sale->tax_paise) }}</td><td class="p-2 font-bold">{{ $money($sale->total_paise) }}</td>
                        <td class="p-2">@if($sale->status === 'recorded' && $flags['counter_sales'] && $flags['inventory'] && $flags['pricing'])
                            <form method="POST" action="{{ route('vendor.grocery.sales.reverse', ['business' => $business->id, 'sale' => $sale->id]) }}" class="flex flex-wrap gap-2">@csrf
                                <input type="hidden" name="branch_id" value="{{ $branch->id }}"><input name="reason" minlength="5" maxlength="2000" required class="rounded border p-2" placeholder="Reason for reversal">
                                <button class="rounded border border-rose-700 px-2 py-1 text-rose-800">Reverse</button>
                            </form>
                        @else — @endif</td></tr>
                @empty <tr><td colspan="5" class="p-3 text-slate-500">No sales recorded for this branch.</td></tr>@endforelse
                </tbody></table></div>
                @endif
            </section>
        @endif
        <p class="py-5 text-center text-xs text-slate-500">TIDONG Grocery V2 — server-side role, branch, feature and stock checks apply to every action. Online payments are not collected.</p>
    </main>
</div>
</body>
</html>
