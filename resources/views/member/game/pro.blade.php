@php
    $marketItems = \App\Models\Member\MarketItem::all();
@endphp
<!-- File Path: resources/views/member/game/pro.blade.php -->
@extends('member.partials.layout')

@section('title', '3D Game Pro World - Tidong®')

@section('content')
<div class="container-fluid py-3 px-3" style="background: #0f172a; min-height: 90vh; position: relative; overflow: hidden; border-radius: 12px;">
    
    <!-- Top HUD / Wallet & Status Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <div>
            <h4 class="fw-bold text-white mb-0"><i class="fas fa-cube text-info me-2"></i>Tidong Living World</h4>
            <small class="text-muted">3D Open-World Simulator | Infinite Roads & Showrooms</small>
        </div>
        <div class="d-flex gap-2">
            <div class="badge bg-success fs-6 px-3 py-2 shadow-sm d-flex align-items-center">
                <i class="fas fa-coins me-2 text-warning"></i> T-Coins: <span id="wallet-balance" class="ms-1">25,400</span>
            </div>
            <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#marketModal">
                <i class="fas fa-store me-1"></i> Market / Showroom
            </button>
        </div>
    </div>

    <!-- 1. Game Canvas Partial (3D World Rendering) -->
    @include('member.game.partials.game-canvas')

    <!-- 2. Player Character & Customization State -->
    @include('member.game.partials.player-character')

    <!-- 3. Touch & Keyboard Controls -->
    @include('member.game.partials.touch-controls')

    <!-- 4. Market / Showroom Modal Partial -->
    @include('member.game.partials.market-modal')

</div>
@endsection