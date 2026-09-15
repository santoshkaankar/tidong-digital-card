@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">💼 Business FinTech Hub</span>
            <h2 class="fw-bold text-dark m-0">Vendor Wallet Dashboard</h2>
            <p class="text-muted small m-0">Manage your daily SaaS credit pool and withdrawable sales earnings.</p>
        </div>
        <div>
            <a href="{{ route('vendor.restaurant.dashboard') }}" class="btn btn-outline-dark rounded-3 px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Balance Cards Grid -->
    <div class="row g-4 mb-5">
        <!-- Bonus Balance Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-uppercase tracking-wider small fw-semibold opacity-75">Avl. Bal.</span>
                        <h1 class="fw-bold display-5 mt-2 mb-0">₹{{ number_format($wallet->bonus_balance ?? 0, 2) }}</h1>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-4">
                        <i class="fas fa-gift fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Balance Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white h-100" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-uppercase tracking-wider small fw-semibold opacity-75">Withdrawable Balance</span>
                        <h1 class="fw-bold display-5 mt-2 mb-0">₹{{ number_format($wallet->sales_balance ?? 0, 2) }}</h1>
                    </div>
                    <div class="bg-white bg-opacity-20 p-3 rounded-4">
                        <i class="fas fa-rupee-sign fa-2x"></i>
                    </div>
                </div>
                <div class="mt-4 pt-2 border-top border-white border-opacity-25 d-flex justify-content-end">
                    <button class="btn btn-light text-success fw-bold px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="fas fa-paper-plane me-1"></i> Withdraw Funds
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Ledger Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark m-0"><i class="fas fa-history text-primary me-2"></i> Wallet Ledger & History</h5>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Total Transactions: {{ $transactions->total() }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3">Wallet Type</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="px-4 fw-bold text-dark">#{{ $tx->id }}</td>
                                <td>
                                    @if($tx->wallet_type == 'bonus')
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill">Bonus Pool</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">Sales Wallet</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->type == 'credit')
                                        <span class="text-success fw-bold"><i class="fas fa-arrow-down me-1"></i> Credit</span>
                                    @else
                                        <span class="text-danger fw-bold"><i class="fas fa-arrow-up me-1"></i> Debit</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold {{ $tx->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type == 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                    </span>
                                </td>
                                <td>{{ $tx->description }}</td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-3">Success</span>
                                </td>
                                <td class="text-end px-4 text-muted small">
                                    {{ $tx->created_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">Abhi tak koi transaction record nahi mila hai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Withdrawal Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('vendor.wallet.withdraw') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-dark">Request Bank Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Available Sales Balance</label>
                        <h3 class="fw-bold text-success">₹{{ number_format($wallet->sales_balance, 2) }}</h3>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">Enter Amount to Withdraw (Min. ₹100)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="number" step="0.01" min="100" max="{{ $wallet->sales_balance }}" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-bold">Proceed Withdrawal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection