<?php

namespace App\Http\Controllers\Vendor\Money_Exchange;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Money_Exchange\MoneyExchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeRateController extends Controller
{
    public function dashboard()
    {
        $vendor = Auth::user();
        $exchange = MoneyExchange::where('user_id', $vendor->id)->first();

        return view('vendor.money_exchange.dashboard', compact('vendor', 'exchange'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exchange_name' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
        ]);

        MoneyExchange::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'exchange_name' => $request->exchange_name,
                'exchange_slug' => \Illuminate\Support\Str::slug($request->exchange_name),
                'license_number' => $request->license_number,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'status' => 'active',
            ]
        );

        return redirect()->back()->with('success', 'Money Exchange details updated successfully.');
    }
}