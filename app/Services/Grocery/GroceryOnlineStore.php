<?php

namespace App\Services\Grocery;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

/** Marketplace-independent Grocery online ordering; NEVER collects or authorizes payments. */
final class GroceryOnlineStore
{
    public const CART_KEY = 'tidong_grocery_cart_v2';
    public const CUSTOMER_STATUSES = ['pending', 'confirmed', 'packing', 'ready', 'completed', 'cancelled', 'rejected'];

    public static function store(int $businessId, int $branchId): array
    {
        abort_unless(Schema::hasTable('grocery_online_orders'), 503, 'Grocery V2 migration is required.');
        GroceryFeatures::requireEnabled('catalogue', $branchId);
        GroceryFeatures::requireEnabled('pricing', $branchId);
        GroceryFeatures::requireEnabled('inventory', $branchId);
        GroceryFeatures::requireEnabled('online_store', $branchId);
        $business = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $businessId)->where('b.business_type', 'grocery')
            ->where('b.status', 'approved')->where('b.is_accepting_orders', true)
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')->where('v.status', 'approved')
            ->select('b.*')->first();
        abort_unless($business, 404, 'This grocery store is unavailable.');
        $branch = DB::table('branches')->where('id', $branchId)->where('business_id', $business->id)
            ->where('status', 'active')->where('is_accepting_orders', true)->whereNull('deleted_at')->first();
        abort_unless($branch, 404, 'This store branch is unavailable.');
        abort_unless(filled($branch->state), 422, 'Set branch state before accepting online orders.');
        return [$business, $branch];
    }

    public static function productQuery(int $businessId, int $branchId)
    {
        return DB::table('grocery_products as p')
            ->where('p.business_id', $businessId)->where('p.branch_id', $branchId)
            ->where('p.is_active', true)->where('p.offline_sale_paise', '>', 0)
            ->whereRaw('COALESCE(p.online_sale_paise, p.offline_sale_paise) > 0');
    }

    /** Real-time stock can change before checkout; only checkout guarantees allocation. */
    public static function available(int $productId, int $businessId, int $branchId): int
    {
        return (int) DB::table('grocery_stock_lots')
            ->where('product_id', $productId)->where('business_id', $businessId)
            ->where('branch_id', $branchId)->where('is_sellable', true)
            ->where('available_qty_milli', '>', 0)
            ->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', today()->toDateString()))
            ->sum('available_qty_milli');
    }

    public static function viewCart(?array $cart, int $businessId, int $branchId): array
    {
        if (!$cart || ($cart['business'] ?? 0) !== $businessId || ($cart['branch'] ?? 0) !== $branchId) {
            return ['lines' => [], 'subtotal' => 0, 'mrp' => 0, 'discount' => 0, 'tax' => 0, 'payable' => 0];
        }
        $ids = array_map('intval', array_keys($cart['items'] ?? []));
        $products = self::productQuery($businessId, $branchId)->whereIn('p.id', $ids)->get()->keyBy('id');
        $lines = []; $subtotal = 0; $mrp = 0; $taxTotal = 0; $payable = 0;
        foreach ($ids as $id) {
            $p = $products->get($id);
            if (!$p) continue; // products can become unavailable; revalidate at checkout
            $quantity = (int) ($cart['items'][$id] ?? 0);
            if ($quantity <= 0) continue;
            $price = (int) ($p->online_sale_paise ?? $p->offline_sale_paise);
            $unitMrp = (int) ($p->online_mrp_paise ?? $p->offline_mrp_paise);
            $line = intdiv($price * $quantity + 500, 1000);
            $lineMrp = intdiv($unitMrp * $quantity + 500, 1000);
            $subtotal += $line; $mrp += $lineMrp;
            $computed = GroceryMath::line($price, $quantity, (int)$p->gst_rate_bps, (bool)$p->tax_inclusive, false);
            $taxTotal += $computed['tax_paise']; $payable += $computed['total_paise'];
            $lines[] = ['product' => $p, 'quantity' => $quantity, 'price' => $price,
                'total' => $line, 'mrp' => $lineMrp, 'available' => self::available($id, $businessId, $branchId)];
        }
        return ['lines' => $lines, 'subtotal' => $subtotal, 'mrp' => $mrp, 'discount' => max(0, $mrp - $subtotal), 'tax' => $taxTotal, 'payable' => $payable];
    }

    public static function place(int $customerId, int $businessId, int $branchId, array $cart, array $data): object
    {
        GroceryFeatures::requireEnabled('online_orders', $branchId);
        abort_unless(($cart['business'] ?? null) === $businessId && ($cart['branch'] ?? null) === $branchId, 422);
        $token = $cart['token'] ?? '';
        abort_unless(is_string($token) && preg_match('/^[a-f0-9-]{36}$/D', $token), 422, 'Invalid cart token.');
        $items = $cart['items'] ?? [];
        if (!is_array($items) || count($items) < 1 || count($items) > 40) {
            throw ValidationException::withMessages(['cart' => 'Your cart needs 1–40 available products.']);
        }
        return DB::transaction(function () use ($customerId, $businessId, $branchId, $items, $token, $data): object {
            // Serialise checkout for the same account (also makes replayed submit idempotent).
            DB::table('users')->where('id', $customerId)->lockForUpdate()->firstOrFail();
            $existing = DB::table('grocery_online_orders')->where('checkout_token', $token)
                ->where('customer_user_id', $customerId)->first();
            if ($existing) return $existing;
            [$business, $branch] = self::store($businessId, $branchId);
            GroceryFeatures::requireEnabled('online_orders', $branchId);
            $state = ($data['fulfilment_type'] === 'pickup') ? $branch->state : trim($data['supply_state']);
            $interstate = mb_strtolower(trim($state)) !== mb_strtolower(trim($branch->state));
            $totals = array_fill_keys(['taxable_paise', 'cgst_paise', 'sgst_paise', 'igst_paise', 'tax_paise', 'total_paise'], 0);
            $lines = []; $allocations = []; $discount = 0;
            ksort($items, SORT_NUMERIC);
            foreach ($items as $productId => $quantity) {
                if (!ctype_digit((string) $productId) || (int) $productId <= 0 || !is_int($quantity) || $quantity <= 0) {
                    throw ValidationException::withMessages(['cart' => 'Invalid cart quantity.']);
                }
                $p = self::productQuery($businessId, $branchId)->where('p.id', (int) $productId)
                    ->lockForUpdate()->first();
                if (!$p) throw ValidationException::withMessages(['cart' => 'A product is no longer available. Please review your cart.']);
                if (in_array($p->unit, ['pcs', 'pack'], true) && $quantity % 1000 !== 0) {
                    throw ValidationException::withMessages(['cart' => 'Pack and piece quantities must be whole numbers.']);
                }
                $unitSale = (int) ($p->online_sale_paise ?? $p->offline_sale_paise);
                $unitMrp = (int) ($p->online_mrp_paise ?? $p->offline_mrp_paise);
                if ($unitSale <= 0 || $unitSale > $unitMrp) {
                    throw ValidationException::withMessages(['cart' => 'A price has changed. Please contact the store.']);
                }
                $lots = DB::table('grocery_stock_lots')->where('product_id', $p->id)
                    ->where('business_id', $businessId)->where('branch_id', $branchId)
                    ->where('is_sellable', true)->where('available_qty_milli', '>', 0)
                    ->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', today()->toDateString()))
                    ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('expiry_date')->orderBy('id')->lockForUpdate()->get();
                $needed = $quantity;
                foreach ($lots as $lot) {
                    if ($needed <= 0) break;
                    $take = min($needed, (int) $lot->available_qty_milli);
                    $allocations[] = ['product_id' => (int) $p->id, 'lot_id' => (int) $lot->id, 'qty' => $take];
                    $needed -= $take;
                }
                if ($needed !== 0) throw ValidationException::withMessages(['cart' => "Not enough unexpired stock for {$p->name}."]);
                $tax = GroceryMath::line($unitSale, $quantity, (int) $p->gst_rate_bps, (bool) $p->tax_inclusive, $interstate);
                foreach ($totals as $key => $v) $totals[$key] += $tax[$key];
                $mrpLine = intdiv($unitMrp * $quantity + 500, 1000);
                $saleLine = intdiv($unitSale * $quantity + 500, 1000);
                $lineDiscount = max(0, $mrpLine - $saleLine); $discount += $lineDiscount;
                $lines[] = compact('p', 'quantity', 'unitMrp', 'unitSale', 'lineDiscount', 'tax');
            }
            $id = DB::table('grocery_online_orders')->insertGetId(array_merge([
                'checkout_token' => $token, 'order_number' => 'GO-'.Str::upper((string) Str::ulid()),
                'business_id' => $businessId, 'branch_id' => $branchId,
                'customer_user_id' => $customerId, 'status' => 'pending',
                'fulfilment_type' => $data['fulfilment_type'],
                'payment_method' => 'pay_at_handover', 'payment_status' => 'unpaid',
                'contact_name' => trim($data['contact_name']), 'contact_phone' => trim($data['contact_phone']),
                'address_line' => $data['fulfilment_type'] === 'delivery_request' ? trim($data['address_line']) : null,
                'city' => $data['fulfilment_type'] === 'delivery_request' ? trim($data['city']) : null,
                'postal_code' => $data['fulfilment_type'] === 'delivery_request' ? trim($data['postal_code']) : null,
                'supply_state' => $state, 'branch_state_snapshot' => $branch->state,
                'interstate' => $interstate, 'customer_note' => $data['customer_note'] ?? null,
                'discount_paise' => $discount, 'created_at' => now(), 'updated_at' => now(),
            ], $totals));
            foreach ($lines as $line) {
                $p = $line['p']; $tax = $line['tax'];
                DB::table('grocery_online_order_items')->insert(array_merge([
                    'order_id' => $id, 'product_id' => $p->id,
                    'sku_snapshot' => $p->sku, 'name_snapshot' => $p->name,
                    'unit_snapshot' => $p->unit, 'hsn_snapshot' => $p->hsn_code,
                    'quantity_milli' => $line['quantity'], 'unit_mrp_paise' => $line['unitMrp'],
                    'unit_sale_paise' => $line['unitSale'], 'discount_paise' => $line['lineDiscount'],
                    'gst_rate_bps_snapshot' => $p->gst_rate_bps, 'tax_inclusive_snapshot' => $p->tax_inclusive,
                    'created_at' => now(),
                ], $tax));
            }
            foreach ($allocations as $a) {
                $changed = DB::table('grocery_stock_lots')->where('id', $a['lot_id'])
                    ->where('available_qty_milli', '>=', $a['qty'])
                    ->decrement('available_qty_milli', $a['qty'], ['updated_at' => now()]);
                if ($changed !== 1) throw ValidationException::withMessages(['cart' => 'Stock just changed. Please retry.']);
                DB::table('grocery_online_order_allocations')->insert([
                    'order_id' => $id, 'product_id' => $a['product_id'],
                    'lot_id' => $a['lot_id'], 'quantity_milli' => $a['qty'], 'created_at' => now(),
                ]);
                DB::table('grocery_stock_movements')->insert([
                    'business_id' => $businessId, 'branch_id' => $branchId,
                    'product_id' => $a['product_id'], 'lot_id' => $a['lot_id'],
                    'sale_id' => null, 'movement_type' => 'online_order',
                    'delta_qty_milli' => -$a['qty'],
                    'reason' => 'Online order #'.$id, 'actor_user_id' => $customerId, 'created_at' => now(),
                ]);
            }
            self::event($id, $customerId, null, 'pending', 'Customer order placed; payment not captured.');
            return DB::table('grocery_online_orders')->where('id', $id)->firstOrFail();
        }, 3);
    }

    public static function transition(object $order, int $actorId, string $next, string $note = ''): void
    {
        $status = $order->status;
        $allowed = [
            'pending' => ['confirmed', 'rejected', 'cancelled'],
            'confirmed' => ['packing', 'rejected'],
            'packing' => ['ready'],
            'ready' => ['completed'],
        ];
        if (!in_array($next, $allowed[$status] ?? [], true)) {
            throw ValidationException::withMessages(['status' => 'This status change is not allowed.']);
        }
        // Rejected/cancelled orders are always restocked atomically. Original movements survive.
        if (in_array($next, ['cancelled', 'rejected'], true)) {
            $allocations = DB::table('grocery_online_order_allocations')->where('order_id', $order->id)
                ->orderBy('lot_id')->get();
            foreach ($allocations as $a) {
                DB::table('grocery_stock_lots')->where('id', $a->lot_id)->increment('available_qty_milli',
                    (int) $a->quantity_milli, ['updated_at' => now()]);
                DB::table('grocery_stock_movements')->insert([
                    'business_id' => $order->business_id, 'branch_id' => $order->branch_id,
                    'product_id' => $a->product_id, 'lot_id' => $a->lot_id,
                    'sale_id' => null, 'movement_type' => 'online_order_restore',
                    'delta_qty_milli' => $a->quantity_milli,
                    'reason' => 'Online order #'.$order->id.' '.$next,
                    'actor_user_id' => $actorId, 'created_at' => now(),
                ]);
            }
        }
        DB::table('grocery_online_orders')->where('id', $order->id)
            ->update(['status' => $next, 'closed_at' => in_array($next, ['completed','cancelled','rejected'], true) ? now() : null,
                'updated_at' => now()]);
        self::event($order->id, $actorId, $status, $next, $note);
    }

    public static function event(int $orderId, int $userId, ?string $from, string $to, ?string $note): void
    {
        DB::table('grocery_online_order_events')->insert([
            'order_id' => $orderId, 'actor_user_id' => $userId,
            'from_status' => $from, 'to_status' => $to, 'note' => $note,
            'created_at' => now(),
        ]);
    }
}
