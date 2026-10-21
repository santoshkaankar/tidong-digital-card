<?php

namespace App\Services\Pos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PosOrderService
{
    /** Restrict this initial counter POS to supported food businesses. */
    public const BUSINESS_TYPES = ['restaurant', 'cafe'];

    public function ownedBranch(Request $request, int $branchId, bool $lock = false): object
    {
        $query = DB::table('branches as br')
            ->join('businesses as bu', 'bu.id', '=', 'br.business_id')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('br.id', $branchId)
            ->where('ve.user_id', $request->user()->id)
            ->whereNull('ve.deleted_at')
            ->whereNull('bu.deleted_at')
            ->whereNull('br.deleted_at')
            ->select('br.id', 'br.name', 'br.status as branch_status',
                'br.is_accepting_orders as branch_accepting', 'bu.id as business_id',
                'bu.name as business_name', 'bu.business_type', 'bu.status as business_status',
                'bu.is_accepting_orders as business_accepting', 've.id as vendor_id',
                've.status as vendor_status');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->firstOrFail();
    }

    public function ownedBusiness(Request $request, int $businessId, bool $lock = false): object
    {
        $query = DB::table('businesses as bu')
            ->join('vendors as ve', 've.id', '=', 'bu.vendor_id')
            ->where('bu.id', $businessId)
            ->where('ve.user_id', $request->user()->id)
            ->whereNull('bu.deleted_at')
            ->whereNull('ve.deleted_at')
            ->select('bu.id', 'bu.name', 'bu.status', 'bu.business_type',
                'bu.is_accepting_orders', 've.status as vendor_status', 've.id as vendor_id');

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->firstOrFail();
    }

    public function ensureApproved(object $row): void
    {
        $businessStatus = $row->business_status ?? $row->status;
        if ($row->vendor_status !== 'approved' || $businessStatus !== 'approved'
            || ! in_array($row->business_type, self::BUSINESS_TYPES, true)) {
            throw ValidationException::withMessages([
                'business' => 'This restaurant/cafe must have approved Vendor and Business status.',
            ]);
        }
    }

    public function ensureCanOrder(object $branch): void
    {
        $this->ensureApproved($branch);
        if ($branch->branch_status !== 'active'
            || ! $branch->business_accepting || ! $branch->branch_accepting) {
            throw ValidationException::withMessages([
                'branch' => 'New orders are disabled for this Business or Branch.',
            ]);
        }
    }

    public function audit(Request $request, string $action, string $type, int $id,
        array $newValues = [], ?array $oldValues = null, ?string $reason = null): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $request->user()->id,
            'action' => $action,
            'entity_type' => $type,
            'entity_id' => $id,
            'reason' => $reason,
            'old_values' => $oldValues === null ? null : json_encode($oldValues),
            'new_values' => json_encode($newValues),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }

    /** No float arithmetic for money. Value is a string such as 125.50. */
    public function rupeesToPaise(string $amount): int
    {
        if (! preg_match('/^\d{1,7}(?:\.\d{1,2})?$/', $amount)) {
            throw ValidationException::withMessages(['price' => 'Enter a valid amount in rupees.']);
        }
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');
        $paise = ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
        if ($paise < 1) {
            throw ValidationException::withMessages(['price' => 'Price must be greater than zero.']);
        }
        return $paise;
    }

    public function createOrder(Request $request, int $branchId, array $data): int
    {
        return DB::transaction(function () use ($request, $branchId, $data): int {
            $branch = $this->ownedBranch($request, $branchId, true);
            $this->ensureCanOrder($branch);

            $inputItems = collect($data['items']);
            $ids = $inputItems->pluck('menu_item_id')->map(fn ($id) => (int) $id)->all();
            if (count($ids) !== count(array_unique($ids))) {
                throw ValidationException::withMessages(['items' => 'Each item can appear only once.']);
            }
            $menuItems = DB::table('pos_menu_items')
                ->where('branch_id', $branchId)
                ->where('is_active', true)
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            if ($menuItems->count() !== count($ids)) {
                throw ValidationException::withMessages(['items' => 'An item is unavailable or belongs to another Branch.']);
            }

            $rows = [];
            $total = 0;
            foreach ($inputItems as $input) {
                $menu = $menuItems->get((int) $input['menu_item_id']);
                $quantity = (int) $input['quantity'];
                $line = (int) $menu->price_paise * $quantity;
                $total += $line;
                $rows[] = [
                    'menu_item_id' => $menu->id,
                    'name_snapshot' => $menu->name,
                    'quantity' => $quantity,
                    'unit_price_paise' => $menu->price_paise,
                    'line_total_paise' => $line,
                    'created_at' => now(), 'updated_at' => now(),
                ];
            }

            $tableId = isset($data['dining_table_id']) && $data['dining_table_id'] !== '' ? (int)$data['dining_table_id'] : null;
            if ($tableId !== null && ! DB::table('restaurant_dining_tables')
                ->where('id',$tableId)->where('branch_id',$branchId)->where('is_active',true)->exists()) {
                throw ValidationException::withMessages(['dining_table_id'=>'This table is not active in the Branch.']);
            }
            $origin = $data['order_origin'] ?? ($tableId === null ? 'counter' : 'waiter');
            if (! in_array($origin,['counter','waiter','qr'],true)) {
                throw ValidationException::withMessages(['order_origin'=>'Invalid order source.']);
            }
            $orderId = DB::table('pos_orders')->insertGetId(['dining_table_id'=>$tableId,'order_origin'=>$origin,
                'order_number' => 'POS-'.Str::upper((string) Str::ulid()),
                'vendor_id' => $branch->vendor_id,
                'business_id' => $branch->business_id,
                'branch_id' => $branchId,
                'created_by_user_id' => $request->user()->id,
                'status' => 'received', 'payment_status' => 'unpaid',
                'subtotal_paise' => $total, 'total_paise' => $total,
                'customer_note' => $data['customer_note'] ?? null,
                'placed_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($rows as &$row) {
                $row['order_id'] = $orderId;
            }
            unset($row);
            DB::table('pos_order_items')->insert($rows);
            $this->audit($request, 'pos.order.created', 'pos_order', $orderId,
                ['branch_id' => $branchId, 'total_paise' => $total, 'status' => 'received']);
            return $orderId;
        });
    }
}
