<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businesses = $user->vendorBusinesses;
        
        // Active business count aur details
        $totalBusinesses = $businesses->count();
        $activeBusinessId = session('active_business_id');

        return view('vendor.dashboard', compact('user', 'businesses', 'totalBusinesses', 'activeBusinessId'));
    }
}