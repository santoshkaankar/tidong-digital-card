<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\VendorWallet;
use App\Models\Payment\WalletTransaction;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = auth()->id();

        $wallet = VendorWallet::firstOrCreate(
            ['vendor_id' => $vendorId],
            ['bonus_balance' => 200.00, 'sales_balance' => 0.00]
        );

        $transactions = WalletTransaction::where('vendor_id', $vendorId)
                        ->latest()
                        ->paginate(10);

        return view('payment.index', compact('wallet', 'transactions'));
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $vendorId = auth()->id();
        $wallet = VendorWallet::where('vendor_id', $vendorId)->firstOrFail();

        if ($wallet->sales_balance < $request->amount) {
            return back()->with('error', 'Aapke sales balance mein itni rashi uplabdh nahi hai.');
        }

        $wallet->decrement('sales_balance', $request->amount);

        WalletTransaction::create([
            'vendor_id' => $vendorId,
            'wallet_type' => 'sales',
            'type' => 'debit',
            'amount' => $request->amount,
            'description' => 'Bank Withdrawal Request Processed',
            'status' => 'success'
        ]);

        return back()->with('success', 'Withdrawal request safalpurvak bhej di gayi hai!');
    }
}