@extends('vendor.card.layout')

@section('title', 'My Digital Cards')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fa-solid fa-id-card me-2"></i>My Digital Cards</h4>
        <a href="{{ route('vendor.card.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Create New Card
        </a>
    </div>

    <div class="row">
        @forelse($cardViews as $view)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ strtoupper($view->theme_style ?? 'Default') }}</span>
                            <small class="text-muted">{{ $view->full_card_no }}</small>
                        </div>
                        <p class="mb-3 text-truncate"><strong>Slug:</strong> {{ $view->card_slug }}</p>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('vendor.card.public', $view->card_slug) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye me-1"></i> View
                            </a>
                            <form action="{{ route('vendor.card.view.delete', $view->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this card view?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center py-4">
                    <p class="mb-2">No digital cards found!</p>
                    <a href="{{ route('vendor.card.create') }}" class="btn btn-sm btn-primary">Create Your First Card</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection