@extends('member.partials.layout')

@section('title', '3D Game Pro World - Tidong®')

@section('content')
<div class="container-fluid p-3" style="background: #090d16; min-height: 90vh; border-radius: 12px; color: #fff;">
    
    <!-- Top HUD / Wallet & Status Bar -->
    <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-dark rounded border border-secondary shadow-sm">
        <div class="d-flex align-items-center gap-4">
            <h4 class="mb-0 text-info fw-bold"><i class="fas fa-city me-2"></i>Tidong Pro World (Subscription Active)</h4>
            
            <!-- Hunger Meter -->
            <div class="d-flex align-items-center gap-2" style="width: 160px;">
                <small class="text-warning fw-bold"><i class="fas fa-utensils"></i> Hunger</small>
                <div class="progress w-100" style="height: 10px; background: #334155;">
                    <div id="hunger-bar" class="progress-bar bg-warning" style="width: 85%;"></div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Persistent Wallet Balance from Database -->
            <div class="badge bg-success fs-6 px-3 py-2 shadow-sm">
                <i class="fas fa-coins text-warning me-1"></i>
                T-Coins: <span id="t-coin-display">{{ number_format($wallet->t_coins ?? 0, 2) }}</span>
            </div>
            
            <a href="{{ route('member.wallet') }}" class="btn btn-warning btn-sm fw-bold shadow-sm text-dark">
                <i class="fas fa-plus-circle me-1"></i> Recharge T-Coins (INR)
            </a>

            <button class="btn btn-info btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#marketModal">
                <i class="fas fa-store me-1"></i> Showroom & Market
            </button>
        </div>
    </div>

    <!-- 1. 3D Canvas Partial -->
    @include('member.game.partials.game-canvas')

    <!-- 2. Touch & Keyboard Controls Partial -->
    @include('member.game.partials.touch-controls')

    <!-- 3. Player Character Customization State -->
    @include('member.game.partials.player-character')

    <!-- 4. Market Modal Partial with Parody Brands -->
    @include('member.game.partials.market-modal')

</div>
@endsection