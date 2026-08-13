@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Top Bar with Back to Dashboard & Create New Card Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm">
            <i class="fas fa-home me-1"></i> Back to Dashboard
        </a>
        <h4 class="mb-0 fw-bold">My Digital Visiting Cards</h4>
        <a href="{{ route('member.card.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Create New Card
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cards Listing Grid -->
    <div class="row">
        @forelse($cards as $card)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <!-- Card Type Badge -->
                            @php
                                $cardType = $card->card_type ?? $card->design_type ?? 'modern';
                            @endphp
                            <span class="badge bg-primary text-uppercase">{{ ucfirst($cardType) }} Style</span>
                            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i>{{ $card->created_at ? $card->created_at->format('d M Y') : '' }}</span>
                        </div>
                        <span class="badge bg-secondary mb-2">Card No: {{ $card->card_no ?? $card->id }}</span>
                        <h5 class="fw-bold text-dark mb-2">{{ $card->business_name ?? 'My Business Name' }}</h5>
                        <p class="mb-1 text-secondary small"><b>Owner:</b> {{ $card->name ?? 'N/A' }}</p>
                        <p class="mb-3 text-secondary small"><b>Phone:</b> {{ $card->phone ?? 'N/A' }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('member.card.show', $card->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye me-1"></i> View Card
                        </a>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/?text={{ urlencode('Check out my digital visiting card: ' . route('member.card.show', $card->id)) }}" target="_blank" class="btn btn-outline-success btn-sm w-50">
                                <i class="fab fa-whatsapp me-1"></i> Share
                            </a>
                            <a href="{{ route('member.card.show', $card->id) }}" onclick="window.print(); return false;" class="btn btn-outline-dark btn-sm w-50">
                                <i class="fas fa-download me-1"></i> Print / PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="card border-0 shadow-sm p-4 bg-light">
                <div class="card-body">
                    <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold text-muted">No Visiting Cards Found</h5>
                    <p class="text-muted small mb-3">Aapne abhi tak koi digital visiting card nahi banaya hai. Niche diye gaye button par click karke apna pehla card banayein.</p>
                    <a href="{{ route('member.card.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Create Your First Card
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection