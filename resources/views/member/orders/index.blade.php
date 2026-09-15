<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Tidong® Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; background: #0f172a; color: #fff; transition: all 0.3s ease; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; }
        #sidebar .sidebar-header { padding: 20px; background: #1e293b; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; color: #38bdf8; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; transition: all 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active a { color: #fff; background: #1e293b; border-left: 4px solid #38bdf8; }
        #content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; transition: all 0.3s ease; }
        .top-navbar { background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .form-card { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; }
        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #sidebar.active { margin-left: 0; } #content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    {{-- Member Sidebar Partial Fixed --}}
    @include('member.partials.sidebar')

    <div id="content">
        {{-- Top Navbar Partial --}}
        @include('member.partials.top-navbar')

        <div class="container-fluid py-4 px-4">
            <!-- Page Header -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">📦 My Orders & Invoices</span>
                    <h2 class="fw-bold text-dark m-0">Order History</h2>
                    <p class="text-muted small m-0">Aapke sabhi digital card orders, subscriptions, aur transactions ka record.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark rounded-3 px-3">
                        <i class="fas fa-arrow-left me-1"></i> Dashboard
                    </a>
                </div>
            </div>

            <!-- Success / Error Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Orders Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark m-0"><i class="fas fa-shopping-bag text-primary me-2"></i> All Orders</h5>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Total: {{ isset($orders) ? count($orders) : 0 }} Orders</span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-7 text-secondary">
                                <tr>
                                    <th class="py-3 px-4">Order ID / No</th>
                                    <th class="py-3">Plan / Item</th>
                                    <th class="py-3">Amount</th>
                                    <th class="py-3">Payment Status</th>
                                    <th class="py-3">Order Status</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3 text-end px-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders ?? [] as $order)
                                    <tr>
                                        <td class="px-4 fw-bold text-dark">
                                            #{{ $order->order_no ?? $order->id }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $order->plan_name ?? $order->item_name ?? 'Digital Visiting Card' }}</div>
                                            <small class="text-muted">{{ $order->description ?? 'Subscription / Activation' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">₹{{ number_format($order->amount ?? 0, 2) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $pStatus = strtolower($order->payment_status ?? 'pending');
                                            @endphp
                                            @if($pStatus == 'paid' || $pStatus == 'success')
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Paid</span>
                                            @elseif($pStatus == 'failed')
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Failed</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $status = strtolower($order->status ?? 'processing');
                                            @endphp
                                            @if($status == 'completed' || $status == 'active')
                                                <span class="badge bg-success text-white px-2 py-1 rounded-3">Completed</span>
                                            @elseif($status == 'cancelled')
                                                <span class="badge bg-danger text-white px-2 py-1 rounded-3">Cancelled</span>
                                            @else
                                                <span class="badge bg-info text-dark px-2 py-1 rounded-3">Processing</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light border rounded-3 px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                    @if(isset($order->invoice_url))
                                                        <li>
                                                            <a class="dropdown-item py-2 small" href="{{ $order->invoice_url }}" target="_blank">
                                                                <i class="fas fa-file-invoice text-primary me-2"></i> Download Invoice
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item py-2 small" href="{{ route('member.orders.show', $order->id ?? 1) }}">
                                                            <i class="fas fa-eye text-info me-2"></i> View Details
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-50"></i>
                                                <h5 class="fw-bold text-dark">Koi order nahi mila!</h5>
                                                <p class="text-muted small mb-3">Aapne abhi tak koi digital card ya plan purchase nahi kiya hai.</p>
                                                <a href="{{ route('member.card.configure') }}" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">
                                                    <i class="fas fa-plus me-1"></i> Configure Card Now
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center py-4 text-muted small border-top mt-5">
            &copy; {{ date('Y') }} Tidong® Portal. All rights reserved.
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
    </script>
</body>
</html>