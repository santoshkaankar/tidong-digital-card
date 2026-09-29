<style>
/* Modal Overflow Fix & Mobile Touch Layering */
#pincodeSearchResults {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 999999 !important;
    background: #ffffff !important;
    max-height: 240px !important;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
    border: 2px solid #2563eb !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3) !important;
    margin-top: 4px !important;
}

.pincode-result-item {
    padding: 12px 14px !important;
    font-size: 14px !important;
    cursor: pointer !important;
    border-bottom: 1px solid #e2e8f0 !important;
    color: #0f172a !important;
    display: block !important;
    width: 100% !important;
    text-align: left !important;
    background: #ffffff !important;
    -webkit-tap-highlight-color: rgba(37, 99, 235, 0.2) !important;
}

.pincode-result-item:last-child {
    border-bottom: none !important;
}

.pincode-result-item:active,
.pincode-result-item:hover {
    background-color: #eff6ff !important;
    color: #2563eb !important;
}
</style>

<script>
    let cartItems = {};
    const DEFAULT_DELIVERY_CHARGE = 30.00;

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
        const searchGroup = document.getElementById('addressSearchGroup');

        if (selectElement.value === 'new') {
            if (streetInput) streetInput.value = '';
            if (pincodeInput) pincodeInput.value = '';
            if (searchInput) searchInput.value = '';
            if (document.getElementById('deliveryAreaInput')) document.getElementById('deliveryAreaInput').value = '';
            if (document.getElementById('deliveryCityInput')) document.getElementById('deliveryCityInput').value = '';
            if (document.getElementById('deliveryStateInput')) document.getElementById('deliveryStateInput').value = '';
            
            if (searchGroup) searchGroup.classList.remove('d-none');
            if (searchInput) searchInput.focus();
        } else {
            const addressVal = selectedOption.getAttribute('data-address') || '';
            const pincodeVal = selectedOption.getAttribute('data-pincode') || '';

            if (streetInput) streetInput.value = addressVal;
            if (pincodeInput) pincodeInput.value = pincodeVal;
            
            if (searchGroup) searchGroup.classList.add('d-none');
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

    // 7. Live AJAX Pincode & Area Search Handler (Mobile & Touch Optimized)
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById('addressSearchInput');
        const searchResults = document.getElementById('pincodeSearchResults');
        let debounceTimer;

        if (searchInput && searchResults) {
            ['input', 'keyup', 'paste'].forEach(eventType => {
                searchInput.addEventListener(eventType, function () {
                    clearTimeout(debounceTimer);
                    let query = this.value.trim();

                    if (query.length < 2) {
                        searchResults.style.display = 'none';
                        searchResults.innerHTML = '';
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        let searchUrl = "{{ route('member.pincodes.search') }}?q=" + encodeURIComponent(query);
                        
                        fetch(searchUrl)
                            .then(res => res.json())
                            .then(data => {
                                searchResults.innerHTML = '';
                                if (!data || data.length === 0) {
                                    searchResults.innerHTML = '<div class="pincode-result-item text-muted">No area or pincode found</div>';
                                } else {
                                    data.forEach(item => {
                                        let area = item.office_name || '';
                                        let district = item.district || '';
                                        let state = item.state || '';
                                        let pincode = item.pincode || '';

                                        let btn = document.createElement('div');
                                        btn.className = 'pincode-result-item';
                                        btn.innerHTML = `<i class="fas fa-map-marker-alt text-danger me-2"></i><b>${area}</b> - ${district} ${state ? '(' + state + ')' : ''} <span class="badge bg-secondary ms-1">${pincode}</span>`;

                                        const selectAddressItem = function (e) {
                                            if (e) e.preventDefault();
                                            
                                            if (document.getElementById('deliveryAreaInput')) document.getElementById('deliveryAreaInput').value = area;
                                            if (document.getElementById('deliveryCityInput')) document.getElementById('deliveryCityInput').value = district;
                                            if (document.getElementById('deliveryStateInput')) document.getElementById('deliveryStateInput').value = state;
                                            if (document.getElementById('deliveryPincodeInput')) document.getElementById('deliveryPincodeInput').value = pincode;

                                            searchInput.value = `${area}, ${district} (${pincode})`;
                                            searchResults.style.display = 'none';

                                            let streetInput = document.getElementById('deliveryStreetInput');
                                            if (streetInput) streetInput.focus();
                                        };

                                        btn.addEventListener('touchstart', selectAddressItem, { passive: false });
                                        btn.addEventListener('click', selectAddressItem);
                                        
                                        searchResults.appendChild(btn);
                                    });
                                }
                                searchResults.style.display = 'block';
                            })
                            .catch(err => {
                                console.error("Live Search Fetch Error:", err);
                            });
                    }, 250);
                });
            });

            ['click', 'touchstart'].forEach(eventType => {
                document.addEventListener(eventType, function (e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.style.display = 'none';
                    }
                });
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
            let streetInput = document.getElementById('deliveryStreetInput');
            let areaInput = document.getElementById('deliveryAreaInput');
            let cityInput = document.getElementById('deliveryCityInput');
            let stateInput = document.getElementById('deliveryStateInput');
            let pincodeInput = document.getElementById('deliveryPincodeInput');

            let street = streetInput ? streetInput.value.trim() : '';
            let area = areaInput ? areaInput.value.trim() : '';
            let city = cityInput ? cityInput.value.trim() : '';
            let state = stateInput ? stateInput.value.trim() : '';
            finalPincode = pincodeInput ? pincodeInput.value.trim() : '';

            if (!finalPincode) {
                alert("Kripya search box se Area / City / Pincode select karein!");
                return;
            }
            if (!street) {
                alert("Kripya House No. / Flat / Street Address enter karein!");
                return;
            }

            finalFullAddress = `${street}${area ? ', ' + area : ''}${city ? ', ' + city : ''}${state ? ', ' + state : ''} - ${finalPincode}`;
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

        fetch("{{ route('member.restaurant.placeOrder') }}", {
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

        fetch("{{ route('member.restaurant.bookTiffin', $restaurant->id) }}", {
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