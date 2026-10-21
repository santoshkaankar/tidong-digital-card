@extends('customer.layout')
@section('title', __('Explore Services'))
@section('content')
    <div class="td-page-heading"><span class="td-kicker">{{ __('DISCOVER TIDONG') }}</span><h1>{{ __('All businesses & services') }}</h1><p>{{ __('Explore active services. More are being developed for a better customer experience.') }}</p></div>
    <div class="td-service-grid td-service-grid-full">
        @foreach($categories as $key => $category)
            @if($category['available'])
                <a href="{{ route('customer.explore.service', $key) }}" class="td-service-tile td-tone-{{ $category['tone'] }}">
                    <span class="td-service-icon" aria-hidden="true">{{ $category['icon'] }}</span><strong>{{ __($category['title']) }}</strong><span class="td-service-status">{{ __($category['description']) }} · {{ $category['business_count'] }} {{ __('stores') }}</span>
                </a>
            @else
                <div class="td-service-tile td-tone-{{ $category['tone'] }} td-inactive">
                    <span class="td-service-icon" aria-hidden="true">{{ $category['icon'] }}</span><strong>{{ __($category['title']) }}</strong><span class="td-service-status">{{ __('Not available yet') }}</span>
                </div>
            @endif
        @endforeach
    </div>
    <p class="td-footnote">{{ __('A business must be approved and explicitly enabled for orders before it can appear in the store directory.') }}</p>
@endsection
