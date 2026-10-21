<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Affiliate\AffiliateEstimateService;
use App\Services\Affiliate\AffiliateMembershipService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Integration candidate tests. Execute ONLY against digital_test_db, never digital_db. */
final class AffiliateModuleReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $catalog = config('tidong_modules');
        $catalog['affiliate'] = [
            'name' => 'Affiliate (Registration Only)', 'installed' => true,
            'default_enabled' => false, 'paths' => ['affiliate', 'affiliate/*', 'admin/affiliate', 'admin/affiliate/*'],
        ];
        config()->set('tidong_modules', $catalog);
        DB::table('platform_modules')->insert([
            'code' => 'affiliate', 'name' => 'Affiliate (Registration Only)', 'is_enabled' => true,
            'updated_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function customer(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $typeId = DB::table('account_types')->where('code', 'customer')->value('id');
        if (! $typeId) {
            $typeId = DB::table('account_types')->insertGetId([
                'code' => 'customer', 'name' => 'Customer', 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        DB::table('user_account_types')->insert([
            'user_id' => $user->id, 'account_type_id' => $typeId,
            'status' => 'active', 'activated_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return $user;
    }

    private function root(): object
    {
        $firstCustomer = $this->customer();
        return app(AffiliateMembershipService::class)->initializeRoot($firstCustomer->id, $firstCustomer->id);
    }

    public function test_enrolls_existing_customer_free_of_charge_without_wallet_credit(): void
    {
        $this->root();
        $user = $this->customer();
        $this->actingAs($user)->post(route('affiliate.enroll'), ['accept_terms' => '1'])
            ->assertRedirect(route('affiliate.index'));
        $this->assertDatabaseHas('affiliate_members', ['user_id' => $user->id]);
        $this->assertDatabaseHas('affiliate_settings', ['id' => 1, 'registration_fee_paise' => 0, 'payouts_enabled' => false]);
        $this->assertDatabaseCount('affiliate_estimates', 0);
    }

    public function test_duplicate_enrollment_keeps_single_membership(): void
    {
        $this->root();
        $user = $this->customer();
        $this->actingAs($user)->post(route('affiliate.enroll'), ['accept_terms' => '1']);
        $this->actingAs($user)->post(route('affiliate.enroll'), ['accept_terms' => '1']);
        $this->assertDatabaseCount('affiliate_members', 2);
    }

    public function test_existing_sponsor_binding_cannot_be_changed_by_another_enrollment(): void
    {
        $this->root();
        $user = $this->customer();
        $this->actingAs($user)->post(route('affiliate.enroll'), ['accept_terms' => '1']);
        $member = DB::table('affiliate_members')->where('user_id', $user->id)->first();
        // Owner cannot re-bind their existing sponsor: second enroll is a no-op.
        $this->actingAs($user)->post(route('affiliate.enroll'), [
            'accept_terms' => '1', 'referral_code' => $member->referral_code,
        ]);
        $this->assertDatabaseHas('affiliate_members', ['user_id' => $user->id, 'referrer_member_id' => null]);
    }

    public function test_guest_cannot_register_as_affiliate(): void
    {
        $this->root();
        $this->post(route('affiliate.enroll'), ['accept_terms' => '1'])
            ->assertRedirect(route('login'));
        $this->assertDatabaseCount('affiliate_members', 1);
    }

    public function test_no_sale_estimates_when_default_rate_zero(): void
    {
        $this->root();
        $user = $this->customer();
        $this->actingAs($user)->post(route('affiliate.enroll'), ['accept_terms' => '1']);
        $result = app(AffiliateEstimateService::class)
            ->recordVerifiedSale('order-demo-001', $user->id, 100000, true);
        $this->assertNull($result);
        $this->assertDatabaseCount('affiliate_estimates', 0);
    }

    public function test_unreferred_customers_always_append_to_first_ids_a_chain(): void
    {
        $root = $this->root();
        $members = app(AffiliateMembershipService::class);
        $a = $members->enroll($this->customer()->id);
        $b = $members->enroll($this->customer()->id);
        $c = $members->enroll($this->customer()->id);

        $this->assertDatabaseHas('affiliate_members', [
            'id' => $a->id, 'placement_parent_member_id' => $root->id,
            'placement_leg' => 'A', 'referrer_member_id' => null,
        ]);
        $this->assertDatabaseHas('affiliate_members', [
            'id' => $b->id, 'placement_parent_member_id' => $a->id,
            'placement_leg' => 'A', 'referrer_member_id' => null,
        ]);
        $this->assertDatabaseHas('affiliate_members', [
            'id' => $c->id, 'placement_parent_member_id' => $b->id,
            'placement_leg' => 'A', 'referrer_member_id' => null,
        ]);
        $this->assertDatabaseHas('affiliate_settings', ['id' => 1, 'a_chain_tail_member_id' => $c->id]);
    }

    public function test_referred_placement_is_separate_from_actual_sponsor_attribution(): void
    {
        $root = $this->root();
        $members = app(AffiliateMembershipService::class);
        $sponsor = $members->enroll($this->customer()->id);
        $invitee = $members->enroll($this->customer()->id, $sponsor->referral_code, 'B');
        $this->assertDatabaseHas('affiliate_members', [
            'id' => $invitee->id, 'referrer_member_id' => $sponsor->id,
            'placement_parent_member_id' => $sponsor->id, 'placement_leg' => 'B',
        ]);
        $next = $members->enroll($this->customer()->id);
        $this->assertDatabaseHas('affiliate_members', [
            'id' => $next->id, 'referrer_member_id' => null,
            'placement_parent_member_id' => $sponsor->id, 'placement_leg' => 'A',
        ]);
        $this->assertNotSame($root->id, $next->placement_parent_member_id);
    }

    public function test_registration_does_not_create_an_arbitrary_first_id(): void
    {
        $user = $this->customer();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(AffiliateMembershipService::class)->enroll($user->id);
    }

    public function test_selected_referral_slot_cannot_be_reused(): void
    {
        $root = $this->root();
        $members = app(AffiliateMembershipService::class);
        $members->enroll($this->customer()->id, $root->referral_code, 'B');
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $members->enroll($this->customer()->id, $root->referral_code, 'B');
    }

    public function test_old_fourteen_stages_are_reference_only(): void
    {
        $this->assertDatabaseCount('affiliate_stage_references', 14);
        $this->assertSame(14, DB::table('affiliate_stage_references')->where('status', 'reference_only')->count());
        $this->assertDatabaseHas('affiliate_settings', ['id' => 1, 'wallet_credit_enabled' => false, 'withdrawals_enabled' => false]);
    }
}
