<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $catalog->address }} - डिजिटल मेनू</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            padding-bottom: 90px;
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
            padding: 10px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .item-img-container {
            width: 60px;
            height: 60px;
            min-width: 60px;
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
        .cart-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #0f172a;
            color: #fff;
            padding: 12px 20px;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.2);
            z-index: 1050;
            display: none;
        }
        .qty-btn {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-weight: bold;
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
                $rawPath = $item->image ?? $item->item_image ?? $item->photo ?? null;
                if ($rawPath) {
                    if (Str::startsWith($rawPath, ['http://', 'https://'])) {
                        $src = $rawPath;
                    } else {
                        $cleanPath = str_replace(['public/', 'storage/'], '', $rawPath);
                        $cleanPath = ltrim($cleanPath, '/');
                        if (!Str::contains($cleanPath, '/')) {
                            $cleanPath = 'global-items/' . $cleanPath;
                        }
                        $src = asset('storage/' . $cleanPath);
                    }
                } else {
                    $src = 'https://via.placeholder.com/100?text=Food';
                }
                $price = $item->price ?? $item->sale_price ?? 0;
            @endphp

            <div class="item-card shadow-sm">
                <!-- Image -->
                <div class="item-img-container">
                    <img src="{{ $src }}" alt="{{ $item->item_name }}" class="item-img" onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=Food';">
                </div>
                
                <!-- Info -->
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 13px;">
                        {{ $item->item_name }}
                    </h6>
                    @if(!empty($item->description))
                        <p class="text-muted mb-1 text-truncate" style="font-size: 10px; line-height: 1.1;">
                            {{ $item->description }}
                        </p>
                    @endif
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="fw-bold text-success" style="font-size: 13px;">
                            ₹{{ number_format($price, 2) }}
                        </span>
                        @if(isset($item->mrp) && $item->mrp > $price)
                            <span class="text-muted text-decoration-line-through" style="font-size: 10px;">
                                ₹{{ number_format($item->mrp, 2) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Toggle / Qty Selector -->
                <div id="btn-container-{{ $item->id }}">
                    <button class="btn btn-outline-success btn-sm fw-bold px-3" 
                            onclick="addToCart({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $price }})">
                        + ADD
                    </button>
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

<!-- Bottom Floating Cart Bar -->
<div class="cart-bar justify-content-between align-items-center" id="cartBar">
    <div>
        <div class="small text-white-50"><span id="cartTotalItems">0</span> आइटम्स चुने गए</div>
        <div class="fw-bold fs-5 text-warning">₹<span id="cartTotalAmount">0.00</span></div>
    </div>
    <button class="btn btn-warning fw-bold px-4 rounded-pill" onclick="submitOrder()">
        ऑर्डर भेजें <i class="fas fa-paper-plane ms-1"></i>
    </button>
</div>

<script>
let cart = {};
const catalogId = {{ $catalog->id }};

function addToCart(id, name, price) {
    cart[id] = { id: id, name: name, price: price, qty: 1 };
    renderCart();
}

function updateQty(id, delta) {
    if (cart[id]) {
        cart[id].qty += delta;
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
    }
    renderCart();
}

function renderCart() {
    let totalQty = 0;
    let totalAmt = 0;

    // Loop through all items to update button state
    Object.keys(cart).forEach(id => {
        totalQty += cart[id].qty;
        totalAmt += cart[id].qty * cart[id].price;
    });

    // Update each item container
    @foreach($items as $item)
        let itemId = {{ $item->id }};
        let price = {{ $item->price ?? $item->sale_price ?? 0 }};
        let name = "{{ addslashes($item->item_name) }}";
        let container = document.getElementById(`btn-container-${itemId}`);

        if (container) {
            if (cart[itemId] && cart[itemId].qty > 0) {
                container.innerHTML = `
                    <div class="d-flex align-items-center gap-2 bg-light border border-success rounded p-1">
                        <button class="btn btn-danger btn-sm qty-btn" onclick="updateQty(${itemId}, -1)">-</button>
                        <span class="fw-bold fs-6 px-1">${cart[itemId].qty}</span>
                        <button class="btn btn-success btn-sm qty-btn" onclick="updateQty(${itemId}, 1)">+</button>
                    </div>
                `;
            } else {
                container.innerHTML = `
                    <button class="btn btn-outline-success btn-sm fw-bold px-3" onclick="addToCart(${itemId}, '${name}', ${price})">
                        + ADD
                    </button>
                `;
            }
        }
    @endforeach

    // Update Bottom Cart Bar Details
    document.getElementById('cartTotalItems').innerText = totalQty;
    document.getElementById('cartTotalAmount').innerText = totalAmt.toFixed(2);

    let cartBar = document.getElementById('cartBar');
    if (totalQty > 0) {
        cartBar.style.display = 'flex';
    } else {
        cartBar.style.display = 'none';
    }
}
</script>

</body>
</html>