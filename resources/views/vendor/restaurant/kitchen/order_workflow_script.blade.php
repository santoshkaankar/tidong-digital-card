<script>
    var currentDoneOrderId = null;
    
    // Tracking variables & Initial Load Flag
    var lastProcessedWaiterCount = 0;
    var lastProcessedCashCount = 0;
    var lastProcessedOrderCount = 0;
    var isInitialFetch = true; // Page load par sound bajne se rokne ke liye
    
    var lastWaiterCallId = null;
    var lastCashCallId = null;

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
        syncLiveCalls();
    }

    // Status Update Request Handler
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
                syncLiveCalls(); // Action ke baad instant update bina reload ke
            } else {
                alert(data.message || 'Status update nahi ho paya.');
            }
        })
        .catch(function(err) {
            console.error('Status Update Error:', err);
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

    // Payment Modal Event Listeners & Auto-sync Timer Start
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

        // --- AUTO SYNC TIMER START (Har 5 Second Me) ---
        setInterval(function() {
            syncLiveCalls();
        }, 5000);
    });

    // Final Complete Order Handler
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
                syncLiveCalls();
            } else {
                alert(data.message || 'Order complete nahi ho saka.');
            }
        })
        .catch(function(err) {
            console.error('Order Done Error:', err);
        });
    }

    // -------------------------------------------------------------
    // PURE JS LIVE SYNC & DYNAMIC HTML BUILDER
    // -------------------------------------------------------------
    function syncLiveCalls() {
        fetch('/vendor/restaurant/kitchen-screen/live-orders', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) { 
            return res.json(); 
        })
        .then(function(data) {
            if (!data.success) return;

            // 1. Render Cash Requests Dynamically via JS
            var cashBox = document.getElementById('cash-requests-container');
            if (cashBox && data.cash_requests) {
                if (data.cash_requests.length > 0) {
                    var cashHtml = '';
                    data.cash_requests.forEach(function(req) {
                        var tableName = (req.table && req.table.table_number) ? req.table.table_number : (req.table_id ? 'Table No ' + req.table_id : 'Table');
                        var timeStr = new Date(req.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        
                        cashHtml += `
                            <div id="cash-card-${req.id}" class="alert alert-success d-flex justify-content-between align-items-center mb-2 shadow-sm" style="border-left: 5px solid #198754;">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="bi bi-cash-stack"></i> Collect Cash Payment from ${tableName}</h6>
                                    <small class="text-muted"><i class="bi bi-clock"></i> Requested at ${timeStr}</small>
                                </div>
                                <button onclick="resolveCashRequest(${req.id})" class="btn btn-success btn-sm fw-bold px-3">
                                    <i class="bi bi-check-circle"></i> Cash Received
                                </button>
                            </div>
                        `;
                    });
                    cashBox.innerHTML = cashHtml;
                } else {
                    cashBox.innerHTML = '';
                }
            }

            // 2. Render Waiter Calls Dynamically via JS
            var waiterBox = document.getElementById('waiter-calls-container');
            if (waiterBox && data.waiter_calls) {
                if (data.waiter_calls.length > 0) {
                    var waiterHtml = '';
                    data.waiter_calls.forEach(function(call) {
                        var tableName = (call.table && call.table.table_number) ? call.table.table_number : (call.table_id ? 'Table No ' + call.table_id : 'Table');
                        var timeStr = new Date(call.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        
                        waiterHtml += `
                            <div id="waiter-call-card-${call.id}" class="alert alert-danger d-flex justify-content-between align-items-center mb-2 shadow-sm" style="border-left: 5px solid #dc3545;">
                                <div>
                                    <h6 class="fw-bold mb-1"><i class="bi bi-bell-fill"></i> Send Waiter to ${tableName}</h6>
                                    <small class="text-muted"><i class="bi bi-clock"></i> Requested at ${timeStr}</small>
                                </div>
                                <button onclick="resolveWaiterCall(${call.id})" class="btn btn-danger btn-sm fw-bold px-3">
                                    <i class="bi bi-check-circle"></i> Resolved / OK
                                </button>
                            </div>
                        `;
                    });
                    waiterBox.innerHTML = waiterHtml;
                } else {
                    waiterBox.innerHTML = '';
                }
            }

            // 3. Render Running Orders Dynamically
            var runningBox = document.getElementById('running-orders-container');
            if (runningBox && data.running_orders_html !== undefined) {
                runningBox.innerHTML = data.running_orders_html;
            }

            // Update Active Orders Count Badge
            var countBadge = document.querySelector('.badge.bg-secondary.rounded-pill');
            if (countBadge && data.activeOrdersCount !== undefined) {
                countBadge.innerText = data.activeOrdersCount + ' Active Orders';
            }

            // 4. Smart Sound Notifier (Calls & Orders Sound with Initial Load Check)
            if (window.KDS_NOTIFIER) {
                var orderCount = data.activeOrdersCount ? parseInt(data.activeOrdersCount) : 0;
                
                // Pehli baar fetch hone par sound nahi bajega, sirf count save hoga
                if (!isInitialFetch) {
                    if (orderCount > lastProcessedOrderCount) {
                        if (typeof window.KDS_NOTIFIER.updateOrders === 'function') {
                            window.KDS_NOTIFIER.updateOrders(orderCount);
                        }
                    }
                } else {
                    isInitialFetch = false; // Pehli fetch complete hone ke baad flag false kar do
                }
                lastProcessedOrderCount = orderCount;

                // Cash Requests Sound
                var cashCount = data.cash_requests ? data.cash_requests.length : 0;
                var latestCashId = (cashCount > 0 && data.cash_requests[0]) ? data.cash_requests[0].id : null;
                
                if (cashCount > lastProcessedCashCount || (latestCashId && latestCashId !== lastCashCallId)) {
                    var cashTable = (cashCount > 0 && data.cash_requests[0].table) 
                        ? (data.cash_requests[0].table.table_number || data.cash_requests[0].table_id) 
                        : '1';
                    window.KDS_NOTIFIER.updateCashRequests(cashCount, cashTable);
                    lastCashCallId = latestCashId;
                }
                lastProcessedCashCount = cashCount;

                // Waiter Calls Sound
                var waiterCount = data.waiter_calls ? data.waiter_calls.length : 0;
                var latestWaiterId = (waiterCount > 0 && data.waiter_calls[0]) ? data.waiter_calls[0].id : null;

                if (waiterCount > lastProcessedWaiterCount || (latestWaiterId && latestWaiterId !== lastWaiterCallId)) {
                    var waiterTable = (waiterCount > 0 && data.waiter_calls[0].table) 
                        ? (data.waiter_calls[0].table.table_number || data.waiter_calls[0].table_id) 
                        : '1';
                    window.KDS_NOTIFIER.updateWaiterCalls(waiterCount, waiterTable);
                    lastWaiterCallId = latestWaiterId;
                }
                lastProcessedWaiterCount = waiterCount;
            }
        })
        .catch(function(err) {
            console.error('Live Sync Polling Error:', err);
        });
    }

    // -------------------------------------------------------------
    // WAITER CALL & CASH REQUEST RESOLVE HANDLERS
    // -------------------------------------------------------------
    function resolveWaiterCall(id) {
        if (!id) return;

        var card = document.getElementById('waiter-call-card-' + id);
        if (card) {
            card.remove();
        }

        fetch('/vendor/restaurant/waiter-calls/' + id + '/resolve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) {
                console.error('Waiter Call Resolve Failed:', data.message);
                location.reload();
            } else {
                lastProcessedWaiterCount = 0; 
                lastWaiterCallId = null;
                syncLiveCalls();
            }
        })
        .catch(function(err) {
            console.error('Waiter Call Network Error:', err);
        });
    }

    function resolveCashRequest(id) {
        if (!id) return;

        var card = document.getElementById('cash-card-' + id);
        if (card) {
            card.remove();
        }

        fetch('/vendor/restaurant/cash-call/resolve/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) {
                console.error('Cash Request Resolve Failed:', data.message);
                location.reload();
            } else {
                lastProcessedCashCount = 0;
                lastCashCallId = null;
                syncLiveCalls();
            }
        })
        .catch(function(err) {
            console.error('Cash Request Network Error:', err);
        });
    }
</script>