@if(isset($activeOrders) && $activeOrders->count() > 0)
    <div class="row g-3">
        @foreach($activeOrders as $order)
            @php
                // Table/Room Number Clean-up
                $rawTable = $order->table->table_number ?? $order->table_id ?? 'N/A';
                $cleanTable = preg_replace('/^table\s*#?/i', '', trim($rawTable));

                // Group Duplicate Items & Prepare Speech Text
                $groupedItems = [];
                $itemSpeechList = [];

                if(isset($order->items) && $order->items->count() > 0) {
                    foreach($order->items as $item) {
                        $iName = $item->item_name ?? $item->name ?? ($item->restaurantItem->globalItem->name ?? 'Item');
                        if(!isset($groupedItems[$iName])) {
                            $groupedItems[$iName] = [
                                'name' => $iName,
                                'quantity' => 0
                            ];
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
                                    
                                    <span class="badge bg-warning text-dark fw-bold">Table {{ $cleanTable }}</span>
                                    
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning text-dark fw-bold">WAITING</span>
                                    @elseif($order->status === 'cooking')
                                        <span class="badge bg-primary text-white fw-bold">COOKING</span>
                                    @elseif($order->status === 'ready' || $order->status === 'served')
                                        <span class="badge bg-success text-white fw-bold">SERVED</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ strtoupper($order->status) }}</span>
                                    @endif

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

                        <!-- Complete Action Buttons Workflow -->
                        <div class="d-flex align-items-center gap-2">
                            @if($order->status === 'pending')
                                <button type="button" class="btn btn-success btn-sm px-3 fw-bold" onclick="updateOrderStatus({{ $order->id }}, 'cooking')">
                                    <i class="bi bi-check-circle me-1"></i> Accept
                                </button>
                            @elseif($order->status === 'cooking')
                                <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" onclick="updateOrderStatus({{ $order->id }}, 'served')">
                                    <i class="bi bi-tray-fill me-1"></i> Serve Order
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
                            <button type="button" class="btn btn-light btn-sm rounded-circle p-2" title="Announce Order" onclick="KDS_NOTIFIER.playNewOrderAlert('Table {{ $cleanTable }}', '{{ addslashes($speechItemsText) }}')">
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentVerifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash-stack me-2"></i>Confirm Payment Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="bi bi-question-circle-fill text-warning display-4 mb-3 d-block"></i>
                <h5 class="fw-bold mb-2" id="modalOrderTitle">Order #...</h5>
                <p class="text-muted">Has the cash/offline payment been received for this order?</p>
            </div>
            <div class="modal-footer d-flex justify-content-between bg-light">
                <button type="button" class="btn btn-outline-danger fw-bold px-4" id="btnPaymentNo">
                    <i class="bi bi-x-circle me-1"></i> No (Pending)
                </button>
                <button type="button" class="btn btn-success fw-bold px-4" id="btnPaymentYes">
                    <i class="bi bi-check-circle me-1"></i> Yes, Payment Received
                </button>
            </div>
        </div>
    </div>
</div>