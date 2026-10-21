@extends('customer.layout')
@section('title', __('Profile'))
@section('content')
<div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold">{{ __('My Profile') }}</h1>
    <p class="mt-2 text-sm text-slate-600">{{ __('Updating your email requires your current password.') }}</p>
    <form method="POST" action="{{ route('customer.profile.update') }}" class="mt-6 space-y-5">
        @csrf @method('PUT')
        <div><label for="name" class="mb-1 block font-medium">{{ __('Full name') }}</label>
            <input id="name" name="name" type="text" autocomplete="name" maxlength="255" required value="{{ old('name', auth()->user()->name) }}" class="w-full rounded-lg border border-slate-300 p-3"></div>
        <div><label for="email" class="mb-1 block font-medium">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" autocomplete="email" maxlength="255" required value="{{ old('email', auth()->user()->email) }}" class="w-full rounded-lg border border-slate-300 p-3"></div>
        <div><label for="phone" class="mb-1 block font-medium">{{ __('Phone (optional)') }}</label>
            <input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="25" value="{{ old('phone', $profile?->phone) }}" class="w-full rounded-lg border border-slate-300 p-3"></div>
        <div><label for="current_password" class="mb-1 block font-medium">{{ __('Current password (only if changing email)') }}</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="w-full rounded-lg border border-slate-300 p-3"></div>
        <button class="rounded-lg bg-indigo-700 px-5 py-3 font-semibold text-white hover:bg-indigo-800" type="submit">{{ __('Save profile') }}</button>
    </form>
</div>
@endsection
