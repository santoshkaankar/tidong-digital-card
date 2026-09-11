<script>
document.addEventListener('DOMContentLoaded', function () {
    let cart = [];

    // Category Filter
    const filterBtns = document.querySelectorAll('.filter-btn');
    const itemCards = document.querySelectorAll('.item-card-wrapper');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const categoryId = this.getAttribute('data-category');

            itemCards.forEach(card => {
                if (categoryId === 'all' || card.getAttribute('data-category-id') === categoryId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Add To Cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));

            const existingIndex = cart.findIndex(item => item.id == id);
            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({ id: id, name: name, price: price, quantity: 1 });
            }

            renderCart();
        });
    });

    // Render Cart Function
    function renderCart() {
        const cartBody = document.getElementById('cart-body');
        const grandTotalElem = document.getElementById('grand-total');

        if (cart.length === 0) {
            cartBody.innerHTML = `<tr><td colspan="5" class="text-muted py-4">No items added to order</td></tr>`;
            grandTotalElem.innerText = '0.00';
            return;
        }

        let html = '';
        let total = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            html += `
                <tr>
                    <td class="text-start text-dark fw-semibold">${item.name}</td>
                    <td>₹${item.price.toFixed(2)}</td>
                    <td>
                        <input type="number" min="1" class="form-control form-control-sm text-center update-qty" data-index="${index}" value="${item.quantity}">
                    </td>
                    <td class="fw-bold">₹${itemTotal.toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm text-danger remove-item p-0" data-index="${index}"><i class="bi bi-x-circle-fill"></i></button>
                    </td>
                </tr>
            `;
        });

        cartBody.innerHTML = html;
        grandTotalElem.innerText = total.toFixed(2);

        // Bind Quantity Changes & Delete
        document.querySelectorAll('.update-qty').forEach(input => {
            input.addEventListener('change', function () {
                const index = this.getAttribute('data-index');
                const val = parseInt(this.value);
                if (val > 0) {
                    cart[index].quantity = val;
                } else {
                    cart.splice(index, 1);
                }
                renderCart();
            });
        });

        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', function () {
                const index = this.getAttribute('data-index');
                cart.splice(index, 1);
                renderCart();
            });
        });
    }

    // Place Order Handler with Auto-Print & Redirect
    document.getElementById('place-order-btn').addEventListener('click', function () {
        if (cart.length === 0) {
            alert('Please add items to cart before placing an order.');
            return;
        }

        const orderType = document.getElementById('order_type').value;
        const tableId = document.getElementById('table_id').value;

        if (orderType === 'dine_in' && !tableId) {
            alert('Please select a table for Dine In order.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Processing...`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch("{{ route('vendor.pos.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                order_type: orderType,
                table_id: orderType === 'dine_in' ? tableId : null,
                cart: cart
            })
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Server error occurred while placing order.');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                cart = [];
                renderCart();

                // 1. Open Print Receipt Popup
                const printUrl = "{{ url('vendor/restaurant/orders') }}/" + data.order_id + "/print";
                window.open(printUrl, '_blank', 'width=400,height=600');

                // 2. Redirect Current Page to Orders List
                window.location.href = "{{ route('vendor.restaurant.orders.index') }}";
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = `<i class="bi bi-printer me-2"></i> Place Order & Print KOT`;
            }
        })
        .catch(error => {
            alert(error.message);
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-printer me-2"></i> Place Order & Print KOT`;
        });
    });
});
</script>