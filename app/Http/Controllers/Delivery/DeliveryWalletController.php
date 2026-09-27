<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryWalletController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Dedicated Wallet Summary Get or Create
        $wallet = DB::table('delivery_wallets')->where('user_id', $userId)->first();

        if (!$wallet) {
            DB::table('delivery_wallets')->insert([
                'user_id'         => $userId,
                'earning_balance' => 0.00,
                'cash_in_hand'    => 0.00,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $wallet = DB::table('delivery_wallets')->where('user_id', $userId)->first();
        }

        // Transactions History
        $transactions = DB::table('delivery_wallet_transactions')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('delivery.wallet.index', compact('wallet', 'transactions'));
    }

    // 1. COD Cash Deposit to Tidong (Company)
    public function depositCash(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'transaction_ref' => 'nullable|string|max:100'
        ]);

        $userId = auth()->id();
        $wallet = DB::table('delivery_wallets')->where('user_id', $userId)->first();

        if (!$wallet || $wallet->cash_in_hand < $request->amount) {
            return back()->with('error', 'Entered amount Cash In Hand balance se zyada hai.');
        }

        DB::transaction(function () use ($userId, $request) {
            DB::table('delivery_wallets')
                ->where('user_id', $userId)
                ->decrement('cash_in_hand', $request->amount);

            DB::table('delivery_wallet_transactions')->insert([
                'user_id'     => $userId,
                'order_id'    => null,
                'type'        => 'cod_cash_settled',
                'amount'      => $request->amount,
                'description' => 'Cash Deposited to Tidong' . ($request->transaction_ref ? " (Txn: {$request->transaction_ref})" : ''),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return back()->with('success', 'Cash payment record ho gaya hai!');
    }

    // 2. Earnings Withdrawal / Payout Request (Bank/UPI me lene ke liye)
    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'upi_id' => 'required|string|max:100'
        ]);

        $userId = auth()->id();
        $wallet = DB::table('delivery_wallets')->where('user_id', $userId)->first();

        if (!$wallet || $wallet->earning_balance < $request->amount) {
            return back()->with('error', 'Earning balance kam hai.');
        }

        DB::transaction(function () use ($userId, $request) {
            // Earning balance se minus karein
            DB::table('delivery_wallets')
                ->where('user_id', $userId)
                ->decrement('earning_balance', $request->amount);

            // Log Transaction
            DB::table('delivery_wallet_transactions')->insert([
                'user_id'     => $userId,
                'order_id'    => null,
                'type'        => 'admin_payout',
                'amount'      => $request->amount,
                'description' => "Payout Request Sent to Admin (UPI: {$request->upi_id})",
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        });

        return back()->with('success', 'Payout Request Admin ko bhej di gayi hai!');
    }
}