@extends('customer.layout')
@section('title', __('My Orders'))
@section('content')
    <div class="td-page-heading"><span class="td-kicker">{{ __('MY ACCOUNT') }}</span><h1>{{ __('My Orders') }}</h1><p>{{ __('A secure, unified order history is planned across TIDONG services.') }}</p></div>
    <div class="td-empty"><span aria-hidden="true">📦</span><h2>{{ __('Customer order history is coming soon') }}</h2><p>{{ __('Existing in-store POS, guest QR and internal Sublimation studio orders are not linked to your login. We will show only verified orders belonging to your account after a reviewed integration.') }}</p><a href="{{ route('customer.explore.index') }}" class="td-button td-button-red">{{ __('Explore services') }} →</a></div>
@endsection
