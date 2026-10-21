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
        'ready' => ['dispatched', 'delivered', 'cancelled'],
        'dispatched' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function index(Request $request, int $business): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $branch = $this->branchForListing($request, $owned);
        $stats = [
            'products' => 0, 'active_products' => 0, 'orders' => 0,
            'awaiting_design' => 0, 'in_production' => 0, 'ready_dispatch' => 0,
            'delivered' => 0, 'open_quote_paise' => 0,
        ];
        $recentOrders = collect();
        if ($branch) {
            $productQuery = DB::table('sublimation_products')
                ->where('business_id', $owned->id)->where('branch_id', $branch->id);
            $orderQuery = DB::table('sublimation_orders')
                ->where('business_id', $owned->id)->where('branch_id', $branch->id);
            $stats['products'] = (clone $productQuery)->count();
            $stats['active_products'] = (clone $productQuery)->where('is_active', true)->count();
            $stats['orders'] = (clone $orderQuery)->count();
            $stats['awaiting_design'] = (clone $orderQuery)->whereIn('status', ['design_pending','approval_pending'])->count();
            $stats['in_production'] = (clone $orderQuery)->whereIn('status', ['approved','in_production'])->count();
            $stats['ready_dispatch'] = (clone $orderQuery)->whereIn('status', ['ready','dispatched'])->count();
            $stats['delivered'] = (clone $orderQuery)->where('status', 'delivered')->count();
            $stats['open_quote_paise'] = (int) (clone $orderQuery)
                ->whereNotIn('status', ['cancelled','delivered'])->sum('total_sale_paise');
            $recentOrders = (clone $orderQuery)->orderByDesc('id')->limit(5)->get();
        }
        return view('vendor.sublimation.index', array_merge(
            $this->studioNavigation($owned, $branch), compact('branches', 'stats', 'recentOrders')
        ));
    }

    private function studioNavigation(object $owned, ?object $branch): array
    {
        return [
            'business' => $owned, 'branch' => $branch,
            'navBusiness' => $owned, 'navBranch' => $branch,
            'navBusinesses' => DB::table('businesses')
                ->where('vendor_id', $owned->vendor_id)->whereNull('deleted_at')
                ->orderBy('name')->get(),
        ];
    }

    private function branchForListing(Request $request, object $owned): ?object
    {
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $id = $request->query('branch');
        if ($id === null || $id === '') return $branches->first();
        abort_unless(ctype_digit((string) $id) && (int) $id > 0, 404);
        $branch = $branches->firstWhere('id', (int) $id);
        abort_unless($branch, 404);
        return $branch;
    }

    public function createProduct(Request $request, int $business): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $branch = $this->branchForListing($request, $owned);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        return view('vendor.sublimation.create-product', array_merge(
            $this->studioNavigation($owned, $branch), compact('branches')
        ));
    }

    public function createOrder(Request $request, int $business): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $branch = $this->branchForListing($request, $owned);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $products = $branch ? DB::table('sublimation_products')
            ->where('business_id', $owned->id)->where('branch_id', $branch->id)
            ->where('is_active', true)->where('sale_price_paise', '>', 0)
            ->orderBy('name')->get() : collect();
        return view('vendor.sublimation.create-order', array_merge(
            $this->studioNavigation($owned, $branch), compact('branches', 'products')
        ));
    }

    public function products(Request $request, int $business): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $branch = $this->branchForListing($request, $owned);
        $query = DB::table('sublimation_products')->where('business_id', $owned->id);
        if ($branch) $query->where('branch_id', $branch->id);
        else $query->whereRaw('1 = 0');
        if ($request->query('state') === 'active') $query->where('is_active', true);
        if ($request->query('state') === 'inactive') $query->where('is_active', false);
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') $query->where('name', 'like', '%'.addcslashes(substr($search, 0, 100), '%_\\').'%');
        $products = $query->orderByDesc('id')->paginate(20)->withQueryString();
        return view('vendor.sublimation.products', array_merge(
            $this->studioNavigation($owned, $branch), compact('branches', 'products')
        ));
    }

    public function product(Request $request, int $business, int $product): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $item = DB::table('sublimation_products')->where('id', $product)
            ->where('business_id', $owned->id)->firstOrFail();
        $branch = $this->ownedBranch($owned->id, (int) $item->branch_id);
        $choices = json_decode($item->option_choices ?? '{}', true) ?: [];
        return view('vendor.sublimation.product', array_merge(
            $this->studioNavigation($owned, $branch), compact('item', 'choices')
        ));
    }

    public function orders(Request $request, int $business): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $branches = DB::table('branches')->where('business_id', $owned->id)
            ->whereNull('deleted_at')->orderBy('name')->get();
        $branch = $this->branchForListing($request, $owned);
        $query = DB::table('sublimation_orders')->where('business_id', $owned->id);
        if ($branch) $query->where('branch_id', $branch->id);
        else $query->whereRaw('1 = 0');
        $queue = (string) $request->query('queue', '');
        $queues = [
            'design' => ['design_pending', 'approval_pending'],
            'production' => ['approved', 'in_production'],
            'delivery' => ['ready', 'dispatched', 'delivered'],
        ];
        if (isset($queues[$queue])) $query->whereIn('status', $queues[$queue]);
        else {
            $status = (string) $request->query('status', '');
            if ($status !== '' && array_key_exists($status, self::TRANSITIONS)) $query->where('status', $status);
        }
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $term = '%'.addcslashes(substr($search, 0, 100), '%_\\').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('order_no', 'like', $term)->orWhere('customer_name', 'like', $term)
                    ->orWhere('product_name_snapshot', 'like', $term);
            });
        }
        $orders = $query->orderByDesc('id')->paginate(20)->withQueryString();
        $statuses = array_keys(self::TRANSITIONS);
        return view('vendor.sublimation.orders', array_merge(
            $this->studioNavigation($owned, $branch), compact('branches', 'orders', 'statuses')
        ));
    }

    public function order(Request $request, int $business, int $order): View
    {
        $owned = $this->ownedBusiness($request, $business);
        $item = DB::table('sublimation_orders')->where('id', $order)
            ->where('business_id', $owned->id)->firstOrFail();
        $branch = $this->ownedBranch($owned->id, (int) $item->branch_id);
        $choices = json_decode($item->personalization_options ?? '{}', true) ?: [];
        $events = DB::table('audit_logs')->where('entity_type', 'sublimation_order')
            ->where('entity_id', $item->id)
            ->whereIn('action', [
                'sublimation.order.created', 'sublimation.order.customer_placed',
                'sublimation.order.status_changed',
            ])->orderBy('created_at')->orderBy('id')->get();
        $next = self::TRANSITIONS[$item->status] ?? [];
        if (($item->fulfillment_method ?? 'pickup') === 'delivery') {
            $next = array_values(array_diff($next, ['delivered']));
            if ($item->status === 'dispatched') $next = self::TRANSITIONS['dispatched'];
        } else $next = array_values(array_diff($next, ['dispatched']));
        return view('vendor.sublimation.order', array_merge(
            $this->studioNavigation($owned, $branch), compact('item', 'choices', 'events', 'next')
        ));
    }

    private function optionChoices(array $data): string
    {
        $choices = [];
        foreach (['size', 'color'] as $key) {
            $raw = explode(',', (string) ($data[$key.'_options'] ?? ''));
            $values = array_values(array_unique(array_filter(array_map('trim', $raw),
                fn ($value) => $value !== '' && mb_strlen($value) <= 50)));
            $choices[$key] = array_slice($values, 0, 20);
        }
        return json_encode($choices, JSON_UNESCAPED_UNICODE);
    }

    private function chosenOptions(array $data, object $product): ?string
    {
        $choices = json_decode($product->option_choices ?? '{}', true) ?: [];
        $selected = [];
        foreach (['size', 'color'] as $key) {
            $allowed = $choices[$key] ?? [];
            if (!is_array($allowed)) $allowed = [];
            $value = trim((string) ($data[$key] ?? ''));
            if ($allowed && ($value === '' || !in_array($value, $allowed, true))) {
                throw ValidationException::withMessages([$key => 'Choose a valid '. $key .' for this product.']);
            }
            if (!$allowed && $value !== '') {
                throw ValidationException::withMessages([$key => 'This product has no '. $key .' choices.']);
            }
            if ($value !== '') $selected[$key] = $value;
        }
        return $selected ? json_encode($selected, JSON_UNESCAPED_UNICODE) : null;
    }

    public function storeProduct(Request $request, int $business): RedirectResponse
    {
        $owned = $this->ownedBusiness($request, $business);
        $data = $request->validate([
            'branch_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', Rule::in(['Mug', 'T-Shirt', 'Cushion', 'Keychain', 'Bottle', 'Photo Frame', 'Mouse Pad', 'Coaster', 'Tote Bag', 'Puzzle', 'Other'])],
            'production_notes' => ['nullable', 'string', 'max:2000'],
            'size_options' => ['nullable', 'string', 'max:600'],
            'color_options' => ['nullable', 'string', 'max:600'],
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
                'option_choices' => $this->optionChoices($data),
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

        return redirect()->route('vendor.sublimation.products.index', ['business' => $business, 'branch' => $branch->id])
            ->with('success', 'Product added to your catalogue.');
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
            'size_options' => ['nullable', 'string', 'max:600'],
            'color_options' => ['nullable', 'string', 'max:600'],
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
                'option_choices' => $this->optionChoices($data),
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
                'option_choices' => $current->option_choices,
            ];
            $this->audit($request, 'sublimation.product.updated', 'sublimation_product', $current->id,
                array_diff_key($update, ['updated_at' => null]), $old);
        });

        return redirect()->route('vendor.sublimation.products.show', ['business' => $business, 'product' => $product])
            ->with('success', 'Product details updated. Previous jobs are unchanged.');
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
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
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
            $options = $this->chosenOptions($data, $product);
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
                'personalization_options' => $options,
                'design_instructions' => $data['design_instructions'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($request, 'sublimation.order.created', 'sublimation_order', $id, [
                'business_id' => $owned->id, 'branch_id' => $branch->id,
                'order_no' => $number, 'status' => 'design_pending',
            ]);
        });

        return redirect()->route('vendor.sublimation.orders.index', ['business' => $business, 'branch' => $branch->id])
            ->with('success', 'Studio order saved. Open its details to manage production and delivery.');
    }

    public function updateStatus(Request $request, int $business, int $order): RedirectResponse
    {
        $owned = $this->ownedBusiness($request, $business);
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::TRANSITIONS))],
            'approval_note' => ['nullable', 'string', 'max:1500', 'required_if:status,approved'],
            'delivery_carrier' => ['nullable', 'string', 'max:120'],
            'delivery_tracking' => ['nullable', 'string', 'max:120'],
            'delivery_confirmation_note' => ['nullable', 'string', 'max:2000'],
        ]);
        $branchId = DB::transaction(function () use ($request, $owned, $order, $data): int {
            $current = DB::table('sublimation_orders')
                ->where('id', $order)->where('business_id', $owned->id)
                ->lockForUpdate()->firstOrFail();
            $this->ownedBranch($owned->id, (int) $current->branch_id);
            if (! in_array($data['status'], self::TRANSITIONS[$current->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'This status change is not allowed.']);
            }
            $method = $current->fulfillment_method ?? 'pickup';
            if ($data['status'] === 'dispatched') {
                if ($method !== 'delivery') {
                    throw ValidationException::withMessages(['status' => 'Pickup orders cannot be dispatched.']);
                }
                if (! filled($data['delivery_carrier'] ?? null)) {
                    throw ValidationException::withMessages(['delivery_carrier' => 'Carrier or local delivery person is required to dispatch.']);
                }
            }
            if ($data['status'] === 'delivered') {
                if ($method === 'delivery' && $current->status !== 'dispatched') {
                    throw ValidationException::withMessages(['status' => 'Delivery orders must be dispatched before completing.']);
                }
                if (! filled($data['delivery_confirmation_note'] ?? null)) {
                    throw ValidationException::withMessages(['delivery_confirmation_note' => 'Record who received the item and how delivery/pickup was confirmed.']);
                }
            }
            $updates = ['status' => $data['status'], 'updated_at' => now()];
            if ($data['status'] === 'dispatched') {
                $updates['delivery_carrier'] = trim((string) $data['delivery_carrier']);
                $updates['delivery_tracking'] = filled($data['delivery_tracking'] ?? null)
                    ? trim((string) $data['delivery_tracking']) : null;
                $updates['dispatched_at'] = now();
            }
            if ($data['status'] === 'delivered') {
                $updates['delivered_at'] = now();
                $updates['delivery_confirmation_note'] = trim((string) $data['delivery_confirmation_note']);
            }
            if ($data['status'] === 'approved') {
                $updates['approval_note'] = trim($data['approval_note']);
                $updates['design_approved_at'] = now();
                $updates['design_approved_recorded_by'] = $request->user()->id;
            }
            DB::table('sublimation_orders')->where('id', $current->id)
                ->where('business_id', $owned->id)->update($updates);
            $this->audit($request, 'sublimation.order.status_changed', 'sublimation_order', $current->id,
                ['status' => $data['status'], 'approval_note' => $updates['approval_note'] ?? null,
                 'delivery_carrier' => $updates['delivery_carrier'] ?? null,
                 'delivery_tracking' => $updates['delivery_tracking'] ?? null,
                 'delivered_at' => isset($updates['delivered_at']) ? now()->toDateTimeString() : null],
                ['status' => $current->status]);

            return (int) $current->branch_id;
        });

        return redirect()->route('vendor.sublimation.orders.show', ['business' => $business, 'order' => $order])
            ->with('success', 'Order status updated. History preserved.');
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
