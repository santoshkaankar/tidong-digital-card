<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorItem;
use App\Models\Vendor\Order;
use App\Models\Vendor\OrderItem;
use App\Models\Vendor\VendorCategory;
use App\Events\OrderSoundAlert;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VendoritemController extends Controller
{
    public function createMenu()
    {
        return view('vendor.menu.create');
    }

    public function showPublicMenu($slug, Request $request)
    {
        $business = User::where('slug', $slug)->firstOrFail();
        $location = $request->query('loc', 'Table 1');

        // 1. Time slot detect
        $currentHour = Carbon::now()->hour;
        if ($currentHour >= 5 && $currentHour < 12) {
            $currentSlot = 'morning';
        } elseif ($currentHour >= 12 && $currentHour < 17) {
            $currentSlot = 'afternoon';
        } else {
            $currentSlot = 'evening';
        }

        $selectedSlot = $request->query('slot', $currentSlot);

        // 2. Categories Order dynamically fetch
        $categoriesQuery = VendorCategory::where('user_id', $business->id);

        if ($selectedSlot !== 'all') {
            $categoriesQuery->orderByRaw("
                CASE 
                    WHEN time_slot = '{$selectedSlot}' THEN 1 
                    WHEN time_slot = 'all_day' THEN 2 
                    ELSE 3 
                END
            ");
        }

        $categoriesOrder = $categoriesQuery->pluck('name')->toArray();

        // 3. Items fetch karke Category wise arrange karna
        $rawMenuItems = VendorItem::where('user_id', $business->id)
            ->where('status', 'active')
            ->get()
            ->groupBy('category');

        $menuItems = collect();
        foreach ($categoriesOrder as $catName) {
            if ($rawMenuItems->has($catName)) {
                $menuItems->put($catName, $rawMenuItems->get($catName));
            }
        }

        foreach ($rawMenuItems as $catName => $items) {
            if (!$menuItems->has($catName)) {
                $menuItems->put($catName, $items);
            }
        }

        // 4. Active Running Order Check
        $runningOrder = Order::where('user_id', $business->id)
            ->where('table_or_room', $location)
            ->where('status', 'running')
            ->with('orderItems')
            ->first();

        return view('vendor.menu.public', compact('business', 'menuItems', 'location', 'runningOrder', 'currentSlot', 'selectedSlot'));
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

        $isUpdate = !$order->wasRecentlyCreated;
        $whatsappMessage = $isUpdate ? "Order UPDATED for *{$location}*:%0A" : "New Order from *{$location}*:%0A";
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

        // Realtime Kitchen Sound Alert Event Trigger
        $alertText = $isUpdate ? "Order updated for {$location}" : "New order received for {$location}";
        event(new OrderSoundAlert($business->id, $location, $alertText));

        $whatsappMessage .= "%0ATotal Bill so far: *₹{$order->total_amount}*%0A";
        
        $whatsappNumber = $business->whatsapp ?? '919999999999'; 
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . $whatsappMessage;

        return redirect($whatsappUrl);
    }
public function kitchenDashboard()
    {
        // 1. Logged in Vendor ke pending aur running dono orders fetch karein
        $runningOrders = Order::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'running'])
            ->with('orderItems')
            ->latest('updated_at')
            ->get();

        return view('vendor.kitchen.dashboard', compact('runningOrders'));
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