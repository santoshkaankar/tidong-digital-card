<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $catalog->address }} - डिजिटल मेनू</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        .catalog-container { 
            max-width: 100%; 
            min-height: 100vh; 
            background: #ffffff; 
        }
        .catalog-header { 
            background: linear-gradient(135deg, #0f172a, #1e293b); 
            color: white; 
            padding: 16px; 
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .item-card { 
            border: 1px solid #edf2f7; 
            border-radius: 10px; 
            background: #fff; 
            padding: 8px 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.2s;
        }
        .item-card:active {
            background-color: #f8fafc;
        }
        .item-img-container {
            width: 55px;
            height: 55px;
            min-width: 55px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
        }
        .item-img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }
    </style>
</head>
<body>

<div class="catalog-container">
    <!-- Sticky Header -->
    <div class="catalog-header text-center">
        <h6 class="fw-bold mb-1 text-white text-truncate" style="max-width: 90%; margin: 0 auto; font-size: 16px;">
            {{ $vendor->name ?? $vendor->business_name ?? 'डिजिटल मेनू' }}
        </h6>
        <div class="mt-1">
            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 fw-bold" style="font-size: 11px;">
                <i class="fas fa-map-marker-alt me-1"></i> {{ $catalog->address }}
            </span>
        </div>
    </div>

    <!-- Items List -->
    <div class="p-2">
        <div class="d-flex justify-content-between align-items-center px-1 mb-2">
            <span class="text-uppercase text-muted fw-bold small" style="font-size: 11px;">उपलब्ध मेनू ({{ count($items) }})</span>
        </div>

        @forelse($items as $item)
            @php
                // Image field detect
                $rawPath = $item->image ?? $item->item_image ?? $item->photo ?? null;
                
                if ($rawPath) {
                    if (Str::startsWith($rawPath, ['http://', 'https://'])) {
                        $src = $rawPath;
                    } else {
                        // public/ या storage/ क्लीन करें
                        $cleanPath = str_replace(['public/', 'storage/'], '', $rawPath);
                        $cleanPath = ltrim($cleanPath, '/');
                        
                        // अगर फोल्डर नाम missing है तो global-items जोड़ें
                        if (!Str::contains($cleanPath, '/')) {
                            $cleanPath = 'global-items/' . $cleanPath;
                        }
                        
                        $src = asset('storage/' . $cleanPath);
                    }
                } else {
                    $src = 'https://via.placeholder.com/100?text=Food';
                }
            @endphp

            <div class="item-card shadow-sm">
                <!-- Image Container -->
                <div class="item-img-container">
                    <img src="{{ $src }}" 
                         alt="{{ $item->item_name }}" 
                         class="item-img"
                         onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=Food';">
                </div>
                
                <!-- Info Container -->
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 13px; line-height: 1.2;">
                        {{ $item->item_name }}
                    </h6>
                    
                    @if(!empty($item->description))
                        <p class="text-muted mb-1 text-truncate" style="font-size: 10px; line-height: 1.1;">
                            {{ $item->description }}
                        </p>
                    @endif

                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="fw-bold text-success" style="font-size: 13px;">
                            ₹{{ number_format($item->price ?? $item->sale_price ?? 0, 2) }}
                        </span>
                        @if(isset($item->mrp) && $item->mrp > ($item->price ?? $item->sale_price))
                            <span class="text-muted text-decoration-line-through" style="font-size: 10px;">
                                ₹{{ number_format($item->mrp, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-utensils text-muted fa-2x mb-2"></i>
                <p class="text-muted small">कोई आइटम उपलब्ध नहीं है।</p>
            </div>
        @endforelse
    </div>
</div>

</body>
</html>