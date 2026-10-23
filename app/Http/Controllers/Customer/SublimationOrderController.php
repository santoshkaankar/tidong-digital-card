<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Customer order requests: no online payment is collected here. */
class SublimationOrderController extends Controller
{
    private const TYPES = ['sublimation', 'custom_printing', 'printing'];

    public function shop(Request $request): View
    {
        $this->enabled();
        $validated = $request->validate([
            'business' => ['nullable', 'integer', 'min:1'],
        ]);
        $selectedBusinessId = isset($validated['business']) ? (int) $validated['business'] : null;
        $query = $this->orderable();
        if ($selectedBusinessId !== null) {
            $query->where('b.id', $selectedBusinessId);
        }
        $products = $query
            ->select('p.id', 'p.name', 'p.category', 'p.sale_price_paise',
                'b.name as business_name', 'br.name as branch_name')
            ->orderBy('p.name')->paginate(24);
        $selectedBusinessName = $selectedBusinessId !== null ? ($products->first()?->business_name) : null;
        return view('customer.sublimation.shop', compact('products', 'selectedBusinessId', 'selectedBusinessName'));
    }

    public function product(Request $request, int $product): View
    {
        $this->enabled();
        $item = $this->orderable()->where('p.id', $product)
            ->select('p.*', 'b.name as business_name', 'br.name as branch_name')->firstOrFail();
        $choices = json_decode($item->option_choices ?? '{}', true) ?: [];
        $savedAddresses = collect();
        if ($request->user() && \Illuminate\Support\Facades\Schema::hasTable('customer_addresses')) {
            $savedAddresses = DB::table('customer_addresses')
                ->where('user_id', (int) $request->user()->id)
                ->orderByDesc('is_default')->orderBy('id')->limit(10)->get();
        }
        return view('customer.sublimation.product', compact('item', 'choices', 'savedAddresses'));
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
            'customer_mobile' => ['required', 'string', 'max:25'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'design_instructions' => ['nullable', 'string', 'max:3000'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'fulfillment_method' => ['required', Rule::in(['pickup', 'delivery'])],
            'delivery_address_id' => ['nullable', 'integer', 'min:1'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],
            'request_token' => ['required', 'uuid'],
        ]);
        $mobile = $this->checkedPhone($data['customer_mobile']);
        $actor = (int) $request->user()->id;
        $token = $data['request_token'];
        ksort($data);
        $hash = hash('sha256', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        if ($existing = DB::table('sublimation_orders')->where('request_token', $token)->first()) {
            return $this->customerReplay($existing, $actor, $hash);
        }
        try {
            DB::transaction(function () use ($request, $actor, $data, $token, $hash, $mobile): void {
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
            $deliveryAddress = null;
            if ($data['fulfillment_method'] === 'delivery') {
                $deliveryAddress = trim((string) ($data['delivery_address'] ?? ''));
                if (!empty($data['delivery_address_id'])) {
                    if (!\Illuminate\Support\Facades\Schema::hasTable('customer_addresses')) {
                        throw ValidationException::withMessages(['delivery_address_id' => 'Saved addresses are not available yet. Enter a delivery address.']);
                    }
                    $saved = DB::table('customer_addresses')
                        ->where('id', (int) $data['delivery_address_id'])
                        ->where('user_id', $actor)->first();
                    if (!$saved) {
                        throw ValidationException::withMessages(['delivery_address_id' => 'The selected address does not belong to your account.']);
                    }
                    $deliveryAddress = implode(', ', array_filter([
                        $saved->recipient_name, $saved->phone, $saved->address_line_1,
                        $saved->address_line_2, $saved->city, $saved->state,
                        $saved->postal_code, $saved->country_code,
                    ], static fn ($part) => $part !== null && trim((string) $part) !== ''));
                }
                if ($deliveryAddress === '') {
                    throw ValidationException::withMessages(['delivery_address' => 'Select a saved address or enter a delivery address.']);
                }
                if (mb_strlen($deliveryAddress) > 1000) {
                    throw ValidationException::withMessages(['delivery_address' => 'The delivery address is too long.']);
                }
            }
            $quantity = (int) $data['quantity'];
            $unitCost = (int) $product->blank_cost_paise + (int) $product->print_cost_paise
                + (int) $product->packing_cost_paise + (int) $product->other_cost_paise;
            $number = 'SUB-'.Str::upper((string) Str::ulid());
            $id = DB::table('sublimation_orders')->insertGetId([
                'business_id' => $product->business_id, 'branch_id' => $product->branch_id,
                'product_id' => $product->id, 'customer_user_id' => $actor,
                'request_token' => $token, 'request_actor_user_id' => $actor, 'request_payload_hash' => $hash,
                'order_no' => $number, 'customer_name' => trim($data['customer_name']),
                'customer_mobile' => $mobile,
                'product_name_snapshot' => $product->name, 'quantity' => $quantity,
                'unit_cost_paise' => $unitCost,
                'unit_sale_paise' => (int) $product->sale_price_paise,
                'total_cost_paise' => $unitCost * $quantity,
                'total_sale_paise' => (int) $product->sale_price_paise * $quantity,
                'status' => 'design_pending',
                'design_instructions' => $data['design_instructions'] ?? null,
                'personalization_options' => $selected ? json_encode($selected, JSON_UNESCAPED_UNICODE) : null,
                'fulfillment_method' => $data['fulfillment_method'],
                'delivery_address' => $deliveryAddress,
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
        } catch (QueryException $e) {
            // Unique request-token collisions from concurrent retries replay the same order.
            $existing = DB::table('sublimation_orders')->where('request_token', $token)->first();
            if (! $existing) {
                throw $e;
            }
            return $this->customerReplay($existing, $actor, $hash);
        }
        return redirect()->route('customer.sublimation.orders')
            ->with('success', 'Order request received. Studio review and design approval are still required.');
    }

    private function checkedPhone(string $value): string
    {
        $phone = trim($value);
        $digits = preg_replace('/\D/', '', $phone);
        if (! preg_match('/^\+?[0-9][0-9 ()-]{6,23}$/D', $phone) || strlen($digits) < 8 || strlen($digits) > 15) {
            throw ValidationException::withMessages(['customer_mobile' => 'Use 8-15 phone digits, with optional +, spaces, brackets or hyphens.']);
        }
        return $phone;
    }

    private function customerReplay(object $existing, int $actor, string $hash): RedirectResponse
    {
        if ((int) $existing->request_actor_user_id !== $actor ||
            (int) $existing->customer_user_id !== $actor ||
            ! hash_equals((string) $existing->request_payload_hash, $hash)) {
            throw ValidationException::withMessages(['request_token' => 'This request token has already been used. Refresh the form.']);
        }
        return redirect()->route('customer.sublimation.orders')
            ->with('success', 'This order request was already received.');
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
