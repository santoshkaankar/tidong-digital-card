@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .content-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
            ORDERS HISTORY
        </span>
        <h2 class="fw-bold mt-2 mb-0">All Restaurant Orders</h2>
        <p class="text-muted small mb-0">View and track all real-time and past customer orders.</p>
    </div>

    @if(Route::has('vendor.restaurant.pos.index'))
        <a href="{{ route('vendor.restaurant.pos.index') }}" class="btn btn-primary fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> New POS Order
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="content-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="border-0">Order No.</th>
                    <th class="border-0">Type</th>
                    <th class="border-0">Table</th>
                    <th class="border-0">Items Summary</th>
                    <th class="border-0">Amount</th>
                    <th class="border-0">Payment</th>
                    <th class="border-0">Status</th>
                    <th class="border-0">Date & Time</th>
                    <th class="border-0 text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $upiId = auth()->user()->upi_id ?? 'merchant@upi';
                        $restName = auth()->user()->restaurant_name ?? auth()->user()->name ?? 'Restaurant';
                        
                        $upiPayUrl = "upi://pay?pa=" . rawurlencode($upiId) . "&pn=" . rawurlencode($restName) . "&am=" . $order->total_amount . "&cu=INR&tn=" . rawurlencode("Order " . $order->order_number);
                        
                        $custPhone = $order->customer_phone ?? $order->phone ?? null;
                        $waMsg = "Hello! Your bill for Order #{$order->order_number} is ₹{$order->total_amount}.\nPay instantly via UPI: " . $upiPayUrl;
                        $waUrl = $custPhone ? "https://wa.me/91" . preg_replace('/[^0-9]/', '', $custPhone) . "?text=" . urlencode($waMsg) : null;
                        
                        $orderTypeLabel = strtoupper(str_replace('_', ' ', $order->order_type));
                        $tableLabel = $order->table ? 'Table #'.$order->table->table_number : 'N/A';

                        // Prepare items list for JS
                        $itemsList = [];
                        foreach($order->items as $orderItem) {
                            $itemName = $orderItem->item->name ?? $orderItem->item_name ?? 'Item';
                            $itemsList[] = [
                                'name' => $itemName,
                                'qty' => $orderItem->quantity,
                                'price' => $orderItem->price ?? ($orderItem->total / max($orderItem->quantity, 1))
                            ];
                        }
                        $taxVal = $order->tax_amount ?? 0;
                    @endphp
                    <tr>
                        <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1">
                                {{ $orderTypeLabel }}
                            </span>
                        </td>
                        <td class="fw-semibold text-dark">
                            {{ $tableLabel }}
                        </td>
                        <td>
                            <span class="text-muted small">
                                @foreach($order->items as $orderItem)
                                    <span class="fw-semibold text-dark">{{ $orderItem->item->name ?? $orderItem->item_name ?? 'Item' }}</span> 
                                    <span class="badge bg-light text-dark border me-1">x{{ $orderItem->quantity }}</span>{{ !$loop->last ? ' ' : '' }}
                                @endforeach
                            </span>
                        </td>
                        <td class="fw-bold text-dark">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} bg-opacity-10 text-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} px-3 py-1.5 rounded-pill fw-semibold">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1.5 rounded-pill fw-semibold">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-dark" title="Print Receipt" onclick='printReceipt(@json($order->order_number), @json($orderTypeLabel), @json($tableLabel), @json($order->total_amount), @json($order->created_at->format("d-m-Y h:i A")), @json($itemsList), @json($taxVal))'>
                                    <i class="bi bi-printer"></i> Prt
                                </button>
                                
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $order->id }}" title="View Order">
                                    <i class="bi bi-eye"></i> View
                                </button>

                                @if(Route::has('vendor.restaurant.orders.edit'))
                                    <a href="{{ route('vendor.restaurant.orders.edit', $order->id) }}" class="btn btn-outline-primary" title="Edit Order">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                @endif

                                @if($waUrl)
                                    <a href="{{ $waUrl }}" target="_blank" class="btn btn-outline-success" title="Send WhatsApp Payment Link">
                                        <i class="bi bi-whatsapp"></i> Pay
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Details & QR -->
                    <div class="modal fade" id="viewModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow rounded-4">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title fw-bold">Order Details #{{ $order->order_number }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <h6 class="fw-bold">Total Amount: ₹{{ number_format($order->total_amount, 2) }}</h6>
                                    
                                    <div class="my-3 p-3 bg-light rounded-3">
                                        <p class="small text-muted mb-2">Scan & Pay via any UPI App</p>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($upiPayUrl) }}" alt="UPI QR" class="img-fluid border p-2 bg-white rounded-3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            No order records found yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="mt-4 pt-3 border-top">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function printReceipt(orderNumber, type, table, amount, date, items, taxAmount = 0) {
        let itemsHtml = '';
        items.forEach(function(item) {
            let itemTotal = (item.price * item.qty);
            itemsHtml += `
                <tr>
                    <td>${item.name} (x${item.qty})</td>
                    <td class="text-right">₹${itemTotal.toFixed(2)}</td>
                </tr>
            `;
        });

        let totalTax = Number(taxAmount) || 0;
        let subtotal = Number(amount) - totalTax;
        let halfTax = totalTax / 2;

        let taxHtml = '';
        if (totalTax > 0) {
            taxHtml = `
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹${subtotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <td>CGST:</td>
                    <td class="text-right">+ ₹${halfTax.toFixed(2)}</td>
                </tr>
                <tr>
                    <td>SGST:</td>
                    <td class="text-right">+ ₹${halfTax.toFixed(2)}</td>
                </tr>
            `;
        } else {
            taxHtml = `
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹${Number(amount).toFixed(2)}</td>
                </tr>
            `;
        }

        let printWin = window.open('', '_blank', 'width=400,height=600');
        printWin.document.write(`
            <html>
            <head>
                <title>Receipt - #${orderNumber}</title>
                <style>
                    body { 
                        font-family: 'Courier New', Courier, monospace; 
                        width: 280px; 
                        margin: 0 auto; 
                        padding: 10px; 
                        font-size: 12px;
                        color: #000000;
                    }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .border-bottom { 
                        border-bottom: 1px dashed #000; 
                        margin-bottom: 8px; 
                        padding-bottom: 8px; 
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                    }
                    td { 
                        text-align: left; 
                        padding: 4px 0; 
                        vertical-align: top;
                    }
                </style>
            </head>
            <body>
                <div class="text-center border-bottom">
                    <h3 style="margin: 0; font-size: 16px;">KOT / RECEIPT</h3>
                    <p style="margin: 4px 0;">Order #: <strong>${orderNumber}</strong></p>
                    <p style="margin: 0;">Type: <strong>${type}</strong> | Table: <strong>${table}</strong></p>
                    <p style="margin: 4px 0;">Date: ${date}</p>
                </div>
                <div class="border-bottom">
                    <table>
                        ${itemsHtml}
                    </table>
                </div>
                <div class="border-bottom">
                    <table>
                        ${taxHtml}
                        <tr>
                            <td><strong>Grand Total:</strong></td>
                            <td class="text-right"><strong>₹${Number(amount).toFixed(2)}</strong></td>
                        </tr>
                    </table>
                </div>
                <p class="text-center" style="margin-top: 12px; margin-bottom: 0;">*** Thank You! ***</p>
            </body>
            </html>
        `);
        printWin.document.close();

        setTimeout(() => {
            printWin.focus();
            printWin.print();
        }, 400);
    }

    @if(session('whatsapp_url'))
        window.open("{{ session('whatsapp_url') }}", '_blank');
    @endif
</script>
@endpush