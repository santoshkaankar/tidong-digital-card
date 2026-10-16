@php
    $marketItems = \App\Models\Member\MarketItem::all();
@endphp
<!-- File Path: resources/views/member/game/pro.blade.php -->
@extends('member.partials.layout')

@section('title', '3D Game Pro World - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4" style="background: #f8fafc; min-height: 85vh;">
    @include('member.game.partials.game-canvas')
</div>
@endsection