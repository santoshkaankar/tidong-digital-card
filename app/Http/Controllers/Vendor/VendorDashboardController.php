<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Order; // आपके मौजूदा Order मॉडल का सही Namespace

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();

        // आपके Order मॉडल के अनुसार लाइव डेटा Fetch करना
        $totalOrders = Order::count();
        
        $runningOrders = Order::whereIn('status', ['pending', 'running', 'preparing', 'kitchen'])->count();
        
        $completedOrders = Order::where('status', 'completed')->count();
        
        // आज की कुल बिक्री
        $todayCollection = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        return view('vendor.dashboard', compact(
            'totalOrders', 
            'runningOrders', 
            'completedOrders', 
            'todayCollection'
        ));
    }
}