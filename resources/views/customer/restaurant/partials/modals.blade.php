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

<!-- Payment Request Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold text-dark">Select Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                
                <!-- Payment Options Buttons -->
                <div id="paymentOptionsGroup" class="d-grid gap-3">
                    <p class="text-muted mb-2">How would you like to pay your bill?</p>
                    
                    <button class="btn btn-outline-success btn-lg fw-bold rounded-3 py-3" onclick="submitPayment('cash')">
                        <i class="fas fa-money-bill-wave me-2"></i> Cash (Pay to Waiter)
                    </button>
                    
                    <button class="btn btn-outline-primary btn-lg fw-bold rounded-3 py-3" onclick="toggleQrDisplay(true)">
                        <i class="fas fa-qrcode me-2"></i> UPI / QR Code Scanning
                    </button>
                </div>

                <!-- Instant QR & Gateway Display -->
                <div id="qrCodeDisplayGroup" class="d-none">
                    <h6 class="fw-bold text-primary mb-2">Scan & Pay via Any UPI App</h6>
                    
                    <div class="p-3 bg-light rounded-4 d-inline-block border my-2">
                        @if(isset($restaurant) && !empty($restaurant->upi_qr_code))
                            <img src="{{ asset('storage/' . $restaurant->upi_qr_code) }}" alt="Payment QR" style="max-width: 200px;" class="img-fluid rounded">
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa={{ $restaurant->upi_id ?? 'merchant@upi' }}%26pn={{ urlencode($restaurant->name ?? 'Restaurant') }}%26am={{ (isset($activeOrder) && $activeOrder) ? $activeOrder->total_amount : 0 }}%26cu=INR" alt="UPI QR" class="img-fluid rounded">
                        @endif
                    </div>

                    <div class="alert alert-info py-2 my-2 small rounded-3">
                        <strong>Bill Amount:</strong> ₹{{ (isset($activeOrder) && $activeOrder) ? number_format($activeOrder->total_amount, 2) : '0.00' }}
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        @if(isset($activeOrder) && $activeOrder && isset($activeOrder->id))
                            <a href="{{ route('payment.checkout', $activeOrder->id) }}" class="btn btn-primary fw-bold py-2">
                                <i class="fas fa-bolt me-1"></i> Pay via PhonePe Gateway
                            </a>
                        @endif

                        <button onclick="submitPayment('upi')" class="btn btn-success fw-bold py-2">
                            <i class="fas fa-check-circle me-1"></i> I Have Paid via QR Code
                        </button>

                        <button onclick="toggleQrDisplay(false)" class="btn btn-link text-muted btn-sm">
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
</script>