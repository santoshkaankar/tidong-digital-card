@extends('retail.layouts.master')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card p-4 text-center bg-light">
                <h4>[ Product Image ]</h4>
            </div>
        </div>
        <div class="col-md-6">
            <h2>{{ $product->title }}</h2>
            <h4 class="text-success">₹{{ $product->price }}</h4>
            <p>{{ $product->description }}</p>
            
            <form action="{{ route('retail.cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="mb-3">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" class="form-control w-25">
                </div>
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>
        </div>
    </div>
</div>
@endsection