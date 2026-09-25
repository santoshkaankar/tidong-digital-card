<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $masterCard->name ?? 'Digital Card' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 p-3">
    <div style="width: 100%; max-width: 440px;">
        @include('vendor.card.render_engine', ['cardView' => $cardView, 'masterCard' => $masterCard])
    </div>
</body>
</html>