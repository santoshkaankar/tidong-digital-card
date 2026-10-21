<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Grocery\GroceryAccess as Access;
use App\Services\Grocery\GroceryFeatures as Features;
use App\Services\Grocery\GroceryMath as Math;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Standalone, vendor-owned Grocery V1: no live payment or customer checkout. */
final class GroceryController extends Controller
{
    public function index(Request $request, int $business): View
    {
        Features::requireEnabled('module');
        $owned = Access::business($request, $business);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $selected = $request->query('branch');
        if ($selected !== null && (!is_string($selected) || !ctype_digit($selected))) abort(404);
        $branch = $selected === null ? $branches->first() : $branches->first(fn ($b) => (string) $b->id === $selected);
        if ($selected !== null && !$branch) abort(404);

        $products = collect(); $sales = collect(); $lots = collect(); $branchPreferences = collect();
        if ($branch) {
            $branchPreferences = DB::table('grocery_branch_features')
                ->where('branch_id', $branch->id)->pluck('enabled', 'feature');
            $products = DB::table('grocery_products')->where('business_id', $owned->id)
                ->where('branch_id', $branch->id)->orderBy('name')->get();
            $sales = DB::table('grocery_sales')->where('business_id', $owned->id)
                ->where('branch_id', $branch->id)->orderByDesc('id')->limit(50)->get();
            $lots = DB::table('grocery_stock_lots')->where('business_id', $owned->id)
                ->where('branch_id', $branch->id)->orderBy('expiry_date')->limit(200)->get();
        }
        $flags = [];
        foreach (Features::LABELS as $code => $label) {
            if ($code !== 'module') $flags[$code] = Features::enabled($code, $branch?->id);
        }
        return view('vendor.grocery.index', [
            'business' => $owned, 'branch' => $branch, 'branches' => $branches,
            'products' => $products, 'sales' => $sales, 'lots' => $lots, 'flags' => $flags,
            'branchPreferences' => $branchPreferences,
            'navBusiness' => $owned, 'navBranch' => $branch,
            'navBusinesses' => DB::table('businesses')->where('vendor_id', $owned->vendor_id)
                ->whereNull('deleted_at')->orderBy('name')->get(),
        ]);
    }

