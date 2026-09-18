@extends('retail.layouts.master')

@section('content')
<div class="container py-4">
    <h2>Shopping Cart</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Sample Product</td>
                    <td>₹500</td>
                    <td>1</td>
                    <td>₹500</td>
                    <td><button class="btn btn-danger btn-sm">Remove</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-end">
        <a href="{{ route('retail.checkout.index') }}" class="btn btn-primary">Proceed to Checkout</a>
    </div>
</div>
@endsection