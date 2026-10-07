<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Restaurant\RestaurantOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DeliveryOrderController extends Controller
{
    /**
     * Show available orders ready for delivery & active orders assigned to rider
     */
    public function index()
    {
        $deliveryBoyId = Auth::id();

        // 1. Available Pickup Orders (Status: ready & Order Type: delivery)
        $availableOrders = RestaurantOrder::with(['items', 'restaurant'])
            ->where('order_type', 'delivery')
            ->where('status', 'ready')
            ->whereNull('delivery_boy_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Active Orders assigned to this rider
        $activeOrders = RestaurantOrder::with(['items', 'restaurant'])
            ->where('delivery_boy_id', $deliveryBoyId)
            ->whereIn('status', ['accepted', 'pickedup', 'picked_up', 'on_the_way', 'out_for_delivery'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('delivery.orders.index', compact('availableOrders', 'activeOrders'));
    }

    /**
     * Accept Order by Delivery Partner & Generate Delivery OTP
     */
    public function accept($id)
    {
        try {
            $order = RestaurantOrder::where('id', $id)
                ->where('status', 'ready')
                ->whereNull('delivery_boy_id')
                ->first();

            if (!$order) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order is no longer available or already taken.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Order is no longer available or already taken.');
            }

            // Assign Delivery Partner
            $order->delivery_boy_id = Auth::id();
            $order->status = 'out_for_delivery';

            // Generate Pickup OTP if not generated yet (4 Digits)
            if (empty($order->pickup_otp)) {
                $order->pickup_otp = rand(1000, 9999);
            }

            // Generate Delivery OTP for Customer Verification (4 Digits)
            if (empty($order->delivery_otp)) {
                $order->delivery_otp = rand(1000, 9999);
            }

            $order->save();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order accepted successfully!',
                    'redirect_url' => route('delivery.orders.index')
                ]);
            }

            return redirect()->route('delivery.orders.index')->with('success', 'Order accepted successfully! Delivery OTP generated.');

        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error accepting order: ' . $e->getMessage());
        }
    }

    /**
     * Verify Pickup OTP from Restaurant
     */
    public function verifyPickupOtp(Request $request, $id)
    {
        $request->validate([
            'pickup_otp' => 'required|digits:4'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', Auth::id())
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        if ($order->pickup_otp && $order->pickup_otp != $request->pickup_otp) {
            return redirect()->back()->with('error', 'Invalid Pickup OTP! Please check with store.');
        }

        $order->status = 'pickedup';
        $order->save();

        return redirect()->route('delivery.orders.index')->with('success', 'Order picked up successfully!');
    }

    /**
     * Update Live Status (On the way / Delivered)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pickedup,on_the_way,out_for_delivery,delivered,completed'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', Auth::id())
            ->first();

        if (!$order) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
            return redirect()->back()->with('error', 'Order not found.');
        }

        $order->status = $request->status;
        if (in_array($request->status, ['delivered', 'completed'])) {
            $order->payment_status = 'paid';
        }
        $order->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated to ' . $order->status
            ]);
        }

        return redirect()->route('delivery.orders.index')->with('success', 'Order status updated successfully!');
    }

    /**
     * Complete Order with Delivery OTP Verification
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'delivery_otp' => 'required|digits:4'
        ]);

        $order = RestaurantOrder::where('id', $id)
            ->where('delivery_boy_id', Auth::id())
            ->first();

        if (!$order) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Verify Customer Delivery OTP
        if ($order->delivery_otp && $order->delivery_otp != $request->delivery_otp) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid Customer Delivery OTP!'], 422);
            }
            return redirect()->back()->with('error', 'Invalid Delivery OTP! Please ask customer for correct 4-digit code.');
        }

        // Complete Order & Update Payment
        $order->status = 'completed';
        $order->payment_status = 'paid';
        $order->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order delivered and completed successfully!'
            ]);
        }

        return redirect()->route('delivery.orders.index')->with('success', 'Order delivered & completed successfully!');
    }

    /**
     * Order History
     */
    public function history()
    {
        $completedOrders = RestaurantOrder::where('delivery_boy_id', Auth::id())
            ->whereIn('status', ['delivered', 'completed'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('delivery.orders.history', compact('completedOrders'));
    }

    /**
     * Manual Assign Delivery Boy by Vendor
     */
    public function assignDeliveryBoyManual(Request $request, $id)
    {
        $request->validate([
            'delivery_boy_id' => 'required|exists:users,id'
        ]);

        $order = RestaurantOrder::where('user_id', Auth::id())->findOrFail($id);
        $order->delivery_boy_id = $request->delivery_boy_id;
        $order->status = 'out_for_delivery';
        $order->save();

        return redirect()->back()->with('success', __('Delivery boy assigned successfully!'));
    }

    /**
     * Auto Assign Delivery Boy by Vendor
     */
    public function assignDeliveryBoyAuto($id)
    {
        $order = RestaurantOrder::where('user_id', Auth::id())->findOrFail($id);

        $availableBoy = User::where('role', 'delivery')
            ->where('duty_status', 'online')
            ->first();

        if (!$availableBoy) {
            // Agar koi online nahi ho toh first available delivery boy fallback assign kar dega
            $availableBoy = User::where('role', 'delivery')->first();
        }

        if (!$availableBoy) {
            return redirect()->back()->with('error', __('No delivery boy available right now!'));
        }

        $order->delivery_boy_id = $availableBoy->id;
        $order->status = 'out_for_delivery';
        $order->save();

        return redirect()->back()->with('success', __('Delivery boy auto-assigned successfully!'));
    }
}