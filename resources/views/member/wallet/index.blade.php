@extends('member.partials.layout')

@section('title', 'My Wallet - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4">
    <!-- Header with Back to Dashboard Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-wallet text-warning me-2"></i>My Wallet</h4>
            <p class="text-muted small mb-0">View your wallet balance and transaction history here</p>
        </div>
        <a href="{{ url('/member/dashboard') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Balance Cards Row -->
    <div class="row g-4 mb-4">
        <!-- 1. Reserved Income Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white rounded-4 p-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-uppercase small fw-bold opacity-75 mb-0">Reserved Reward</h6>
                        <span class="badge bg-dark text-warning px-2 py-1"><i class="fas fa-hourglass-half me-1"></i> Pending Unlock</span>
                    </div>
                    <h2 class="fw-bold mb-3">₹ {{ number_format($wallet->non_withdrawable_balance ?? 0, 2) }}</h2>
                    <span class="small opacity-90"><i class="fas fa-shield-alt me-1"></i> Guaranteed reward balance (Unlocked upon stage completion)</span>
                </div>
            </div>
        </div>

        <!-- 2. Withdrawable Balance Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4 p-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">Withdrawable Bal.</h6>
                    <h2 class="fw-bold mb-3">₹ {{ number_format($wallet->real_balance ?? 0, 2) }}</h2>
                    <button class="btn btn-light text-primary fw-bold rounded-pill px-3 btn-sm">
                        <i class="fas fa-exchange-alt me-1"></i> Fund Transfer
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. T-Coins Wallet Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white rounded-4 p-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">T-Coin Bal.</h6>
                    <h2 class="fw-bold mb-3"><i class="fas fa-coins text-warning me-1"></i> {{ number_format($wallet->t_coins ?? 0, 2) }}</h2>
                    <div class="badge bg-white text-success fw-bold rounded-pill px-3 py-2 fs-6 shadow-sm">
                        <i class="fas fa-check-circle text-success me-1"></i> 1 T-Coin = 1 RS
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rules Box (English Transformed + Transaction Criteria) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light border-start border-4 border-warning">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-warning me-2"></i> Important Rules: T-Coin Decay, TDS & Limits</h5>
            <div class="row g-4 small text-dark">
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded-3 border h-100">
                        <h6 class="fw-bold text-primary"><i class="fas fa-fire me-1"></i> Daily T-Coin Decay Rule</h6>
                        <p class="mb-1">The faster you complete 14 stages, the lower your daily deduction will be:</p>
                        <ul class="mb-0 ps-3">
                            <li><strong>Stage 0:</strong> 14 T-Coins / day</li>
                            <li><strong>Stage 1:</strong> 13 T-Coins / day</li>
                            <li><strong>Stage 2:</strong> 12 T-Coins / day</li>
                            <li><strong>Stage 10:</strong> 4 T-Coins / day</li>
                            <li><strong>Stage 14:</strong> 0 T-Coins / day</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded-3 border h-100">
                        <h6 class="fw-bold text-danger"><i class="fas fa-percent me-1"></i> TDS & Admin Deductions</h6>
                        <p class="mb-1">The following deductions apply upon amount transfer:</p>
                        <ul class="mb-0 ps-3">
                            <li><strong>10%</strong> Admin Service Charge</li>
                            <li><strong>5% TDS</strong> (With linked PAN Card)</li>
                            <li><strong>20% TDS</strong> (Without PAN Card)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-white rounded-3 border h-100">
                        <h6 class="fw-bold text-success"><i class="fas fa-receipt me-1"></i> Transaction Criteria</h6>
                        <p class="mb-1">Requirements to unlock Reserved Balance:</p>
                        <ul class="mb-0 ps-3">
                            <li>Self minimum transaction: <strong>₹25,000</strong></li>
                            <li>Leg A required transaction: <strong>₹25,000</strong></li>
                            <li>Leg B required transaction: <strong>₹25,000</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real Money Transactions History -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">INR Transactions History</h5>
            
            @if(isset($transactions) && $transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tx ID</th>
                                <th>Gateway</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                <tr>
                                    <td class="small font-monospace">{{ $tx->transaction_id }}</td>
                                    <td><span class="badge bg-secondary text-uppercase">{{ $tx->payment_gateway }}</span></td>
                                    <td>{{ $tx->description ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $tx->type == 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $tx->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type == 'credit' ? '+' : '-' }} ₹ {{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tx->status == 'success' ? 'success' : ($tx->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tx->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ date('d M Y, h:i A', strtotime($tx->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted my-3">No INR transaction available till now.</p>
            @endif
        </div>
    </div>

    <!-- T-Coin Transactions History -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">T-Coins History</h5>
            
            @if(isset($tCoinTransactions) && $tCoinTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Coins</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tCoinTransactions as $tc)
                                <tr>
                                    <td>{{ $tc->description }}</td>
                                    <td>
                                        <span class="badge {{ $tc->type == 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                            {{ $tc->type }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $tc->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $tc->type == 'credit' ? '+' : '-' }} {{ number_format($tc->coins, 2) }} T-Coins
                                    </td>
                                    <td class="text-muted small">{{ date('d M Y, h:i A', strtotime($tc->created_at)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted my-3">No T-Coins transaction available till now.</p>
            @endif
        </div>
    </div>
</div>
@endsection