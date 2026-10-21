@extends('customer.layout')
@section('title', __('Security'))
@section('content')
<div class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold">{{ __('Account security') }}</h1>
    <p class="mt-2 text-sm text-slate-600">{{ __('Changing your password requires your current password. Previous remember-me cookies are revoked; other open sessions may remain signed in.') }}</p>
    <form method="POST" action="{{ route('customer.security.password') }}" class="mt-6 space-y-5">
        @csrf @method('PUT')
        <div><label for="current_password" class="mb-1 block font-medium">{{ __('Current password') }}</label>
            <input class="w-full rounded-lg border border-slate-300 p-3" type="password" name="current_password" id="current_password" required autocomplete="current-password"></div>
        <div><label for="password" class="mb-1 block font-medium">{{ __('New password (10+ characters, letters and numbers)') }}</label>
            <input class="w-full rounded-lg border border-slate-300 p-3" type="password" name="password" id="password" minlength="10" required autocomplete="new-password"></div>
        <div><label for="password_confirmation" class="mb-1 block font-medium">{{ __('Confirm new password') }}</label>
            <input class="w-full rounded-lg border border-slate-300 p-3" type="password" name="password_confirmation" id="password_confirmation" minlength="10" required autocomplete="new-password"></div>
        <button class="rounded-lg bg-indigo-700 px-5 py-3 font-semibold text-white hover:bg-indigo-800" type="submit">{{ __('Change password') }}</button>
    </form>
    <section class="mt-8 border-t border-slate-200 pt-6">
        <h2 class="text-lg font-semibold">{{ __('Recent account activity') }}</h2>
        @forelse($recentActivity as $event)
            <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 py-3 text-sm">
                <span>{{ __(ucwords(str_replace('_', ' ', $event->event_code))) }}</span>
                <time datetime="{{ $event->created_at->toIso8601String() }}" class="text-slate-500">{{ $event->created_at->format('d M Y, H:i') }}</time>
            </div>
        @empty
            <p class="mt-3 text-sm text-slate-600">{{ __('No account changes recorded yet.') }}</p>
        @endforelse
    </section>
</div>
@endsection
