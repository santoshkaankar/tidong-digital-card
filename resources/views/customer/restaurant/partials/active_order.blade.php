@if($activeOrder)
<div class="container mt-3" id="activeOrderBanner">
    <div class="card bg-warning text-dark border-0 shadow-sm rounded-3">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1 fw-bold">
                    <i class="fas fa-fire me-1"></i> 
                    <span id="activeStatusText">
                        @switch(strtolower($activeOrder->status))
                            @case('cooking')
                            @case('preparing')
                                Preparing / Cooking
                                @break
                            @case('served')
                            @case('ready')
                                Served / Ready
                                @break
                            @case('completed')
                                Completed
                                @break
                            @case('cancelled')
                                Cancelled
                                @break
                            @default
                                Order in Kitchen
                        @endswitch
                    </span> 
                    (#{{ $activeOrder->order_number }})
                </h6>
                <small class="fw-semibold">Total Bill: ₹<span id="activeTotal">{{ number_format($activeOrder->total_amount, 2) }}</span></small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" onclick="viewOrderDetails()">
                    <i class="fas fa-eye me-1"></i> View Items
                </button>
                <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold" onclick="openPaymentModal({{ $activeOrder->id }})">
                    Pay Bill
                </button>
            </div>
        </div>
    </div>
</div>
@endif