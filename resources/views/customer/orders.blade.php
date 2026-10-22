@extends('customer.layout')
@section('title', __('My Orders'))
@section('content')
<div class="td-page-heading">
    <span class="td-kicker">{{ __('MY ACCOUNT') }}</span>
    <h1>{{ __('My Orders') }}</h1>
    <p>{{ __('Personalized gift orders linked to your account appear here. Guest QR and walk-in orders are not linked automatically.') }}</p>
</div>
@if($orders && $orders->count())
    <div class="space-y-4">
    @foreach($orders as $order)
        <article class="rounded-2xl border bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-rose-700">{{ $order->order_no }}</p>
                    <h2 class="text-lg font-bold">{{ $order->product_name_snapshot }} × {{ $order->quantity }}</h2>
                    <p class="text-sm text-slate-600">{{ __('Placed') }} {{ $order->created_at }}</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
            </div>
            <p class="mt-2">{{ __('Product quote') }}: <strong>₹{{ number_format($order->total_sale_paise / 100, 2) }}</strong></p>
            @if(\Illuminate\Support\Facades\Route::has('customer.sublimation.orders.show'))
                <a class="td-text-link" href="{{ route('customer.sublimation.orders.show', $order->id) }}">{{ __('View order and delivery details') }} →</a>
            @endif
        </article>
    @endforeach
    </div>
    <div class="mt-5">{{ $orders->links() }}</div>
@else
    <div class="td-empty">
        <span aria-hidden="true">📦</span><h2>{{ __('No linked orders yet') }}</h2>
        <p>{{ __('Orders placed through your signed-in personalized gifts account will appear here once available. Other services are integrated separately.') }}</p>
        <a href="{{ route('customer.explore.index') }}" class="td-button td-button-red">{{ __('Explore services') }} →</a>
    </div>
@endif
@endsection
