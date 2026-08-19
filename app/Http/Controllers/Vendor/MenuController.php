<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorItem;
use App\Models\Vendor\Order;
use App\Models\Vendor\OrderItem;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function createMenu()
    {
        return redirect()->route('catalog');
    }

    public function showPublicMenu($slug, Request $request)
    {
        $business = User::where('slug', $slug)->firstOrFail();
        $location = $request->query('loc', 'Table 1');

        $menuItems = VendorItem::where('user_id', $business->id)
            ->where('status', 'active')
            ->get()
            ->groupBy('category');

        $runningOrder = Order::where('user_id', $business->id)
            ->where('table_or_room', $location)
            ->where('status', 'running')
            ->with('orderItems')
            ->first();

        return view('member.menu', compact('business', 'menuItems', 'location', 'runningOrder'));
    }

    public function placeOrder(Request $request, $slug)
    {
        $request->validate([
            'location' => 'required|string',
            'items' => 'required|array',
        ]);

        $business = User::where('slug', $slug)->firstOrFail();
        $location = $request->location;

        $order = Order::firstOrCreate(
            [
                'user_id' => $business->id,
                'table_or_room' => $location,
                'status' => 'running'
            ],
            [
                'payment_status' => 'pending',
                'total_amount' => 0
            ]
        );

        $whatsappMessage = "New Order from *{$location}*:%0A";
        $totalAddAmount = 0;

        foreach ($request->items as $itemId => $qty) {
            if ($qty > 0) {
                $vendorItem = VendorItem::findOrFail($itemId);
                $itemPrice = $vendorItem->sale_price ?? $vendorItem->price ?? 0;
                $itemTotal = $itemPrice * $qty;
                $totalAddAmount += $itemTotal;

                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('item_name', $vendorItem->item_name)
                    ->first();

                if ($orderItem) {
                    $orderItem->quantity += $qty;
                    $orderItem->save();
                } else {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $vendorItem->id,
                        'item_name' => $vendorItem->item_name,
                        'quantity' => $qty,
                        'price' => $itemPrice
                    ]);
                }

                $whatsappMessage .= "- {$vendorItem->item_name} (Qty: {$qty}) - ₹{$itemTotal}%0A";
            }
        }

        $order->total_amount += $totalAddAmount;
        $order->save();

        $whatsappMessage .= "%0ATotal Bill so far: ₹{$order->total_amount}*%0A";
        
        $whatsappNumber = $business->whatsapp ?? '919999999999'; 
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . $whatsappMessage;

        return redirect($whatsappUrl);
    }

    public function completeOrder(Request $request, $orderId)
    {
        $request->validate([
            'payment_mode' => 'required|in:cash,online'
        ]);

        $order = Order::with('user')->findOrFail($orderId);
        $order->status = 'completed';
        $order->payment_mode = $request->payment_mode;
        $order->payment_status = $request->payment_mode == 'cash' ? 'pending_cash' : 'paid_online';
        $order->save();

        $loc = $order->table_or_room;
        $mode = strtoupper($request->payment_mode);
        $amount = $order->total_amount;

        $whatsappNumber = $order->user->whatsapp ?? '919999999999';
        $msg = "Bill Completed for *{$loc}*!%0APayment Mode: *{$mode}*%0ATotal Amount: *₹{$amount}*%0Aकृपया भुगतान verify करें।";
        
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . $msg;

        return redirect($whatsappUrl);
    }
}