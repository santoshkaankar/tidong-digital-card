<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Opening\OutletOpeningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class OutletOpeningController extends Controller
{
    public function __construct(private readonly OutletOpeningService $service) {}

    public function index(Request $request, int $business, int $branch): View
    {
        $outlet = $this->service->ownedOutlet((int) $request->user()->id, $business, $branch);
        $state = DB::table('outlet_opening_states')->where('branch_id', $branch)->first();
        $ledgers = DB::table('finance_accounts as a')
            ->join('finance_ledger_groups as g', 'g.id', '=', 'a.ledger_group_id')
            ->join('outlet_ledger_opening_lines as l', 'l.ledger_id', '=', 'a.id')
            ->where('a.owner_type', 'branch')->where('a.owner_id', $branch)->where('l.branch_id', $branch)
            ->whereNull('a.deleted_at')
            ->select('a.id', 'a.ledger_name', 'a.current_balance', 'a.opening_balance', 'g.group_name',
                'l.direction', 'l.amount_paise')->orderBy('a.ledger_name')->get();
        $existingLedgers = DB::table('finance_accounts as a')
            ->where('a.owner_type', 'branch')->where('a.owner_id', $branch)
            ->where('a.currency', 'INR')->where('a.status', 'active')->whereNull('a.deleted_at')
            ->whereNotIn('a.id', DB::table('outlet_ledger_opening_lines')->select('ledger_id'))
            ->orderBy('a.ledger_name')->get(['a.id', 'a.ledger_name', 'a.opening_balance']);
        $groups = DB::table('finance_ledger_groups')->where('is_active', true)
            ->whereIn('nature', ['asset', 'expense', 'liability', 'equity', 'income'])->orderBy('group_name')->get();
        $stock = DB::table('outlet_stock_items')->where('business_id', $business)->where('branch_id', $branch)
            ->orderBy('name')->get();
        $filters = $request->validate([
            'audit_actor' => ['nullable', 'integer', 'min:1'],
            'audit_from' => ['nullable', 'date_format:Y-m-d'],
            'audit_to' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $auditQuery = DB::table('outlet_opening_audit_logs as a')->join('users as u', 'u.id', '=', 'a.actor_user_id')
            ->where('a.business_id', $business)->where('a.branch_id', $branch)
            ->select('a.*', 'u.name as actor_name');
        if (! empty($filters['audit_actor'])) $auditQuery->where('a.actor_user_id', $filters['audit_actor']);
        if (! empty($filters['audit_from'])) $auditQuery->whereDate('a.created_at', '>=', $filters['audit_from']);
        if (! empty($filters['audit_to'])) $auditQuery->whereDate('a.created_at', '<=', $filters['audit_to']);
        $audit = $auditQuery->orderByDesc('a.id')->paginate(30)->withQueryString();
        return view('vendor.outlet-opening', compact('outlet', 'state', 'ledgers', 'existingLedgers', 'groups', 'stock', 'audit', 'business', 'branch'));
    }

    public function begin(Request $request, int $business, int $branch): RedirectResponse
    {
        $v = $request->validate(['opening_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today']]);
        $this->service->begin((int) $request->user()->id, $business, $branch, $v['opening_date'], $request->ip());
        return back()->with('success', 'Opening date recorded. Sales and purchases may continue.');
    }

    public function setLock(Request $request, int $business, int $branch): RedirectResponse
    {
        $v = $request->validate(['lock' => ['required', 'boolean'], 'reason' => ['required', 'string', 'min:10', 'max:500']]);
        $this->service->setLock((int) $request->user()->id, $business, $branch, (bool) $v['lock'], $v['reason'], $request->ip());
        return back()->with('success', 'Opening lock state changed and audited.');
    }

    public function createLedger(Request $request, int $business, int $branch): RedirectResponse
    {
        $v = $request->validate(['ledger_name' => ['required', 'string', 'min:2', 'max:180'], 'ledger_group_id' => ['required', 'integer', 'min:1']]);
        $this->service->createLedger((int) $request->user()->id, $business, $branch,
            $v['ledger_name'], (int) $v['ledger_group_id'], $request->ip());
        return back()->with('success', 'Outlet ledger created. Set its opening amount when reconciled.');
    }

    public function attachLedger(Request $request, int $business, int $branch): RedirectResponse
    {
        $v = $request->validate(['ledger_id' => ['required', 'integer', 'min:1']]);
        $this->service->attachExistingLedger((int) $request->user()->id, $business, $branch,
            (int) $v['ledger_id'], $request->ip());
        return back()->with('success', 'Existing outlet ledger registered without changing its balance.');
    }

    public function ledgerOpening(Request $request, int $business, int $branch, int $ledger): RedirectResponse
    {
        $v = $request->validate([
            'direction' => ['required', Rule::in(['debit', 'credit'])],
            'amount' => ['required', 'string', 'regex:/^(?:0|[1-9]\d{0,11})(?:\.\d{1,2})?$/D'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);
        $this->service->setLedgerOpening((int) $request->user()->id, $business, $branch, $ledger,
            $v['direction'], $v['amount'], $v['reason'], $request->ip());
        return back()->with('success', 'Ledger opening updated with audit trail.');
    }

    public function createStock(Request $request, int $business, int $branch): RedirectResponse
    {
        $v = $request->validate([
            'sku' => ['required', 'string', 'regex:/^[A-Za-z0-9][A-Za-z0-9._-]{0,79}$/D'],
            'name' => ['required', 'string', 'min:2', 'max:180'],
            'unit' => ['required', Rule::in(['pcs', 'kg', 'g', 'l', 'ml', 'm'])],
        ]);
        $this->service->createStock((int) $request->user()->id, $business, $branch,
            $v['sku'], $v['name'], $v['unit'], $request->ip());
        return back()->with('success', 'Stock item created. Its opening may be recorded later.');
    }

    public function stockOpening(Request $request, int $business, int $branch, int $item): RedirectResponse
    {
        $v = $request->validate([
            'quantity' => ['required', 'string', 'regex:/^(?:0|[1-9]\d{0,9})(?:\.\d{1,3})?$/D'],
            'unit_cost' => ['required', 'string', 'regex:/^(?:0|[1-9]\d{0,11})(?:\.\d{1,2})?$/D'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);
        $this->service->setStockOpening((int) $request->user()->id, $business, $branch,
            $item, $v['quantity'], $v['unit_cost'], $v['reason'], $request->ip());
        return back()->with('success', 'Stock opening updated and current quantity recalculated.');
    }
}
