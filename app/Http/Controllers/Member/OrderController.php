<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Agar aapka Order model kisi specific namespace mein hai toh use yahan use karein, jaise:
// use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        // Agar aapke paas Order model hai, toh aap is tarah data fetch kar sakte hain:
        // $orders = Order::where('user_id', auth()->id())->latest()->paginate(10);
        
        // Filhal testing ke liye empty pagination ya collection pass kar rahe hain:
        $orders = collect(); // Jab database table ban jaye tab model query use karein

        return view('member.orders.index', compact('orders'));
    }

    public function show($id)
    {
        // $order = Order::findOrFail($id);
        return view('member.orders.show', compact('id'));
    }
}