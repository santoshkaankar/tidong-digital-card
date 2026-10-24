<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Branch POS</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row xl:p-6">
@include('vendor.partials.sidebar')
<main class="w-full min-w-0 flex-1 space-y-5">
<header class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-6 shadow-sm">
<div><h1 class="text-2xl font-bold">{{ $branchRecord->business_name }} · {{ $branchRecord->name }}</h1>
<p class="text-sm">Business: {{ $branchRecord->business_status }} · Business orders: {{ $branchRecord->business_accepting ? 'ON' : 'OFF' }} · Branch orders: {{ $branchRecord->branch_accepting ? 'ON' : 'OFF' }}</p></div>
<div class="flex gap-2"><a href="{{ route('pos.home') }}" class="rounded-lg border px-4 py-2">All Branches</a><a href="{{ route('kds.branches.show', $branchRecord->id) }}" class="rounded-lg border px-4 py-2">Kitchen Screen</a></div>
</header>
<section aria-label="Outlet statistics" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Total Items</p><p class="mt-1 text-3xl font-bold">{{ $stats['items'] }}</p></div>
    <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Active Items</p><p class="mt-1 text-3xl font-bold">{{ $stats['active_items'] }}</p></div>
    <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Total Orders</p><p class="mt-1 text-3xl font-bold">{{ $stats['orders'] }}</p><p class="text-xs text-slate-500">{{ $stats['active_orders'] }} active</p></div>
    <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-slate-500">Recorded Order Value</p><p class="mt-1 text-3xl font-bold">₹{{ number_format($stats['order_value_paise'] / 100, 2) }}</p><p class="text-xs text-slate-500">Excludes cancelled orders; not confirmed payment revenue</p></div>
</section>
@if(session('success'))<p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="rounded-xl bg-white p-6 space-y-3">
<h2 class="text-lg font-bold">Branch Orders</h2>
<form method="POST" action="{{ route('pos.branches.accepting', $branchRecord->id) }}">@csrf
<input name="enabled" type="hidden" value="{{ $branchRecord->branch_accepting ? 0 : 1 }}">
<button class="rounded-lg bg-slate-900 px-4 py-2 text-white">{{ $branchRecord->branch_accepting ? 'Stop New Branch Orders' : 'Enable Branch Orders' }}</button>
</form><p class="text-sm text-slate-600">Requires approved Business, business orders ON, active Branch and at least one active Menu item.</p>
</section>
@include('pos.partials.cashier')
<section class="rounded-xl bg-white p-6 space-y-3"><h2 class="text-lg font-bold">Recent Orders</h2>
<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-left"><th class="p-2">Order</th><th class="p-2">Status</th><th class="p-2">Payment</th><th class="p-2">Amount</th><th class="p-2">Cancel</th></tr></thead><tbody>
@forelse($orders as $order)<tr class="border-b"><td class="p-2">{{ $order->order_number }}</td><td class="p-2">{{ $order->status }}</td><td class="p-2">{{ $order->payment_status }}</td><td class="p-2">₹{{ number_format($order->total_paise / 100, 2) }}</td>
<td class="p-2">@if(in_array($order->status, ['received','preparing']))<form method="POST" action="{{ route('pos.orders.cancel', $order->id) }}" class="flex gap-2">@csrf<input name="reason" aria-label="Cancellation reason" required minlength="5" maxlength="500" class="border rounded p-1" placeholder="Reason"><button class="rounded border px-2">Cancel</button></form>@endif</td></tr>
@empty<tr><td colspan="5" class="p-2 text-slate-500">No Orders yet.</td></tr>@endforelse</tbody></table></div>
</section>
<section class="rounded-xl bg-white p-5"><h2 class="font-bold text-lg mb-2">Table-wise QR Menu</h2><p class="mb-3">Create a separate QR for each restaurant table. Print and place it on the table.</p><a class="inline-block rounded-lg bg-indigo-700 px-5 py-3 text-white" href="{{ route('pos.tables.index', $branchRecord->id) }}">Manage Tables & QR Codes</a> <a class="inline-block rounded-lg bg-emerald-700 px-5 py-3 text-white ml-2" href="{{ route('pos.qr.requests', $branchRecord->id) }}">Review QR Order Requests</a></section>
</main>
</div>
</body></html>
