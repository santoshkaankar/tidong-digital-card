<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Order; // Order Model Un-commented
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the logged-in user's orders.
     */
    public function index()
    {
        // Auth user ke orders latest paginate karke fetch karein
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('member.orders.index', compact('orders'));
    }

    /**
     * Display the specified order details.
     */
    public function show($id)
    {
        // Check karein ki order exist karta hai aur wo isi user ka hai
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('member.orders.show', compact('order'));
    }
}