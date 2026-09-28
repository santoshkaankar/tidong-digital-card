<script>
    let cartItems = {};
    const DEFAULT_DELIVERY_CHARGE = 30.00; // Standard Delivery Charge

    // 1. Cart Quantity Update
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

    // 2. Floating Bar Render
    function renderFloatingBar() {
        let totalCount = 0;
        let totalAmount = 0;
        Object.values(cartItems).forEach(item => {
            totalCount += item.quantity;
            totalAmount += (item.price * item.quantity);
        });

        const bar = document.getElementById('floatingOrderBar');
        if (bar) {
            if (totalCount > 0) {
                if(document.getElementById('selectedItemsCount')) {
                    document.getElementById('selectedItemsCount').innerText = `${totalCount} Items Selected`;
                }
                if(document.getElementById('selectedTotalAmount')) {
                    document.getElementById('selectedTotalAmount').innerText = `₹${totalAmount.toFixed(2)}`;
                }
                bar.classList.remove('d-none');
            } else {
                bar.classList.add('d-none');
            }
        }
    }

    // 3. Saved Address Dropdown Change Handler
    function handleSavedAddressChange(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const streetInput = document.getElementById('deliveryStreetInput');
        const pincodeInput = document.getElementById('deliveryPincodeInput');
        const searchInput = document.getElementById('addressSearchInput');

        if (selectElement.value === 'new') {
            if (streetInput) streetInput.value = '';
            if (pincodeInput) pincodeInput.value = '';
            if (searchInput) searchInput.value = '';
            document.getElementById('deliveryAreaInput').value = '';
            document.getElementById('deliveryCityInput').value = '';
            document.getElementById('deliveryStateInput').value = '';
            if (searchInput) searchInput.focus();
        } else {
            const addressVal = selectedOption.getAttribute('data-address') || '';
            const pincodeVal = selectedOption.getAttribute('data-pincode') || '';

            if (streetInput) streetInput.value = addressVal;
            if (pincodeInput) pincodeInput.value = pincodeVal;
        }
    }

    // 4. Toggle Delivery / Dine-in / Takeaway
    function toggleOrderTypeView() {
        const orderTypeSelect = document.getElementById('orderTypeSelect');
        if (!orderTypeSelect) return;

        const orderType = orderTypeSelect.value;
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

    // 5. Calculate Bill Details in Modal
    function calculateModalBill() {
        let subtotal = 0;
        Object.values(cartItems).forEach(item => {
            subtotal += (item.price * item.quantity);
        });

        const orderTypeSelect = document.getElementById('orderTypeSelect');
        const orderType = orderTypeSelect ? orderTypeSelect.value : 'delivery';

        let deliveryCharge = (orderType === 'delivery') ? DEFAULT_DELIVERY_CHARGE : 0.00;
        let grandTotal = subtotal + deliveryCharge;

        if (document.getElementById('modalSubtotal')) {
            document.getElementById('modalSubtotal').innerText = `₹${subtotal.toFixed(2)}`;
        }
        if (document.getElementById('modalDeliveryCharge')) {
            document.getElementById('modalDeliveryCharge').innerText = deliveryCharge > 0 ? `+ ₹${deliveryCharge.toFixed(2)}` : 'FREE';
        }
        if (document.getElementById('modalTotalPayable')) {
            document.getElementById('modalTotalPayable').innerText = `₹${grandTotal.toFixed(2)}`;
        }
        if (document.getElementById('btnTotalText')) {
            document.getElementById('btnTotalText').innerText = `₹${grandTotal.toFixed(2)}`;
        }
    }

    // 6. Open Order Modal
    function submitLiveOrder() {
        if (Object.keys(cartItems).length === 0) {
            alert("Kripya pehle items select karein!");
            return;
        }

        calculateModalBill();
        toggleOrderTypeView();

        let modalElem = document.getElementById('deliveryAddressModal');
        if (modalElem) {
            let modal = new bootstrap.Modal(modalElem);
            modal.show();
        }
    }

    // 7. Live Address Search & Auto-fill Logic
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('addressSearchInput');
        const searchResults = document.getElementById('pincodeSearchResults');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                let query = this.value.trim();

                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    searchResults.innerHTML = '';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('member.pincodes.search') }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            if (!data || data.length === 0) {
                                searchResults.innerHTML = '<div class="list-group-item small text-muted p-2">No matching area or pincode found</div>';
                            } else {
                                data.forEach(item => {
                                    let area = item.office_name || '';
                                    let district = item.district || '';
                                    let state = item.state || 'Odisha';
                                    let pincode = item.pincode || '';

                                    let label = `${area}${district ? ', ' + district : ''} (${pincode})`;
                                    
                                    let btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'list-group-item list-group-item-action small py-2 text-start';
                                    btn.innerHTML = `<i class="fas fa-map-marker-alt text-danger me-2"></i><b>${area}</b> - ${district} <span class="badge bg-secondary ms-1">${pincode}</span>`;
                                    
                                    btn.onclick = function () {
                                        document.getElementById('deliveryAreaInput').value = area;
                                        document.getElementById('deliveryCityInput').value = district;
                                        document.getElementById('deliveryStateInput').value = state;
                                        document.getElementById('deliveryPincodeInput').value = pincode;
                                        
                                        searchInput.value = label;
                                        searchResults.style.display = 'none';

                                        let savedDropdown = document.getElementById('savedAddressDropdown');
                                        if (savedDropdown) savedDropdown.value = 'new';

                                        let streetInput = document.getElementById('deliveryStreetInput');
                                        if (streetInput) streetInput.focus();
                                    };
                                    searchResults.appendChild(btn);
                                });
                            }
                            searchResults.style.display = 'block';
                        })
                        .catch(err => console.error("Search Error:", err));
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }
    });

    // 8. Process Final Order Submission
    function processFinalOrder() {
        let orderTypeSelect = document.getElementById('orderTypeSelect');
        let orderType = orderTypeSelect ? orderTypeSelect.value : 'delivery';
        let finalFullAddress = "";
        let finalPincode = "";

        if (orderType === 'delivery') {
            let street = document.getElementById('deliveryStreetInput').value.trim();
            let area = document.getElementById('deliveryAreaInput').value.trim();
            let city = document.getElementById('deliveryCityInput').value.trim();
            let state = document.getElementById('deliveryStateInput').value.trim();
            finalPincode = document.getElementById('deliveryPincodeInput').value.trim();

            if (!finalPincode || !area) {
                alert("Kripya search box se Area / City / Pincode select karein!");
                return;
            }
            if (!street) {
                alert("Kripya House No. / Flat / Street Address enter karein!");
                return;
            }

            finalFullAddress = `${street}, ${area}, ${city}, ${state} - ${finalPincode}`;
        }

        let itemsPayload = [];
        let subtotal = 0;
        Object.values(cartItems).forEach(item => {
            let cleanId = item.id.toString().replace('global_', '').replace('custom_', '');
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
                delivery_address: finalFullAddress,
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

    // 9. Tiffin Custom Dates Toggle
    function toggleCustomDates(val) {
        let container = document.getElementById('toDateContainer');
        if (container) {
            container.style.display = (val === 'custom') ? 'block' : 'none';
        }
    }

    // 10. Submit Tiffin Booking
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
        })
        .catch(err => alert("Tiffin booking me error aaya: " + err));
    }
</script>