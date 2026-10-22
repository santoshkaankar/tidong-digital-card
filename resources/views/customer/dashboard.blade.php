@extends('customer.layout')
@section('title', __('Home'))
@section('content')
    <div class="td-welcome"><span>{{ __('Welcome back, :name', ['name' => \Illuminate\Support\Str::limit(auth()->user()->name, 40)]) }}</span><span class="td-welcome-dot" aria-hidden="true">✦</span></div>
    <section class="td-hero" aria-labelledby="hero-title">
        <div class="td-hero-text">
            <span class="td-hero-eyebrow">🎁 {{ __('FEATURED: PERSONALIZED GIFTS') }}</span>
            <h1 id="hero-title">{{ __('Create something') }} <em>{{ __('special.') }}</em></h1>
            <p>{{ __('Explore personalized gifts, photo printing and more—all from your TIDONG home.') }}</p>
            <a href="{{ route('customer.explore.service', 'sublimation') }}" class="td-button td-button-white">{{ __('Explore gifts') }} <span aria-hidden="true">→</span></a>
        </div>
        <div class="td-hero-art" aria-hidden="true">
            <div class="td-floating td-mug">☕<small>LOVE</small></div>
            <div class="td-floating td-shirt">👕</div>
            <div class="td-floating td-gift">🎁</div>
            <span class="td-spark td-spark-a">✦</span><span class="td-spark td-spark-b">♡</span>
        </div>
    </section>
    <section class="td-section" aria-labelledby="services-title">
        <div class="td-section-head"><div><p class="td-kicker">{{ __('ALL IN ONE PLACE') }}</p><h2 id="services-title">{{ __('Explore businesses & services') }}</h2></div><a href="{{ route('customer.explore.index') }}" class="td-text-link">{{ __('View all') }} →</a></div>
        <div class="td-service-grid">
            @foreach($categories as $key => $category)
                @if($category['available'])
                    <a href="{{ route('customer.explore.service', $key) }}" class="td-service-tile td-tone-{{ $category['tone'] }}">
                        <span class="td-service-icon" aria-hidden="true">{{ $category['icon'] }}</span>
                        <strong>{{ __($category['title']) }}</strong>
                        <span class="td-service-status">{{ __('Explore stores') }}</span>
                    </a>
                @else
                    <div class="td-service-tile td-tone-{{ $category['tone'] }} td-inactive" aria-label="{{ __($category['title']) }}: {{ __('Not available yet') }}">
                        <span class="td-service-icon" aria-hidden="true">{{ $category['icon'] }}</span>
                        <strong>{{ __($category['title']) }}</strong>
                        <span class="td-service-status">{{ __('Not available yet') }}</span>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
    <section class="td-feature-row" aria-label="{{ __('Discover TIDONG') }}">
        <div class="td-feature-card td-feature-food"><div><span class="td-small-tag">🍽️ {{ __('RESTAURANT') }}</span><h2>{{ __('Food made easier') }}</h2><p>{{ __('Find active restaurants when they become available.') }}</p>
            @if($categories['restaurant']['available'] ?? false)<a href="{{ route('customer.explore.service', 'restaurant') }}">{{ __('Explore restaurants') }} →</a>@else<span>{{ __('Coming soon') }}</span>@endif</div><span aria-hidden="true" class="td-feature-emoji">🍕</span></div>
        <div class="td-feature-card td-feature-gift"><div><span class="td-small-tag">💝 {{ __('SUBLIMATION') }}</span><h2>{{ __('Gifts with a personal touch') }}</h2><p>{{ __('Mugs, T-shirts, photo frames and memories.') }}</p>
            @if($categories['sublimation']['available'] ?? false)<a href="{{ route('customer.explore.service', 'sublimation') }}">{{ __('Explore stores') }} →</a>@else<span>{{ __('Coming soon') }}</span>@endif</div><span aria-hidden="true" class="td-feature-emoji">📸</span></div>
    </section>
    <section class="td-section" aria-labelledby="quick-title">
        <div class="td-section-head"><div><p class="td-kicker">{{ __('ACCOUNT') }}</p><h2 id="quick-title">{{ __('Quick access') }}</h2></div></div>
        <div class="td-quick-grid">
            <a href="{{ route('customer.orders.index') }}"><span aria-hidden="true">📦</span><b>{{ __('My Orders') }}</b><small>{{ $orderCount }} {{ __('linked orders') }}</small></a>
            <a href="{{ route('customer.addresses.index') }}"><span aria-hidden="true">📍</span><b>{{ __('Addresses') }}</b><small>{{ $addressCount }} {{ __('saved') }}</small></a>
            <a href="{{ route('customer.profile.edit') }}"><span aria-hidden="true">👤</span><b>{{ __('My Profile') }}</b></a>
            <a href="{{ route('customer.security.edit') }}"><span aria-hidden="true">🔒</span><b>{{ __('Security') }}</b></a>
        </div>
    </section>
    <section class="td-info-row">
        <p>ℹ️ {{ __('Personalized gift order requests and status tracking are available for signed-in customers where stores accept orders. Online payment is not yet collected by this flow.') }}</p>
        @if($hasVendor)<a href="{{ route('vendor.home') }}">{{ __('Open Vendor Workspace') }} →</a>@else<a href="{{ route('vendor.apply') }}">{{ __('Become a Vendor') }} →</a>@endif
    </section>
@endsection
