<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorItem;
use App\Models\Vendor\Order;
use App\Models\Vendor\OrderItem;
use App\Models\Vendor\VendorCategory;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // Current Hour ke basis par time_slot detect karna
        $currentHour = Carbon::now()->hour;
        $currentSlot = 'all_day';

        if ($currentHour >= 5 && $currentHour < 12) {
            $currentSlot = 'morning';
        } elseif ($currentHour >= 12 && $currentHour < 17) {
            $currentSlot = 'afternoon';
        } else {
            $currentSlot = 'evening';
        }

        // Time slot ke anusar categories ka order set karna
        $categoriesOrder = VendorCategory::where('user_id', $business->id)
            ->orderByRaw("
                CASE 
                    WHEN time_slot = '{$currentSlot}' THEN 1 
                    WHEN time_slot = 'all_day' THEN 2 
                    ELSE 3 
                END
            ")
            ->pluck('name')
            ->toArray();

        // Items fetch karke category ke hisab se group karna
        $rawMenuItems = VendorItem::where('user_id', $business->id)
            ->where('status', 'active')
            ->get()
            ->groupBy('category');

        // Categories ko sorted order mein arrange karna
        $menuItems = collect();
        foreach ($categoriesOrder as $catName) {
            if ($rawMenuItems->has($catName)) {
                $menuItems->put($catName, $rawMenuItems->get($catName));
            }
        }

        // Agar koi remaining categories bachi ho unhe last me add karna
        foreach ($rawMenuItems as $catName => $items) {
            if (!$menuItems->has($catName)) {
                $menuItems->put($catName, $items);
            }
        }

        // Running Order Check (Table/Room)
        $runningOrder = Order::where('user_id', $business->id)
            ->where('table_or_room', $location)
            ->where('status', 'running')
            ->with('orderItems')
            ->first();

        return view('member.menu', compact('business', 'menuItems', 'location', 'runningOrder', 'currentSlot'));
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