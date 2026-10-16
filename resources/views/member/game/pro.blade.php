@extends('member.partials.layout')

@section('title', '3D Game Pro - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-0 shadow-lg rounded-4 bg-dark text-white p-5">
                <div class="mb-3">
                    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="fas fa-crown me-1"></i> Pro Pass Active (365 Days)</span>
                </div>
                <h1 class="fw-bold text-warning mb-3">Minecraft Real 3D (Pro World)</h1>
                <p class="text-muted mb-4">
                    Aapka Pro subscription active hai! Yeh continuous world mode hai jisme aapke saare builds, inventory aur T-Coin rewards permanently save rahenge.
                </p>
                <div class="p-4 bg-secondary bg-opacity-25 rounded-3 border border-secondary mb-4">
                    <h5 class="text-info fw-bold mb-2"><i class="fas fa-tools me-2"></i> Game Engine Loading...</h5>
                    <p class="small text-muted mb-0">Full 3D WebGL game canvas yahan jald hi load hoga. Aapka progress secure hai.</p>
                </div>
                <a href="{{ url('/member/dashboard') }}" class="btn btn-outline-warning rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection