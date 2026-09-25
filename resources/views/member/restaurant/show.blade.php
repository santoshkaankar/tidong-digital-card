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
    .category-btn.active {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 600;
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
                <div class="nav flex-column nav-pills gap-1" id="v-pills-tab" role="tablist">
                    <button class="nav-link category-btn active text-start rounded-3 py-2" id="cat-all-tab" data-bs-toggle="pill" data-bs-target="#cat-all" type="button" role="tab">
                        <i class="fas fa-utensils me-2"></i> All Items
                    </button>
                    @foreach($categories as $category)
                        <button class="nav-link category-btn text-start rounded-3 py-2" id="cat-{{ $category->id }}-tab" data-bs-toggle="pill" data-bs-target="#cat-{{ $category->id }}" type="button" role="tab">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu Items Tab Content -->
        <div class="col-lg-9">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                <h5 class="fw-bold text-dark mb-3">Menu Items</h5>

                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- TAB 1: ALL ITEMS -->
                    <div class="tab-pane fade show active" id="cat-all" role="tabpanel">
                        <div class="row g-3">
                            @forelse($items as $item)
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
                                    <p class="mb-0">No active food items added yet for this restaurant.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB 2: CATEGORY WISE FILTERED ITEMS -->
                    @foreach($categories as $category)
                        @php
                            $catItems = $items->where('category_id',$category->id);
                        @endphp
                        <div class="tab-pane fade" id="cat-{{ $category->id }}" role="tabpanel">
                            <div class="row g-3">
                                @forelse($catItems as $item)
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
                                            <div>
                                                <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 100px;">
                                                    <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty({{ $item->id }}, {{$item->price }}, -1)">-</button>
                                                    <input type="text" id="qty-cat-{{ $category->id }}-{{$item->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                                    <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty({{ $item->id }}, {{$item->price }}, 1)">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 py-5 text-center text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">No items available in {{ $category->name }}.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach

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

<!-- Order Confirmation Modal with Order Tracking -->
<div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 rounded-4 border-0 shadow">
            <div class="mb-3 text-success fs-1">
                <i class="fas fa-check-circle"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Order Sent to Kitchen!</h4>
            <p class="text-muted small mb-4">Your order has been transmitted directly to the vendor KDS display.</p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="{{ url('/member/orders') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-tasks me-1"></i> Track Order
                </a>
            </div>
        </div>
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
            syncQtyInputs(itemId, 0);
        } else {
            syncQtyInputs(itemId, cartItems[itemId].quantity);
        }

        renderFloatingBar();
    }

    function syncQtyInputs(itemId, val) {
        document.querySelectorAll(`[id^="qty-"][id$="-${itemId}"], #qty-${itemId}`).forEach(el => {
            el.value = val;
        });
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
                // Reset cart
                Object.keys(cartItems).forEach(id => syncQtyInputs(id, 0));
                cartItems = {};
                renderFloatingBar();

                // Open Order Success & Tracking Modal
                let modal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
                modal.show();
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