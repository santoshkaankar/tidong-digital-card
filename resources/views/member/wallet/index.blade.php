@php
if (!function_exists('format_indian_currency')) {
    function format_indian_currency($number) {
        $decimal = (string)($number - floor($number));
        $money = floor($number);
        $length = strlen($money);
        $delimiter = '';
        $output = '';
        if ($length > 3) {
            $delimiter = substr($money, -3);
            $money = substr($money, 0, strlen($money) - 3);
            while (strlen($money) > 2) {
                $delimiter = substr($money, -2) . ',' . $delimiter;
                $money = substr($money, 0, strlen($money) - 2);
            }
            if (strlen($money) > 0) {
                $delimiter = $money . ',' . $delimiter;
            }
            $output = $delimiter;
        } else {
            $output = $money;
        }
        $decimal = preg_replace("/0\./", "", $decimal);
        $decimal = str_pad($decimal, 2, "0", STR_PAD_RIGHT);
        return $output . '.' . substr($decimal, 0, 2);
    }
}

$cardStatus = $wallet->credit_card_status ?? 'waiting';
$cardProgress = $wallet->credit_card_progress ?? 25;
@endphp

@extends('member.partials.layout')

@section('title', 'My Wallet - Tidong®')

@section('content')
<div class="container-fluid py-4 px-4">
    <!-- Header with Back to Dashboard Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="fas fa-wallet text-warning me-2"></i>My Wallet</h4>
            <p class="text-muted small mb-0">
                View your wallet balance, credit limits, and transaction history
                @if(isset($authUser) && $authUser->is_child)
                    <span class="badge bg-primary ms-2"><i class="fas fa-child me-1"></i> Shared Parent Wallet Mode</span>
                @endif
            </p>
        </div>
        <a href="{{ url('/member/dashboard') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Real Metallic Credit Card View (Compact Width) -->
    <div class="row mb-4 justify-content-center">
        <div class="col-12 col-md-6 col-lg-4 d-flex flex-column align-items-center">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative text-white w-100" 
                 style="max-width: 380px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); border: 1px solid rgba(255, 255, 255, 0.15) !important;">
                
                <div class="card-body p-3 position-relative">
                    <!-- Header: Brand & Contactless Icon -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold fs-6 tracking-wider text-warning" style="letter-spacing: 1.5px;">TIDONG®</span>
                            <span class="badge bg-warning text-dark fw-bold ms-2 px-2 py-0.5" style="font-size: 0.6rem;">CREDIT</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-wifi text-light opacity-75"></i>
                        </div>
                    </div>

                    <!-- EMV Chip & Facility Info -->
                    <div class="d-flex justify-content-between align-items-center my-2">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Simulated EMV Metallic Chip -->
                            <div class="rounded-2 bg-warning bg-gradient p-1 border border-dark shadow-sm" style="width: 32px; height: 24px;">
                                <div class="border-bottom border-dark opacity-50 h-50"></div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-uppercase text-light opacity-75 d-block" style="font-size: 0.6rem;">Credit Limit</span>
                            <span class="fw-bold text-warning fs-6">₹ {{ format_indian_currency(($wallet->credit_limit ?? 500000) - ($wallet->credit_used ?? 0)) }}</span>
                        </div>
                    </div>

                    <!-- Card Number (Embossed Look) -->
                    <div class="my-2 py-0.5">
                        <span class="text-uppercase text-light opacity-50 d-block mb-0.5" style="font-size: 0.55rem;">Card Number</span>
                        <div class="font-monospace fs-6 text-light tracking-widest fw-bold opacity-90" style="letter-spacing: 2px; text-shadow: 1px 1px 2px #000;">
                            **** **** **** {{ substr(Auth::user()->mobile ?? '2537', -4) }}
                        </div>
                    </div>

                    <!-- Footer: Card Holder & Activation Status Badge -->
                    <div class="d-flex justify-content-between align-items-end mt-2 pt-2 border-top border-secondary border-opacity-25">
                        <div>
                            <span class="text-uppercase text-light opacity-50 d-block" style="font-size: 0.55rem;">Card Holder</span>
                            <span class="fw-bold text-light font-monospace text-uppercase small" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                {{ Auth::user()->name }}
                            </span>
                        </div>
                        <div class="text-end">
                            @if($cardStatus === 'active')
                                <span class="badge bg-success text-white border border-success px-2 py-1" style="font-size: 0.6rem;">
                                    <i class="fas fa-check-circle me-1"></i> Active
                                </span>
                            @else
                                <span class="badge bg-warning text-dark border border-warning px-2 py-1" style="font-size: 0.6rem;">
                                    <i class="fas fa-clock me-1"></i> Waiting
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Activation Progress Bar Section -->
            <div class="bg-white p-3 rounded-4 shadow-sm mt-3 border w-100" style="max-width: 380px;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small font-bold text-dark" style="font-size: 0.8rem;"><i class="fas fa-tasks text-warning me-1"></i> Card Unlocking Progress</span>
                    <span class="small font-bold text-primary" style="font-size: 0.8rem;">{{ $cardProgress }}%</span>
                </div>
                <div class="progress rounded-pill style-progress mb-2" style="height: 8px; background-color: #e9ecef;">
                    <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated rounded-pill" 
                         role="progressbar" 
                         style="width: {{ $cardProgress }}%;" 
                         aria-valuenow="{{ $cardProgress }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.7rem;">
                    <span><i class="fas fa-info-circle me-1"></i> Complete verifications & tasks</span>
                    <span class="badge bg-primary-subtle text-primary fw-bold">Target: 100%</span>
                </div>
            </div>

            <!-- Direct Pay Button -->
            <div class="text-center mt-3 w-100" style="max-width: 380px;">
                @if($cardStatus === 'active')
                    <button class="btn btn-warning text-dark font-bold rounded-pill px-4 shadow-sm w-100 py-2">
                        <i class="fas fa-shopping-bag me-1"></i> Pay Vendor Direct (₹ 5 Lakh Limit)
                    </button>
                @else
                    <button class="btn btn-secondary text-white font-bold rounded-pill px-4 shadow-sm w-100 py-2 opacity-75" disabled style="font-size: 0.85rem;">
                        <i class="fas fa-lock me-1"></i> Card Locked (Activation Pending)
                    </button>
                @endif
            </div>
        </div>
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
                    <h2 class="fw-bold mb-3">₹ {{ format_indian_currency($wallet->non_withdrawable_balance ?? 0) }}</h2>
                    <span class="small opacity-90"><i class="fas fa-shield-alt me-1"></i> Guaranteed reward balance (Zero daily deduction policy active)</span>
                </div>
            </div>
        </div>

        <!-- 2. Withdrawable Balance Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4 p-4 h-100">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold opacity-75">Withdrawable Bal.</h6>
                    <h2 class="fw-bold mb-3">₹ {{ format_indian_currency($wallet->real_balance ?? 0) }}</h2>
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
                    <h2 class="fw-bold mb-3"><i class="fas fa-coins text-warning me-1"></i> {{ format_indian_currency($wallet->t_coins ?? 0) }}</h2>
                    <div class="badge bg-white text-success fw-bold rounded-pill px-3 py-2 fs-6 shadow-sm">
                        <i class="fas fa-check-circle text-success me-1"></i> 1 T-Coin = 1 RS
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rules Box (Updated: Removed Daily Deduction Box) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light border-start border-4 border-warning">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-info-circle text-warning me-2"></i> Important Rules: TDS, Admin Deductions & Limits</h5>
            <div class="row g-4 small text-dark">
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded-3 border h-100">
                        <h6 class="fw-bold text-danger"><i class="fas fa-percent me-1"></i> TDS & Admin Deductions</h6>
                        <p class="mb-1">The following deductions apply upon amount transfer:</p>
                        <ul class="mb-0 ps-3">
                            <li><strong>10%</strong> Admin Service Charge</li>
                            <li><strong>5% TDS</strong> (With PAN) / <strong>20% TDS</strong> (Without PAN)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-white rounded-3 border h-100">
                        <h6 class="fw-bold text-success"><i class="fas fa-receipt me-1"></i> Transaction Criteria</h6>
                        <p class="mb-1">Requirements to unlock Reserved Balance & Credit Facility:</p>
                        <ul class="mb-0 ps-3">
                            <li>Complete 100% Milestone Progress</li>
                            <li>Fulfill all verification & shopping criteria</li>
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
                                        {{ $tx->type == 'credit' ? '+' : '-' }} ₹ {{ format_indian_currency($tx->amount) }}
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
    <div class="card border-0 shadow-sm rounded-4 mb-4">
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
                                        {{ $tc->type == 'credit' ? '+' : '-' }} {{ format_indian_currency($tc->amount) }} T-Coins
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

    <!-- Credit Card Transactions History -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-credit-card text-warning me-2"></i>Credit Card Transactions History</h5>
            
            @if(isset($creditCardTransactions) && $creditCardTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tx ID</th>
                                <th>Vendor / Purpose</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditCardTransactions as $ccTx)
                                <tr>
                                    <td class="small font-monospace">{{ $ccTx->transaction_id ?? 'CC-'.rand(10000,99999) }}</td>
                                    <td>{{ $ccTx->description ?? 'Direct Vendor Payment' }}</td>
                                    <td>
                                        <span class="badge {{ ($ccTx->type ?? 'debit') == 'credit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                            {{ $ccTx->type ?? 'debit' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-danger">
                                        - ₹ {{ format_indian_currency($ccTx->amount ?? 0) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ ($ccTx->status ?? 'success') == 'success' ? 'success' : 'warning' }}">
                                            {{ ucfirst($ccTx->status ?? 'success') }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ date('d M Y, h:i A', strtotime($ccTx->created_at ?? now())) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted my-3">No credit card transaction available till now (Card is in waiting state).</p>
            @endif
        </div>
    </div>
</div>
@endsection