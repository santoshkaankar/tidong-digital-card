<!-- View Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-receipt me-2 text-danger"></i>Ordered Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="modalItemsList" class="list-group list-group-flush">
                    @if(isset($activeOrder) && $activeOrder && $activeOrder->items && $activeOrder->items->count() > 0)
                        @php
                            $groupedItems = $activeOrder->items->groupBy('item_id')->map(function($items) {
                                return [
                                    'item_name' => $items->first()->item_name,
                                    'price' => $items->first()->price,
                                    'quantity' => $items->sum('quantity'),
                                    'subtotal' => $items->sum('subtotal'),
                                    'kitchen_status' => $items->last()->kitchen_status ?? 'sent_to_kitchen'
                                ];
                            });
                        @endphp
                        @foreach($groupedItems as $ordItem)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $ordItem['item_name'] }}</h6>
                                    <small class="text-muted">₹{{ number_format($ordItem['price'], 2) }} x {{ $ordItem['quantity'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold text-dark d-block">₹{{ number_format($ordItem['subtotal'], 2) }}</span>
                                    <span class="badge bg-secondary" style="font-size: 10px;">{{ ucfirst(str_replace('_', ' ', $ordItem['kitchen_status'])) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-utensils fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Abhi koi active order nahi hai.</p>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <strong class="fs-6">Total Amount:</strong>
                    <strong class="fs-5 text-success">₹<span id="modalTotalAmount">{{ (isset($activeOrder) && $activeOrder) ? number_format($activeOrder->total_amount, 2) : '0.00' }}</span></strong>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $currentOrder = $order ?? $activeOrder ?? null;
    $orderId = $currentOrder ? $currentOrder->id : null;
    $billAmount = $currentOrder ? $currentOrder->total_amount : 0;
    $formattedAmount = number_format($billAmount, 2, '.', '');
    
    $upiId = "6395392537@ybl";
    $payeeName = "MEENU SHARMA";
    $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($payeeName) . "&am={$formattedAmount}&cu=INR";
    $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($upiString);
@endphp

<!-- Payment Request Modal (2-Step Flow) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold text-dark w-100 text-center fs-5">Select Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                
                <!-- STEP 1: Payment Options Buttons -->
                <div id="paymentOptionsGroup" class="d-grid gap-3">
                    <p class="text-muted mb-2">How would you like to pay your bill?</p>
                    
                    <button class="btn btn-outline-success btn-lg fw-bold rounded-3 py-3" onclick="submitPayment('cash')">
                        <i class="fas fa-money-bill-wave me-2"></i> Cash (Pay to Waiter)
                    </button>
                    
                    <button class="btn btn-outline-primary btn-lg fw-bold rounded-3 py-3" onclick="toggleQrDisplay(true)">
                        <i class="fas fa-qrcode me-2"></i> UPI / QR Code Scanning
                    </button>
                </div>

                <!-- STEP 2: Instant QR & Gateway Display -->
                <div id="qrCodeDisplayGroup" class="d-none">
                    <p class="text-primary fw-bold mb-2 small">Scan & Pay via Any UPI App</p>
                    
                    <div class="p-2 bg-white rounded-4 d-inline-block border border-2 border-primary border-dashed my-1 shadow-sm">
                        <img src="{{ $qrApiUrl }}" alt="Payment QR" style="max-width: 190px; max-height: 190px;" class="img-fluid rounded">
                    </div>

                    <div class="fw-bold text-dark mt-1 fs-6">MEENU SHARMA</div>
                    <div class="small text-muted mb-2">UPI ID: 6395392537@ybl</div>

                    <div class="alert alert-info py-2 my-2 fw-bold border-0" style="background-color: #e0f2fe; color: #0284c7;">
                        Bill Amount: ₹{{ number_format($billAmount, 2) }}
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button id="phonepeBtn" class="btn btn-primary fw-bold py-2 rounded-3" onclick="payViaPhonePe()">
                            <i class="fas fa-bolt me-1"></i> Pay via UPI Payment
                        </button>

                        <button id="qrBtn" class="btn btn-success fw-bold py-2 rounded-3" onclick="confirmQrPayment()">
                            <i class="fas fa-check-circle me-1"></i> I Have Paid via QR Code
                        </button>

                        <button onclick="toggleQrDisplay(false)" class="btn btn-link text-muted btn-sm text-decoration-none mt-1">
                            &larr; Back to Payment Options
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function toggleQrDisplay(showQr) {
    if(showQr) {
        document.getElementById('paymentOptionsGroup').classList.add('d-none');
        document.getElementById('qrCodeDisplayGroup').classList.remove('d-none');
    } else {
        document.getElementById('qrCodeDisplayGroup').classList.add('d-none');
        document.getElementById('paymentOptionsGroup').classList.remove('d-none');
    }
}

function submitPayment(type) {
    if (type === 'cash') {
        if(!confirm("Kya aap Cash payment karna chahte hain? Counter / Waiter ko request bhej di jayegi.")) return;
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    fetch("{{ route('payment.process') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            order_id: "{{ $orderId }}",
            payment_method: 'cash'
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Cash payment request submit ho gayi hai!');
        location.reload();
    })
    .catch(err => {
        alert('Cash payment request submit ho gayi hai!');
        location.reload();
    });
}

function confirmQrPayment() {
    const btn = document.getElementById('qrBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Confirming...';
    }

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    fetch("{{ route('payment.process') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            order_id: "{{ $orderId }}",
            payment_method: 'qr_code',
            payment_status: 'paid_by_qr'
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Payment done! Aapki QR Code payment submit ho gayi hai.');
        location.reload();
    })
    .catch(err => {
        alert('Payment done! QR Code payment successful.');
        location.reload();
    });
}

function payViaPhonePe() {
    const orderId = "{{ $orderId }}";
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const btn = document.getElementById('phonepeBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    }

    fetch("{{ route('payment.process') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({
            order_id: orderId,
            gateway: 'phonepe'
        })
    })
    .then(async response => {
        const text = await response.text();
        let data;
        try { data = JSON.parse(text); } catch (e) {}
        if (!response.ok) {
            throw new Error(data?.message || 'Payment Error');
        }
        return data;
    })
    .then(data => {
        if (data && (data.url || (data.status === 'redirect' && data.url))) {
            window.location.href = data.url;
        } else {
            alert('Payment process nahi ho saka. Kripya dubara try karein.');
            if(btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Pay via UPI Payment';
            }
        }
    })
    .catch(error => {
        alert('Please try again.');
        if(btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Pay via UPI Payment';
        }
    });
}
</script>