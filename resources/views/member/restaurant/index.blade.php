@extends('member.partials.layout')

@section('content')
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .food-hero-banner {
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        color: #1e293b;
    }
    .filter-pill {
        border-radius: 25px;
        padding: 7px 18px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none !important;
        border: 1px solid #cbd5e1;
        color: #475569;
        background: #ffffff;
    }
    .filter-pill:hover { background: #f1f5f9; color: #0f172a; }
    .filter-pill.active-all { background: #2563eb; color: #ffffff !important; border-color: #2563eb; }
    .filter-pill.active-veg { background: #16a34a; color: #ffffff !important; border-color: #16a34a; }
    .filter-pill.active-nonveg { background: #dc2626; color: #ffffff !important; border-color: #dc2626; }
    .restaurant-card { border: 1px solid #e2e8f0; border-radius: 16px; transition: all 0.25s ease; background: #ffffff; }
    .restaurant-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05) !important; border-color: #cbd5e1; }
    .rest-avatar { width: 50px; height: 50px; border-radius: 12px; background: #eff6ff; border: 1px solid #dbeafe; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 700; }
</style>

<div class="container-fluid py-4 px-4">

    <!-- 1. Banner Partial -->
    @include('member.restaurant.partials.banner')

    <!-- 2. Search & Filter Partial -->
    @include('member.restaurant.partials.filters')

    <!-- 3. Restaurant Grid -->
    <div class="row g-3">
        @forelse($restaurants as $restaurant)
            @include('member.restaurant.partials.card', ['restaurant' => $restaurant])
        @empty
            @include('member.restaurant.partials.empty')
        @endforelse
    </div>

    <!-- 4. Pagination -->
    @if(method_exists($restaurants, 'links'))
        <div class="mt-4 d-flex justify-content-center">
            {{ $restaurants->links() }}
        </div>
    @endif

</div>
@endsection