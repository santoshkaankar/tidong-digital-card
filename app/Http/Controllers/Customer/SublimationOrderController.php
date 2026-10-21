<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Customer order requests: no online payment is collected here. */
class SublimationOrderController extends Controller
{
    private const TYPES = ['sublimation', 'custom_printing', 'printing'];

    public function shop(): View
    {
        $this->enabled();
        $products = $this->orderable()
            ->select('p.id', 'p.name', 'p.category', 'p.sale_price_paise',
                'b.name as business_name', 'br.name as branch_name')
            ->orderBy('p.name')->paginate(24);
        return view('customer.sublimation.shop', compact('products'));
    }

    public function product(int $product): View
    {
        $this->enabled();
        $item = $this->orderable()->where('p.id', $product)
            ->select('p.*', 'b.name as business_name', 'br.name as branch_name')->firstOrFail();
        $choices = json_decode($item->option_choices ?? '{}', true) ?: [];
        return view('customer.sublimation.product', compact('item', 'choices'));
    }

    public function order(Request $request, int $order): View
    {
        $this->enabled();
        $item = DB::table('sublimation_orders')->where('id', $order)
            ->where('customer_user_id', (int) $request->user()->id)->firstOrFail();
        $choices = json_decode($item->personalization_options ?? '{}', true) ?: [];
        return view('customer.sublimation.order', compact('item', 'choices'));
    }

    public function place(Request $request): RedirectResponse
    {
        $this->enabled();
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'customer_name' => ['required', 'string', 'max:160'],
            'customer_mobile' => ['required', 'string', 'min:8', 'max:25'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'design_instructions' => ['nullable', 'string', 'max:3000'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'fulfillment_method' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_address' => ['required_if:fulfillment_method,delivery', 'nullable', 'string', 'max:1000'],
        ]);
        $actor = (int) $request->user()->id;
        DB::transaction(function () use ($request, $actor, $data): void {
            $product = $this->orderable()->where('p.id', (int) $data['product_id'])
                ->select('p.*')->lockForUpdate()->first();
            if (! $product) {
                throw ValidationException::withMessages(['product_id' => 'This item is not accepting orders.']);
            }
            $choices = json_decode($product->option_choices ?? '{}', true) ?: [];
            $selected = [];
            foreach (['size', 'color'] as $key) {
                $allowed = is_array($choices[$key] ?? null) ? $choices[$key] : [];
                $value = trim((string) ($data[$key] ?? ''));
                if ($allowed && ($value === '' || !in_array($value, $allowed, true))) {
                    throw ValidationException::withMessages([$key => 'Select an available '. $key .'.']);
                }
                if (!$allowed && $value !== '') {
                    throw ValidationException::withMessages([$key => 'This item has no '. $key .' option.']);
                }
                if ($value !== '') $selected[$key] = $value;
            }
            $quantity = (int) $data['quantity'];
            $unitCost = (int) $product->blank_cost_paise + (int) $product->print_cost_paise
                + (int) $product->packing_cost_paise + (int) $product->other_cost_paise;
            $number = 'SUB-'.Str::upper((string) Str::ulid());
            $id = DB::table('sublimation_orders')->insertGetId([
                'business_id' => $product->business_id, 'branch_id' => $product->branch_id,
                'product_id' => $product->id, 'customer_user_id' => $actor,
                'order_no' => $number, 'customer_name' => trim($data['customer_name']),
                'customer_mobile' => trim($data['customer_mobile']),
                'product_name_snapshot' => $product->name, 'quantity' => $quantity,
                'unit_cost_paise' => $unitCost,
                'unit_sale_paise' => (int) $product->sale_price_paise,
                'total_cost_paise' => $unitCost * $quantity,
                'total_sale_paise' => (int) $product->sale_price_paise * $quantity,
                'status' => 'design_pending',
                'design_instructions' => $data['design_instructions'] ?? null,
                'personalization_options' => $selected ? json_encode($selected, JSON_UNESCAPED_UNICODE) : null,
                'fulfillment_method' => $data['fulfillment_method'],
                'delivery_address' => $data['fulfillment_method'] === 'delivery'
                    ? trim((string) $data['delivery_address']) : null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id' => $actor, 'action' => 'sublimation.order.customer_placed',
                'entity_type' => 'sublimation_order', 'entity_id' => $id,
                'new_values' => json_encode([
                    'business_id' => $product->business_id, 'branch_id' => $product->branch_id,
                    'order_no' => $number, 'status' => 'design_pending',
                    'fulfillment_method' => $data['fulfillment_method'],
                ]),
                'ip_address' => $request->ip(), 'created_at' => now(),
            ]);
        });
        return redirect()->route('customer.sublimation.orders')
            ->with('success', 'Order request received. Studio review and design approval are still required.');
    }

    public function orders(Request $request): View
    {
        $this->enabled();
        $orders = DB::table('sublimation_orders')
            ->where('customer_user_id', (int) $request->user()->id)
            ->orderByDesc('id')->paginate(20);
        return view('customer.sublimation.orders', compact('orders'));
    }

    private function orderable()
    {
        return DB::table('sublimation_products as p')
            ->join('businesses as b', 'b.id', '=', 'p.business_id')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->join('branches as br', function ($join) {
                $join->on('br.id', '=', 'p.branch_id')->on('br.business_id', '=', 'p.business_id');
            })
            ->whereIn('b.business_type', self::TYPES)
            ->where('b.status', 'approved')->where('v.status', 'approved')
            ->where('br.status', 'active')
            ->where('b.is_accepting_orders', true)->where('br.is_accepting_orders', true)
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')->whereNull('br.deleted_at')
            ->where('p.is_active', true)->where('p.sale_price_paise', '>', 0);
    }

    private function enabled(): void
    {
        abort_unless(app(ModuleRegistry::class)->isEnabled('sublimation'), 503,
            'Sublimation ordering is temporarily unavailable.');
    }
}
