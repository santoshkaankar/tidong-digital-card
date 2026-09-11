<script>
    let cart = {};
    const token = "{{ $table->qr_code_token }}";
    let currentOrderId = "{{ $activeOrder->id ?? '' }}";

    const orderUrl = "{{ route('customer.restaurant.order.place', ['token' => $table->qr_code_token]) }}";
    const callWaiterUrl = "{{ route('customer.restaurant.call_waiter', ['token' => $table->qr_code_token]) }}";
    const requestPaymentUrl = "{{ route('customer.restaurant.payment.request', ['token' => $table->qr_code_token]) }}";

    if (currentOrderId) {
        setInterval(pollOrderStatus, 5000);
    }

    function pollOrderStatus() {
        if (!currentOrderId) return;

        fetch(`/menu/table/${token}/status/${currentOrderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const statusTxtEl = document.getElementById('activeStatusText');
                    if (statusTxtEl) {
                        statusTxtEl.innerText = data.status_text;
                    }

                    const totalEl = document.getElementById('activeTotal');
                    if (totalEl) {
                        totalEl.innerText = Number(data.total_amount).toFixed(2);
                    }

                    const modalTotalEl = document.getElementById('modalTotalAmount');
                    if (modalTotalEl) {
                        modalTotalEl.innerText = Number(data.total_amount).toFixed(2);
                    }

                    if (data.items && data.items.length > 0) {
                        const modalList = document.getElementById('modalItemsList');
                        if (modalList) {
                            modalList.innerHTML = data.items.map(item => `
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">${item.item_name}</h6>
                                        <small class="text-muted">₹${Number(item.price).toFixed(2)} x ${item.quantity}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-dark d-block">₹${Number(item.subtotal).toFixed(2)}</span>
                                        <span class="badge bg-secondary" style="font-size: 10px;">${item.kitchen_status}</span>
                                    </div>
                                </div>
                            `).join('');
                        }
                    }

                    if (data.payment_status === 'paid' || data.status === 'completed' || data.status === 'cancelled') {
                        location.reload();
                    }
                }
            })
            .catch(err => console.error('Polling error:', err));
    }

    function filterCategory(catClass, element) {
        document.querySelectorAll('.category-badge').forEach(b => b.classList.remove('active'));
        element.classList.add('active');

        if(catClass === 'all') {
            document.querySelectorAll('.category-section').forEach(s => s.style.display = 'block');
        } else {
            document.querySelectorAll('.category-section').forEach(s => s.style.display = 'none');
            document.querySelectorAll('.' + catClass).forEach(s => s.style.display = 'block');
        }
    }

    function updateCart(id, name, price, change) {
        if(!cart[id]) {
            cart[id] = { id: id, name: name, price: price, quantity: 0 };
        }

        cart[id].quantity += change;

        if(cart[id].quantity <= 0) {
            delete cart[id];
            document.getElementById(`qty-${id}`).innerText = '0';
        } else {
            document.getElementById(`qty-${id}`).innerText = cart[id].quantity;
        }

        renderCartBar();
    }

    function renderCartBar() {
        let totalItems = 0;
        let totalPrice = 0;

        Object.values(cart).forEach(item => {
            totalItems += item.quantity;
            totalPrice += (item.quantity * item.price);
        });

        const cartBar = document.getElementById('cartBar');
        if(totalItems > 0) {
            cartBar.classList.remove('d-none');
            document.getElementById('cartCount').innerText = totalItems;
            document.getElementById('cartTotal').innerText = totalPrice.toFixed(2);
        } else {
            cartBar.classList.add('d-none');
        }
    }

    function viewOrderDetails() {
        var detailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        detailsModal.show();
    }

    function placeOrder() {
        const items = Object.values(cart).map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        if(items.length === 0) return;

        fetch(orderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ items: items })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || 'Server Error');
            }
            return data;
        })
        .then(data => {
            if(data.success) {
                alert('Your order has been sent to the kitchen!');
                cart = {};
                document.querySelectorAll('[id^="qty-"]').forEach(el => el.innerText = '0');
                renderCartBar();
                location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }

    function callWaiter() {
        fetch(callWaiterUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => alert(data.message))
        .catch(err => alert('Alert sent to waiter.'));
    }

    function openPaymentModal(orderId) {
        currentOrderId = orderId;
        var paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }

    function submitPayment(method) {
        fetch(requestPaymentUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                order_id: currentOrderId,
                payment_method: method
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            var paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if(paymentModal) paymentModal.hide();
        });
    }
</script>