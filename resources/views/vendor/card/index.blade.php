@extends('vendor.card.layout')

@section('title', 'My Digital Cards')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="fa-solid fa-id-card me-2"></i>Vendor Cards Dashboard</h3>
        <a href="{{ route('vendor.cards.create') }}" class="btn btn-success fw-bold"><i class="fa-solid fa-plus me-2"></i>Create New View / Config</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($cardViews as $view)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ strtoupper($view->theme_style) }}</span>
                            <small class="text-muted">{{ $view->full_card_no }}</small>
                        </div>
                        <p class="mb-1 text-truncate"><strong>Slug:</strong> {{ $view->card_slug }}</p>
                        
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('vendor.card.public', $view->card_slug) }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill"><i class="fa-solid fa-eye me-1"></i>View</a>
                            <form action="{{ route('vendor.cards.view.delete', $view->id) }}" method="POST" onsubmit="return confirm('Delete karein?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Koi custom card view nahi mila. Naya card view generate karein!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection