@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">My Digital Visiting Cards</h3>
            <p class="text-muted small m-0">Aapke sabhi custom card variants aur sharing links</p>
        </div>
        
        <!-- Action Buttons Group -->
        <div class="d-flex align-items-center gap-2">
            <!-- Back to Dashboard Button Added Here -->
            <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>

            <a href="{{ route('member.card.view.create') }}" class="btn btn-primary rounded-3">
                <i class="fas fa-plus me-1"></i> Create New Variant
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($cardViews as $view)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary-subtle text-primary text-uppercase px-3 py-2 rounded-pill small fw-bold">
                            {{ ucfirst($view->theme_style) }} Theme
                        </span>
                        <small class="text-muted">{{ $view->created_at ? $view->created_at->format('d M, Y') : '' }}</small>
                    </div>

                    <div class="card-body px-3 py-2 text-center bg-light my-2 mx-3 rounded-3" style="transform: scale(0.9); transform-origin: top center; min-height: 180px;">
                        @include('member.card.render_engine', [
                            'masterCard'   => $masterCard,
                            'themeStyle'   => $view->theme_style,
                            'fullCardNo'   => $view->full_card_no ?? $masterCard->card_no,
                            'fieldToggles' => $view->field_toggles
                        ])
                    </div>

                    <div class="card-footer bg-white border-top-0 p-3">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ route('member.card.public', $view->card_slug) }}" id="card-url-{{ $view->id }}">
                            <button class="btn btn-sm btn-outline-secondary" onclick="copyUrl('card-url-{{ $view->id }}')">
                                <i class="fa-regular fa-copy"></i> Copy
                            </button>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('member.card.public', $view->card_slug) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 rounded-2">
                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview
                            </a>
                            <form action="{{ route('member.card.view.destroy', $view->id) }}" method="POST" onsubmit="return confirm('Kya aap is card variant ko delete karna chahte hain?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm p-5 bg-white rounded-4">
                    <div class="card-body">
                        <i class="fa-solid fa-id-card text-muted display-4 mb-3"></i>
                        <p class="text-muted small mb-4">Aapne abhi tak koi digital visiting card nahi banaya hai.</p>
                        <a href="{{ route('member.card.view.create') }}" class="btn btn-primary px-4 py-2 rounded-pill">
                            <i class="fas fa-plus me-1"></i> Create Your First Card
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function copyUrl(elementId) {
    var copyText = document.getElementById(elementId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    alert("Card URL Copied: " + copyText.value);
}
</script>
@endpush
@endsection