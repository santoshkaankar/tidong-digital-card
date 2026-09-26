<!-- Food Menu Categories & Items List -->
<div class="container mt-2">
    @foreach($categories as $category)
        <div class="category-section cat-{{ $category->id }}">
            <h6 class="fw-bold text-muted my-2 px-1">{{ $category->name }}</h6>
            @foreach($category->items as $item)
                @php
                    $displayName = $item->globalItem->item_name ?? $item->name ?? 'Food Item';
                    $foodType = strtolower($item->globalItem->food_type ?? 'veg');
                @endphp
                <div class="card food-card p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="food-type-icon {{ $foodType == 'non-veg' || $foodType == 'non_veg' ? 'type-nonveg' : 'type-veg' }}"></span>
                            <strong class="fs-6 text-dark">{{ $displayName }}</strong>
                            <div class="mt-1">
                                <span class="fw-bold text-dark">₹{{ number_format($item->price, 2) }}</span>
                                @if($item->mrp > $item->price)
                                    <small class="text-muted text-decoration-line-through ms-1">₹{{ number_format($item->mrp, 2) }}</small>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="qty-btn-group" id="btn-group-{{ $item->id }}">
                                <button onclick="updateCart({{ $item->id }}, '{{ addslashes($displayName) }}', {{ $item->price }}, -1)">-</button>
                                <span id="qty-{{ $item->id }}">0</span>
                                <button onclick="updateCart({{ $item->id }}, '{{ addslashes($displayName) }}', {{ $item->price }}, 1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

@php
    // Current Order Bill Amount & ID Calculation
    $currentOrder = $order ?? $activeOrder ?? null;
    $orderId = $currentOrder ? $currentOrder->id : null;
    $billAmount = $currentOrder ? $currentOrder->total_amount : 240;
    $formattedAmount = number_format($billAmount, 2, '.', '');
    
    // MEENU SHARMA UPI Data
    $upiId = "6395392537@ybl";
    $payeeName = "MEENU SHARMA";
    $upiString = "upi://pay?pa={$upiId}&pn=" . urlencode($payeeName) . "&am={$formattedAmount}&cu=INR";
    $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($upiString);
@endphp

<!-- Payment Modal with MEENU SHARMA Dynamic QR Code -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold w-100 text-dark">Select Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-primary fw-bold mb-2">Scan & Pay via Any UPI App</p>

                <!-- Dynamic QR Code Container -->
                <div class="border border-2 border-primary border-dashed p-2 rounded-3 d-inline-block bg-white my-2">
                    <img src="{{ $qrApiUrl }}" alt="Scan & Pay QR" style="width: 200px; height: 200px; object-fit: contain;">
                </div>

                <div class="fw-bold text-dark mt-1">MEENU SHARMA</div>
                <div class="small text-muted mb-3">UPI ID: 6395392537@ybl</div>

                <div class="alert alert-info py-2 mb-3">
                    <strong>Bill Amount: ₹{{ number_format($billAmount, 2) }}</strong>
                </div>

                <div class="d-grid gap-2">
                    <button id="phonepeBtn" class="btn btn-primary fw-bold py-2" onclick="payViaPhonePe()">
                        <i class="fas fa-bolt me-1"></i> Pay via PhonePe Gateway
                    </button>
                    <button id="qrBtn" class="btn btn-success fw-bold py-2" onclick="confirmQrPayment()">
                        <i class="fas fa-check-circle me-1"></i> I Have Paid via QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Logic for Payment Buttons -->
<script>
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
            resetPhonePeBtn();
        }
    })
    .catch(error => {
        alert('Please try again.');
        resetPhonePeBtn();
    });
}

function resetPhonePeBtn() {
    const btn = document.getElementById('phonepeBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Pay via PhonePe Gateway';
    }
}

function confirmQrPayment() {
    const btn = document.getElementById('qrBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Confirming...';
    }

    alert('Payment request submit ho gayi hai! Kripya prateeksha karein.');
    location.reload();
}
</script>