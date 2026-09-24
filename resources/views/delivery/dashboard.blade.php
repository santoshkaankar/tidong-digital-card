@extends('delivery.partials.layout')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Stats Section -->
        @include('delivery.partials.stats')

        <!-- Available Orders Section -->
        @include('delivery.partials.orders')
    </div>
@endsection