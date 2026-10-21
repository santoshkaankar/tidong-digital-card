@extends('customer.layout')
@section('title', __('Preferences'))
@section('content')
<div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold">{{ __('Preferences') }}</h1>
    <form method="POST" action="{{ route('customer.preferences.update') }}" class="mt-6 space-y-5">
        @csrf @method('PUT')
        <div><label class="mb-1 block font-medium" for="preferred_locale">{{ __('Preferred language') }}</label>
            <select class="w-full rounded-lg border border-slate-300 p-3" name="preferred_locale" id="preferred_locale">
                @foreach(config('tidong_customer.supported_locales', ['en']) as $locale)
                    <option value="{{ $locale }}" @selected(old('preferred_locale', $profile?->preferred_locale ?? 'en') === $locale)>{{ $locale === 'hi' ? __('Hindi') : __('English') }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-slate-500">{{ __('This is saved as a preference. Full interface translation is a separate future phase.') }}</p>
        </div>
        <input type="hidden" name="marketing_opt_in" value="0">
        <label class="flex items-start gap-3"><input class="mt-1" type="checkbox" name="marketing_opt_in" value="1" @checked(old('marketing_opt_in', $profile?->marketing_opt_in ?? false))>
            <span>{{ __('I agree to receive optional promotional updates.') }}</span></label>
        <button class="rounded-lg bg-indigo-700 px-5 py-3 font-semibold text-white hover:bg-indigo-800" type="submit">{{ __('Save preferences') }}</button>
    </form>
</div>
@endsection
