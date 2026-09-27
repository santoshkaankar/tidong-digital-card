<script>
    let cartItems = {};
    const DEFAULT_DELIVERY_CHARGE = 30.00; // Standard Delivery Charge

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

    function toggleAddressInput(isNew) {
        const newBox = document.getElementById('newAddressBox');
        if (newBox) {
            newBox.style.display = isNew ? 'block' : 'none';
        }
    }

    function toggleOrderTypeView() {
        const orderType = document.getElementById('orderTypeSelect').value;
        const addressGroup = document.getElementById('addressGroup');
        const deliveryFeeRow = document.getElementById('deliveryFeeRow');

        if (orderType === 'delivery') {
            if (addressGroup) addressGroup.style.display = 'block';
            if (deliveryFeeRow) deliveryFeeRow.style.display = 'flex';
        } else {
            if (addressGroup) addressGroup.style.display = 'none';
            if (deliveryFeeRow) deliveryFeeRow.style.display = 'none';
        }
        calculateModalBill();
    }

    function calculateModalBill() {
        let subtotal = 0;
        Object.values(cartItems).forEach(item => {
            subtotal += (item.price * item.quantity);
        });

        const orderType = document.getElementById('orderTypeSelect').value;
        let deliveryCharge = (orderType === 'delivery') ? DEFAULT_DELIVERY_CHARGE : 0.00;
        let grandTotal = subtotal + deliveryCharge;

        document.getElementById('modalSubtotal').innerText = `₹${subtotal.toFixed(2)}`;
        document.getElementById('modalDeliveryCharge').innerText = deliveryCharge > 0 ? `+ ₹${deliveryCharge.toFixed(2)}` : 'FREE';
        document.getElementById('modalTotalPayable').innerText = `₹${grandTotal.toFixed(2)}`;
        document.getElementById('btnTotalText').innerText = `₹${grandTotal.toFixed(2)}`;
    }

    function submitLiveOrder() {
        if (Object.keys(cartItems).length === 0) {
            alert("Kripya pehle items select karein!");
            return;
        }

        calculateModalBill();
        toggleOrderTypeView();

        let modal = new bootstrap.Modal(document.getElementById('deliveryAddressModal'));
        modal.show();
    }

    function processFinalOrder() {
        let orderType = document.getElementById('orderTypeSelect').value;
        let finalAddress = "";
        let finalPincode = "";

        if (orderType === 'delivery') {
            let selectedRadio = document.querySelector('input[name="selected_address"]:checked');
            if (!selectedRadio) {
                alert("Kripya delivery address select karein!");
                return;
            }

            if (selectedRadio.value === 'new') {
                finalAddress = document.getElementById('deliveryAddressInput').value.trim();
                finalPincode = document.getElementById('deliveryPincodeInput').value.trim();
                if (!finalAddress) {
                    alert("Kripya naya delivery address enter karein!");
                    return;
                }
            } else {
                finalAddress = selectedRadio.value;
                finalPincode = selectedRadio.getAttribute('data-pincode') || "";
            }
        }

        let itemsPayload = [];
        let subtotal = 0;
        Object.values(cartItems).forEach(item => {
            let cleanId = item.id.replace('global_', '').replace('custom_', '');
            subtotal += (item.price * item.quantity);
            itemsPayload.push({
                id: parseInt(cleanId) || cleanId,
                quantity: item.quantity,
                price: item.price,
                name: item.name
            });
        });

        let deliveryCharge = (orderType === 'delivery') ? DEFAULT_DELIVERY_CHARGE : 0.00;
        let totalAmount = subtotal + deliveryCharge;

        fetch("{{ route('hub.restaurant.placeOrder') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                restaurant_id: "{{ $restaurant->id }}",
                order_type: orderType,
                delivery_address: finalAddress,
                pincode: finalPincode,
                delivery_charge: deliveryCharge,
                total_amount: totalAmount,
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