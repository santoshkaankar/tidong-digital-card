@if(isset($activeOrders) && $activeOrders->count() > 0)
    <div class="row g-3">
        @foreach($activeOrders as $order)
            @php
                $rawTable = $order->table->table_number ?? $order->table_id ?? 'N/A';
                $cleanTable = preg_replace('/^table\s*#?/i', '', trim($rawTable));
                $orderType = strtolower($order->order_type ?? 'dine_in');

                $groupedItems = [];
                $itemSpeechList = [];

                if(isset($order->items) && $order->items->count() > 0) {
                    foreach($order->items as $item) {
                        $iName = $item->item_name ?? $item->name ?? ($item->restaurantItem->globalItem->name ?? 'Item');
                        if(!isset($groupedItems[$iName])) {
                            $groupedItems[$iName] = ['name' => $iName, 'quantity' => 0];
                        }
                        $groupedItems[$iName]['quantity'] += $item->quantity;
                    }
                    foreach($groupedItems as $gItem) {
                        $itemSpeechList[] = $gItem['quantity'] . ' ' . $gItem['name'];
                    }
                }

                $speechItemsText = implode(', ', $itemSpeechList);
                $isOnlinePaid = (strtolower($order->payment_status ?? '') === 'paid') || (strtolower($order->payment_method ?? '') === 'upi') || (strtolower($order->payment_method ?? '') === 'online');
            @endphp

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 p-3 position-relative overflow-hidden mb-3" style="background: var(--card-bg); border-left: 5px solid {{ $order->status === 'pending' ? '#ffc107' : ($order->status === 'cooking' ? '#0d6efd' : '#198754') }} !important;">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-4">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h6 class="fw-bold mb-0" style="color: var(--text-main);">#{{ $order->order_number ?? 'ORD-'.$order->id }}</h6>
                                    
                                    @if($orderType === 'delivery')
                                        <span class="badge bg-primary fw-bold"><i class="bi bi-truck me-1"></i>DELIVERY</span>
                                    @elseif($orderType === 'takeaway')
                                        <span class="badge bg-info text-dark fw-bold"><i class="bi bi-bag-check me-1"></i>TAKEAWAY</span>
                                    @else
                                        <span class="badge bg-warning text-dark fw-bold">Table {{ $cleanTable }}</span>
                                    @endif
                                    
                                    <span class="badge bg-secondary text-white">{{ strtoupper($order->status) }}</span>

                                    @if($isOnlinePaid)
                                        <span class="badge bg-info text-dark"><i class="bi bi-patch-check-fill me-1"></i>ONLINE PAID</span>
                                    @endif
                                </div>

                                <div class="text-muted small">
                                    <i class="bi bi-clock me-1"></i>{{ $order->created_at ? $order->created_at->format('h:i A') : '' }} &bull; <span class="text-primary fw-semibold">{{ count($groupedItems) }} Unique Item(s)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Items Preview -->
                        <div class="text-muted small text-truncate d-none d-lg-block" style="max-width: 350px;">
                            <i class="bi bi-bag me-1"></i>
                            @if(count($groupedItems) > 0)
                                @php $count = 0; $totalTypes = count($groupedItems); @endphp
                                @foreach($groupedItems as $gItem)
                                    @php $count++; @endphp
                                    {{ $gItem['quantity'] }}x {{ $gItem['name'] }}@if($count < $totalTypes), @endif
                                @endforeach
                            @endif
                        </div>

                        <!-- Dynamic Action Buttons Workflow -->
