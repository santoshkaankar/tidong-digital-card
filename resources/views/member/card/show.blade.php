<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $masterCard->name ?? 'Digital Business Card' }} - Tidong</title>
    
    @php
        // Supabase Storage Bucket Public URL
        $supabaseBucketUrl = "https://wjuodttwrxzpmaifoqhz.supabase.co/storage/v1/object/public/uploads/";
        
        $rawImage = $cardView->card_preview ?? $masterCard->card_image ?? $masterCard->photo ?? null;
        
        if ($rawImage) {
            if (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                $imageUrl = $rawImage;
            } else {
                $cleanPath = ltrim($rawImage, '/');
                if (\Illuminate\Support\Str::startsWith($cleanPath, 'uploads/')) {
                    $cleanPath = \Illuminate\Support\Str::replaceFirst('uploads/', '', $cleanPath);
                }
                $imageUrl = $supabaseBucketUrl . $cleanPath;
            }
        } else {
            $imageUrl = asset('images/default-card.png');
        }

        // Secure HTTPS link ensure karein
        $imageUrl = str_replace('http://', 'https://', $imageUrl);
    @endphp

    <!-- WhatsApp & Social Media Open Graph (OG) Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $masterCard->name ?? 'Digital Card' }} - {{ $masterCard->business_name ?? 'Tidong' }}">
    <meta property="og:description" content="🎴 Click here to view my complete Digital Business Card.">

    <meta property="og:image" content="{{ $imageUrl }}">
    <meta property="og:image:secure_url" content="{{ $imageUrl }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

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
    
    <!-- Render Engine Component -->
    @include('member.card.render_engine', [
        'masterCard'   => $masterCard,
        'themeStyle'   => $cardView->theme_style,
        'fullCardNo'   => $cardView->full_card_no ?? $masterCard->card_no,
        'fieldToggles' => $cardView->field_toggles
    ])

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
        <!-- Direct WhatsApp Share Button -->
        <a href="https://api.whatsapp.com/send?text={{ urlencode('Check out my Digital Visiting Card: ' . url()->current()) }}" 
           target="_blank" 
           class="btn btn-success rounded-pill px-4 shadow">
            <i class="fa-brands fa-whatsapp me-2"></i> Share on WhatsApp
        </a>

        <!-- System Native Share Button -->
        <button onclick="shareCard()" class="btn btn-outline-light rounded-pill px-4 shadow">
            <i class="fa-solid fa-share-nodes me-2"></i> Other Share
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