<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $masterCard->name ?? 'Digital Business Card' }} - Tidong</title>
    
    <!-- Meta tags for WhatsApp Link Preview -->
    <meta property="og:title" content="Digital Visiting Card: {{ $masterCard->name ?? 'Tidong Member' }}">
    <meta property="og:description" content="{{ $masterCard->designation ?? 'Professional Business Card' }} | {{ $masterCard->business_name ?? 'Tidong Digital' }}">
    
    @if(!empty($masterCard->photo))
        @php
            $photoUrl = Str::startsWith($masterCard->photo, 'http') ? $masterCard->photo : url($masterCard->photo);
        @endphp
        <meta property="og:image" content="{{ $photoUrl }}">
        <meta property="og:image:secure_url" content="{{ $photoUrl }}">
        <meta property="og:image:width" content="400">
        <meta property="og:image:height" content="400">
    @else
        <meta property="og:image" content="{{ asset('images/default-card.png') }}">
    @endif

    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Card Material CSS -->
    <link rel="stylesheet" href="{{ asset('css/card-materials.css') }}">
    
    <style>
        body {
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
            margin: 0;
            font-family: inherit;
        }
        .public-card-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="public-card-container text-center">
    
    @include('member.card.render_engine', [
        'masterCard'   => $masterCard,
        'themeStyle'   => $cardView->theme_style,
        'fullCardNo'   => $cardView->full_card_no ?? $masterCard->card_no,
        'fieldToggles' => $cardView->field_toggles
    ])

    <div class="d-flex justify-content-center gap-2 mt-4">
        <button onclick="shareCard()" class="btn btn-outline-light rounded-pill px-4 shadow">
            <i class="fa-solid fa-share-nodes me-2"></i> Share Card
        </button>
    </div>

    <div class="text-white-50 small mt-4">
        Created with <a href="{{ url('/') }}" target="_blank" class="text-white text-decoration-none fw-bold">Tidong Card Studio</a>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function shareCard() {
    var cardName = "{{ $masterCard->name ?? 'Digital Card' }}";
    var shareText = "Hello, check out my Digital Visiting Card: " + cardName + "\n" + window.location.href;

    if (navigator.share) {
        navigator.share({
            title: cardName + " - Digital Visiting Card",
            text: shareText,
            url: window.location.href
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(shareText);
        alert('Card link copied to clipboard!');
    }
}
</script>

</body>
</html>