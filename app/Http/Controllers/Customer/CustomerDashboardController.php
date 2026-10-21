<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerMarketplace;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request, CustomerMarketplace $marketplace)
    {
        $user = $request->user();

        return view('customer.dashboard', [
            'hasVendor' => $user->vendor()->exists(),
            'addressCount' => $user->customerAddresses()->count(),
            'categories' => $marketplace->categories(),
        ]);
    }
}
