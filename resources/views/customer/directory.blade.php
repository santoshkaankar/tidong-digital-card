@extends('customer.layout')
@section('title', __($definition['title']))
@section('content')
    <div class="td-page-heading"><a class="td-text-link" href="{{ route('customer.explore.index') }}">← {{ __('All services') }}</a><span class="td-kicker">{{ __('STORE DIRECTORY') }}</span><h1>{{ $definition['icon'] }} {{ __($definition['title']) }}</h1><p>{{ __($definition['description']) }}</p></div>
    <form class="td-search-form" action="{{ route('customer.explore.service', $service) }}" method="GET" role="search">
        <label class="sr-only" for="store-search">{{ __('Search store name') }}</label>
        <input id="store-search" name="q" type="search" maxlength="70" value="{{ $term }}" placeholder="{{ __('Search active stores by name') }}">
        <button type="submit">{{ __('Search') }}</button>
    </form>
    @if($businesses->isEmpty())
        <div class="td-empty"><span aria-hidden="true">🏬</span><h2>{{ __('No active stores found') }}</h2><p>{{ __('Try another search. Only approved businesses accepting orders appear here.') }}</p></div>
    @else
        <div class="td-store-grid">
            @foreach($businesses as $business)
                <article class="td-store-card">
                    <span class="td-store-avatar" aria-hidden="true">{{ $definition['icon'] }}</span>
                    <div><h2>{{ $business->name }}</h2><p>{{ __('Verified directory listing') }}</p><span class="td-store-state">● {{ __('Active business') }}</span></div>
                </article>
            @endforeach
        </div>
        <div class="td-pagination">{{ $businesses->links() }}</div>
    @endif
    <p class="td-footnote">{{ __('Store directory only. Customer checkout, live products and order history are not connected yet; please do not use vendor-only ordering links.') }}</p>
@endsection
