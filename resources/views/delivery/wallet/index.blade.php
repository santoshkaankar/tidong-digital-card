@extends('delivery.partials.layout')

@section('content')
<div class="max-w-2xl mx-auto px-3 sm:px-4 py-4 space-y-4" x-data="{ showDepositModal: false, showPayoutModal: false }">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-semibold rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-rose-100 border border-rose-300 text-rose-800 text-xs font-semibold rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">My Delivery Wallet</h2>
        <span class="text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold px-2.5 py-1 rounded-full">Dedicated Wallet</span>
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-2 gap-3">
        <!-- 1. Earnings Card & Settlement Button -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-4 rounded-2xl text-white shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-emerald-100 text-[11px] font-semibold">
                    <span>Total Earnings</span>
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="text-xl font-black mt-2">₹{{ number_format($wallet->earning_balance ?? 0, 2) }}</div>
                <p class="text-[10px] text-emerald-100/80 mt-1">Ready for settlement</p>
            </div>

            @if(($wallet->earning_balance ?? 0) > 0)
                <button @click="showPayoutModal = true" class="mt-3 w-full bg-white text-emerald-700 font-bold text-[11px] py-1.5 px-2 rounded-xl shadow-xs hover:bg-emerald-50 transition">
                    Withdraw Earnings
                </button>
            @endif
        </div>

        <!-- 2. COD Cash Card & Settlement Button -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-4 rounded-2xl text-white shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-amber-100 text-[11px] font-semibold">
                    <span>Cash In Hand (COD)</span>
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <div class="text-xl font-black mt-2">₹{{ number_format($wallet->cash_in_hand ?? 0, 2) }}</div>
                <p class="text-[10px] text-amber-100/80 mt-1">Payable to company</p>
            </div>

            @if(($wallet->cash_in_hand ?? 0) > 0)
                <button @click="showDepositModal = true" class="mt-3 w-full bg-white text-orange-600 font-bold text-[11px] py-1.5 px-2 rounded-xl shadow-xs hover:bg-amber-50 transition">
                    Pay / Deposit Cash
                </button>
            @endif
        </div>
    </div>

    <!-- History / Passbook -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Wallet History</h3>
            <span class="text-[10px] text-slate-400">Real-time log</span>
        </div>

        <div class="divide-y divide-slate-100 text-xs">
            @forelse($transactions as $trx)
                <div class="p-3.5 flex items-center justify-between">
                    <div class="space-y-0.5">
                        <div class="font-bold text-slate-800">{{ $trx->description }}</div>
                        <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, h:i A') }}</div>
                    </div>

                    <div class="text-right">
                        @if($trx->type == 'earning_credit')
                            <span class="font-extrabold text-emerald-600">+₹{{ number_format($trx->amount, 2) }}</span>
                            <div class="text-[9px] font-bold text-emerald-700 uppercase">Earning</div>
                        @elseif($trx->type == 'admin_payout')
                            <span class="font-extrabold text-indigo-600">-₹{{ number_format($trx->amount, 2) }}</span>
                            <div class="text-[9px] font-bold text-indigo-700 uppercase">Payout Request</div>
                        @elseif($trx->type == 'cod_cash_collected')
                            <span class="font-extrabold text-amber-600">+₹{{ number_format($trx->amount, 2) }}</span>
                            <div class="text-[9px] font-bold text-amber-700 uppercase">COD Cash</div>
                        @elseif($trx->type == 'cod_cash_settled')
                            <span class="font-extrabold text-rose-600">-₹{{ number_format($trx->amount, 2) }}</span>
                            <div class="text-[9px] font-bold text-rose-700 uppercase">Paid to Tidong</div>
                        @else
                            <span class="font-extrabold text-slate-700">₹{{ number_format($trx->amount, 2) }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-400 font-medium">
                    No wallet transactions recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal 1: Deposit Cash (COD) -->
    <div x-show="showDepositModal" class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-sm rounded-2xl p-5 space-y-4 shadow-xl" @click.away="showDepositModal = false">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Pay Cash to Tidong</h3>
                <button @click="showDepositModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-xmark"></i></button>
            </div>
            <form action="{{ route('delivery.wallet.deposit') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" max="{{ $wallet->cash_in_hand ?? 0 }}" value="{{ $wallet->cash_in_hand ?? 0 }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Txn Ref / UPI ID (Optional)</label>
                    <input type="text" name="transaction_ref" placeholder="e.g. UPI Ref 82371923" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="pt-2 flex gap-2">
                    <button type="button" @click="showDepositModal = false" class="w-1/2 py-2 text-xs font-bold text-slate-600 bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="w-1/2 py-2 text-xs font-bold text-white bg-orange-600 rounded-xl">Confirm Pay</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Withdraw Earnings -->
    <div x-show="showPayoutModal" class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white w-full max-w-sm rounded-2xl p-5 space-y-4 shadow-xl" @click.away="showPayoutModal = false">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Request Earnings Withdrawal</h3>
                <button @click="showPayoutModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-xmark"></i></button>
            </div>
            <form action="{{ route('delivery.wallet.payout') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Withdraw Amount (₹)</label>
                    <input type="number" step="0.01" name="amount" max="{{ $wallet->earning_balance ?? 0 }}" value="{{ $wallet->earning_balance ?? 0 }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Your UPI ID / GPay Number</label>
                    <input type="text" name="upi_id" placeholder="e.g. 98290XXXXX@paytm" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="pt-2 flex gap-2">
                    <button type="button" @click="showPayoutModal = false" class="w-1/2 py-2 text-xs font-bold text-slate-600 bg-slate-100 rounded-xl">Cancel</button>
                    <button type="submit" class="w-1/2 py-2 text-xs font-bold text-white bg-emerald-600 rounded-xl">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection