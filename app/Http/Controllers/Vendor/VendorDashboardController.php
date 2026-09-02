<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor\Order;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $runningOrders = Order::whereIn('status', ['pending', 'running', 'preparing', 'kitchen'])->count();
        $completedOrders = Order::where('status', 'completed')->count();
        
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

    // 1. प्रोफ़ाइल व्यू पेज
    public function profile()
    {
        return view('vendor.profile');
    }

    // 2. प्रोफ़ाइल व QR अपडेट लॉजिक
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'payment_qr' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();
        $user->name = $request->name;

        if ($request->hasFile('payment_qr')) {
            $file = $request->file('payment_qr');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('qr_codes', $filename, 'public');
            
            $user->payment_qr = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'प्रोफ़ाइल और Payment QR कोड सफलतापूर्वक अपडेट हो गया है!');
    }
}