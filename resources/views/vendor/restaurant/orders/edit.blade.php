@extends('layouts.vendor_restaurant')

@section('title', 'Edit Order #' . $order->order_number)

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Edit Order: #{{ $order->order_number }}</h3>
            <p class="text-muted small mb-0">Modify customer info, change order items, status, and payment details.</p>
        </div>
        <a href="{{ route('vendor.restaurant.orders.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Orders List
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('vendor.restaurant.orders.update', $order->id) }}" method="POST" id="editOrderForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Side: Order Details & Items -->
            <div class="col-lg-8">
                <!-- Order Information Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="bi bi-person-badge me-2"></i>Order Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $order->customer_name) }}" placeholder="Guest / Counter Customer">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Customer Mobile</label>
                                <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $order->customer_phone) }}" placeholder="10 Digit Mobile Number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Order Type</label>
                                <select name="order_type" id="orderTypeSelect" class="form-select">
                                    <option value="dine_in" {{ $order->order_type === 'dine_in' ? 'selected' : '' }}>Dine In</option>
                                    <option value="takeaway" {{ $order->order_type === 'takeaway' ? 'selected' : '' }}>Takeaway</option>
                                    <option value="delivery" {{ $order->order_type === 'delivery' ? 'selected' : '' }}>Delivery</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="tableSelectWrapper">
                                <label class="form-label small text-muted fw-bold">Table</label>
                                <select name="table_id" class="form-select">
                                    <option value="">Select Table</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ $order->table_id == $table->id ? 'selected' : '' }}>
                                            {{ $table->table_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items in Order Card -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="bi bi-basket me-2"></i>Items in Order
                        </h5>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle fw-bold" type="button" id="addItemDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-plus-lg me-1"></i> Add Food Item
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="max-height: 250px; overflow-y: auto;">
                                @foreach($availableItems as $item)
                                    <li>
                                        <a class="dropdown-menu-item dropdown-item d-flex justify-content-between align-items-center cursor-pointer" 
                                           href="#" 
                                           onclick="addItemToTable('{{ $item->id }}', '{{ addslashes($item->globalItem->item_name ?? $item->name) }}', '{{ $item->price }}'); return false;">
                                            <span>{{ $item->globalItem->item_name ?? $item->name }}</span>
                                            <span class="badge bg-light text-dark ms-2">₹{{ number_format($item->price, 2) }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="orderItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%;">Item Name</th>
                                        <th style="width: 20%;">Price (₹)</th>
                                        <th style="width: 15%;">Quantity</th>
                                        <th style="width: 20%;">Subtotal (₹)</th>
                                        <th style="width: 5%; text-align: center;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $index => $item)
                                        <tr data-item-id="{{ $item->item_id }}">
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->item_id }}">
                                                <input type="hidden" name="items[{{ $index }}][name]" value="{{ $item->item_name }}">
                                                <span class="fw-bold text-dark">{{ $item->item_name }}</span>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="items[{{ $index }}][price]" class="form-control form-control-sm item-price" value="{{ $item->price }}" onchange="recalculateTotals()">
                                            </td>
                                            <td>
                                                <input type="number" min="1" name="items[{{ $index }}][qty]" class="form-control form-control-sm item-qty" value="{{ $item->quantity }}" onchange="recalculateTotals()">
                                            </td>
                                            <td>
                                                <span class="fw-bold item-subtotal">₹{{ number_format($item->subtotal, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeItemRow(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Status & Save Controls -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="bi bi-sliders me-2"></i>Order Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Kitchen Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="cooking" {{ $order->status === 'cooking' ? 'selected' : '' }}>Cooking</option>
                                <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="served" {{ $order->status === 'served' ? 'selected' : '' }}>Served</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-muted">Total Amount</span>
                            <span class="fs-4 fw-bold text-success" id="displayGrandTotal">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Update & Save Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count($order->items) }};

    function recalculateTotals() {
        let total = 0;
        const rows = document.querySelectorAll('#orderItemsTable tbody tr');

        rows.forEach(row => {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const qty = parseInt(row.querySelector('.item-qty').value) || 0;
            const subtotal = price * qty;
            
            row.querySelector('.item-subtotal').innerText = '₹' + subtotal.toFixed(2);
            total += subtotal;
        });

        document.getElementById('displayGrandTotal').innerText = '₹' + total.toFixed(2);
    }

    function addItemToTable(id, name, price) {
        const tbody = document.querySelector('#orderItemsTable tbody');
        
        const tr = document.createElement('tr');
        tr.setAttribute('data-item-id', id);
        tr.innerHTML = `
            <td>
                <input type="hidden" name="items[${itemIndex}][item_id]" value="${id}">
                <input type="hidden" name="items[${itemIndex}][name]" value="${name}">
                <span class="fw-bold text-dark">${name}</span>
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemIndex}][price]" class="form-control form-control-sm item-price" value="${price}" onchange="recalculateTotals()">
            </td>
            <td>
                <input type="number" min="1" name="items[${itemIndex}][qty]" class="form-control form-control-sm item-qty" value="1" onchange="recalculateTotals()">
            </td>
            <td>
                <span class="fw-bold item-subtotal">₹${parseFloat(price).toFixed(2)}</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="removeItemRow(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        itemIndex++;
        recalculateTotals();
    }

    function removeItemRow(btn) {
        const row = btn.closest('tr');
        row.remove();
        recalculateTotals();
    }

    document.addEventListener('DOMContentLoaded', function() {
        recalculateTotals();
    });
</script>
@endsection