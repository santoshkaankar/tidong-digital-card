@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Top Bar with Back to Dashboard & Create New Card Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm">🏠 Back to Dashboard</a>
        <h4 class="mb-0 fw-bold">My Digital Visiting Cards</h4>
        <a href="{{ route('member.card.create') }}" class="btn btn-primary btn-sm">+ Create New Card</a>
    </div>

    <!-- Cards Listing Grid -->
    <div class="row">
        @foreach($cards as $card)
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm p-3">
                <h5>{{ $card->business_name }}</h5>
                <p class="mb-1"><b>Owner:</b> {{ $card->name }}</p>
                <p class="mb-2"><b>Card No:</b> <span class="badge bg-secondary">{{ $card->card_no }}</span></p>
                <a href="{{ route('member.card.show', $card->id) }}" class="btn btn-success btn-sm w-100">View Card</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection