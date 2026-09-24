@extends('member.partials.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .restaurant-header {
        background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }
    .menu-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .menu-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .badge-veg {
        border: 1px solid #16a34a;
        color: #16a34a;
        font-size: 0.75rem;
    }
    .badge-nonveg {
        border: 1px solid #dc2626;
        color: #dc2626;
        font-size: 0.75rem;
    }
    .floating-order-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 600px;
        background: #ffffff;
        border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 1050;
        padding: 12px 24px;
    }
</style>

<div class="container-fluid py-4 px-4 pb-5">

    <!-- Back Button & Breadcrumb -->
    <div class="mb-3">
        <a href="{{ route('member.restaurant.index') }}" class="btn btn-sm btn-light border text-muted rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Restaurants
        </a>
    </div>

    <!-- Restaurant Info Header -->
    <div class="restaurant-header p-4 mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 60px; height: 60px;">
                        {{ strtoupper(substr($restaurant->name ?? 'R', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ $restaurant->name ?? 'Restaurant Name' }}</h3>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ $restaurant->city ?? 'Kota' }}, {{ $restaurant->state ?? 'Rajasthan' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                    <i class="fas fa-check-circle me-1"></i> Accepting Orders
                </span>
            </div>
        </div>
    </div>

    <!-- Menu Section -->
    <div class="row">
        <!-- Categories Sidebar / Filter -->
        <div class="col-lg-3 mb-4">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                <h6 class="fw-bold text-dark mb-3">Categories</h6>
                <div class="nav flex-column nav-pills gap-1">
                    <button class="nav-link active text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-all">
                        All Items
                    </button>
                    @foreach($categories as$category)
                        <button class="nav-link text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu Items List -->
        <div class="col-lg-9">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                <h5 class="fw-bold text-dark mb-3">Menu Items</h5>

                <div class="row g-3">
                    @forelse($items as$item)
                        <div class="col-md-6">
                            <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @if(isset($item->item_type) && in_array($item->item_type, ['veg', 'pure_veg']))
                                            <span class="badge badge-veg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> VEG</span>
                                        @else
                                            <span class="badge badge-nonveg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> NON-VEG</span>
                                        @endif
                                        <h6 class="fw-bold text-dark mb-0">{{ $item->name }}</h6>
                                    </div>
                                    <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">
                                        {{ $item->description ?? 'Freshly prepared food item.' }}
                                    </p>
                                    <span class="fw-bold text-dark">₹{{ number_format($item->price ?? 0, 2) }}</span>
                                </div>
                                
                                <!-- Quantity Control Counter Buttons -->
                                <div>
                                    <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 100px;">
                                        <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty({{ $item->id }}, {{$item->price }}, -1)">-</button>
                                        <input type="text" id="qty-{{ $item->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                        <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty({{ $item->id }}, {{$item->price }}, 1)">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center text-muted">
                            <i class="fas fa-utensils fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">No food items added yet for this restaurant.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Floating Bottom Order Bar -->
<div id="floatingOrderBar" class="floating-order-bar d-none">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold text-muted small d-block" id="selectedItemsCount">0 Items Selected</span>
            <span class="fw-bold text-success fs-5" id="selectedTotalAmount">₹0.00</span>
        </div>
        <button type="button" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="submitLiveOrder()">
            Send Order <i class="fas fa-arrow-right ms-1"></i>
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let cartItems = {};

    function updateQty(itemId, price, change) {
        if (!cartItems[itemId]) {
            cartItems[itemId] = { id: itemId, price: price, quantity: 0 };
        }

        cartItems[itemId].quantity += change;

        if (cartItems[itemId].quantity <= 0) {
            delete cartItems[itemId];
            document.getElementById(`qty-${itemId}`).value = 0;
        } else {
            document.getElementById(`qty-${itemId}`).value = cartItems[itemId].quantity;
        }

        renderFloatingBar();
    }

    function renderFloatingBar() {
        let totalCount = 0;
        let totalAmount = 0;

        Object.values(cartItems).forEach(item => {
            totalCount += item.quantity;
            totalAmount += (item.price * item.quantity);
        });

        const bar = document.getElementById('floatingOrderBar');
        if (totalCount > 0) {
            document.getElementById('selectedItemsCount').innerText = `${totalCount} Items Selected`;
            document.getElementById('selectedTotalAmount').innerText = `₹${totalAmount.toFixed(2)}`;
            bar.classList.remove('d-none');
        } else {
            bar.classList.add('d-none');
        }
    }

    function submitLiveOrder() {
        let itemsArray = Object.values(cartItems);

        if (itemsArray.length === 0) return;

        fetch("{{ route('hub.restaurant.order.place') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                restaurant_id: "{{ $restaurant->id }}", 
                items: itemsArray 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                Object.keys(cartItems).forEach(id => {
                    document.getElementById(`qty-${id}`).value = 0;
                });
                cartItems = {};
                renderFloatingBar();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error placing order:", error);
            alert("Something went wrong!");
        });
    }
</script>
@endpush