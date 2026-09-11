<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Restaurant Orders — Restaurant Hub</title>
    
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4f46e5;
            --bg-canvas: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            overflow-x: hidden;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-viewport {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill">ORDERS HISTORY</span>
        <h2 class="fw-bold mt-2 mb-0">All Restaurant Orders</h2>
        <p class="text-muted small mb-0">View and track all real-time and past customer orders.</p>
    </div>

    <!-- Direct Back to POS Button -->
    <a href="{{ route('vendor.restaurant.pos.index') }}" class="btn btn-primary fw-semibold">
        <i class="bi bi-plus-lg me-1"></i> New POS Order
    </a>
</div>
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Restaurant Sidebar Partial -->
    @include('vendor.restaurant.partials.sidebar')

    <div class="main-viewport p-4 p-lg-5">
        
        <!-- Header Bar -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                    ORDERS HISTORY
                </span>
                <h2 class="fw-bold mt-2 mb-0">All Restaurant Orders</h2>
                <p class="text-muted small mb-0">View and track all real-time and past customer orders.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Orders Table Card -->
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2.5 py-1">
                                        {{ strtoupper(str_replace('_', ' ', $order->order_type)) }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    {{ $order->table ? 'Table #'.$order->table->table_number : 'N/A' }}
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        @foreach($order->items as $orderItem)
                                            <span class="fw-semibold text-dark">{{ $orderItem->item->name ?? 'Item' }}</span> 
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-receipt display-5 d-block mb-3 opacity-25"></i>
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

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>