@extends('layouts.app') {{-- Agar aapka layout use ho raha hai, warna HTML structure rakhein --}}

@section('content')
<div class="welcome-wrapper">
    <!-- Navbar -->
    @include('partials.welcome.navbar')

    <!-- Hero Section -->
    @include('partials.welcome.hero')

    <!-- Quick Services Menu Grid -->
    @include('partials.welcome.services')

    <!-- Main Ad Platform Carousel -->
    @include('partials.welcome.carousel-ads')

    <!-- Universal QR Scan Section -->
    @include('partials.welcome.qr-section')

    <!-- Features Section -->
    @include('partials.welcome.features')

    <!-- Instructions & Guides Cards Grid (40-50 Cards) -->
    @include('partials.welcome.instructions-grid')

    <!-- Sponsored Ads & Partner Offers Grid -->
    @include('partials.welcome.sponsored-ads')

    <!-- Modals (About, Terms, Privacy, Contact) -->
    @include('partials.welcome.modals')

    <!-- Footer -->
    @include('partials.welcome.footer')
</div>
@endsection