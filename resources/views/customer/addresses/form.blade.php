@extends('customer.layout')
@section('title', $address ? __('Edit address') : __('Add address'))
@section('content')
<div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold">{{ $address ? __('Edit address') : __('Add address') }}</h1>
    <form method="POST" action="{{ $address ? route('customer.addresses.update', $address->id) : route('customer.addresses.store') }}" class="mt-6 space-y-4">
        @csrf
        @if($address) @method('PUT') @endif
        <div><label class="mb-1 block font-medium" for="label">{{ __('Address label') }}</label>
            <select name="label" id="label" required class="w-full rounded-lg border border-slate-300 p-3">
                @foreach(['Home', 'Work', 'Other'] as $label)
                    <option value="{{ $label }}" @selected(old('label', $address?->label ?? 'Home') === $label)>{{ __($label) }}</option>
                @endforeach
            </select></div>
        @foreach([
            'recipient_name' => ['Recipient name', 'name'],
            'phone' => ['Phone', 'tel'],
            'address_line_1' => ['Address line 1', 'street-address'],
            'address_line_2' => ['Address line 2 (optional)', 'off'],
            'city' => ['City', 'address-level2'],
            'state' => ['State / province', 'address-level1'],
            'postal_code' => ['Postal code', 'postal-code'],
            'country_code' => ['Country code (e.g. IN)', 'off'],
        ] as $field => [$caption, $autocomplete])
            <div><label for="{{ $field }}" class="mb-1 block font-medium">{{ __($caption) }}</label>
                <input type="text" id="{{ $field }}" name="{{ $field }}" class="w-full rounded-lg border border-slate-300 p-3" value="{{ old($field, $address?->{$field} ?? ($field === 'country_code' ? 'IN' : '')) }}" maxlength="{{ in_array($field, ['recipient_name', 'city', 'state']) ? 120 : ($field === 'country_code' ? 2 : (in_array($field, ['phone', 'postal_code']) ? 25 : 255)) }}" @if($field !== 'address_line_2') required @endif autocomplete="{{ $autocomplete }}"></div>
        @endforeach
        <input type="hidden" name="is_default" value="0">
        <label class="flex items-center gap-3"><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default ?? false))>
            <span>{{ __('Use as my default address') }}</span></label>
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-3 font-semibold text-white hover:bg-indigo-800">{{ __('Save address') }}</button>
            <a href="{{ route('customer.addresses.index') }}" class="rounded-lg border px-5 py-3">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
