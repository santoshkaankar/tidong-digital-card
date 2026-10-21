<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Counter POS - Digital Platform</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8">
<main class="mx-auto max-w-5xl space-y-6">
<header class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-6 shadow-sm">
<div><h1 class="text-2xl font-bold">Counter POS</h1><p class="text-sm text-slate-600">Restaurant / Café, manual counter orders only. Payment is not processed here.</p></div>
<a class="rounded-lg border px-4 py-2" href="{{ route('vendor.home') }}">Vendor Dashboard</a>
</header>
@if(session('success'))<p class="rounded-lg bg-green-50 p-4">{{ session('success') }}</p>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="space-y-4">
@forelse($businesses as $business)
<div class="rounded-xl bg-white p-6 shadow-sm space-y-3">
<h2 class="text-xl font-bold">{{ $business->name }}</h2>
<p class="text-sm text-slate-600">Vendor: {{ $business->vendor_status }} · Business: {{ $business->status }} · Business orders: {{ $business->is_accepting_orders ? 'ON' : 'OFF' }}</p>
<form action="{{ route('pos.businesses.accepting', $business->id) }}" method="POST" class="flex gap-2">@csrf
<input type="hidden" name="enabled" value="{{ $business->is_accepting_orders ? 0 : 1 }}">
<button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-white">{{ $business->is_accepting_orders ? 'Stop New Orders for Business' : 'Enable Business for POS Orders' }}</button>
</form>
<p class="text-xs text-slate-600">Enabling requires approved Vendor/Business and an active Branch. Each Branch is enabled separately, after adding menu items.</p>
@forelse($branches->get($business->id, collect()) as $branch)
<div class="border-t pt-3 flex flex-wrap items-center justify-between gap-3"><div><p class="font-semibold">{{ $branch->name }}</p><p class="text-sm">Branch: {{ $branch->status }} · New orders: {{ $branch->is_accepting_orders ? 'ON' : 'OFF' }}</p></div>
<div class="flex flex-wrap gap-2"><a class="rounded-lg border px-4 py-2" href="{{ route('pos.branches.show', $branch->id) }}">Menu and POS</a>
<a class="rounded-lg border px-4 py-2" href="{{ route('kds.branches.show', $branch->id) }}">Kitchen Screen</a></div></div>
@empty<p class="text-slate-500">Add a Branch from Vendor Dashboard.</p>@endforelse
</div>
@empty<div class="rounded-xl bg-white p-6">No Restaurant or Café businesses yet.</div>@endforelse
</section></main></body></html>
