<?php

namespace Tests\Support;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Platform\FeatureRegistry;
use Illuminate\Support\Facades\DB;

/** Fixture uses the actual role-protected approval route; no direct status spoofing. */
trait ApprovesDeliveryPartnerForTest
{
    protected function approvePlatformDeliveryForTest(int $partnerId): void
    {
        $admin = User::factory()->create(['email' => bin2hex(random_bytes(8)).'@example.test']);
        $type = AccountType::firstOrCreate(['code' => 'platform_admin'],
            ['name' => 'Platform Admin', 'is_active' => true]);
        $type->update(['is_active' => true]);
        $admin->userAccountTypes()->create(['account_type_id' => $type->id,
            'status' => 'active', 'activated_at' => now()]);
        $role = Role::firstOrCreate(['code' => 'super_admin'],
            ['name' => 'Super Admin', 'is_system' => true]);
        $admin->roleAssignments()->create(['role_id' => $role->id, 'scope_type' => 'platform',
            'scope_id' => null, 'is_active' => true, 'starts_at' => now()->subMinute()]);
        $riderId = DB::table('delivery_partners')->where('id', $partnerId)->value('user_id');
        DB::table('users')->where('id', $riderId)->update(['email_verified_at' => now()]);
        DB::table('delivery_partners')->where('id', $partnerId)->update([
            'phone' => '9999999999', 'vehicle_type' => 'bike', 'updated_at' => now(),
        ]);
        app(FeatureRegistry::class)->setEnabled('delivery.partner_approval', true, (int)$admin->id);
        $this->actingAs($admin)->post(route('admin.delivery-partner-approvals.approve', $partnerId))
            ->assertRedirect();
        $this->assertDatabaseHas('delivery_partners', [
            'id' => $partnerId, 'approved_by' => $admin->id,
        ]);
    }
}
