<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $masterCard->name ?? 'Digital Business Card' }} - Tidong</title>
    
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
        Created with <strong class="text-white">Tidong Card Studio</strong>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function shareCard() {
    if (navigator.share) {
        navigator.share({
            title: "{{ $masterCard->name }} - Digital Card",
            url: window.location.href
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Card URL copied to clipboard!');
    }
}
</script>

</body>
</html>