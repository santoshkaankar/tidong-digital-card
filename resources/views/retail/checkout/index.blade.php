@extends('retail.layouts.master')

@section('content')
<div class="container py-4">
    <h2>Checkout</h2>
    <form action="{{ route('retail.checkout.process') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <h4>Shipping Address</h4>
                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
            </div>
            <div class="col-md-5">
                <h4>Payment Method</h4>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="payment_method" value="cod" checked>
                    <label class="form-check-label">Cash on Delivery (COD)</label>
                </div>
                <button type="submit" class="btn btn-success w-100">Place Order</button>
            </div>
        </div>
    </form>
</div>
@endsection