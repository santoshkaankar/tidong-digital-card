<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $masterCard->name ?? 'Digital Business Card' }} - Tidong</title>
    
    @php
        $supabaseBucketUrl = "https://wjuodttwrxzpmaifoqhz.supabase.co/storage/v1/object/public/uploads/";
        
        $rawImage = $cardView->card_preview ?? $masterCard->card_image ?? $masterCard->photo ?? null;
        
        if ($rawImage) {
            if (\Illuminate\Support\Str::startsWith($rawImage, ['http://', 'https://'])) {
                $imageUrl = $rawImage;
            } else {
                $cleanPath = ltrim($rawImage, '/');
                $cleanPath = preg_replace('#^(storage/|public/|uploads/)+#i', '', $cleanPath);
                $imageUrl = $supabaseBucketUrl . $cleanPath;
            }
        } else {
            $imageUrl = asset('images/default-card.png');
        }

        // Force HTTPS and add cache buster for WhatsApp
        $imageUrl = str_replace('http://', 'https://', $imageUrl);
        $shareImageUrl = $imageUrl . '?v=' . time();
    @endphp

    <!-- WhatsApp & Social Media Open Graph (OG) Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $masterCard->name ?? 'Digital Card' }} - {{ $masterCard->business_name ?? 'Tidong' }}">
    <meta property="og:description" content="🎴 Click here to view my complete Digital Business Card.">
    <meta property="og:image" content="{{ $shareImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $shareImageUrl }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="600">
    <meta property="og:image:height" content="315">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/card-materials.css') }}">
    
    <style>
        body { background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px 10px; margin: 0; }
        .public-card-container { width: 100%; max-width: 480px; margin: 0 auto; }
        
        /* Card Container Styling to remove Extra White Space */
        #card-capture-area {
            display: block;
            width: 100%;
            background: transparent !important;
            border-radius: 20px;
            overflow: hidden;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class="public-card-container text-center">
    
    <!-- Render Engine Component -->
    <div id="card-capture-area">
        @include('member.card.render_engine', [
            'masterCard'   => $masterCard,
            'themeStyle'   => $cardView->theme_style,
            'fullCardNo'   => $cardView->full_card_no ?? $masterCard->card_no,
            'fieldToggles' => $cardView->field_toggles
        ])
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
        <button onclick="downloadCardAsImage()" class="btn btn-warning rounded-pill px-4 shadow text-dark fw-bold">
            <i class="fa-solid fa-file-image me-2"></i> Download Image
        </button>

        <button onclick="downloadCardAsPDF()" class="btn btn-danger rounded-pill px-4 shadow text-white fw-bold">
            <i class="fa-solid fa-file-pdf me-2"></i> Download Clickable PDF
        </button>

        <a href="https://api.whatsapp.com/send?text={{ urlencode('Check out my Digital Visiting Card: ' . url()->current()) }}" 
           target="_blank" 
           class="btn btn-success rounded-pill px-4 shadow">
            <i class="fa-brands fa-whatsapp me-2"></i> Share WhatsApp
        </a>

        <button onclick="shareCard()" class="btn btn-outline-light rounded-pill px-4 shadow">
            <i class="fa-solid fa-share-nodes me-2"></i> Share
        </button>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<!-- html2pdf Library for Clickable PDF Support -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadCardAsImage() {
    const cardElement = document.getElementById('card-capture-area');
    
    html2canvas(cardElement, {
        useCORS: true,
        allowTaint: false,
        backgroundColor: null,
        scale: 3,
        logging: false
    }).then(canvas => {
        let link = document.createElement('a');
        link.download = '{{ Str::slug($masterCard->name ?? "visiting-card") }}-card.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }).catch(err => {
        console.error('Download image error:', err);
    });
}

function downloadCardAsPDF() {
    const cardElement = document.getElementById('card-capture-area');
    
    const opt = {
        margin:       0,
        filename:     '{{ Str::slug($masterCard->name ?? "visiting-card") }}-card.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 3, useCORS: true, logging: false },
        jsPDF:        { 
            unit: 'px', 
            format: [cardElement.offsetWidth, cardElement.offsetHeight], 
            orientation: 'landscape',
            compress: true
        }
    };

    html2pdf().set(opt).from(cardElement).save();
}

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