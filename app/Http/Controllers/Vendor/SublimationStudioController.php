<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * V1: private, owner-only internal catalogue / job cards.
 * No public ordering, tax invoices, payment capture, file uploads, or employee delegation.
 */
class SublimationStudioController extends Controller
{
    private const BUSINESS_TYPES = ['sublimation', 'custom_printing', 'printing'];

    private const TRANSITIONS = [
        'design_pending' => ['approval_pending', 'cancelled'],
        'approval_pending' => ['design_pending', 'approved', 'cancelled'],
        'approved' => ['in_production', 'cancelled'],
        'in_production' => ['ready', 'cancelled'],
        'ready' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function index(Request $request, int $business): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $requestedBranch = $request->query('branch');
        $branch = $requestedBranch !== null
            ? $branches->firstWhere('id', (int) $requestedBranch)
            : $branches->first();
        if ($requestedBranch !== null && (!ctype_digit((string) $requestedBranch) || (string) ($branch?->id) !== (string) $requestedBranch)) {
            abort(404, 'Branch not found in your business.');
        }

        $products = collect();
        $orders = collect();
        if ($branch) {
            $products = DB::table('sublimation_products')
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                ->orderByDesc('id')->get();
            $orders = DB::table('sublimation_orders')
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                ->orderByDesc('id')->limit(100)->get();
        }

        return view('vendor.sublimation.index', [
            'business' => $owned, 'branches' => $branches, 'branch' => $branch,
            'products' => $products, 'orders' => $orders,
            'navBusiness' => $owned, 'navBranch' => $branch,
            'navBusinesses' => DB::table('businesses')->where('vendor_id', $owned->vendor_id)
                ->whereNull('deleted_at')->orderBy('name')->get(),
        ]);
    }

    public function storeProduct(Request $request, int $business): RedirectResponse
    {
        $owned = $this->ownedBusiness($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', Rule::in(['Mug', 'T-Shirt', 'Cushion', 'Keychain', 'Bottle', 'Photo Frame', 'Mouse Pad', 'Coaster', 'Tote Bag', 'Puzzle', 'Other'])],
            'production_notes' => ['nullable', 'string', 'max:2000'],
            'blank_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'print_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'packing_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'other_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'sale_price' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
        ]);
        $branch = $this->ownedBranch($owned->id, (int) $data['branch_id']);

