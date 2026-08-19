@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Header with Back to Dashboard Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">My Wallet</h3>
            <p class="text-muted small m-0">Apna INR balance aur T-Coins history yahan dekhein</p>
        </div>
        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Balance Cards Row -->
    <div class="row g-4 mb-4">
        <!-- INR Balance Wallet Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4 p-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">INR Balance</h6>
                    <h2 class="fw-bold mb-3">₹ {{ number_format($wallet->real_balance ?? 0, 2) }}</h2>
                    <button class="btn btn-light text-primary fw-bold rounded-pill px-4 btn-sm" disabled>
                        <i class="fas fa-plus me-1"></i> Add Money (Coming Soon)
                    </button>
                </div>
            </div>
        </div>

        <!-- T-Coins Wallet Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-success text-white rounded-4 p-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">T-Coins</h6>
                    <h2 class="fw-bold mb-3"><i class="fas fa-coins me-1"></i> {{ number_format($wallet->t_coins ?? 0, 2) }}</h2>
                    <button class="btn btn-light text-success fw-bold rounded-pill px-4 btn-sm" disabled>
                        <i class="fas fa-exchange-alt me-1"></i> Earn / Spend Tokens
                    </button>
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