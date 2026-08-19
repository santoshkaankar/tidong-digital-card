<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Check if wallet exists, if not create one with 0 balance
        $wallet = DB::table('wallets')->where('user_id', $userId)->first();
        if (!$wallet) {
            DB::table('wallets')->insert([
                'user_id' => $userId,
                'real_balance' => 0.00,
                't_coins' => 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $wallet = DB::table('wallets')->where('user_id', $userId)->first();
        }

        // Fetch Real Money Transactions (PhonePe / UPI)
        $transactions = DB::table('transactions')
                            ->where('user_id', $userId)
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Fetch T-Coin Transactions
        $tCoinTransactions = DB::table('t_coin_transactions')
                                ->where('user_id', $userId)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('member.wallet.index', compact('wallet', 'transactions', 'tCoinTransactions'));
    }
}