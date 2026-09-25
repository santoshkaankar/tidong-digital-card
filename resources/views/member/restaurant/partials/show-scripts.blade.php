<script>
    let cartItems = {};

    function updateQty(itemId, price, change, name = 'Food Item') {
        if (!cartItems[itemId]) {
            cartItems[itemId] = { id: itemId, price: price, quantity: 0, name: name };
        }
        cartItems[itemId].quantity += change;

        if (cartItems[itemId].quantity <= 0) {
            delete cartItems[itemId];
            if(document.getElementById(`qty-${itemId}`)) {
                document.getElementById(`qty-${itemId}`).value = 0;
            }
        } else {
            if(document.getElementById(`qty-${itemId}`)) {
                document.getElementById(`qty-${itemId}`).value = cartItems[itemId].quantity;
            }
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
        if (Object.keys(cartItems).length === 0) {
            alert("Kripya pehle items select karein!");
            return;
        }
        let modal = new bootstrap.Modal(document.getElementById('deliveryAddressModal'));
        modal.show();
    }

    function processFinalOrder() {
        let orderType = document.getElementById('orderTypeSelect').value;
        let address = document.getElementById('deliveryAddressInput').value;
        let pincode = document.getElementById('deliveryPincodeInput').value;

        if (orderType === 'delivery' && !address) {
            alert("Kripya delivery address enter karein!");
            return;
        }

        let itemsPayload = [];
        Object.values(cartItems).forEach(item => {
            let cleanId = item.id.replace('global_', '').replace('custom_', '');
            itemsPayload.push({
                id: parseInt(cleanId) || cleanId,
                quantity: item.quantity,
                price: item.price,
                name: item.name
            });
        });

        fetch("{{ route('hub.restaurant.placeOrder') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                restaurant_id: "{{ $restaurant->id }}",
                order_type: orderType,
                delivery_address: address,
                pincode: pincode,
                items: itemsPayload
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => alert("Order process karne me error aaya: " + err));
    }

    function toggleCustomDates(val) {
        document.getElementById('toDateContainer').style.display = (val === 'custom') ? 'block' : 'none';
    }

    function submitTiffinBooking() {
        let duration = document.getElementById('tiffinDuration').value;
        let fromDate = document.getElementById('tiffinFromDate').value;
        let toDate = document.getElementById('tiffinToDate').value;
        let catalogId = document.getElementById('tiffinCatalogId').value;

        let meals = [];
        document.querySelectorAll('input[name="meal_types[]"]:checked').forEach(cb => {
            meals.push(cb.value);
        });

        if (!catalogId) {
            alert("Kripya Tiffin Package select karein!");
            return;
        }

        fetch("{{ route('hub.restaurant.bookTiffin', $restaurant->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                duration: duration,
                from_date: fromDate,
                to_date: toDate,
                meal_types: meals,
                catalog_id: catalogId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        });
    }
</script>