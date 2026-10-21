<?php

namespace App\Http\Controllers\Kds;

use App\Http\Controllers\Controller;
use App\Services\Pos\PosOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KdsController extends Controller
{
    public function __construct(private readonly PosOrderService $pos) {}

    public function index(Request $request): View
    {
        $branches = DB::table('branches as br')
            ->join('businesses as bu', 'bu.id', '=', 'br.business_id')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('ve.user_id', $request->user()->id)
            ->whereNull('br.deleted_at')->whereNull('bu.deleted_at')->whereNull('ve.deleted_at')
            ->whereIn('bu.business_type', PosOrderService::BUSINESS_TYPES)
            ->select('br.id', 'br.name', 'bu.name as business_name')
            ->orderBy('br.name')->get();
        return view('kds.index', compact('branches'));
    }

    public function showBranch(Request $request, int $branch): View
    {
        $branchRecord = $this->pos->ownedBranch($request, $branch);
        abort_unless(in_array($branchRecord->business_type, PosOrderService::BUSINESS_TYPES, true), 404);
        $orders = DB::table('pos_orders as po')->leftJoin('restaurant_dining_tables as t','t.id','=','po.dining_table_id')
            ->where('po.branch_id', $branch)
            ->whereIn('po.status', ['received', 'preparing', 'ready'])
            ->select('po.*','t.label as dining_table_label')
            ->orderBy('po.placed_at')->limit(100)->get();
        $items = DB::table('pos_order_items')->whereIn('order_id', $orders->pluck('id'))
            ->orderBy('id')->get()->groupBy('order_id');
        return view('kds.branch', compact('branchRecord', 'orders', 'items'));
    }

    public function updateStatus(Request $request, int $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:preparing,ready,served']]);
        DB::transaction(function () use ($request, $order, $data): void {
            $row = DB::table('pos_orders')->where('id', $order)->lockForUpdate()->first();
            abort_unless($row, 404);
            $branch = $this->pos->ownedBranch($request, (int) $row->branch_id, true);
            $this->pos->ensureApproved($branch);
            $next = ['received' => 'preparing', 'preparing' => 'ready', 'ready' => 'served'];
            if (($next[$row->status] ?? null) !== $data['status']) {
                throw ValidationException::withMessages(['status' => 'Invalid Kitchen status transition.']);
            }
            DB::table('pos_orders')->where('id', $order)->update([
                'status' => $data['status'], 'updated_at' => now(),
            ]);
            $this->pos->audit($request, 'kds.order.status_changed', 'pos_order', $order,
                ['status' => $data['status']], ['status' => $row->status]);
        });
        return back()->with('success', 'Kitchen order updated.');
    }
}
