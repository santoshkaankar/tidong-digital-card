<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(private readonly PosOrderService $pos) {}

    public function index(Request $request): View
    {
        $businesses = DB::table('businesses as bu')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('ve.user_id', $request->user()->id)
            ->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
            ->whereIn('bu.business_type', PosOrderService::BUSINESS_TYPES)
            ->select('bu.id', 'bu.name', 'bu.status', 'bu.is_accepting_orders', 've.status as vendor_status')
            ->orderBy('bu.name')->get();
        $branches = DB::table('branches as br')
            ->join('businesses as bu', 'bu.id', '=', 'br.business_id')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('ve.user_id', $request->user()->id)
            ->whereNull('ve.deleted_at')->whereNull('bu.deleted_at')->whereNull('br.deleted_at')
            ->whereIn('bu.business_type', PosOrderService::BUSINESS_TYPES)
            ->select('br.id', 'br.business_id', 'br.name', 'br.status', 'br.is_accepting_orders')
            ->orderBy('br.name')->get()->groupBy('business_id');
        return view('pos.index', compact('businesses', 'branches'));
    }

    public function showBranch(Request $request, int $branch): View
    {
        $branchRecord = $this->pos->ownedBranch($request, $branch);
        abort_unless(in_array($branchRecord->business_type, PosOrderService::BUSINESS_TYPES, true), 404);
        $items = DB::table('pos_menu_items')->where('branch_id', $branch)->orderBy('name')->get();
        $orders = DB::table('pos_orders')->where('branch_id', $branch)->orderByDesc('id')->limit(30)->get();
        $diningTables = DB::table('restaurant_dining_tables')->where('branch_id',$branch)->where('is_active',true)->orderBy('label')->get();
        // Aggregate across all orders, not just the 30 recent rows shown below.
        $orderStats = DB::table('pos_orders')->where('branch_id', $branch)
            ->selectRaw("COUNT(*) AS total_orders, SUM(CASE WHEN status IN ('received', 'preparing') THEN 1 ELSE 0 END) AS active_orders, COALESCE(SUM(CASE WHEN status <> 'cancelled' THEN total_paise ELSE 0 END), 0) AS order_value_paise")
            ->first();
        $stats = [
            'items' => $items->count(),
            'active_items' => $items->where('is_active', true)->count(),
            'orders' => (int) ($orderStats->total_orders ?? 0),
            'active_orders' => (int) ($orderStats->active_orders ?? 0),
            'order_value_paise' => (int) ($orderStats->order_value_paise ?? 0),
        ];
        $navBusiness = (object) [
            'id' => $branchRecord->business_id,
            'name' => $branchRecord->business_name,
            'business_type' => $branchRecord->business_type,
            'status' => $branchRecord->business_status,
        ];
        $navBranch = (object) ['id' => $branchRecord->id, 'name' => $branchRecord->name];
        $navBusinesses = DB::table('businesses')->where('vendor_id', $branchRecord->vendor_id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        return view('pos.branch', compact('branchRecord', 'items', 'orders', 'diningTables',
            'stats', 'navBusiness', 'navBranch', 'navBusinesses'));
    }

    public function addMenuItem(Request $request, int $branch): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'price' => ['required', 'string', 'regex:/^\d{1,7}(\.\d{1,2})?$/'],
        ]);
        if (preg_match('/\b(thali|combo|platter|meal\s*deal)\b/i', $data['name'])) {
            throw ValidationException::withMessages(['name'=>'Thali/Combo will be created using the future merge system.']);
        }
        $paise = $this->pos->rupeesToPaise($data['price']);
        DB::transaction(function () use ($request, $branch, $data, $paise): void {
            $branchRecord = $this->pos->ownedBranch($request, $branch, true);
            $this->pos->ensureApproved($branchRecord);
            $id = DB::table('pos_menu_items')->insertGetId([
                'branch_id' => $branch, 'name' => trim($data['name']),
                'price_paise' => $paise, 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->pos->audit($request, 'pos.menu.created', 'pos_menu_item', $id,
                ['branch_id' => $branch, 'name' => trim($data['name']), 'price_paise' => $paise]);
        });
        return back()->with('success', 'Menu item added.');
    }

    public function menuAvailability(Request $request, int $item): RedirectResponse
    {
        $data = $request->validate(['active' => ['required', 'boolean']]);
        DB::transaction(function () use ($request, $item, $data): void {
            $itemReference = DB::table('pos_menu_items')->where('id', $item)->first();
            abort_unless($itemReference, 404);
            // Lock Branch before Menu item, matching new-order lock ordering.
            $branch = $this->pos->ownedBranch($request, (int) $itemReference->branch_id, true);
            $menu = DB::table('pos_menu_items')->where('id', $item)
                ->where('branch_id', $branch->id)->lockForUpdate()->firstOrFail();
            $this->pos->ensureApproved($branch);
            DB::table('pos_menu_items')->where('id', $item)->update([
                'is_active' => (bool) $data['active'], 'updated_at' => now(),
            ]);
            // If the last active item is turned off, stop this Branch taking new orders.
            if (! $data['active'] && ! DB::table('pos_menu_items')
                ->where('branch_id', $menu->branch_id)->where('is_active', true)->exists()) {
                DB::table('branches')->where('id', $menu->branch_id)->update([
                    'is_accepting_orders' => false, 'updated_at' => now(),
                ]);
            }
            $this->pos->audit($request, 'pos.menu.availability_changed', 'pos_menu_item', $item,
                ['is_active' => (bool) $data['active']], ['is_active' => (bool) $menu->is_active]);
        });
        return back()->with('success', 'Menu availability updated.');
    }

    public function businessAccepting(Request $request, int $business): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        DB::transaction(function () use ($request, $business, $data): void {
            $record = $this->pos->ownedBusiness($request, $business, true);
            if ($data['enabled']) {
                $this->pos->ensureApproved($record);
                $hasActiveBranch = DB::table('branches')->where('business_id', $business)
                    ->where('status', 'active')->whereNull('deleted_at')->exists();
                if (! $hasActiveBranch) {
                    throw ValidationException::withMessages(['business' => 'Add an active Branch before enabling orders.']);
                }
            }
            DB::table('businesses')->where('id', $business)->update([
                'is_accepting_orders' => (bool) $data['enabled'], 'updated_at' => now(),
            ]);
            if (! $data['enabled']) {
                DB::table('branches')->where('business_id', $business)->update([
                    'is_accepting_orders' => false, 'updated_at' => now(),
                ]);
            }
            $this->pos->audit($request, 'pos.business.accepting_changed', 'business', $business,
                ['is_accepting_orders' => (bool) $data['enabled']],
                ['is_accepting_orders' => (bool) $record->is_accepting_orders]);
        });
        return back()->with('success', 'Business order setting updated.');
    }

    public function branchAccepting(Request $request, int $branch): RedirectResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean']]);
        DB::transaction(function () use ($request, $branch, $data): void {
            $record = $this->pos->ownedBranch($request, $branch, true);
            if ($data['enabled']) {
                $this->pos->ensureApproved($record);
                if ($record->branch_status !== 'active' || ! $record->business_accepting) {
                    throw ValidationException::withMessages(['branch' => 'Activate the Business and active Branch first.']);
                }
                if (! DB::table('pos_menu_items')->where('branch_id', $branch)->where('is_active', true)->exists()) {
                    throw ValidationException::withMessages(['branch' => 'Add an active Menu item before enabling this Branch.']);
                }
            }
            DB::table('branches')->where('id', $branch)->update([
                'is_accepting_orders' => (bool) $data['enabled'], 'updated_at' => now(),
            ]);
            $this->pos->audit($request, 'pos.branch.accepting_changed', 'branch', $branch,
                ['is_accepting_orders' => (bool) $data['enabled']],
                ['is_accepting_orders' => (bool) $record->branch_accepting]);
        });
        return back()->with('success', 'Branch order setting updated.');
    }

    public function placeOrder(Request $request, int $branch): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:25'],
            'items.*.menu_item_id' => ['required', 'integer', 'min:1', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:500'],
            'dining_table_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $data['order_origin'] = ! empty($data['dining_table_id']) ? 'waiter' : 'counter';
        $id = $this->pos->createOrder($request, $branch, $data);
        return redirect()->route('pos.branches.show', $branch)
            ->with('success', 'Order #'.$id.' placed. Payment remains UNPAID.');
    }

    public function cancelOrder(Request $request, int $order): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);
        DB::transaction(function () use ($request, $order, $data): void {
            $row = DB::table('pos_orders')->where('id', $order)->lockForUpdate()->first();
            abort_unless($row, 404);
            $this->pos->ownedBranch($request, (int) $row->branch_id, true);
            if (! in_array($row->status, ['received', 'preparing'], true)) {
                throw ValidationException::withMessages(['order' => 'Only received/preparing Orders can be cancelled.']);
            }
            DB::table('pos_orders')->where('id', $order)->update([
                'status' => 'cancelled', 'updated_at' => now(),
            ]);
            $this->pos->audit($request, 'pos.order.cancelled', 'pos_order', $order,
                ['status' => 'cancelled'], ['status' => $row->status], $data['reason']);
        });
        return back()->with('success', 'Order cancelled. Original record was retained.');
    }
}
