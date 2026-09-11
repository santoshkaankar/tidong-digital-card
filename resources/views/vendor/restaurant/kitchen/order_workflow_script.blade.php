<script>
    var currentDoneOrderId = null;

    // Helper: CSRF Token Fetcher
    function getCsrfToken() {
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta) return tokenMeta.getAttribute('content');
        var inputToken = document.querySelector('input[name="_token"]');
        if (inputToken) return inputToken.value;
        return '';
    }

    // Helper: Refresh KDS Orders UI Across Systems
    function refreshKDSOrders() {
        if (typeof fetchLiveOrders === 'function') {
            fetchLiveOrders();
        } else if (typeof loadKitchenOrders === 'function') {
            loadKitchenOrders();
        } else if (typeof fetchRunningOrders === 'function') {
            fetchRunningOrders();
        } else {
            location.reload();
        }
    }

    // Status Update Request Handler (Pending -> Cooking -> Served)
    function updateOrderStatus(orderId, newStatus) {
        if (!orderId || !newStatus) return;

        fetch('/vendor/restaurant/kitchen-orders/' + orderId + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: newStatus,
                _token: getCsrfToken()
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                refreshKDSOrders();
            } else {
                alert(data.message || 'Status update nahi ho paya.');
            }
        })
        .catch(function(err) {
            console.error('Status Update Error:', err);
            alert('Server Error! Route check karein.');
        });
    }

    // Done Button Click Workflow
    function handleOrderDone(orderId, isOnlinePaid, orderNumber) {
        if (isOnlinePaid) {
            completeOrderProcess(orderId, 'paid');
        } else {
            currentDoneOrderId = orderId;
            var titleEl = document.getElementById('modalOrderTitle');
            if (titleEl) {
                titleEl.innerText = 'Order #' + orderNumber;
            }
            
            var modalElement = document.getElementById('paymentVerifyModal');
            if (modalElement) {
                var pModal = bootstrap.Modal.getOrCreateInstance(modalElement);
                pModal.show();
            }
        }
    }

    // Payment Modal Event Listeners Setup & Auto-sync Timer Start
    document.addEventListener("DOMContentLoaded", function () {
        var yesBtn = document.getElementById('btnPaymentYes');
        var noBtn = document.getElementById('btnPaymentNo');

        if (yesBtn) {
            yesBtn.onclick = function() {
                if(currentDoneOrderId) {
                    completeOrderProcess(currentDoneOrderId, 'paid');
                }
            };
        }

        if (noBtn) {
            noBtn.onclick = function() {
                if(currentDoneOrderId) {
                    completeOrderProcess(currentDoneOrderId, 'unpaid');
                }
            };
        }

        // Live Auto-Sync Every 5 Seconds (Bina Page Refresh Ke)
        setInterval(syncLiveCalls, 5000);
    });

    // Final Complete Order Handler (Shift Order & Free Table)
    function completeOrderProcess(orderId, paymentStatus) {
        var modalEl = document.getElementById('paymentVerifyModal');
        if (modalEl) {
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }

        fetch('/vendor/restaurant/kitchen-orders/' + orderId + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                status: 'completed',
                payment_status: paymentStatus,
                _token: getCsrfToken()
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                refreshKDSOrders();
            } else {
                alert(data.message || 'Order complete nahi ho saka.');
            }
        })
        .catch(function(err) {
            console.error('Order Done Error:', err);
            alert('Server error! Order complete nahi ho paya.');
        });
    }

    // -------------------------------------------------------------
    // LIVE CASH REQUESTS & WAITER CALLS POLLING (Bina Refresh Ke)
    // -------------------------------------------------------------
    function syncLiveCalls() {
        fetch('/vendor/restaurant/kitchen/live-orders', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) return;

            // 1. Render Cash Requests HTML
            if (data.cash_requests_html !== undefined) {
                var cashBox = document.getElementById('cash-requests-container');
                if (cashBox) cashBox.innerHTML = data.cash_requests_html;
            }

            // 2. Render Waiter Calls HTML
            if (data.waiter_calls_html !== undefined) {
                var waiterBox = document.getElementById('waiter-calls-container');
                if (waiterBox) waiterBox.innerHTML = data.waiter_calls_html;
            }

            // 3. Sound Notifier Trigger
            if (window.KDS_NOTIFIER) {
                var cashCount = data.cash_requests ? data.cash_requests.length : 0;
                var cashTable = (cashCount > 0 && data.cash_requests[0].table) 
                    ? (data.cash_requests[0].table.table_number || data.cash_requests[0].table_id) 
                    : '1';
                window.KDS_NOTIFIER.updateCashRequests(cashCount, cashTable);

                var waiterCount = data.waiter_calls ? data.waiter_calls.length : 0;
                var waiterTable = (waiterCount > 0 && data.waiter_calls[0].table) 
                    ? (data.waiter_calls[0].table.table_number || data.waiter_calls[0].table_id) 
                    : '1';
                window.KDS_NOTIFIER.updateWaiterCalls(waiterCount, waiterTable);
            }
        })
        .catch(function(err) {
            // Background Silent Error Handled
        });
    }
</script>