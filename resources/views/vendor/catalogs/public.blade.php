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
            padding-bottom: 120px;
        }
        .catalog-container { max-width: 100%; min-height: 100vh; background: #ffffff; }
        .catalog-header { 
            background: linear-gradient(135deg, #0f172a, #1e293b); 
            color: white; padding: 16px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .item-card { 
            border: 1px solid #edf2f7; border-radius: 10px; background: #fff; 
            padding: 10px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px;
        }
        .item-img-container {
            width: 60px; height: 60px; min-width: 60px; border-radius: 8px;
            overflow: hidden; background-color: #f1f5f9; border: 1px solid #e2e8f0;
        }
        .item-img { width: 100%; height: 100%; object-fit: cover; }
        .cart-bar {
            position: fixed; bottom: 0; left: 0; right: 0; background: #0f172a;
            color: #fff; padding: 12px 20px; border-top-left-radius: 16px;
            border-top-right-radius: 16px; box-shadow: 0 -4px 15px rgba(0,0,0,0.2);
            z-index: 1050; display: none;
        }
        .active-order-floating-bar {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: #1e293b; color: #fff; padding: 10px 16px;
            z-index: 1040; border-top: 2px solid #ffc107;
        }
        .qty-btn { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>

<div class="catalog-container">
    <!-- Header -->
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

    <!-- Alert Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-2 p-2 small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Items List -->
    <div class="p-2">
        <div class="d-flex justify-content-between align-items-center px-1 mb-2">
            <span class="text-uppercase text-muted fw-bold small" style="font-size: 11px;">उपलब्ध मेनू ({{ count($items) }})</span>
        </div>

        @forelse($items as $item)
            @php
                $rawPath = $item->image ?? $item->item_image ?? $item->photo ?? null;
                $src = 'https://via.placeholder.com/100?text=Food';
                if ($rawPath) {
                    if (Str::startsWith($rawPath, ['http://', 'https://'])) {
                        $src = $rawPath;
                    } else {
                        $cleanPath = str_replace(['public/', 'storage/'], '', $rawPath);
                        $cleanPath = ltrim($cleanPath, '/');
                        $src = asset('storage/' . $cleanPath);
                    }
                }
                $price = $item->price ?? $item->sale_price ?? 0;
            @endphp

            <div class="item-card shadow-sm">
                <div class="item-img-container">
                    <img src="{{ $src }}" alt="" class="item-img" onerror="this.src='https://via.placeholder.com/100?text=Food';">
                </div>
                
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
                    </div>
                </div>

                <div id="btn-container-{{ $item->id }}">
                    <button class="btn btn-outline-success btn-sm fw-bold px-3" 
                            onclick="window.addToCart({{ $item->id }}, '{{ addslashes($item->item_name) }}', {{ $price }})">
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

<!-- Active Order Floating Bar -->
@if($activeOrder || session('active_guest_order_id'))
    @php
        $activeId = $activeOrder->id ?? session('active_guest_order_id');
    @endphp
    <div class="active-order-floating-bar d-flex justify-content-between align-items-center shadow-lg" id="activeOrderNotice">
        <div>
            <span class="badge bg-warning text-dark me-1"><i class="fas fa-spinner fa-spin me-1"></i> Running Order</span>
            <div class="small text-white-50" style="font-size: 11px;">ऑर्डर ट्रैकिंग व बिल देखें</div>
        </div>
        <a href="{{ route('guest.order.status', $activeId) }}" class="btn btn-warning btn-sm fw-bold px-3">
            <i class="fas fa-receipt me-1"></i> View Order
        </a>
    </div>
@endif

<!-- Fixed Bottom Cart Bar -->
<div class="cart-bar justify-content-between align-items-center" id="cartBar">
    <div>
        <div class="small text-white-50"><span id="cartTotalItems">0</span> आइटम्स चुने गए</div>
        <div class="fw-bold fs-5 text-warning">₹<span id="cartTotalAmount">0.00</span></div>
    </div>
    <button class="btn btn-warning fw-bold px-4 rounded-pill" onclick="window.submitOrder()">
        ऑर्डर भेजें <i class="fas fa-paper-plane ms-1"></i>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.cart = {};
window.catalogId = {{ $catalog->id }};

window.addToCart = function(id, name, price) {
    window.cart[id] = { id: id, name: name, price: price, qty: 1 };
    window.renderCart();
};

window.updateQty = function(id, delta) {
    if (window.cart[id]) {
        window.cart[id].qty += delta;
        if (window.cart[id].qty <= 0) {
            delete window.cart[id];
        }
    }
    window.renderCart();
};

window.renderCart = function() {
    let totalQty = 0;
    let totalAmt = 0;

    const allButtons = document.querySelectorAll('[id^="btn-container-"]');
    allButtons.forEach(container => {
        let itemId = container.id.replace('btn-container-', '');
        let itemInCart = window.cart[itemId];

        if (itemInCart && itemInCart.qty > 0) {
            container.innerHTML = `
                <div class="d-flex align-items-center gap-2 bg-light border border-success rounded p-1">
                    <button class="btn btn-danger btn-sm qty-btn" onclick="window.updateQty(${itemId}, -1)">-</button>
                    <span class="fw-bold fs-6 px-1">${itemInCart.qty}</span>
                    <button class="btn btn-success btn-sm qty-btn" onclick="window.updateQty(${itemId}, 1)">+</button>
                </div>
            `;
        } else {
            container.innerHTML = container.getAttribute('data-default-btn') || container.innerHTML;
        }
    });

    Object.values(window.cart).forEach(item => {
        totalQty += item.qty;
        totalAmt += item.qty * item.price;
    });

    document.getElementById('cartTotalItems').innerText = totalQty;
    document.getElementById('cartTotalAmount').innerText = totalAmt.toFixed(2);

    let cartBar = document.getElementById('cartBar');
    let activeNotice = document.getElementById('activeOrderNotice');

    if (totalQty > 0) {
        cartBar.style.display = 'flex';
        if(activeNotice) activeNotice.style.display = 'none';
    } else {
        cartBar.style.display = 'none';
        if(activeNotice) activeNotice.style.display = 'flex';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="btn-container-"]').forEach(container => {
        container.setAttribute('data-default-btn', container.innerHTML);
    });
});

window.submitOrder = function() {
    if (Object.keys(window.cart).length === 0) return;

    let submitBtn = document.querySelector('#cartBar button');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'भेजा जा रहा है...';

    fetch("{{ route('menu.order', $catalog->slug) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            catalog_id: window.catalogId,
            cart: window.cart
        })
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'ऑर्डर भेजें <i class="fas fa-paper-plane ms-1"></i>';
        
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'ऑर्डर भेजें <i class="fas fa-paper-plane ms-1"></i>';
        alert("ऑर्डर भेजने में समस्या आई, पुनः प्रयास करें।");
    });
};
</script>

</body>
</html>