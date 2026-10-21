@extends('layouts.admin')
@section('title', 'Grocery Feature Controls')
@section('page_heading', 'Grocery Feature Controls')
@section('content')
<div class="max-w-4xl space-y-5">
    <header class="rounded-xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-bold">Grocery Feature Controls</h1>
        <p class="mt-2 text-sm text-slate-600">Only Super Admin can change these switches. A global OFF overrides all branch choices. All settings start OFF until explicitly enabled.</p>
        <p class="mt-2 text-sm text-amber-800">Turning features OFF blocks new actions. Existing products, stock movements and sale records are retained.</p>
    </header>
    @if(session('success'))<p role="status" class="rounded-lg bg-emerald-50 p-3 text-emerald-900">{{ session('success') }}</p>@endif
    @if($errors->any())<div role="alert" class="rounded-lg bg-rose-50 p-3 text-rose-900">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <section class="overflow-hidden rounded-xl bg-white shadow-sm">
        @foreach($labels as $code => $label)
            @php($isOn = (bool) ($settings[$code] ?? false))
            <div class="flex flex-wrap items-center justify-between gap-3 border-b p-5 last:border-0">
                <div>
                    <h2 class="font-semibold">{{ $label }}</h2>
                    <p class="text-sm {{ $isOn ? 'text-emerald-700' : 'text-rose-700' }}">Global state: {{ $isOn ? 'ON' : 'OFF' }}</p>
                </div>
                <form method="POST" action="{{ route('admin.grocery.features.update', ['feature' => $code]) }}">
                    @csrf
                    <input type="hidden" name="enabled" value="{{ $isOn ? 0 : 1 }}">
                    <button type="submit" class="rounded-lg px-5 py-2 text-sm font-bold text-white {{ $isOn ? 'bg-rose-700' : 'bg-emerald-700' }}">Turn {{ $isOn ? 'OFF' : 'ON' }}</button>
                </form>
            </div>
        @endforeach
    </section>
</div>
@endsection
