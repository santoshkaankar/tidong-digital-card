@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Digital Visiting Cards</h2>
        <a href="{{ route('customer.card.create') }}" class="btn btn-primary">+ Create New Card</a>
    </div>

    @if(isset($cards) && count($cards) > 0)
        <div class="row">
            @foreach($cards as $card)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm p-3">
                        <h4>{{ $card->business_name }}</h4>
                        <p class="mb-1"><b>Owner:</b> {{ $card->name }}</p>
                        <p class="mb-1"><b>Card No:</b> <span class="badge bg-secondary">{{ $card->card_no ?? 'N/A' }}</span></p>
                        <p class="mb-3"><b>Phone:</b> {{ $card->phone }}</p>
                        <a href="{{ route('customer.card.show', $card->id) }}" class="btn btn-sm btn-success">View Card</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            No visiting cards found yet! <a href="{{ route('customer.card.create') }}">Create your first card</a>.
        </div>
    @endif
</div>
@endsection