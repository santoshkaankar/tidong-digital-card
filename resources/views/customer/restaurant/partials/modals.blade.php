<!-- View Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-receipt me-2 text-danger"></i>Ordered Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div id="modalItemsList" class="list-group list-group-flush">
                    @if($activeOrder)
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
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <strong class="fs-6">Total Amount:</strong>
                    <strong class="fs-5 text-success">₹<span id="modalTotalAmount">{{ $activeOrder ? number_format($activeOrder->total_amount, 2) : '0.00' }}</span></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Request Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold">Select Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <p class="text-muted mb-4">How would you like to pay your bill?</p>
                <div class="d-grid gap-3">
                    <button class="btn btn-outline-success btn-lg fw-bold rounded-3" onclick="submitPayment('cash')">
                        <i class="fas fa-money-bill-wave me-2"></i> Cash (Pay to Waiter)
                    </button>
                    <button class="btn btn-outline-primary btn-lg fw-bold rounded-3" onclick="submitPayment('upi')">
                        <i class="fas fa-qrcode me-2"></i> UPI / QR Code Scanning
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>