<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Branch POS</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8"><main class="mx-auto max-w-6xl space-y-5">
<header class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-6 shadow-sm">
<div><h1 class="text-2xl font-bold">{{ $branchRecord->business_name }} · {{ $branchRecord->name }}</h1>
<p class="text-sm">Business: {{ $branchRecord->business_status }} · Business orders: {{ $branchRecord->business_accepting ? 'ON' : 'OFF' }} · Branch orders: {{ $branchRecord->branch_accepting ? 'ON' : 'OFF' }}</p></div>
<div class="flex gap-2"><a href="{{ route('pos.home') }}" class="rounded-lg border px-4 py-2">All Branches</a><a href="{{ route('kds.branches.show', $branchRecord->id) }}" class="rounded-lg border px-4 py-2">Kitchen Screen</a></div>
</header>
@if(session('success'))<p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="rounded-xl bg-white p-6 space-y-3">
<h2 class="text-lg font-bold">Branch Orders</h2>
<form method="POST" action="{{ route('pos.branches.accepting', $branchRecord->id) }}">@csrf
<input name="enabled" type="hidden" value="{{ $branchRecord->branch_accepting ? 0 : 1 }}">
<button class="rounded-lg bg-slate-900 px-4 py-2 text-white">{{ $branchRecord->branch_accepting ? 'Stop New Branch Orders' : 'Enable Branch Orders' }}</button>
</form><p class="text-sm text-slate-600">Requires approved Business, business orders ON, active Branch and at least one active Menu item.</p>
</section>
<section class="rounded-xl bg-white p-6 space-y-3"><h2 class="text-lg font-bold">Menu Management</h2>
<form method="POST" action="{{ route('pos.menu.store', $branchRecord->id) }}" class="flex flex-wrap gap-3 items-end">@csrf
<div><label class="block text-sm">Item name</label><input class="rounded-lg border p-2" name="name" required maxlength="255" placeholder="Masala Dosa"></div>
<div><label class="block text-sm">Price (INR)</label><input class="rounded-lg border p-2" name="price" required placeholder="125.00" inputmode="decimal"></div>
<button class="rounded-lg bg-blue-700 text-white px-4 py-2">Add Menu Item</button></form>
<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-left"><th class="p-2">Item</th><th class="p-2">Price</th><th class="p-2">Status</th><th class="p-2">Action</th></tr></thead><tbody>
@forelse($items as $item)
<tr class="border-b"><td class="p-2"><div class="flex items-center gap-2">
@if($item->photo_path)<img alt="{{ $item->name }}" loading="lazy" class="h-12 w-14 rounded object-cover" src="{{ str_starts_with($item->photo_path,'branch-menu/') ? asset('storage/'.$item->photo_path) : asset(ltrim($item->photo_path,'/')) }}">@endif
<span>{{ $item->name }}</span></div></td><td class="p-2">₹{{ number_format($item->price_paise / 100, 2) }}</td><td class="p-2">{{ $item->is_active ? 'Active' : 'Off' }}</td><td class="p-2"><form method="POST" action="{{ route('pos.menu.availability', $item->id) }}">@csrf
<input type="hidden" name="active" value="{{ $item->is_active ? 0 : 1 }}"><button class="rounded border px-3 py-1">{{ $item->is_active ? 'Disable' : 'Enable' }}</button></form></td></tr>
@empty<tr><td colspan="4" class="p-2 text-slate-500">No Menu items yet.</td></tr>@endforelse</tbody></table></div>
</section>
<section class="rounded-xl bg-white p-6 space-y-4"><h2 class="text-lg font-bold">New Counter Order</h2>
<p class="text-sm text-slate-600">Enter quantity for each item (0 means omit). Prices are calculated on the server. Orders remain unpaid.</p>
<form method="POST" action="{{ route('pos.orders.store', $branchRecord->id) }}" class="space-y-3">@csrf
@php $activeItems = $items->where('is_active', true)->values(); @endphp
@foreach($activeItems as $i => $item)
<div class="flex flex-wrap items-center justify-between gap-3 border-b pb-2"><label for="qty{{ $item->id }}">{{ $item->name }} — ₹{{ number_format($item->price_paise / 100, 2) }}</label>
<input type="hidden" name="items[{{ $i }}][menu_item_id]" value="{{ $item->id }}"><input id="qty{{ $item->id }}" class="w-24 rounded-lg border p-2" type="number" name="items[{{ $i }}][quantity]" min="0" max="100" value="0" required></div>
@endforeach
<div><label class="block text-sm">Dining table (optional: for Waiter Order)</label><select class="border rounded p-2" name="dining_table_id"><option value="">Counter / takeaway</option>@foreach($diningTables as $diningTable)<option value="{{ $diningTable->id }}">{{ $diningTable->label }}</option>@endforeach</select></div>
<div><label class="block text-sm">Kitchen note (optional)</label><textarea class="w-full rounded-lg border p-2" name="customer_note" maxlength="500" rows="2"></textarea></div>
<button type="submit" class="rounded-lg bg-blue-700 text-white px-5 py-3" {{ $activeItems->isEmpty() ? 'disabled' : '' }}>Place Order</button>
</form><p class="text-xs text-slate-500">At least one item must have quantity greater than 0; only selected items are sent by the browser helper below.</p>
</section>
<section class="rounded-xl bg-white p-6 space-y-3"><h2 class="text-lg font-bold">Recent Orders</h2>
<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-left"><th class="p-2">Order</th><th class="p-2">Status</th><th class="p-2">Payment</th><th class="p-2">Amount</th><th class="p-2">Cancel</th></tr></thead><tbody>
@forelse($orders as $order)<tr class="border-b"><td class="p-2">{{ $order->order_number }}</td><td class="p-2">{{ $order->status }}</td><td class="p-2">{{ $order->payment_status }}</td><td class="p-2">₹{{ number_format($order->total_paise / 100, 2) }}</td>
<td class="p-2">@if(in_array($order->status, ['received','preparing']))<form method="POST" action="{{ route('pos.orders.cancel', $order->id) }}" class="flex gap-2">@csrf<input name="reason" aria-label="Cancellation reason" required minlength="5" maxlength="500" class="border rounded p-1" placeholder="Reason"><button class="rounded border px-2">Cancel</button></form>@endif</td></tr>
@empty<tr><td colspan="5" class="p-2 text-slate-500">No Orders yet.</td></tr>@endforelse</tbody></table></div>
</section>
<section class="rounded-xl bg-white p-5"><h2 class="font-bold text-lg mb-2">Table-wise QR Menu</h2><p class="mb-3">Create a separate QR for each restaurant table. Print and place it on the table.</p><a class="inline-block rounded-lg bg-indigo-700 px-5 py-3 text-white" href="{{ route('pos.tables.index', $branchRecord->id) }}">Manage Tables & QR Codes</a> <a class="inline-block rounded-lg bg-emerald-700 px-5 py-3 text-white ml-2" href="{{ route('pos.qr.requests', $branchRecord->id) }}">Review QR Order Requests</a></section>
</main>
<script>
// Send only quantities > 0; the server re-validates everything and calculates all prices.
for (const form of document.querySelectorAll('form[action$="/orders"]')) {
    form.addEventListener('submit', (event) => {
        const inputs = [...form.querySelectorAll('input[type="number"]')];
        const selected = inputs.filter(input => Number(input.value) > 0);
        if (selected.length === 0) { event.preventDefault(); alert('Select at least one Menu item.'); return; }
        inputs.filter(input => Number(input.value) === 0).forEach(input => {
            const sibling = form.querySelector(`input[name="${input.name.replace('[quantity]','[menu_item_id]')}"]`);
            if (sibling) sibling.disabled = true;
            input.disabled = true;
        });
        // Normalize sparse field indices to dense indices for PHP/Laravel array validation.
        selected.forEach((input, idx) => {
            const id = input.name.match(/items\[(\d+)\]/)[1];
            const sibling = form.querySelector(`input[name="items[${id}][menu_item_id]"]`);
            input.name = `items[${idx}][quantity]`;
            if (sibling) sibling.name = `items[${idx}][menu_item_id]`;
        });
    });
}
</script></body></html>
