<style>
/* Live Pincode Search Dropdown Styling */
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
}

.pincode-result-item:hover, .pincode-result-item:active {
    background-color: #eff6ff !important;
    color: #2563eb !important;
}
</style>

<script>
    let cartItems = {};
    let selectedTipAmount = 0.00;
    const FREE_DELIVERY_THRESHOLD = 999.00;
    const DEFAULT_DELIVERY_CHARGE = 30.00;

    // 1. Tip Selection Handlers
    function selectTip(amount, element) {
        selectedTipAmount = parseFloat(amount) || 0;
        const customInput = document.getElementById('customTipInput');
        if (customInput) customInput.value = selectedTipAmount > 0 ? selectedTipAmount : '';

        document.querySelectorAll('.tip-btn').forEach(btn => {
            btn.classList.remove('btn-primary', 'active');
            btn.classList.add('btn-outline-primary');
        });

        if (element) {
            element.classList.remove('btn-outline-primary', 'btn-outline-secondary');
            element.classList.add('btn-primary', 'active');
        }

        calculateModalBill();
    }

    function applyCustomTip(value) {
        selectedTipAmount = parseFloat(value) || 0;
        document.querySelectorAll('.tip-btn').forEach(btn => {
            btn.classList.remove('btn-primary', 'active');
            btn.classList.add('btn-outline-primary');
        });
        calculateModalBill();
    }

    // 2. Cart Quantity Update (Includes GST % parameter)
    function updateQty(itemId, price, change, name = 'Food Item', taxPercent = 5.00) {
        let taxVal = parseFloat(taxPercent);
        if (isNaN(taxVal) || taxVal <= 0) taxVal = 5.00;

        if (!cartItems[itemId]) {
            cartItems[itemId] = { 
                id: itemId, 
                price: parseFloat(price), 
                quantity: 0, 
                name: name,
                taxPercent: taxVal
            };
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

    // 3. Floating Bottom Cart Bar
    function renderFloatingBar() {
        let totalCount = 0;
        let totalAmount = 0;
        Object.values(cartItems).forEach(item => {
            totalCount += item.quantity;
            let itemSubtotal = (item.price * item.quantity);
            let taxRate = (item.taxPercent && item.taxPercent > 0) ? item.taxPercent : 5.00;
            let itemTax = (itemSubtotal * taxRate) / 100;
            totalAmount += (itemSubtotal + itemTax);
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

    // 4. Saved Address Selection Handler
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

    // 5. Toggle Delivery / Dine-in / Takeaway
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

    // 6. Complete Bill Calculation (Subtotal + CGST + SGST + Delivery + Tip)
    function calculateModalBill() {
        let subtotal = 0;
        let totalTax = 0;

        Object.values(cartItems).forEach(item => {
            let itemSub = (item.price * item.quantity);
            let taxRate = (item.taxPercent && item.taxPercent > 0) ? item.taxPercent : 5.00;
            let itemTax = (itemSub * taxRate) / 100;
            subtotal += itemSub;
            totalTax += itemTax;
        });

        let cgst = totalTax / 2;
        let sgst = totalTax / 2;

        const orderTypeSelect = document.getElementById('orderTypeSelect');
        const orderType = orderTypeSelect ? orderTypeSelect.value : 'delivery';

        let deliveryCharge = 0.00;
        if (orderType === 'delivery') {
            deliveryCharge = (subtotal >= FREE_DELIVERY_THRESHOLD) ? 0.00 : DEFAULT_DELIVERY_CHARGE;
        }

        let grandTotal = subtotal + totalTax + deliveryCharge + selectedTipAmount;

        if (document.getElementById('modalSubtotal')) {
            document.getElementById('modalSubtotal').innerText = `₹${subtotal.toFixed(2)}`;
        }
        if (document.getElementById('modalCGST')) {
            document.getElementById('modalCGST').innerText = `+ ₹${cgst.toFixed(2)}`;
        }
        if (document.getElementById('modalSGST')) {
            document.getElementById('modalSGST').innerText = `+ ₹${sgst.toFixed(2)}`;
        }
        if (document.getElementById('modalTipAmount')) {
            document.getElementById('modalTipAmount').innerText = `+ ₹${selectedTipAmount.toFixed(2)}`;
        }
        if (document.getElementById('modalDeliveryCharge')) {
            if (orderType !== 'delivery') {
                document.getElementById('modalDeliveryCharge').innerText = 'N/A';
            } else if (deliveryCharge === 0) {
                document.getElementById('modalDeliveryCharge').innerHTML = '<span class="badge bg-success">FREE</span>';
            } else {
                document.getElementById('modalDeliveryCharge').innerText = `+ ₹${deliveryCharge.toFixed(2)}`;
            }
        }
        if (document.getElementById('modalTotalPayable')) {
            document.getElementById('modalTotalPayable').innerText = `₹${grandTotal.toFixed(2)}`;
        }
        if (document.getElementById('btnTotalText')) {
            document.getElementById('btnTotalText').innerText = `₹${grandTotal.toFixed(2)}`;
        }
    }

    // 7. Trigger Live Order Modal (Fixed double modal background issue)
    function submitLiveOrder() {
        if (Object.keys(cartItems).length === 0) {
            alert("Kripya pehle items select karein!");
            return;
        }

        calculateModalBill();
        toggleOrderTypeView();

        let modalElem = document.getElementById('deliveryAddressModal');
        if (modalElem) {
            let modal = bootstrap.Modal.getInstance(modalElem) || new bootstrap.Modal(modalElem);
            modal.show();
        }
    }

    // 8. Live AJAX Pincode Search Event Listener
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

    // 9. Process Final Order Submission
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

        let selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';

        let itemsPayload = [];
        let subtotal = 0;
        let totalTax = 0;

        Object.values(cartItems).forEach(item => {
            let cleanId = item.id.toString().replace('global_', '').replace('custom_', '');
            let itemSub = (item.price * item.quantity);
            let taxRate = (item.taxPercent && item.taxPercent > 0) ? item.taxPercent : 5.00;
            let itemTax = (itemSub * taxRate) / 100;

            subtotal += itemSub;
            totalTax += itemTax;

            itemsPayload.push({
                id: parseInt(cleanId) || cleanId,
                quantity: item.quantity,
                price: item.price,
                name: item.name,
                tax_percent: taxRate
            });
        });

        let deliveryCharge = (orderType === 'delivery') 
            ? ((subtotal >= FREE_DELIVERY_THRESHOLD) ? 0.00 : DEFAULT_DELIVERY_CHARGE) 
            : 0.00;

        let totalAmount = subtotal + totalTax + deliveryCharge + selectedTipAmount;

        let confirmBtn = document.querySelector('#deliveryAddressModal button[onclick="processFinalOrder()"]');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        }

        fetch("{{ route('member.restaurant.placeOrder') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                restaurant_id: "{{ $restaurant->id }}",
                order_type: orderType,
                delivery_address: finalFullAddress,
                pincode: finalPincode,
                payment_method: selectedPaymentMethod,
                delivery_charge: deliveryCharge,
                tip_amount: selectedTipAmount,
                tax_amount: totalTax,
                total_amount: totalAmount,
                items: itemsPayload
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (selectedPaymentMethod === 'online' || data.is_online) {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else if (data.order_id) {
                        window.location.href = "/payment/checkout/" + data.order_id;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert(data.message || "Order placed successfully!");
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else if (data.order_id) {
                        window.location.href = "/member/orders/" + data.order_id;
                    } else {
                        window.location.reload();
                    }
                }
            } else {
                alert("Error: " + (data.message || "Order process nahi ho saka."));
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `Confirm & Place Order (<span id="btnTotalText">₹${totalAmount.toFixed(2)}</span>)`;
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert("Order process karne me error aaya.");
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `Confirm & Place Order (<span id="btnTotalText">₹${totalAmount.toFixed(2)}</span>)`;
            }
        });
    }

    // 10. Tiffin Custom Date Toggle
    function toggleCustomDates(val) {
        let container = document.getElementById('toDateContainer');
        if (container) {
            container.style.display = (val === 'custom') ? 'block' : 'none';
        }
    }

    // 11. Tiffin Booking Submission
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