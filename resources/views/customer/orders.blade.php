@extends('customer.layout')
@section('title', __('My Orders'))
@section('content')
<style>
.td-myorders{max-width:1080px;margin:0 auto}.td-myorders-tabs{display:flex;gap:.5rem;overflow-x:auto;scrollbar-width:thin;padding:.5rem 0 1rem}
.td-myorders-tabs a{display:inline-flex;min-height:44px;align-items:center;border:1px solid #cbd5e1;background:white;border-radius:999px;padding:.55rem .9rem;color:#334155;text-decoration:none;white-space:nowrap;font-weight:700}
.td-myorders-tabs a[aria-current=page]{color:white;background:#881337;border-color:#881337}
.td-myorders-search{display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:1.5rem}
.td-myorders-search input{border:1px solid #94a3b8;border-radius:12px;padding:.65rem .85rem;min-height:44px;flex:1 1 200px;min-width:0}
.td-myorders-action{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:.65rem .9rem;background:#be123c;color:#fff;text-decoration:none;border:0;border-radius:12px;font-weight:700;cursor:pointer}
.td-myorders-group{margin:1.2rem 0 2rem}.td-myorders-group h2{font-size:1.2rem;font-weight:800;margin-bottom:.8rem}
.td-myorders-list{display:grid;gap:.85rem}
.td-myorders-card{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:1rem;border:1px solid #e2e8f0;background:white;padding:1rem 1.15rem;border-radius:18px}
.td-myorders-card h3{font-size:1.05rem;font-weight:800;margin:.3rem 0;overflow-wrap:anywhere}
.td-myorders-meta{color:#475569;font-size:.88rem;display:flex;gap:.45rem 1rem;flex-wrap:wrap}
.td-myorders-status{display:inline-flex;border-radius:999px;background:#f1f5f9;color:#334155;padding:.25rem .65rem;font-size:.8rem;font-weight:700;margin-top:.5rem}
.td-myorders-empty{background:#f8fafc;border:1px dashed #cbd5e1;border-radius:16px;padding:1rem;color:#475569}
.td-myorders-label{font-size:.78rem;font-weight:800;color:#9f1239}.td-myorders-help{color:#64748b;font-size:.88rem;line-height:1.5}
.td-myorders a:focus-visible,.td-myorders button:focus-visible,.td-myorders input:focus-visible{outline:3px solid #2563eb;outline-offset:2px}
@media(max-width:680px){.td-myorders-card{grid-template-columns:minmax(0,1fr)}.td-myorders-card .td-myorders-action{width:100%}.td-myorders-meta{display:grid;gap:.2rem}}
</style>
<div class="td-myorders">
    <header class="td-page-heading">
        <span class="td-kicker">{{ __('MY ACCOUNT') }}</span>
        <h1>{{ __('My Orders') }}</h1>
        <p>{{ __('Track orders linked to your account, or review their details.') }}</p>
    </header>
    @php($orderFilters = ['all' => 'All orders', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])
    <nav class="td-myorders-tabs" aria-label="{{ __('Filter orders') }}">
        @foreach($orderFilters as $key => $label)
            <a href="{{ route('customer.orders.index', array_filter(['view' => $key === 'all' ? null : $key, 'q' => $search ?: null])) }}"
               @if($view === $key) aria-current="page" @endif>{{ __($label) }}</a>
        @endforeach
    </nav>
    <form method="GET" action="{{ route('customer.orders.index') }}" class="td-myorders-search" role="search">
        @if($view !== 'all')<input type="hidden" name="view" value="{{ $view }}">@endif
        <label class="sr-only" for="td-order-number">{{ __('Search by order number') }}</label>
        <input id="td-order-number" type="search" name="q" value="{{ $search }}" maxlength="60" placeholder="{{ __('Search by order number') }}">
        <button class="td-myorders-action" type="submit">{{ __('Search') }}</button>
        @if($search !== '')<a class="td-myorders-action" href="{{ route('customer.orders.index', $view === 'all' ? [] : ['view' => $view]) }}">{{ __('Clear') }}</a>@endif
    </form>

    @if($groceryOrders !== null)
        <section id="grocery-orders" class="td-myorders-group" aria-labelledby="grocery-heading">
            <h2 id="grocery-heading">{{ __('Grocery orders') }} ({{ $groceryOrders->total() }})</h2>
            @if($groceryOrders->count())
                <div class="td-myorders-list">
                    @foreach($groceryOrders as $order)
                        <article class="td-myorders-card">
                            <div>
                                <span class="td-myorders-label">{{ $order->order_number }}</span>
                                <h3>{{ __('Grocery order') }} · ₹{{ number_format((int) $order->total_paise / 100, 2) }}</h3>
                                <div class="td-myorders-meta">
                                    <span>{{ __('Placed') }}: {{ $order->created_at }}</span>
                                    <span>{{ __('Payment') }}: {{ ucwords(str_replace('_', ' ', $order->payment_status ?? 'unavailable')) }}</span>
                                </div>
                                <span class="td-myorders-status">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                            <a class="td-myorders-action" href="{{ route('grocery.shop.orders.show', $order->id) }}">{{ __('View details') }} →</a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-4">{{ $groceryOrders->fragment('grocery-orders')->links() }}</div>
            @else
                <p class="td-myorders-empty">{{ __('No grocery orders match this filter.') }}</p>
            @endif
        </section>
    @endif

    @if($orders !== null)
        <section id="gift-orders" class="td-myorders-group" aria-labelledby="gift-heading">
            <h2 id="gift-heading">{{ __('Personalized gift orders') }} ({{ $orders->total() }})</h2>
            @if($orders->count())
                <div class="td-myorders-list">
                    @foreach($orders as $order)
                        <article class="td-myorders-card">
                            <div>
                                <span class="td-myorders-label">{{ $order->order_no ?? ('#'.$order->id) }}</span>
                                <h3>{{ $order->product_name_snapshot ?? __('Personalized order') }} @if(isset($order->quantity)) × {{ $order->quantity }} @endif</h3>
                                <div class="td-myorders-meta">
                                    <span>{{ __('Placed') }}: {{ $order->created_at }}</span>
                                    <span>{{ __('Product quote') }}: ₹{{ number_format((int) ($order->total_sale_paise ?? 0) / 100, 2) }}</span>
                                </div>
                                <span class="td-myorders-status">{{ ucwords(str_replace('_', ' ', $order->status ?? 'pending')) }}</span>
                            </div>
                            @if(\Illuminate\Support\Facades\Route::has('customer.sublimation.orders.show'))
                                <a class="td-myorders-action" href="{{ route('customer.sublimation.orders.show', $order->id) }}">{{ __('View details') }} →</a>
                            @endif
                        </article>
                    @endforeach
                </div>
                <div class="mt-4">{{ $orders->fragment('gift-orders')->links() }}</div>
            @else
                <p class="td-myorders-empty">{{ __('No personalized gift orders match this filter.') }}</p>
            @endif
        </section>
    @endif

    @if($orders === null && $groceryOrders === null)
        <div class="td-myorders-empty">{{ __('Verified order history is not available until its required tables and account links are installed.') }}</div>
    @endif
    <p class="td-myorders-help">{{ __('Only purchases linked to your signed-in customer account are shown. A cancellation or refund request is not a payment refund confirmation.') }}</p>
</div>
@endsection
