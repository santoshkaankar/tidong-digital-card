<?php
namespace App\Services\Grocery;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** V1 owner-only. Employee delegation deliberately not granted without vetted scoped permissions. */
final class GroceryAccess
{
    public static function business(Request $request, int $id): object
    {
        $business = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $id)->where('b.business_type', 'grocery')
            ->where('v.user_id', $request->user()->id)->where('v.status', 'approved')
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')->select('b.*')->first();
        abort_unless($business, 404, 'Grocery business was not found for this Vendor.');
        return $business;
    }

    public static function branch(object $business, int $branchId): object
    {
        $branch = DB::table('branches')->where('id', $branchId)
            ->where('business_id', $business->id)->whereNull('deleted_at')->first();
        abort_unless($branch, 404, 'Branch does not belong to this Grocery business.');
        return $branch;
    }

    public static function sellingAllowed(object $business, object $branch): void
    {
        abort_unless($business->status === 'approved' && (bool) $business->is_accepting_orders
            && $branch->status === 'active' && (bool) $branch->is_accepting_orders,
            403, 'Business and Branch must be approved, active and accepting orders.');
    }

    public static function audit(Request $request, string $action, string $type, ?int $id, array $data): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $request->user()->id, 'action' => $action,
            'entity_type' => $type, 'entity_id' => $id,
            'new_values' => json_encode($data, JSON_THROW_ON_ERROR),
            'ip_address' => $request->ip(), 'created_at' => now(),
        ]);
    }
}
