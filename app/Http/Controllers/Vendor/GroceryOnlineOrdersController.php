<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Grocery\GroceryAccess as Access;
use App\Services\Grocery\GroceryFeatures as Features;
use App\Services\Grocery\GroceryOnlineStore as Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Vendor owner-only V2 until delegated employee permissions are integrated by Master. */
final class GroceryOnlineOrdersController extends Controller
{
    public function index(Request $request, int $business): View
    {
        Features::requireEnabled('module');
        $owned = Access::business($request, $business);
        $branches = DB::table('branches')->where('business_id', $business)->whereNull('deleted_at')->orderBy('name')->get();
        $branchId = $request->query('branch');
        $branch = $branchId !== null ? Access::branch($owned, (int) $branchId) : $branches->first();
        $orders = collect();
        if ($branch) {
            Features::requireEnabled('online_orders', $branch->id);
            $orders = DB::table('grocery_online_orders')->where('business_id', $business)
                ->where('branch_id', $branch->id)->orderByDesc('id')->paginate(30)->withQueryString();
        }
        return view('vendor.grocery.online-orders', compact('owned', 'branches', 'branch', 'orders'));
    }

    public function show(Request $request, int $business, int $order): View
    {
        $owned = Access::business($request, $business);
        $data = DB::table('grocery_online_orders')->where('id', $order)->where('business_id', $business)->firstOrFail();
        Access::branch($owned, $data->branch_id);
        Features::requireEnabled('online_orders', $data->branch_id);
        return view('vendor.grocery.online-order', [
            'owned' => $owned, 'order' => $data,
            'items' => DB::table('grocery_online_order_items')->where('order_id', $order)->get(),
            'events' => DB::table('grocery_online_order_events')->where('order_id', $order)->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, int $business, int $order): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'rejected', 'packing', 'ready', 'completed'])],
            'note' => ['nullable', 'string', 'max:500'],
        ]);
        DB::transaction(function () use ($request, $owned, $business, $order, $data): void {
            $current = DB::table('grocery_online_orders')->where('id', $order)
                ->where('business_id', $business)->lockForUpdate()->firstOrFail();
            Access::branch($owned, $current->branch_id);
            Features::requireEnabled('online_orders', $current->branch_id);
            Shop::transition($current, $request->user()->id, $data['status'], $data['note'] ?? '');
            Access::audit($request, 'grocery.online_order.status', 'grocery_online_order', $current->id,
                ['previous' => $current->status, 'next' => $data['status']]);
        }, 3);
        return back()->with('success', 'Order status saved with immutable history.');
    }
}
