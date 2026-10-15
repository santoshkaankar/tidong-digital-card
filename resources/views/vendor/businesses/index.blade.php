@extends('layouts.vendor_master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Form Partial -->
        <div class="col-md-4">
            @include('vendor.businesses.partials._form')
        </div>

        <!-- List Partial -->
        <div class="col-md-8">
            @include('vendor.businesses.partials._list')
        </div>
    </div>
</div>
@endsection