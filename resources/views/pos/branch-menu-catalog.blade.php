<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Branch Menu Library</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-slate-100 p-4 text-slate-900 sm:p-8"><main class="mx-auto max-w-6xl space-y-5">
<header class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-6"><div><h1 class="text-2xl font-bold">Global & Private Menu Items</h1><p class="text-sm text-slate-500">{{ $branchRecord->business_name }} · {{ $branchRecord->name }}</p></div>
<div class="flex gap-2"><a href="{{ route('pos.branches.show',$branchRecord->id) }}" class="rounded-lg border px-3 py-2">Back to Branch POS</a><a href="{{ route('vendor.home') }}" class="rounded-lg border px-3 py-2">Vendor Dashboard</a></div></header>
@if(session('success'))<div class="rounded-lg bg-green-50 p-4">{{session('success')}}</div>@endif
@if($errors->any())<div class="rounded-lg bg-red-50 p-4">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="rounded-xl bg-white p-5 space-y-3"><h2 class="text-xl font-bold">Choose from Global Master</h2><p class="text-sm text-slate-600">Every Branch chooses its own selling price. Nothing is ordered or billed until you explicitly enable ordering.</p>
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
@forelse($catalog as $item)<article class="rounded-xl border overflow-hidden">
@if($item->image_path)<img class="h-40 w-full object-cover" loading="lazy" alt="{{ $item->name }}" src="{{ asset(ltrim($item->image_path,'/')) }}">@endif
<div class="space-y-2 p-3"><p class="font-semibold">{{ $item->name }}</p><p class="text-xs text-slate-500">{{ $item->category }}</p>
@if(in_array((int)$item->id,$attached,true))<p class="font-medium text-emerald-700">Already in this Branch</p>
@else<form method="POST" action="{{ route('pos.menu-library.global',$branchRecord->id) }}" class="flex flex-wrap items-end gap-2">@csrf
<input type="hidden" name="global_menu_item_id" value="{{ $item->id }}"><label class="text-sm">Your price (INR)<input required name="price" inputmode="decimal" placeholder="125.00" class="mt-1 block w-28 rounded border p-2"></label>
<button class="rounded bg-blue-700 px-3 py-2 text-white">Add to Branch</button></form>@endif</div></article>
@empty<p>No active Global Items. Ask Super Admin to add them.</p>@endforelse</div></section>
<section class="rounded-xl bg-white p-5 space-y-3"><h2 class="text-xl font-bold">Create Branch-Private Item</h2>
<p class="text-sm text-slate-600">Private items are only shown for this Branch. Other vendors and other branches cannot list them.</p>
<form method="POST" enctype="multipart/form-data" action="{{ route('pos.menu-library.custom',$branchRecord->id) }}" class="grid gap-3 sm:grid-cols-2">@csrf
<label class="text-sm">Item Name<input name="name" maxlength="150" required placeholder="Branch Special" class="mt-1 block w-full rounded border p-2"></label>
<label class="text-sm">Category (optional)<input name="category" maxlength="80" placeholder="Snacks" class="mt-1 block w-full rounded border p-2"></label>
<label class="text-sm">Your Price (INR)<input name="price" required placeholder="120.00" inputmode="decimal" class="mt-1 block w-full rounded border p-2"></label>
<label class="text-sm">Food Photo (optional, JPG/PNG/WEBP max 3MB)<input name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full rounded border p-2"></label>
<button class="rounded bg-emerald-700 px-4 py-2 text-white sm:col-span-2">Save Private Item to this Branch</button></form></section>
<section class="rounded-xl bg-white p-5"><h2 class="mb-3 text-xl font-bold">This Branch Menu</h2><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
@forelse($branchItems as $menu)<article class="rounded border p-3 space-y-2">
@if($menu->photo_path)<img loading="lazy" class="h-32 w-full rounded object-cover" alt="{{ $menu->name }}" src="{{ str_starts_with($menu->photo_path,'branch-menu/') ? asset('storage/'.$menu->photo_path) : asset(ltrim($menu->photo_path,'/')) }}">@endif
<p class="font-semibold">{{ $menu->name }}</p><p class="text-sm">₹{{ number_format($menu->price_paise/100,2) }} · {{ $menu->is_active ? 'Active' : 'Off' }}</p>
<p class="text-xs text-slate-600">{{ $menu->source_kind==='global' ? 'Global Reference' : 'Private to this Branch' }}</p>
@if($menu->source_kind!=='global')<form enctype="multipart/form-data" method="POST" action="{{route('pos.menu-library.photo',$menu->id)}}" class="space-y-2">@csrf
<label class="block text-xs">Replace private photo<input class="mt-1 block w-full text-sm" type="file" name="photo" accept="image/jpeg,image/png,image/webp" required></label><button class="rounded border px-3 py-1 text-sm">Upload Photo</button></form>@endif
</article>@empty<p>No menu items yet.</p>@endforelse
</div></section>
</main></body></html>
