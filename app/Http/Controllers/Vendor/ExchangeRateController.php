<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeRateController extends Controller
{
    // Taxi & Guide Duty Online/Offline Switch
    public function updateDutyStatus(Request $request)
    {
        $user = Auth::user();
        $user->duty_status = $request->status;
        $user->save();

        return back()->with('success', 'Duty status updated to ' . strtoupper($request->status));
    }

    // Money Exchange FX Rates Page
    public function ratesIndex()
    {
        return view('vendor.exchange-rates');
    }

    // Taxi Active Rides Page
    public function taxiRides()
    {
        return view('vendor.taxi-rides');
    }

    // Guide Tour Bookings Page
    public function guideBookings()
    {
        return view('vendor.guide-bookings');
    }
}