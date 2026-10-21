<?php

namespace Tests\Feature;

use App\Models\Customer\CustomerAccountAuditLog;
use App\Models\Customer\CustomerAddress;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountModuleTest extends TestCase
{
    use RefreshDatabase;

    // Prevent accidentally refreshing the main/production MySQL or PostgreSQL database.
    protected function beforeRefreshingDatabase(): void
    {
        $driver = config('database.default');
        $database = config("database.connections.{$driver}.database");
        $safe = ($driver === 'sqlite' && $database === ':memory:')
            || (in_array($driver, ['mysql', 'pgsql'], true) && $database === 'digital_test_db');
        if (! $safe) {
            throw new \RuntimeException('STOP: Run Customer tests only with SQLite :memory: or digital_test_db.');
        }
    }

    private function customer(string $email = 'customer@example.test'): User
    {
        $type = AccountType::firstOrCreate(
            ['code' => 'customer'],
            ['name' => 'Customer', 'is_active' => true],
        );
        $user = User::create([
            'name' => 'Test Customer',
            'email' => $email,
            'password' => 'CurrentPassword123!',
        ]);
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        return $user;
    }

    private function addressData(array $changes = []): array
    {
        return array_merge([
            'label' => 'Home',
            'recipient_name' => 'A Customer',
            'phone' => '+919876543210',
            'address_line_1' => '21 Sample Road',
            'address_line_2' => '',
            'city' => 'Kota',
            'state' => 'Rajasthan',
            'postal_code' => '324001',
            'country_code' => 'IN',
            'is_default' => '0',
        ], $changes);
    }

    public function test_public_registration_is_closed_on_get_and_post_by_default(): void
    {
        config()->set('tidong_customer.public_registration_enabled', false);
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Unapproved', 'email' => 'unapproved@example.test',
            'password' => 'newpassword123', 'password_confirmation' => 'newpassword123',
        ])->assertNotFound();
        $this->assertDatabaseMissing('users', ['email' => 'unapproved@example.test']);
    }

    public function test_registration_can_be_explicitly_enabled_for_approved_environment(): void
    {
        AccountType::create(['code' => 'customer', 'name' => 'Customer', 'is_active' => true]);
        config()->set('tidong_customer.public_registration_enabled', true);
        $this->post('/register', [
            'name' => 'Approved Customer', 'email' => 'new@example.test',
            'password' => 'newpassword123', 'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('customer.home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'new@example.test']);
    }

    public function test_only_active_customer_accounts_can_access_customer_area(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->get(route('customer.home'))->assertOk();
        $customer->userAccountTypes()->update(['status' => 'inactive']);
        $this->actingAs($customer)->get(route('customer.home'))->assertForbidden();
        $this->actingAs($customer)->get(route('customer.addresses.index'))->assertForbidden();
    }

    public function test_customer_module_flag_blocks_customer_area(): void
    {
        config()->set('tidong_customer.enabled', false);
        $this->actingAs($this->customer())->get(route('customer.home'))->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('customer.profile.edit'))->assertRedirect(route('login'));
        $this->get(route('customer.addresses.index'))->assertRedirect(route('login'));
    }

    public function test_profile_update_is_isolated_and_audited(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->put(route('customer.profile.update'), [
            'name' => 'Changed Name', 'email' => $customer->email, 'phone' => '+919998887776',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'name' => 'Changed Name']);
        $this->assertDatabaseHas('customer_profiles', ['user_id' => $customer->id, 'phone' => '+919998887776']);
        $this->assertDatabaseHas('customer_account_audit_logs', ['user_id' => $customer->id, 'event_code' => 'profile_updated']);
    }

    public function test_email_change_requires_current_password(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->from(route('customer.profile.edit'))->put(route('customer.profile.update'), [
            'name' => $customer->name, 'email' => 'other@example.test', 'phone' => '',
        ])->assertSessionHasErrors('current_password');
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'email' => $customer->email]);
        $this->actingAs($customer)->put(route('customer.profile.update'), [
            'name' => $customer->name, 'email' => 'other@example.test',
            'current_password' => 'CurrentPassword123!', 'phone' => '',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'email' => 'other@example.test']);
    }

    public function test_preferences_use_explicit_marketing_opt_in(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->put(route('customer.preferences.update'), [
            'preferred_locale' => 'hi', 'marketing_opt_in' => '1',
        ])->assertRedirect();
        $this->assertDatabaseHas('customer_profiles', ['user_id' => $customer->id, 'preferred_locale' => 'hi', 'marketing_opt_in' => true]);
        $this->actingAs($customer)->put(route('customer.preferences.update'), [
            'preferred_locale' => 'en', 'marketing_opt_in' => '0',
        ])->assertRedirect();
        $this->assertDatabaseHas('customer_profiles', ['user_id' => $customer->id, 'preferred_locale' => 'en', 'marketing_opt_in' => false]);
    }

    public function test_password_change_validates_current_password_and_writes_audit(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->put(route('customer.security.password'), [
            'current_password' => 'wrong-password', 'password' => 'DifferentPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('CurrentPassword123!', $customer->fresh()->password));

        $this->actingAs($customer)->put(route('customer.security.password'), [
            'current_password' => 'CurrentPassword123!', 'password' => 'DifferentPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('DifferentPassword123!', $customer->fresh()->password));
        $this->assertDatabaseHas('customer_account_audit_logs', ['user_id' => $customer->id, 'event_code' => 'password_changed']);
    }

    public function test_first_address_is_default_and_only_owner_can_read_or_modify_it(): void
    {
        $owner = $this->customer('owner@example.test');
        $other = $this->customer('other@example.test');
        $this->actingAs($owner)->post(route('customer.addresses.store'), $this->addressData())
            ->assertRedirect(route('customer.addresses.index'));
        $address = CustomerAddress::where('user_id', $owner->id)->firstOrFail();
        $this->assertTrue($address->is_default);

        $this->actingAs($other)->get(route('customer.addresses.edit', $address->id))->assertNotFound();
        $this->actingAs($other)->put(route('customer.addresses.update', $address->id), $this->addressData(['city' => 'Jaipur']))->assertNotFound();
        $this->actingAs($other)->post(route('customer.addresses.default', $address->id))->assertNotFound();
        $this->actingAs($other)->delete(route('customer.addresses.destroy', $address->id))->assertNotFound();
        $this->assertDatabaseHas('customer_addresses', ['id' => $address->id, 'city' => 'Kota']);
    }

    public function test_default_address_switches_and_reassigns_after_deletion(): void
    {
        $user = $this->customer();
        $this->actingAs($user)->post(route('customer.addresses.store'), $this->addressData());
        $this->actingAs($user)->post(route('customer.addresses.store'), $this->addressData(['label' => 'Work', 'city' => 'Udaipur']));
        $first = $user->customerAddresses()->orderBy('id')->firstOrFail();
        $second = $user->customerAddresses()->orderByDesc('id')->firstOrFail();
        $this->actingAs($user)->post(route('customer.addresses.default', $second->id))->assertRedirect();
        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($second->fresh()->is_default);
        $this->actingAs($user)->delete(route('customer.addresses.destroy', $second->id))->assertRedirect();
        $this->assertTrue($first->fresh()->is_default);
        $this->assertSame(1, $user->customerAddresses()->where('is_default', true)->count());
    }

    public function test_address_count_limit_and_validation_protect_database(): void
    {
        config()->set('tidong_customer.max_addresses', 1);
        $user = $this->customer();
        $this->actingAs($user)->post(route('customer.addresses.store'), $this->addressData())->assertRedirect();
        $this->actingAs($user)->post(route('customer.addresses.store'), $this->addressData(['label' => 'Work']))->assertSessionHasErrors('address');
        $this->assertSame(1, $user->customerAddresses()->count());
        $this->actingAs($user)->put(route('customer.addresses.update', $user->customerAddresses()->first()->id),
            $this->addressData(['country_code' => '???', 'address_line_1' => '']))->assertSessionHasErrors(['country_code', 'address_line_1']);
    }

    public function test_customer_dashboard_does_not_expose_other_accounts(): void
    {
        $user = $this->customer('only@example.test');
        $other = $this->customer('else@example.test');
        $other->customerAddresses()->create($this->addressData(['is_default' => true]));
        $this->actingAs($user)->get(route('customer.addresses.index'))
            ->assertOk()->assertDontSee('21 Sample Road');
        $this->assertSame(0, $user->customerAddresses()->count());
    }
}
