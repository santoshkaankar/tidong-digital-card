<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sublimation Studio · Tidong</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
      @media print { .no-print {display:none!important;} body{background:white!important;} .job-print{break-inside:avoid;border:1px solid #ddd;padding:12px;margin-bottom:12px} }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
@php
  $money = fn ($paise) => '₹'.number_format(((int)$paise) / 100, 2);
  $statusLabels = [
    'design_pending' => 'Design Pending', 'approval_pending' => 'Waiting for Design Approval',
    'approved' => 'Approved for Print', 'in_production' => 'In Production',
    'ready' => 'Ready', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled',
  ];
  $transitions = [
    'design_pending' => ['approval_pending' => 'Send for Approval', 'cancelled' => 'Cancel Job'],
    'approval_pending' => ['design_pending' => 'Revise Design', 'approved' => 'Record Customer Approval', 'cancelled' => 'Cancel Job'],
    'approved' => ['in_production' => 'Start Production', 'cancelled' => 'Cancel Job'],
    'in_production' => ['ready' => 'Mark Ready', 'cancelled' => 'Cancel Job'],
    'ready' => ['delivered' => 'Mark Delivered', 'cancelled' => 'Cancel Job'],
    'delivered' => [], 'cancelled' => [],
  ];
@endphp
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row xl:p-6">
    @include('vendor.partials.sidebar')
    <div class="w-full min-w-0 flex-1">
<div class="no-print bg-slate-950 text-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-5">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/tidong-studio-logo.jpg') }}" alt="Tidong" class="h-12 w-32 rounded-lg bg-white object-contain p-1">
            <div>
                <p class="text-xl font-black tracking-wide">TIDONG <span class="font-medium text-rose-300">Sublimation Studio</span></p>
                <p class="text-xs text-slate-300">Personalized Printing · Internal Business Workspace</p>
            </div>
        </div>
        <a href="{{ route('vendor.home') }}" class="rounded-lg border border-slate-500 px-4 py-2 text-sm hover:bg-slate-800">← Vendor Dashboard</a>
    </div>
</div>
<main class="mx-auto max-w-7xl space-y-7 px-0 py-7 sm:px-2">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-rose-600">Custom Gifts & Printing</p>
            <h1 class="mt-1 text-3xl font-bold">{{ $business->name }}</h1>
            <p class="mt-2 text-slate-500">Create products, record custom jobs, track design approval and production.</p>
        </div>
        <div class="rounded-lg border bg-white px-4 py-2 text-sm">Business status: <strong>{{ ucfirst($business->status) }}</strong></div>
    </div>

    @if (session('success'))
        <div class="no-print rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="no-print rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
            <strong>Please correct:</strong>
            <ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if (!$branch)
        <div class="rounded-2xl border bg-white p-8">
            <h2 class="text-xl font-semibold">Set up your first branch</h2>
            <p class="mt-2">Create a branch for this business from Vendor Dashboard. Products and orders are kept within their own business and branch.</p>
            <a href="{{ route('vendor.home') }}" class="mt-4 inline-block rounded-lg bg-rose-600 px-5 py-3 font-semibold text-white">Open Vendor Dashboard</a>
        </div>
    @else
        <div class="no-print flex flex-wrap items-center gap-3 rounded-xl border bg-white p-4">
            <span class="font-semibold">Working branch:</span>
            @foreach ($branches as $option)
                <a href="{{ route('vendor.sublimation.index', ['business' => $business->id, 'branch' => $option->id]) }}"
                   class="rounded-full px-4 py-2 text-sm {{ $option->id == $branch->id ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $option->name }}</a>
            @endforeach
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Products</p><p class="mt-2 text-3xl font-bold">{{ $products->count() }}</p></div>
            <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Recent Jobs</p><p class="mt-2 text-3xl font-bold">{{ $orders->count() }}</p></div>
            <div class="rounded-2xl border bg-white p-5"><p class="text-sm text-slate-500">Waiting for Approval</p><p class="mt-2 text-3xl font-bold">{{ $orders->where('status', 'approval_pending')->count() }}</p></div>
        </div>
        <div class="no-print grid gap-6 xl:grid-cols-2">
            <section id="studio-add-product" class="rounded-2xl border bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-xl font-bold">01 · Add a Product</h2>
                <p class="mb-5 mt-1 text-sm text-slate-500">For example, Photo Mug, T-Shirt, Cushion or Keychain. Prices shown here are internal quotes, not GST invoices.</p>
                <form action="{{ route('vendor.sublimation.products.store', $business->id) }}" method="POST" class="grid gap-3 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    <label class="sm:col-span-2 text-sm font-semibold">Product Name
                        <input name="name" value="{{ old('name') }}" maxlength="160" required class="mt-1 w-full rounded-lg border p-3" placeholder="Personalized Ceramic Mug">
                    </label>
                    <label class="text-sm font-semibold">Category
                        <select name="category" class="mt-1 w-full rounded-lg border bg-white p-3">
                            @foreach (['Mug', 'T-Shirt', 'Cushion', 'Keychain', 'Bottle', 'Photo Frame', 'Mouse Pad', 'Coaster', 'Tote Bag', 'Puzzle', 'Other'] as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-semibold">Blank Product Cost (₹)
                        <input type="number" name="blank_cost" min="0" step="0.01" required value="{{ old('blank_cost', '0.00') }}" class="mt-1 w-full rounded-lg border p-3">
                    </label>
                    <label class="text-sm font-semibold">Ink & Paper Cost (₹)
                        <input type="number" name="print_cost" min="0" step="0.01" required value="{{ old('print_cost', '0.00') }}" class="mt-1 w-full rounded-lg border p-3">
                    </label>
                    <label class="text-sm font-semibold">Packing Cost (₹)
                        <input type="number" name="packing_cost" min="0" step="0.01" required value="{{ old('packing_cost', '0.00') }}" class="mt-1 w-full rounded-lg border p-3">
                    </label>
                    <label class="text-sm font-semibold">Other / Pressing Cost (₹)
                        <input type="number" name="other_cost" min="0" step="0.01" required value="{{ old('other_cost', '0.00') }}" class="mt-1 w-full rounded-lg border p-3">
                    </label>
                    <label class="text-sm font-semibold">Selling Price / Piece (₹)
                        <input type="number" name="sale_price" min="0" step="0.01" required value="{{ old('sale_price', '0.00') }}" class="mt-1 w-full rounded-lg border p-3">
                    </label>
                    <label class="sm:col-span-2 text-sm font-semibold">Production Notes
                        <textarea name="production_notes" maxlength="2000" rows="2" class="mt-1 w-full rounded-lg border p-3" placeholder="Heat press settings, blank type, print size (optional)">{{ old('production_notes') }}</textarea>
                    </label>
                    <button type="submit" class="sm:col-span-2 rounded-lg bg-rose-600 px-5 py-3 font-semibold text-white">Save Product</button>
                </form>
            </section>
            <section id="studio-new-job" class="rounded-2xl border bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-xl font-bold">02 · New Custom Job</h2>
                <p class="mb-5 mt-1 text-sm text-slate-500">Capture customer details and printing instructions. This does not create a platform customer account.</p>
                @if ($products->where('is_active', 1)->where('sale_price_paise', '>', 0)->isEmpty())
                    <p class="rounded-lg bg-amber-50 p-4 text-amber-800">Add a product, set a positive selling price, and activate it before creating a custom job.</p>
                @else
                    <form action="{{ route('vendor.sublimation.orders.store', $business->id) }}" method="POST" class="grid gap-3 sm:grid-cols-2">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                        <label class="sm:col-span-2 text-sm font-semibold">Product
                            <select name="product_id" required class="mt-1 w-full rounded-lg border bg-white p-3">
                                @foreach ($products->where('is_active', 1)->where('sale_price_paise', '>', 0) as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} · {{ $money($product->sale_price_paise) }} / piece</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="text-sm font-semibold">Customer Name
                            <input name="customer_name" required maxlength="160" value="{{ old('customer_name') }}" class="mt-1 w-full rounded-lg border p-3" placeholder="Customer name">
                        </label>
                        <label class="text-sm font-semibold">Phone (optional)
                            <input name="customer_mobile" maxlength="25" value="{{ old('customer_mobile') }}" class="mt-1 w-full rounded-lg border p-3" placeholder="Contact number">
                        </label>
                        <label class="text-sm font-semibold">Quantity
                            <input type="number" name="quantity" min="1" max="10000" required value="{{ old('quantity', 1) }}" class="mt-1 w-full rounded-lg border p-3">
                        </label>
                        <label class="text-sm font-semibold">Due Date
                            <input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-1 w-full rounded-lg border p-3">
                        </label>
                        <label class="sm:col-span-2 text-sm font-semibold">Photo / Text / Print Instructions
                            <textarea name="design_instructions" maxlength="3000" rows="4" class="mt-1 w-full rounded-lg border p-3" placeholder="e.g. customer will share a photo; print 'Happy Birthday' and their name">{{ old('design_instructions') }}</textarea>
                        </label>
                        <button type="submit" class="sm:col-span-2 rounded-lg bg-slate-900 px-5 py-3 font-semibold text-white">Create Internal Job</button>
                    </form>
                @endif
            </section>
        </div>

        <section class="rounded-2xl border bg-white p-5 shadow-sm sm:p-6">
            <h2 id="studio-prices" class="text-xl font-bold">Products & Pricing</h2>
            <p class="mt-1 text-sm text-slate-500">All amounts are per piece. Unpriced sample products remain inactive until you set your costs and selling price.</p>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-slate-50"><tr><th class="p-3">Product</th><th class="p-3">Category</th><th class="p-3">Cost</th><th class="p-3">Sale</th><th class="p-3">Gross Difference</th></tr></thead>
                    <tbody>
                    @forelse ($products as $product)
                        @php($cost = (int)$product->blank_cost_paise + (int)$product->print_cost_paise + (int)$product->packing_cost_paise + (int)$product->other_cost_paise)
                        <tr class="border-b"><td class="p-3 font-semibold">{{ $product->name }}</td><td class="p-3">{{ $product->category }}</td><td class="p-3">{{ $money($cost) }}</td><td class="p-3">{{ $money($product->sale_price_paise) }}</td><td class="p-3 {{ $product->sale_price_paise < $cost ? 'text-rose-700' : 'text-emerald-700' }}">{{ $money($product->sale_price_paise - $cost) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="p-5 text-center text-slate-500">No products yet. Add one above.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="no-print rounded-2xl border bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-bold">Edit Product Prices &amp; Activate</h2>
            <p class="mt-1 text-sm text-slate-500">Sample templates start inactive at ₹0. Only activate after confirming the real selling price. Existing job price snapshots will not change.</p>
            <div class="mt-4 space-y-3">
                @foreach ($products as $product)
                    <details class="rounded-xl border p-4">
                        <summary class="cursor-pointer font-semibold">{{ $product->name }} — {{ $product->is_active ? 'Active' : 'Inactive / Price pending' }}</summary>
                        <form method="POST" action="{{ route('vendor.sublimation.products.update', ['business' => $business->id, 'product' => $product->id]) }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                            <label class="sm:col-span-2 text-sm font-semibold">Product Name
                                <input name="name" value="{{ $product->name }}" maxlength="160" required class="mt-1 w-full rounded-lg border p-3">
                            </label>
                            <label class="text-sm font-semibold">Category
                                <select name="category" class="mt-1 w-full rounded-lg border bg-white p-3">
                                    @foreach (['Mug', 'T-Shirt', 'Cushion', 'Keychain', 'Bottle', 'Photo Frame', 'Mouse Pad', 'Coaster', 'Tote Bag', 'Puzzle', 'Other'] as $category)
                                        <option value="{{ $category }}" @selected($product->category === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="text-sm font-semibold">Blank Cost (₹)
                                <input type="number" name="blank_cost" min="0" step="0.01" required value="{{ number_format($product->blank_cost_paise / 100, 2, '.', '') }}" class="mt-1 w-full rounded-lg border p-3">
                            </label>
                            <label class="text-sm font-semibold">Ink / Paper Cost (₹)
                                <input type="number" name="print_cost" min="0" step="0.01" required value="{{ number_format($product->print_cost_paise / 100, 2, '.', '') }}" class="mt-1 w-full rounded-lg border p-3">
                            </label>
                            <label class="text-sm font-semibold">Packing Cost (₹)
                                <input type="number" name="packing_cost" min="0" step="0.01" required value="{{ number_format($product->packing_cost_paise / 100, 2, '.', '') }}" class="mt-1 w-full rounded-lg border p-3">
                            </label>
                            <label class="text-sm font-semibold">Pressing / Other Cost (₹)
                                <input type="number" name="other_cost" min="0" step="0.01" required value="{{ number_format($product->other_cost_paise / 100, 2, '.', '') }}" class="mt-1 w-full rounded-lg border p-3">
                            </label>
                            <label class="text-sm font-semibold">Selling Price (₹)
                                <input type="number" name="sale_price" min="0" step="0.01" required value="{{ number_format($product->sale_price_paise / 100, 2, '.', '') }}" class="mt-1 w-full rounded-lg border p-3">
                            </label>
                            <label class="sm:col-span-2 text-sm font-semibold">Production Notes
                                <textarea name="production_notes" maxlength="2000" rows="2" class="mt-1 w-full rounded-lg border p-3">{{ $product->production_notes }}</textarea>
                            </label>
                            <label class="sm:col-span-2 flex items-center gap-2 text-sm font-medium">
                                <input type="checkbox" name="is_active" value="1" @checked($product->is_active)>
                                Active — allow internal custom job orders
                            </label>
                            <button type="submit" class="sm:col-span-2 rounded-lg bg-rose-600 px-5 py-3 font-semibold text-white">Save Product Changes</button>
                        </form>
                    </details>
                @endforeach
            </div>
        </section>

        <section id="studio-job-cards" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-xl font-bold">Production Job Cards</h2><button type="button" onclick="window.print()" class="no-print rounded-lg border bg-white px-4 py-2 text-sm">Print Job Cards</button></div>
            @forelse ($orders as $order)
                <article class="job-print rounded-2xl border bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-rose-600">{{ $order->order_no }}</p>
                            <h3 class="mt-1 text-lg font-bold">{{ $order->product_name_snapshot }} · {{ $order->quantity }} pcs</h3>
                            <p class="text-sm text-slate-500">Customer: {{ $order->customer_name }} {{ $order->customer_mobile ? '· '.$order->customer_mobile : '' }}</p>
                            @if ($order->due_date)<p class="text-sm">Due: {{ $order->due_date }}</p>@endif
                        </div>
                        <div class="text-right"><span class="rounded-lg bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                            <p class="mt-3 text-sm">Quote: <strong>{{ $money($order->total_sale_paise) }}</strong></p>
                            <p class="text-sm text-slate-500">Cost: {{ $money($order->total_cost_paise) }}</p>
                            <p class="text-sm text-slate-500">Gross difference: {{ $money($order->total_sale_paise - $order->total_cost_paise) }}</p>
                        </div>
                    </div>
                    @if ($order->design_instructions)<p class="mt-3 whitespace-pre-line rounded-lg bg-slate-50 p-3 text-sm">{{ $order->design_instructions }}</p>@endif
                    @if ($order->approval_note)<p class="mt-2 text-sm"><strong>Recorded customer approval:</strong> {{ $order->approval_note }}</p>@endif
                    @if (!empty($transitions[$order->status]))
                        <form action="{{ route('vendor.sublimation.orders.status', ['business' => $business->id, 'order' => $order->id]) }}" method="POST" class="no-print mt-4 flex flex-wrap items-end gap-3 border-t pt-4">
                            @csrf
                            <label class="text-sm font-semibold">Next Step
                                <select name="status" class="mt-1 rounded-lg border bg-white p-2" data-status-select>
                                    @foreach ($transitions[$order->status] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @if (isset($transitions[$order->status]['approved']))
                                <label class="min-w-64 flex-1 text-sm font-semibold">Customer Approval Evidence / Note
                                    <input name="approval_note" maxlength="1500" placeholder="e.g. approved by customer via WhatsApp on date/time" class="mt-1 w-full rounded-lg border p-2">
                                </label>
                            @endif
                            <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Update Status</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border bg-white p-8 text-center text-slate-500">No custom printing jobs for this branch yet.</div>
            @endforelse
        </section>
    @endif
    <p class="no-print pb-6 text-xs text-slate-500">Private Vendor Studio · No public registration, GST invoice, payment collection or online orders in this starter release. Product/order records are retained; status changes are audited. Printed gross difference is not net taxable profit.</p>
</main>
    </div>
</div>
</body>
</html>
