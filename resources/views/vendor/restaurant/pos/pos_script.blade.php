<script>
$(document).ready(function () {
    let cart = {};

    // CSRF Header setup
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

    // 3. Add to Cart
    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();

        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));

        if (!id) return;

        if (cart[id]) {
            cart[id].quantity += 1;
        } else {
            cart[id] = { id: id, name: name, price: price, quantity: 1 };
        }

        renderCart();
    });

    // Quantity Plus/Minus
    $(document).on('click', '.btn-qty', function () {
        const id = $(this).data('id');
        const action = $(this).data('action');

        if (!cart[id]) return;

        if (action === 'increase') {
            cart[id].quantity += 1;
        } else if (action === 'decrease') {
            cart[id].quantity -= 1;
            if (cart[id].quantity <= 0) {
                delete cart[id];
            }
        }
        renderCart();
    });

    // Remove Item
    $(document).on('click', '.remove-item', function () {
        const id = $(this).data('id');
        if (cart[id]) {
            delete cart[id];
            renderCart();
        }
    });

    // Render Cart HTML
    function renderCart() {
        const $cartBody = $('#cart-body');
        $cartBody.empty();

        const keys = Object.keys(cart);

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
            const item = cart[id];
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
        const cartItems = Object.values(cart);

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

                    cart = {};
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