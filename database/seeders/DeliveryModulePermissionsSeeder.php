<?php

namespace Database\Seeders;

use App\Services\Platform\PlatformCapability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryModulePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $role = DB::table('roles')->where('code', 'admin_employee')->first();
            if (! $role) DB::table('roles')->insert([
                'code' => 'admin_employee', 'name' => 'Admin Employee',
                'description' => 'Individual capability grants only by default.', 'is_system' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach (PlatformCapability::CAPABILITIES as $code) {
                DB::table('permissions')->updateOrInsert(['code' => $code], [
                    'name' => ucwords(str_replace(['.', '_'], ' ', $code)),
                    'module' => str_starts_with($code, 'delivery.') ? 'delivery' : (str_starts_with($code, 'features.') ? 'features' : 'modules'),
                    'description' => 'Technical platform controls and aggregate diagnostics only; no firm operations.',
                    'updated_at' => now(), 'created_at' => now(),
                ]);
            }
            $admin = DB::table('roles')->where('code', 'admin')->first();
            if ($admin) {
                $ids = DB::table('permissions')->whereIn('code', PlatformCapability::CAPABILITIES)->pluck('id');
                foreach ($ids as $id) DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $admin->id, 'permission_id' => $id],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
            // Firm-specific permissions exist in the catalog, but are NOT granted to any
            // platform role. The Vendor must separately assign a scoped business role.
            foreach (['delivery.jobs.manage', 'delivery.partners.manage'] as $code) {
                DB::table('permissions')->updateOrInsert(['code' => $code], [
                    'name' => ucwords(str_replace(['.', '_'], ' ', $code)),
                    'module' => 'delivery',
                    'description' => 'Vendor-authorized business/branch operation; never a platform staff permission.',
                    'updated_at' => now(), 'created_at' => now(),
                ]);
            }
            // Never mass-assign permissions to 'admin_employee'.
        });
    }
}
