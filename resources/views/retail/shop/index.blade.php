@extends('retail.layouts.master')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-md-3">
            <h3>Categories</h3>
            <ul class="list-group">
                @foreach($categories as $category)
                    <li class="list-group-item">
                        <a href="{{ route('retail.shop.index', ['category' => $category->id]) }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Product Grid -->
        <div class="col-md-9">
            <h2>Shop Products</h2>
            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->title }}</h5>
                                <p class="card-text">₹{{ $product->price }}</p>
                                <a href="{{ route('retail.shop.show', $product->id) }}" class="btn btn-primary btn-sm">View Product</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>Koi product nahi mila.</p>
                @endforelse
            </div>
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection