@extends('customer.layout')
@section('title', __('Addresses'))
@section('content')
<div class="rounded-xl bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold">{{ __('Saved Addresses') }}</h1>
        <a href="{{ route('customer.addresses.create') }}" class="rounded-lg bg-indigo-700 px-4 py-2 font-semibold text-white hover:bg-indigo-800">{{ __('Add address') }}</a>
    </div>
    <p class="mt-2 text-sm text-slate-500">{{ __('Up to :count addresses per account.', ['count' => config('tidong_customer.max_addresses', 10)]) }}</p>
    @forelse($addresses as $address)
        <article class="mt-5 rounded-lg border border-slate-200 p-4">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-semibold">{{ __($address->label) }} · {{ $address->recipient_name }}</h2>
                @if($address->is_default)<span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-900">{{ __('Default') }}</span>@endif
            </div>
            <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif
{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }} · {{ $address->country_code }}
{{ $address->phone }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('customer.addresses.edit', $address->id) }}" class="rounded-lg border px-3 py-2 text-sm">{{ __('Edit') }}</a>
                @unless($address->is_default)
                <form method="POST" action="{{ route('customer.addresses.default', $address->id) }}">@csrf
                    <button type="submit" class="rounded-lg border px-3 py-2 text-sm">{{ __('Set default') }}</button>
                </form>
                @endunless
                <form method="POST" action="{{ route('customer.addresses.destroy', $address->id) }}">@csrf @method('DELETE')
                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm text-red-700">{{ __('Remove') }}</button>
                </form>
            </div>
        </article>
    @empty
        <p class="mt-5 text-slate-600">{{ __('No addresses saved yet.') }}</p>
    @endforelse
    <div class="mt-5">{{ $addresses->links() }}</div>
</div>
@endsection
