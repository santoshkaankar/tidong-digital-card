<div class="gr-top"><div class="container"><span>Fresh essentials, clear prices, easy shopping</span><span>Secure local store • Pay at handover</span></div></div>
<header class="gr-header"><div class="container">
    <a class="gr-brand" href="{{ route('grocery.shop.stores') }}" aria-label="TIDONG Grocery home">TIDONG<span>•</span>Grocery</a>
    <nav class="gr-nav" aria-label="Grocery navigation">
        <a href="{{ route('grocery.shop.stores') }}">{{ __('grocery.stores') }}</a>
        @isset($store) @isset($outlet) <a href="{{ route('grocery.shop.catalog', [$store->id, $outlet->id]) }}">{{ __('grocery.shop') }}</a>
        <a class="gr-pill" href="{{ route('grocery.shop.basket', [$store->id, $outlet->id]) }}">🛒 {{ __('grocery.basket') }} @isset($cart) ({{ count($cart['lines']) }}) @endisset</a>
        @endisset @endisset
        @auth <a href="{{ route('grocery.shop.orders.index') }}">{{ __('grocery.my_orders') }}</a> @else <a href="{{ route('login') }}">{{ __('grocery.sign_in') }}</a> @endauth
    </nav>
</div></header>
@if(session('success'))<div class="container"><div class="gr-alert" role="status">{{ session('success') }}</div></div>@endif
@if($errors->any())<div class="container"><div class="gr-alert bad" role="alert"><strong>Please check:</strong><ul>@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div></div>@endif
