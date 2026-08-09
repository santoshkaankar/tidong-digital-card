<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BusinessItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    // 1. Business Owner ke liye Menu / Catalog Setup view
    public function createMenu()
    {
        return redirect()->route('business.catalog');
    }

    // 2. Public View: Customer/Guest ke liye Menu (Table/Room auto-detect ke sath) via Slug
    public function showPublicMenu($slug, Request $request)
    {
        // Slug se business find karo
        $business = User::where('slug', $slug)->where('role', 'business')->firstOrFail();
        
        $location = $request->query('loc', 'Table 1'); // Default location agar query mein na ho

        // Us business ki active inventory items fetch karo aur categories ke hisaab se group karo
        $menuItems = BusinessItem::where('user_id', $business->id)
            ->where('status', 'active')
            ->get()
            ->groupBy('category');

        // Check karein ki is table/room ka koi running order hai ya nahi
        $runningOrder = Order::where('user_id', $business->id)
            ->where('table_or_room', $location)
            ->where('status', 'running')
            ->with('orderItems')
            ->first();

        return view('customer.menu', compact('business', 'menuItems', 'location', 'runningOrder'));
    }

    // 3. Order Place ya Update Karna (Beech mein items add karna)
    public function placeOrder(Request $request, $slug)
    {
        $request->validate([
            'location' => 'required|string',
            'items' => 'required|array', // [item_id => quantity]
        ]);

        $business = User::where('slug', $slug)->firstOrFail();
        $location = $request->location;

        // Active running order dhundho ya naya banao
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

        $whatsappMessage = "New Order from *{$location}* (%0A";
        $totalAddAmount = 0;

        foreach ($request->items as $itemId => $qty) {
            if ($qty > 0) {
                $businessItem = BusinessItem::findOrFail($itemId);
                $itemTotal = $businessItem->price * $qty;
                $totalAddAmount += $itemTotal;

                // Check karo kya ye item pehle se order mein hai? Agar hai toh quantity update karo
                $orderItem = OrderItem::where('order_id', $order->id)
                    ->where('item_name', $businessItem->item_name)
                    ->first();

                if ($orderItem) {
                    $orderItem->quantity += $qty;
                    // Note: price column mein aap unit price ya total store kar rahe hain uske hisaab se adjust kar sakte hain
                    $orderItem->save();
                } else {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $businessItem->id, // fallback / mapping reference
                        'item_name' => $businessItem->item_name,
                        'quantity' => $qty,
                        'price' => $businessItem->price
                    ]);
                }

                $whatsappMessage .= "- {$businessItem->item_name} (Qty: {$qty}) - ₹{$itemTotal}%0A";
            }
        }

        // Total amount update karo
        $order->total_amount += $totalAddAmount;
        $order->save();

        $whatsappMessage .= "%0ATotal Bill so far: ₹{$order->total_amount}*%0A";
        
        // Business owner ka whatsapp number agar user table me hai ya fallback
        $whatsappNumber = $business->whatsapp_number ?? '919999999999'; 
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . $whatsappMessage;

        return redirect($whatsappUrl);
    }

    // 4. Order Complete / Bill Generate Karna (Cash or Online)
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

        $whatsappNumber = $order->user->whatsapp_number ?? '919999999999';
        $msg = "Bill Completed for *{$loc}*!%0APayment Mode: *{$mode}*%0ATotal Amount: *₹{$amount}*%0Aकृपया भुगतान verify करें।";
        
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . $msg;

        return redirect($whatsappUrl);
    }
}