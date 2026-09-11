@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .content-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .item-card {
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #fff;
    }

    .item-card:hover {
        border-color: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    }

    .filter-btn.active {
        background-color: #4f46e5 !important;
        color: #fff !important;
        border-color: #4f46e5 !important;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
            POINT OF SALE
        </span>
        <h2 class="fw-bold mt-2 mb-0">POS / Counter Billing</h2>
        <p class="text-muted small mb-0">Create quick bills, print KOT, and manage table orders.</p>
    </div>
    
    <a href="{{ route('vendor.restaurant.orders.index') }}" class="btn btn-outline-primary fw-semibold">
        <i class="bi bi-list-task me-1"></i> View All Orders
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Items Menu & Filters -->
    <div class="col-lg-7">
        <div class="content-card p-3 mb-4">
            <div class="d-flex flex-wrap gap-2" id="category-filters">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn active" data-category="all">All Items</button>
                @foreach($categories as $category)
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
            </div>
        </div>

        <div class="row g-3" id="items-grid">
            @forelse($items as $item)
                @php
                    $itemName = $item->globalItem->item_name 
                                ?? $item->globalItem->name 
                                ?? $item->name 
                                ?? 'Item #' . $item->id;
                @endphp
                <div class="col-md-4 col-sm-6 item-card-wrapper" data-category-id="{{ $item->restaurant_category_id }}">
                    <div class="item-card p-3 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-dark mb-2" style="min-height: 38px;">{{ $itemName }}</h6>
                            <p class="text-primary fw-bold fs-6 mb-3">₹{{ number_format($item->price, 2) }}</p>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold add-to-cart-btn" 
                                data-id="{{ $item->id }}" 
                                data-name="{{ addslashes($itemName) }}" 
                                data-price="{{ $item->price }}">
                            <i class="bi bi-plus-lg me-1"></i> Add
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox display-5 d-block mb-2 opacity-25"></i>
                    No food items available in the menu.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Cart & Billing Area -->
    <div class="col-lg-5">
        <div class="content-card p-4 sticky-top" style="top: 20px;">
            <h5 class="fw-bold mb-3"><i class="bi bi-cart3 text-primary me-2"></i> Current Order</h5>
            
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Order Type</label>
                    <select id="order_type" class="form-select rounded-3">
                        <option value="dine_in">Dine In</option>
                        <option value="takeaway">Takeaway</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
                <div class="col-md-6" id="table-wrapper">
                    <label class="form-label small fw-semibold text-muted">Table Number</label>
                    <select id="table_id" class="form-select rounded-3">
                        <option value="">Select Table</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}">{{ $table->table_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Customer Name</label>
                    <input type="text" id="customer_name" class="form-control rounded-3" placeholder="Optional">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Mobile Number</label>
                    <input type="tel" id="customer_phone" class="form-control rounded-3" placeholder="10 Digit Number">
                </div>
            </div>

            <div class="table-responsive my-3" style="min-height: 180px; max-height: 320px; overflow-y: auto;">
                <table class="table align-middle text-center mb-0" id="cart-table">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="text-start border-0">Item</th>
                            <th class="border-0">Price</th>
                            <th class="border-0" style="width: 80px;">Qty</th>
                            <th class="border-0">Total</th>
                            <th class="border-0"></th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr>
                            <td colspan="5" class="text-muted py-4">No items added to order</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-6 fw-semibold text-muted">Grand Total</span>
                    <span class="fs-4 fw-bold text-success">₹<span id="grand-total">0.00</span></span>
                </div>

                <button class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm" id="place-order-btn">
                    <i class="bi bi-printer me-2"></i> Place Order & Print KOT
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery CDN added directly to prevent $ undefined error -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    let posCart = {};

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 1. Category Filter
        $('#category-filters').on('click', '.filter-btn', function () {
            $('#category-filters .filter-btn').removeClass('active');
            $(this).addClass('active');

            const selectedCategory = $(this).data('category');

            if (selectedCategory === 'all') {
                $('.item-card-wrapper').removeClass('d-none');
            } else {
                $('.item-card-wrapper').each(function () {
                    const itemCategory = $(this).data('category-id');
                    if (itemCategory == selectedCategory) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                });
            }
        });

        // 2. Hide / Show Table Option
        $('#order_type').on('change', function () {
            if ($(this).val() === 'dine_in') {
                $('#table-wrapper').slideDown(200);
            } else {
                $('#table-wrapper').slideUp(200);
                $('#table_id').val('');
            }
        });

        // 3. Add to Cart Click
        $(document).on('click', '.add-to-cart-btn', function (e) {
            e.preventDefault();

            const id = $(this).data('id');
            const name = $(this).data('name');
            const price = parseFloat($(this).data('price'));

            if (!id) return;

            if (posCart[id]) {
                posCart[id].quantity += 1;
            } else {
                posCart[id] = { id: id, name: name, price: price, quantity: 1 };
            }

            renderCart();
        });

        // Quantity Plus/Minus
        $(document).on('click', '.btn-qty', function () {
            const id = $(this).data('id');
            const action = $(this).data('action');

            if (!posCart[id]) return;

            if (action === 'increase') {
                posCart[id].quantity += 1;
            } else if (action === 'decrease') {
                posCart[id].quantity -= 1;
                if (posCart[id].quantity <= 0) {
                    delete posCart[id];
                }
            }
            renderCart();
        });

        // Remove Item
        $(document).on('click', '.remove-item', function () {
            const id = $(this).data('id');
            if (posCart[id]) {
                delete posCart[id];
                renderCart();
            }
        });

        // Render Cart HTML
        function renderCart() {
            const $cartBody = $('#cart-body');
            $cartBody.empty();

            const keys = Object.keys(posCart);

            if (keys.length === 0) {
                $cartBody.html(`
                    <tr>
                        <td colspan="5" class="text-muted py-4">No items added to order</td>
                    </tr>
                `);
                $('#grand-total').text('0.00');
                return;
            }

            let grandTotal = 0;

            keys.forEach(id => {
                const item = posCart[id];
                const itemTotal = item.price * item.quantity;
                grandTotal += itemTotal;

                const row = `
                    <tr>
                        <td class="text-start fw-semibold small text-truncate" style="max-width: 120px;">${item.name}</td>
                        <td class="small">₹${item.price.toFixed(2)}</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center border rounded-2 p-1">
                                <button type="button" class="btn btn-sm btn-link text-dark p-0 me-1 btn-qty" data-id="${item.id}" data-action="decrease">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <span class="fw-bold small px-1">${item.quantity}</span>
                                <button type="button" class="btn btn-sm btn-link text-dark p-0 ms-1 btn-qty" data-id="${item.id}" data-action="increase">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td class="fw-bold small">₹${itemTotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-sm text-danger p-0 remove-item" data-id="${item.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $cartBody.append(row);
            });

            $('#grand-total').text(grandTotal.toFixed(2));
        }

        // 4. Place Order AJAX Submit
        $('#place-order-btn').on('click', function () {
            const orderType = $('#order_type').val();
            const tableId = $('#table_id').val();
            const customerName = $('#customer_name').val();
            const customerPhone = $('#customer_phone').val();
            const cartItems = Object.values(posCart);

            if (cartItems.length === 0) {
                alert('Please add at least one item to the cart.');
                return;
            }

            if (orderType === 'dine_in' && !tableId) {
                alert('Please select a table for Dine In orders.');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');

            const payload = {
                order_type: orderType,
                table_id: orderType === 'dine_in' ? tableId : null,
                customer_name: customerName,
                customer_phone: customerPhone,
                cart: cartItems
            };

            $.ajax({
                url: "{{ route('vendor.restaurant.pos.store') }}",
                type: "POST",
                data: JSON.stringify(payload),
                contentType: "application/json",
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        alert(response.message || 'Order placed successfully!');

                        if (response.whatsapp_url) {
                            window.open(response.whatsapp_url, '_blank');
                        }

                        posCart = {};
                        renderCart();
                        $('#customer_name').val('');
                        $('#customer_phone').val('');
                        $('#table_id').val('');
                    } else {
                        alert(response.message || 'Error occurred while saving order.');
                    }
                },
                error: function (xhr) {
                    let msg = 'Failed to place order.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="bi bi-printer me-2"></i> Place Order & Print KOT');
                }
            });
        });
    });
</script>
@endpush