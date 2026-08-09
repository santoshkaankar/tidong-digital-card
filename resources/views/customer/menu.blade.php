<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business->name }} - Digital Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Header / Restaurant Info -->
    <div class="bg-dark text-white py-4 px-3 text-center shadow-sm">
        <h2 class="fw-bold mb-1"><i class="fas fa-utensils text-warning me-2"></i> {{ $business->name }}</h2>
        <p class="text-white-50 small mb-0"><i class="fas fa-store me-1"></i> {{ ucfirst($business->business_type ?? 'Restaurant / Cafe') }}</p>
    </div>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Menu Items List -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-4 text-dark border-bottom pb-2">Menu Catalog</h4>

                    @forelse($menuItems as $category => $items)
                        <div class="mb-4">
                            <h5 class="fw-bold text-primary bg-light p-2 rounded-2"><i class="fas fa-layer-group me-1"></i> {{ $category }}</h5>
                            <div class="row g-3">
                                @foreach($items as $item)
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between shadow-sm">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="fw-bold mb-1">{{ $item->item_name }}</h6>
                                                <span class="text-success fw-bold">₹{{ $item->price }}</span>
                                            </div>
                                            <p class="text-muted small mb-2">{{ $item->description ?? 'Delicious freshly prepared item.' }}</p>
                                        </div>
                                        <div class="text-end">
                                            <button class="btn btn-outline-primary btn-sm px-3" onclick="addToCart('{{ $item->id }}', '{{ $item->item_name }}', '{{ $item->price }}')">
                                                <i class="fas fa-plus me-1"></i> Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-utensils fs-1 text-secondary mb-3"></i>
                            <p>No menu items available right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cart & Order Form Section -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white sticky-top" style="top: 20px;">
                    <h4 class="fw-bold mb-3 text-dark"><i class="fas fa-shopping-cart text-warning me-2"></i> Your Order</h4>
                    
                    <form action="{{ route('menu.order', $business->slug) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Customer Name</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Enter your name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Table Number / Details</label>
                            <input type="text" name="table_no" class="form-control" placeholder="e.g. Table 4" required>
                        </div>

                        <div class="border-top border-bottom py-3 my-3">
                            <p class="text-muted small text-center mb-0" id="empty-cart-text">Cart is empty. Select items from menu.</p>
                            <div id="cart-items-container"></div>
                        </div>

                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total Amount:</span>
                            <span class="text-success" id="cart-total">₹0.00</span>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold py-2"><i class="fas fa-check-circle me-1"></i> Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Cart JavaScript -->
    <script>
        let cart = {};

        function addToCart(id, name, price) {
            if (cart[id]) {
                cart[id].qty += 1;
            } else {
                cart[id] = { name: name, price: parseFloat(price), qty: 1 };
            }
            renderCart();
        }

        function renderCart() {
            let container = document.getElementById('cart-items-container');
            let emptyText = document.getElementById('empty-cart-text');
            let totalElement = document.getElementById('cart-total');
            
            container.innerHTML = '';
            let total = 0;
            let count = 0;

            for (let id in cart) {
                count++;
                let item = cart[id];
                let itemTotal = item.price * item.qty;
                total += itemTotal;

                container.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <div>
                            <strong>${item.name}</strong><br>
                            <span class="text-muted">₹${item.price} x ${item.qty}</span>
                        </div>
                        <div>
                            <span class="fw-bold text-success">₹${itemTotal}</span>
                            <input type="hidden" name="items[${id}]" value="${item.qty}">
                        </div>
                    </div>
                `;
            }

            if (count > 0) {
                emptyText.style.display = 'none';
            } else {
                emptyText.style.display = 'block';
            }

            totalElement.innerText = '₹' + total.toFixed(2);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>