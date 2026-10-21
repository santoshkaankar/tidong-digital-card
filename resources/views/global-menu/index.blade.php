@extends('layouts.admin')
@section('title','Global Item Master')
@section('page_heading','Global Item Master')
@section('content')
<section class="rounded-xl bg-white p-5 shadow-sm space-y-3 mb-5">
    <div class="flex flex-wrap items-center justify-between gap-2"><h1 class="text-xl font-bold">Global Food Item Master</h1><a href="{{ route('home') }}" class="rounded border px-3 py-2">Back to Dashboard</a></div>
    <p class="text-sm text-slate-600">One curated global item; each vendor branch chooses it and sets its own price. No Thali / Combo in this single-item directory.</p>
    @if(session('success'))<p class="rounded bg-green-50 p-3">{{session('success')}}</p>@endif
    @if($errors->any())<div class="rounded bg-red-50 p-3">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <form method="POST" action="{{ route('admin.global-menu.store') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">@csrf
        <label class="text-sm">Name <input required name="name" maxlength="150" value="{{ old('name') }}" class="mt-1 block w-full rounded border p-2" placeholder="Masala Dosa"></label>
        <label class="text-sm">Category <input required name="category" maxlength="80" value="{{ old('category') }}" class="mt-1 block w-full rounded border p-2" placeholder="South Indian"></label>
        <label class="text-sm">Description (optional) <input name="description" maxlength="1500" value="{{ old('description') }}" class="mt-1 block w-full rounded border p-2"></label>
        <button class="rounded bg-blue-700 px-4 py-2 text-white md:col-span-3">Add Global Item</button>
    </form>
</section>
<section class="rounded-xl bg-white p-5 shadow-sm">
    <form class="mb-5 flex gap-2"><input name="search" value="{{ $search }}" placeholder="Find item or category" class="w-full rounded border p-2"><button class="rounded bg-slate-900 px-4 py-2 text-white">Search</button></form>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($items as $item)
        <article class="overflow-hidden rounded-lg border">
            @if($item->image_path)<img loading="lazy" src="{{ asset(ltrim($item->image_path, '/')) }}" alt="{{ $item->name }}" class="h-36 w-full object-cover">@endif
            <div class="space-y-2 p-3"><p class="font-semibold">{{ $item->name }}</p><p class="text-xs text-slate-500">{{ $item->category }} · {{ $item->is_active ? 'Active' : 'Inactive' }}</p>
                <p class="text-xs">{{ $item->description }}</p>
                <form method="POST" action="{{ route('admin.global-menu.availability', $item->id) }}">@csrf<input type="hidden" name="active" value="{{ $item->is_active ? 0 : 1 }}"><button class="rounded border px-3 py-1 text-sm">{{ $item->is_active ? 'Hide from new selection' : 'Make selectable' }}</button></form>
            </div>
        </article>
        @empty<p>No Global Items yet. Run the optional seed command after reviewing the demo list.</p>@endforelse
    </div><div class="mt-5">{{ $items->links() }}</div>
</section>
@endsection