    public function branchFeature(Request $request, int $business): RedirectResponse
    {
        Features::requireEnabled('module');
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'feature' => ['required', Rule::in(array_values(array_diff(array_keys(Features::LABELS), ['module'])))],
            'enabled' => ['required', 'boolean'],
        ]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        DB::transaction(function () use ($request, $branch, $data): void {
            DB::table('grocery_branch_features')->updateOrInsert([
                'branch_id' => $branch->id, 'feature' => $data['feature'],
            ], ['enabled' => (bool) $data['enabled'], 'updated_by' => $request->user()->id,
                'updated_at' => now(), 'created_at' => now()]);
            Access::audit($request, 'grocery.branch_feature.changed', 'grocery_branch', $branch->id,
                ['feature' => $data['feature'], 'enabled' => (bool) $data['enabled']]);
        });
        return $this->redirect($owned->id, $branch->id)->with('success', 'Branch option updated. Global Admin OFF always takes precedence.');
    }

    public function product(Request $request, int $business): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'sku' => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_.-]+$/'],
            'barcode' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', Rule::in(['pcs', 'pack', 'kg', 'g', 'l', 'ml'])],
            'hsn_code' => ['nullable', 'string', 'max:12', 'regex:/^[A-Za-z0-9]+$/'],
            'gst_rate' => ['required', 'numeric', 'between:0,40', 'decimal:0,2'],
            'tax_inclusive' => ['required', 'boolean'],
            'offline_mrp' => ['required', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
            'offline_sale' => ['required', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
            'online_mrp' => ['nullable', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
            'online_sale' => ['nullable', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
        ]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::requireEnabled('catalogue', $branch->id);
        Features::requireEnabled('pricing', $branch->id);
        $mrp = Math::paise((string) $data['offline_mrp']);
        $sale = Math::paise((string) $data['offline_sale']);
        if ($sale > $mrp) throw ValidationException::withMessages(['offline_sale' => 'Sale price must not exceed MRP.']);
        $onlineMrp = isset($data['online_mrp']) ? Math::paise((string) $data['online_mrp']) : null;
        $onlineSale = isset($data['online_sale']) ? Math::paise((string) $data['online_sale']) : null;
        if (($onlineSale ?? $sale) > ($onlineMrp ?? $mrp)) throw ValidationException::withMessages(['online_sale' => 'Effective online sale price must not exceed effective MRP.']);
        $id = DB::transaction(function () use ($request, $owned, $branch, $data, $mrp, $sale, $onlineMrp, $onlineSale): int {
            $exists = DB::table('grocery_products')->where('branch_id', $branch->id)
                ->where(function ($q) use ($data): void {
                    $q->where('sku', trim($data['sku']));
                    if (!empty($data['barcode'])) $q->orWhere('barcode', trim($data['barcode']));
                })->exists();
            if ($exists) throw ValidationException::withMessages(['sku' => 'SKU or barcode already exists in this Branch.']);
            $id = DB::table('grocery_products')->insertGetId([
                'business_id' => $owned->id, 'branch_id' => $branch->id,
                'sku' => trim($data['sku']), 'barcode' => !empty($data['barcode']) ? trim($data['barcode']) : null,
                'name' => trim($data['name']), 'category' => trim($data['category']),
                'unit' => $data['unit'], 'hsn_code' => $data['hsn_code'] ?? null,
                'gst_rate_bps' => Math::paise((string) $data['gst_rate']),
                'tax_inclusive' => (bool) $data['tax_inclusive'],
                'offline_mrp_paise' => $mrp, 'offline_sale_paise' => $sale,
                'online_mrp_paise' => $onlineMrp, 'online_sale_paise' => $onlineSale,
                'is_active' => false, 'created_at' => now(), 'updated_at' => now(),
            ]);
            Access::audit($request, 'grocery.product.created', 'grocery_product', $id,
                ['business_id' => $owned->id, 'branch_id' => $branch->id, 'sku' => $data['sku']]);
            return $id;
        });
        return $this->redirect($business, $branch->id)->with('success', "Product #{$id} added INACTIVE. Activate after checking price and stock.");
    }

    public function price(Request $request, int $business, int $product): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'offline_mrp' => ['required', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
            'offline_sale' => ['required', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
            'online_mrp' => ['nullable', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
            'online_sale' => ['nullable', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,2'],
        ]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::requireEnabled('pricing', $branch->id);
        $mrp = Math::paise((string) $data['offline_mrp']); $sale = Math::paise((string) $data['offline_sale']);
        $onlineMrp = isset($data['online_mrp']) ? Math::paise((string) $data['online_mrp']) : null;
        $onlineSale = isset($data['online_sale']) ? Math::paise((string) $data['online_sale']) : null;
        if ($sale > $mrp || ($onlineSale ?? $sale) > ($onlineMrp ?? $mrp)) {
            throw ValidationException::withMessages(['offline_sale' => 'Effective sale price cannot exceed effective MRP.']);
        }
        DB::transaction(function () use ($request, $owned, $branch, $product, $mrp, $sale, $onlineMrp, $onlineSale): void {
            $p = DB::table('grocery_products')->where('id', $product)->where('business_id', $owned->id)
                ->where('branch_id', $branch->id)->lockForUpdate()->firstOrFail();
            DB::table('grocery_products')->where('id', $p->id)->update([
                'offline_mrp_paise' => $mrp, 'offline_sale_paise' => $sale,
                'online_mrp_paise' => $onlineMrp, 'online_sale_paise' => $onlineSale, 'updated_at' => now(),
            ]);
            Access::audit($request, 'grocery.product.priced', 'grocery_product', $p->id,
                ['old_sale_paise' => $p->offline_sale_paise, 'sale_paise' => $sale]);
        });
        return $this->redirect($business, $branch->id)->with('success', 'Pricing updated. Existing sales retain original snapshots.');
    }

    public function availability(Request $request, int $business, int $product): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate(['branch_id' => ['required', 'integer'], 'active' => ['required', 'boolean']]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::requireEnabled('catalogue', $branch->id);
        DB::transaction(function () use ($request, $owned, $branch, $product, $data): void {
            $p = DB::table('grocery_products')->where('id', $product)
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                ->lockForUpdate()->firstOrFail();
            abort_unless((int) $p->offline_sale_paise > 0, 422);
            DB::table('grocery_products')->where('id', $p->id)
                ->update(['is_active' => (bool) $data['active'], 'updated_at' => now()]);
            Access::audit($request, 'grocery.product.availability', 'grocery_product', $p->id,
                ['old' => (bool) $p->is_active, 'active' => (bool) $data['active']]);
        });
        return $this->redirect($business, $branch->id)->with('success', 'Product availability updated.');
    }

    public function receive(Request $request, int $business): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'], 'product_id' => ['required', 'integer'],
            'lot_code' => ['required', 'string', 'max:80'],
            'quantity' => ['required', 'numeric', 'gt:0', 'max:1000000', 'decimal:0,3'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::requireEnabled('inventory', $branch->id);
        $qty = Math::milli((string) $data['quantity']);
        DB::transaction(function () use ($request, $owned, $branch, $data, $qty): void {
            $p = DB::table('grocery_products')->where('id', $data['product_id'])
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                ->lockForUpdate()->firstOrFail();
            if (in_array($p->unit, ['pcs','pack'], true) && $qty % 1000 !== 0) {
                throw ValidationException::withMessages(['quantity' => 'Items sold as pcs/pack require whole-number quantities.']);
            }
            $lotId = DB::table('grocery_stock_lots')->insertGetId([
                'business_id' => $owned->id, 'branch_id' => $branch->id, 'product_id' => $p->id,
                'lot_code' => trim($data['lot_code']), 'expiry_date' => $data['expiry_date'] ?? null,
                'received_qty_milli' => $qty, 'available_qty_milli' => $qty, 'is_sellable' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('grocery_stock_movements')->insert([
                'business_id' => $owned->id, 'branch_id' => $branch->id, 'product_id' => $p->id,
                'lot_id' => $lotId, 'movement_type' => 'receive', 'delta_qty_milli' => $qty,
                'actor_user_id' => $request->user()->id, 'created_at' => now(),
            ]);
            Access::audit($request, 'grocery.stock.received', 'grocery_stock_lot', $lotId,
                ['product_id' => $p->id, 'quantity_milli' => $qty]);
        });
        return $this->redirect($business, $branch->id)->with('success', 'Stock received with append-only movement history.');
    }

    public function sale(Request $request, int $business): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'], 'supply_state' => ['required', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.product_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:100000', 'decimal:0,3'],
        ]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::writableForSale($branch->id);
        Access::sellingAllowed($owned, $branch);
        if (!$branch->state) throw ValidationException::withMessages(['branch_id' => 'Set Branch state before billing.']);
        $state = mb_strtolower(trim($data['supply_state']));
        $interstate = $state !== mb_strtolower(trim($branch->state));
        $saleId = DB::transaction(function () use ($request, $owned, $branch, $data, $interstate): int {
            Features::writableForSale($branch->id);
            $totals = array_fill_keys(['taxable_paise','cgst_paise','sgst_paise','igst_paise','tax_paise','total_paise'], 0);
            $lines = []; $allocated = [];
            // Consistent product locking order reduces deadlock risk across concurrent POS requests.
            $input = collect($data['items'])->sortBy(fn ($item) => (int) $item['product_id'])->values();
            foreach ($input as $item) {
                $p = DB::table('grocery_products')->where('id', $item['product_id'])
                    ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                    ->where('is_active', true)->lockForUpdate()->firstOrFail();
                $qty = Math::milli((string) $item['quantity']);
                if (in_array($p->unit, ['pcs','pack'], true) && $qty % 1000 !== 0) {
                    throw ValidationException::withMessages(['items' => 'pcs/pack products require whole quantities.']);
                }
                $needed = $qty; $picks = [];
                $lots = DB::table('grocery_stock_lots')->where('product_id', $p->id)
                    ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                    ->where('is_sellable', true)->where('available_qty_milli', '>', 0)
                    ->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString()))
                    ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('expiry_date')->orderBy('id')->lockForUpdate()->get();
                foreach ($lots as $lot) {
                    if ($needed === 0) break;
                    $take = min($needed, (int) $lot->available_qty_milli);
                    $picks[] = ['lot_id' => $lot->id, 'quantity' => $take]; $needed -= $take;
                }
                if ($needed !== 0) throw ValidationException::withMessages(['items' => "Insufficient unexpired stock for {$p->sku}."]);
                $price = (int) $p->offline_sale_paise;
                $tax = Math::line($price, $qty, (int) $p->gst_rate_bps, (bool) $p->tax_inclusive, $interstate);
                foreach ($totals as $key => $sum) $totals[$key] += $tax[$key];
                $mrpLine = intdiv((int) $p->offline_mrp_paise * $qty + 500, 1000);
                $saleLine = intdiv($price * $qty + 500, 1000);
                $discount = max(0, $mrpLine - $saleLine);
                $discountBps = intdiv(max(0, (int)$p->offline_mrp_paise - $price) * 10000 + intdiv((int)$p->offline_mrp_paise, 2), (int)$p->offline_mrp_paise);
                $lines[] = ['product' => $p, 'quantity' => $qty, 'tax' => $tax,
                    'discount_paise' => $discount, 'discount_rate_bps_snapshot' => $discountBps];
                $allocated[$p->id] = $picks;
            }
            $id = DB::table('grocery_sales')->insertGetId(array_merge([
                'business_id' => $owned->id, 'branch_id' => $branch->id,
                'sale_number' => 'GR-'.Str::upper((string) Str::ulid()), 'status' => 'recorded',
                'supply_state' => trim($data['supply_state']), 'branch_state_snapshot' => $branch->state,
                'interstate' => $interstate, 'recorded_by' => $request->user()->id,
                'created_at' => now(), 'updated_at' => now(),
            ], $totals));
            foreach ($lines as $line) {
                $p = $line['product'];
                DB::table('grocery_sale_items')->insert(array_merge([
                    'sale_id' => $id, 'product_id' => $p->id,
                    'sku_snapshot' => $p->sku, 'name_snapshot' => $p->name,
                    'unit_snapshot' => $p->unit, 'hsn_snapshot' => $p->hsn_code,
                    'quantity_milli' => $line['quantity'], 'unit_mrp_paise' => $p->offline_mrp_paise,
                    'unit_sale_paise' => $p->offline_sale_paise,
                    'discount_paise' => $line['discount_paise'],
                    'discount_rate_bps_snapshot' => $line['discount_rate_bps_snapshot'],
                    'gst_rate_bps_snapshot' => $p->gst_rate_bps,
                    'tax_inclusive_snapshot' => $p->tax_inclusive,
                    'created_at' => now(), 'updated_at' => now(),
                ], $line['tax']));
                foreach ($allocated[$p->id] as $pick) {
                    $changed = DB::table('grocery_stock_lots')->where('id', $pick['lot_id'])
                        ->where('available_qty_milli', '>=', $pick['quantity'])
                        ->decrement('available_qty_milli', $pick['quantity'], ['updated_at' => now()]);
                    if ($changed !== 1) throw ValidationException::withMessages(['items' => 'Stock changed; please retry.']);
                    DB::table('grocery_stock_movements')->insert([
                        'business_id' => $owned->id, 'branch_id' => $branch->id, 'product_id' => $p->id,
                        'lot_id' => $pick['lot_id'], 'sale_id' => $id,
                        'movement_type' => 'sale', 'delta_qty_milli' => -$pick['quantity'],
                        'actor_user_id' => $request->user()->id, 'created_at' => now(),
                    ]);
                }
            }
            Access::audit($request, 'grocery.sale.recorded', 'grocery_sale', $id,
                ['business_id' => $owned->id, 'branch_id' => $branch->id, 'total_paise' => $totals['total_paise']]);
            return $id;
        }, 3);
        return $this->redirect($business, $branch->id)->with('success', "Counter sale #{$saleId} recorded; no payment captured.");
    }

    public function reverse(Request $request, int $business, int $sale): RedirectResponse
    {
        $owned = Access::business($request, $business);
        $data = $request->validate(['branch_id' => ['required', 'integer'], 'reason' => ['required','string','min:5','max:2000']]);
        $branch = Access::branch($owned, (int) $data['branch_id']);
        Features::writableForSale($branch->id);
        DB::transaction(function () use ($request, $owned, $branch, $sale, $data): void {
            $current = DB::table('grocery_sales')->where('id', $sale)->where('business_id', $owned->id)
                ->where('branch_id', $branch->id)->lockForUpdate()->firstOrFail();
            if ($current->status !== 'recorded') {
                throw ValidationException::withMessages(['reason' => 'This sale has already been reversed.']);
            }
            $movements = DB::table('grocery_stock_movements')->where('sale_id', $sale)
                ->where('movement_type', 'sale')->orderBy('lot_id')->get();
            foreach ($movements as $move) {
                DB::table('grocery_stock_lots')->where('id', $move->lot_id)->increment('available_qty_milli', -$move->delta_qty_milli, ['updated_at' => now()]);
                DB::table('grocery_stock_movements')->insert([
                    'business_id' => $owned->id, 'branch_id' => $branch->id,
                    'product_id' => $move->product_id, 'lot_id' => $move->lot_id,
                    'sale_id' => $sale, 'movement_type' => 'sale_reversal',
                    'delta_qty_milli' => -$move->delta_qty_milli,
                    'reason' => Str::limit(trim($data['reason']), 500, ''),
                    'actor_user_id' => $request->user()->id, 'created_at' => now(),
                ]);
            }
            DB::table('grocery_sale_reversals')->insert([
                'sale_id' => $sale, 'actor_user_id' => $request->user()->id,
                'reason' => trim($data['reason']), 'created_at' => now(),
            ]);
            DB::table('grocery_sales')->where('id', $sale)->update([
                'status' => 'reversed', 'reversed_at' => now(), 'updated_at' => now(),
            ]);
            Access::audit($request, 'grocery.sale.reversed', 'grocery_sale', $sale,
                ['reason' => $data['reason'], 'restored_lots' => $movements->count()]);
        }, 3);
        return $this->redirect($business, $branch->id)->with('success', 'Sale reversed with full history; original was retained.');
    }

    private function redirect(int $business, int $branch): RedirectResponse
    {
        return redirect()->route('vendor.grocery.index', ['business' => $business, 'branch' => $branch]);
    }
}
