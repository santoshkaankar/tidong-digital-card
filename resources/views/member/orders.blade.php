@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">My Orders</h3>
            <p class="text-muted small m-0">Aapke sabhi orders aur transactions ka record</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
        <p class="text-muted my-4">Abhi tak aapne koi order nahi kiya hai.</p>
    </div>
</div>
@endsection