        DB::transaction(function () use ($request, $owned, $branch, $data): void {
            $id = DB::table('sublimation_products')->insertGetId([
                'business_id' => $owned->id,
                'branch_id' => $branch->id,
                'name' => trim($data['name']),
                'category' => $data['category'],
                'production_notes' => $data['production_notes'] ?? null,
                'blank_cost_paise' => $this->paise($data['blank_cost']),
                'print_cost_paise' => $this->paise($data['print_cost']),
                'packing_cost_paise' => $this->paise($data['packing_cost']),
                'other_cost_paise' => $this->paise($data['other_cost']),
                'sale_price_paise' => $this->paise($data['sale_price']),
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($request, 'sublimation.product.created', 'sublimation_product', $id, [
                'business_id' => $owned->id, 'branch_id' => $branch->id,
                'name' => trim($data['name']),
            ]);
        });

        return redirect()->route('vendor.sublimation.index', ['business' => $business, 'branch' => $branch->id])
            ->with('success', 'Product added to your private studio catalogue.');
    }

    /**
     * Owner-only changes to per-branch product costs, quote and availability.
     * Inactive templates cannot be used by a customer job.
     */
    public function updateProduct(Request $request, int $business, int $product): RedirectResponse
    {
        $owned = $this->ownedBusiness($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', Rule::in(['Mug', 'T-Shirt', 'Cushion', 'Keychain', 'Bottle', 'Photo Frame', 'Mouse Pad', 'Coaster', 'Tote Bag', 'Puzzle', 'Other'])],
            'production_notes' => ['nullable', 'string', 'max:2000'],
            'blank_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'print_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'packing_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'other_cost' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'sale_price' => ['required', 'numeric', 'min:0', 'max:1000000', 'decimal:0,2'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $branch = $this->ownedBranch($owned->id, (int) $data['branch_id']);
        $active = $request->boolean('is_active');
        if ($active && $this->paise($data['sale_price']) <= 0) {
            throw ValidationException::withMessages([
                'sale_price' => 'A positive selling price is required to activate a product.',
            ]);
        }

        DB::transaction(function () use ($request, $owned, $branch, $product, $data, $active): void {
            $current = DB::table('sublimation_products')->where('id', $product)
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)
                ->lockForUpdate()->firstOrFail();
            $update = [
                'name' => trim($data['name']), 'category' => $data['category'],
                'production_notes' => $data['production_notes'] ?? null,
                'blank_cost_paise' => $this->paise($data['blank_cost']),
                'print_cost_paise' => $this->paise($data['print_cost']),
                'packing_cost_paise' => $this->paise($data['packing_cost']),
                'other_cost_paise' => $this->paise($data['other_cost']),
                'sale_price_paise' => $this->paise($data['sale_price']),
                'is_active' => $active, 'updated_at' => now(),
            ];
            DB::table('sublimation_products')->where('id', $current->id)
                ->where('business_id', $owned->id)->where('branch_id', $branch->id)->update($update);
            $old = [
                'name' => $current->name, 'category' => $current->category,
                'blank_cost_paise' => $current->blank_cost_paise,
                'print_cost_paise' => $current->print_cost_paise,
                'packing_cost_paise' => $current->packing_cost_paise,
                'other_cost_paise' => $current->other_cost_paise,
                'sale_price_paise' => $current->sale_price_paise,
                'is_active' => (bool) $current->is_active,
            ];
            $this->audit($request, 'sublimation.product.updated', 'sublimation_product', $current->id,
                array_diff_key($update, ['updated_at' => null]), $old);
        });

        return redirect()->route('vendor.sublimation.index', ['business' => $business, 'branch' => $branch->id])
            ->with('success', 'Product details and pricing updated. Previous jobs are unchanged.');
    }

    public function storeOrder(Request $request, int $business): RedirectResponse
    {
        $owned = $this->ownedBusiness($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'customer_name' => ['required', 'string', 'max:160'],
            'customer_mobile' => ['nullable', 'string', 'max:25'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'design_instructions' => ['nullable', 'string', 'max:3000'],
            'due_date' => ['nullable', 'date'],
        ]);
        $branch = $this->ownedBranch($owned->id, (int) $data['branch_id']);
        DB::transaction(function () use ($request, $owned, $branch, $data): void {
            $product = DB::table('sublimation_products')
                ->where('id', $data['product_id'])->where('business_id', $owned->id)
                ->where('branch_id', $branch->id)->where('is_active', true)
                ->lockForUpdate()->firstOrFail();

            if ((int) $product->sale_price_paise <= 0) {
                throw ValidationException::withMessages([
                    'product_id' => 'Set a selling price and activate the product before creating a job.',
                ]);
            }

            $unitCost = (int) $product->blank_cost_paise + (int) $product->print_cost_paise
                + (int) $product->packing_cost_paise + (int) $product->other_cost_paise;
            $quantity = (int) $data['quantity'];
            $number = 'SUB-'.Str::upper((string) Str::ulid());
            $id = DB::table('sublimation_orders')->insertGetId([
                'business_id' => $owned->id, 'branch_id' => $branch->id,
                'product_id' => $product->id, 'order_no' => $number,
                'customer_name' => trim($data['customer_name']),
                'customer_mobile' => $data['customer_mobile'] ?? null,
                'product_name_snapshot' => $product->name,
                'quantity' => $quantity,
                'unit_cost_paise' => $unitCost,
                'unit_sale_paise' => $product->sale_price_paise,
                'total_cost_paise' => $unitCost * $quantity,
                'total_sale_paise' => (int) $product->sale_price_paise * $quantity,
                'status' => 'design_pending',
                'design_instructions' => $data['design_instructions'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($request, 'sublimation.order.created', 'sublimation_order', $id, [
                'business_id' => $owned->id, 'branch_id' => $branch->id,
                'order_no' => $number, 'status' => 'design_pending',
            ]);
        });

        return redirect()->route('vendor.sublimation.index', ['business' => $business, 'branch' => $branch->id])
            ->with('success', 'Internal custom-printing job saved.');
    }

    public function updateStatus(Request $request, int $business, int $order): RedirectResponse
    {
        $owned = $this->ownedBusiness($request, $business);
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'approval_note' => ['nullable', 'string', 'max:1500', 'required_if:status,approved'],
        ]);
        $branchId = DB::transaction(function () use ($request, $owned, $order, $data): int {
            $current = DB::table('sublimation_orders')
                ->where('id', $order)->where('business_id', $owned->id)
                ->lockForUpdate()->firstOrFail();
            $this->ownedBranch($owned->id, (int) $current->branch_id);
            if (! in_array($data['status'], self::TRANSITIONS[$current->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'This status change is not allowed.']);
            }
            $updates = ['status' => $data['status'], 'updated_at' => now()];
            if ($data['status'] === 'approved') {
                $updates['approval_note'] = trim($data['approval_note']);
                $updates['design_approved_at'] = now();
                $updates['design_approved_recorded_by'] = $request->user()->id;
            }
            DB::table('sublimation_orders')->where('id', $current->id)
                ->where('business_id', $owned->id)->update($updates);
            $this->audit($request, 'sublimation.order.status_changed', 'sublimation_order', $current->id,
                ['status' => $data['status'], 'approval_note' => $updates['approval_note'] ?? null],
                ['status' => $current->status]);

            return (int) $current->branch_id;
        });

        return redirect()->route('vendor.sublimation.index', ['business' => $business, 'branch' => $branchId])
            ->with('success', 'Job status updated. History preserved.');
    }

    private function ownedBusiness(Request $request, int $business): object
    {
        // Owner-only by design for V1. Do not use platform-wide permissions as vendor permissions.
        $owned = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $business)
            ->where('v.user_id', $request->user()->id)
            ->where('v.status', 'approved')
            ->whereNull('v.deleted_at')->whereNull('b.deleted_at')
            ->whereIn('b.business_type', self::BUSINESS_TYPES)
            ->select('b.*')->first();
        abort_unless($owned, 404, 'Sublimation business not found for this Vendor.');

        return $owned;
    }

    private function ownedBranch(int $businessId, int $branchId): object
    {
        $branch = DB::table('branches')
            ->where('id', $branchId)->where('business_id', $businessId)
            ->whereNull('deleted_at')->first();
        abort_unless($branch, 404, 'Branch not found in this business.');

        return $branch;
    }

    private function paise(string|int|float $rupees): int
    {
        // Input is validated to at most 2 fractional digits. Convert without floating-point money arithmetic.
        $value = number_format((float) $rupees, 2, '.', '');
        [$whole, $fraction] = explode('.', $value);
        return (int) $whole * 100 + (int) $fraction;
    }

    private function audit(Request $request, string $action, string $type, int $id, array $newValues, ?array $oldValues = null): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $request->user()->id,
            'action' => $action, 'entity_type' => $type, 'entity_id' => $id,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => json_encode($newValues),
            'ip_address' => $request->ip(), 'created_at' => now(),
        ]);
    }
}