<div class="d-flex align-items-center gap-2">
    @if($order->status === 'pending' || $order->status === 'waiting')
        <button type="button" class="btn btn-success btn-sm px-3 fw-bold" onclick="updateOrderStatus({{ $order->id }}, 'cooking')">
            <i class="bi bi-check-circle me-1"></i> Accept
        </button>

    @elseif($order->status === 'cooking')
        @if(in_array($orderType, ['delivery', 'takeaway']))
            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" onclick="updateOrderStatus({{ $order->id }}, 'ready')">
                <i class="bi bi-box-seam me-1"></i> Mark Ready
            </button>
        @else
            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" onclick="updateOrderStatus({{ $order->id }}, 'served')">
                <i class="bi bi-tray-fill me-1"></i> Serve Order
            </button>
        @endif

    @elseif(in_array($order->status, ['ready', 'pickedup', 'picked_up', 'out_for_delivery', 'on_the_way']) && in_array($orderType, ['delivery', 'takeaway']))
        <!-- Delivery / Takeaway OTP Handover Verification Button -->
        <button type="button" class="btn btn-warning text-dark btn-sm px-3 fw-bold" onclick="openOtpHandoverModal({{ $order->id }}, '{{ $order->order_number }}')">
            <i class="bi bi-shield-lock-fill me-1"></i> Handover (Verify OTP)
        </button>

    @else
        <button type="button" class="btn btn-dark btn-sm px-3 fw-bold" onclick="handleOrderDone({{ $order->id }}, {{ $isOnlinePaid ? 'true' : 'false' }}, '{{ $order->order_number ?? 'ORD-'.$order->id }}')">
            <i class="bi bi-check2-all me-1"></i> Mark Done
        </button>
    @endif

    <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-semibold d-flex align-items-center gap-1" onclick="openOrderDetailModal({{ $order->id }})">
        <i class="bi bi-eye"></i> View Detail
    </button>

    <!-- Voice Notification Megaphone -->
    <button type="button" class="btn btn-light btn-sm rounded-circle p-2" title="Announce Order" onclick="KDS_NOTIFIER.playNewOrderAlert('{{ $orderType === 'delivery' ? 'Delivery Order' : 'Table ' . $cleanTable }}', '{{ addslashes($speechItemsText) }}')">
        <i class="bi bi-megaphone-fill text-primary"></i>
    </button>
</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-info text-center my-4 py-4">
        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
        No active orders at the moment.
    </div>
@endif

<!-- Handover OTP Modal -->
<div class="modal fade" id="otpHandoverModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i>Verify Handover OTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="text-muted small mb-3">Delivery boy se OTP le kar yahan verify karein:</p>
                <input type="hidden" id="handover_order_id">
                <input type="text" id="entered_handover_otp" maxlength="4" class="form-control form-control-lg text-center fs-2 fw-bold border-2 border-warning rounded-3" placeholder="____" autocomplete="off">
                <div id="otp_error_msg" class="text-danger small mt-2 d-none"></div>
            </div>
            <div class="modal-footer justify-content-center bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning fw-bold px-4" onclick="submitHandoverOtp()">Verify & Handover</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openOtpHandoverModal(orderId, orderNum) {
        document.getElementById('handover_order_id').value = orderId;
        document.getElementById('entered_handover_otp').value = '';
        document.getElementById('otp_error_msg').classList.add('d-none');
        
        var modalEl = document.getElementById('otpHandoverModal');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function submitHandoverOtp() {
        var orderId = document.getElementById('handover_order_id').value;
        var otp = document.getElementById('entered_handover_otp').value;
        var errDiv = document.getElementById('otp_error_msg');

        if (!otp || otp.length < 4) {
            errDiv.innerText = "Please enter 4-digit OTP";
            errDiv.classList.remove('d-none');
            return;
        }

        fetch('/vendor/restaurant/kitchen-orders/' + orderId + '/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                otp: otp,
                _token: getCsrfToken()
            })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                var modalEl = document.getElementById('otpHandoverModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Clear gray backdrop overlay
                var backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(b) { b.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = 'auto';

                // Instantly remove order from Active KDS screen
                syncLiveCalls(true);
            } else {
                errDiv.innerText = data.message || "Invalid OTP! Try again.";
                errDiv.classList.remove('d-none');
            }
        })
        .catch(function(err) {
            console.error(err);
            errDiv.innerText = "Server error occurred!";
            errDiv.classList.remove('d-none');
        });
    }
</script>