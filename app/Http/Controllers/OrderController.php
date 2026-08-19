<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // 1. Business Owner ke dashboard par apne orders dekhne ke liye
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

        return view('vendor.orders.index', compact('orders'));
    }

    // 2. Order ki details dekhne ke liye
    public function show($id)
    {
        $order = Order::with('orderItems')->where('user_id', Auth::id())->findOrFail($id);
        return view('vendor.orders.show', compact('order'));
    }

    // 3. Order ka status update karne ke liye (e.g., pending to processing or completed)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:running,completed,cancelled'
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    // 4. Order delete karne ke liye
    public function destroy($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $order->orderItems()->delete(); // Pehle items delete honge
        $order->delete();

        return redirect()->back()->with('success', 'Order deleted successfully!');
    }
}