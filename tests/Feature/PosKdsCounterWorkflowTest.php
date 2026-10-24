<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PosKdsCounterWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        AccountType::firstOrCreate(['code' => 'vendor'], [
            'name' => 'Vendor', 'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    /** @return array{0: User, 1: int, 2: int, 3: int} */
    private function setupShop(bool $approved = true, string $type = 'restaurant'): array
    {
        $user = User::factory()->create();
        $account = AccountType::where('code', 'vendor')->firstOrFail();
        $user->userAccountTypes()->create([
            'account_type_id' => $account->id,
            'status' => 'active', 'activated_at' => now(),
        ]);
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $user->id,
            'vendor_code' => 'POS-'.Str::upper(Str::random(16)),
            'display_name' => 'Counter Shop',
            'status' => $approved ? 'approved' : 'pending',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor,
            'business_type' => $type, 'name' => 'Counter Restaurant',
            'slug' => 'pos-test-'.Str::lower((string) Str::ulid()),
            'status' => $approved ? 'approved' : 'pending',
            'is_accepting_orders' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business,
            'name' => 'Main Counter', 'branch_code' => 'POS-'.Str::upper(Str::random(16)),
            'status' => 'active', 'is_accepting_orders' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$user, $vendor, $business, $branch];
    }

    private function addItem(int $branch, int $price = 12550, string $name = 'Lunch'): int
    {
        return DB::table('pos_menu_items')->insertGetId([
            'branch_id' => $branch, 'name' => $name,
            'price_paise' => $price, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function enableShop(int $business, int $branch): void
    {
        $this->post(route('pos.businesses.accepting', $business), ['enabled' => 1])->assertSessionHasNoErrors();
        $this->post(route('pos.branches.accepting', $branch), ['enabled' => 1])->assertSessionHasNoErrors();
    }

    public function test_guests_cannot_access_pos_or_kitchen(): void
    {
        $this->get(route('pos.home'))->assertRedirect(route('login'));
        $this->get(route('kds.home'))->assertRedirect(route('login'));
    }

    public function test_vendor_can_open_pos_and_kds(): void
    {
        [$user, , , $branch] = $this->setupShop();
        $this->actingAs($user)->get(route('pos.home'))->assertOk();
        $this->get(route('pos.branches.show', $branch))->assertOk();
        $this->get(route('kds.branches.show', $branch))->assertOk();
    }

    public function test_pending_business_cannot_add_menu_or_enable_orders(): void
    {
        [$user, , $business, $branch] = $this->setupShop(false);
        $this->actingAs($user)->post(route('pos.menu.store', $branch), [
            'name' => 'Tea', 'price' => '15.00',
        ])->assertSessionHasErrors('business');
        $this->post(route('pos.businesses.accepting', $business), ['enabled' => 1])
            ->assertSessionHasErrors('business');
        $this->assertDatabaseHas('branches', ['id' => $branch, 'is_accepting_orders' => 0]);
    }

    public function test_branch_cannot_enable_without_active_menu_or_business(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $this->actingAs($user)->post(route('pos.branches.accepting', $branch), ['enabled' => 1])
            ->assertSessionHasErrors('branch');
        $this->post(route('pos.businesses.accepting', $business), ['enabled' => 1])
            ->assertSessionHasNoErrors();
        $this->post(route('pos.branches.accepting', $branch), ['enabled' => 1])
            ->assertSessionHasErrors('branch');
        $this->assertDatabaseHas('branches', ['id' => $branch, 'is_accepting_orders' => 0]);
    }

    public function test_owner_can_setup_menu_and_explicitly_enable_orders(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $this->actingAs($user)->post(route('pos.menu.store', $branch), [
            'name' => 'Tea', 'price' => '19.75',
        ])->assertSessionHasNoErrors();
        $this->enableShop($business, $branch);
        $this->assertDatabaseHas('pos_menu_items', [
            'branch_id' => $branch, 'name' => 'Tea', 'price_paise' => 1975,
        ]);
        $this->assertDatabaseHas('branches', ['id' => $branch, 'is_accepting_orders' => 1]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'pos.branch.accepting_changed', 'entity_id' => $branch]);
    }

    public function test_order_total_is_calculated_from_saved_menu_price(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $item = $this->addItem($branch, 12050);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id' => $item, 'quantity' => 3, 'unit_price_paise' => 1]],
            'total_paise' => 1,
        ])->assertSessionHasNoErrors();
        $order = DB::table('pos_orders')->where('branch_id', $branch)->first();
        $this->assertNotNull($order);
        $this->assertSame(36150, (int) $order->total_paise);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertDatabaseHas('pos_order_items', [
            'order_id' => $order->id, 'unit_price_paise' => 12050,
            'quantity' => 3, 'line_total_paise' => 36150,
        ]);
    }

    public function test_other_vendor_cannot_read_or_write_to_branch(): void
    {
        [$ownerA, , $businessA, $branchA] = $this->setupShop();
        $this->addItem($branchA);
        [$ownerB, , , $branchB] = $this->setupShop();
        $this->actingAs($ownerB)
            ->get(route('pos.branches.show', $branchA))->assertNotFound();
        $this->get(route('kds.branches.show', $branchA))->assertNotFound();
        $this->post(route('pos.branches.accepting', $branchA), ['enabled' => 1])->assertNotFound();
        $this->post(route('pos.businesses.accepting', $businessA), ['enabled' => 1])->assertNotFound();
        $this->assertDatabaseHas('branches', ['id' => $branchB, 'is_accepting_orders' => 0]);
    }

    public function test_menu_item_from_other_branch_is_rejected(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $otherBranch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'Second Branch',
            'branch_code' => 'POS-'.Str::upper(Str::random(16)),
            'status' => 'active', 'is_accepting_orders' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $ownItem = $this->addItem($branch);
        $foreignItem = $this->addItem($otherBranch);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id' => $foreignItem, 'quantity' => 1]],
        ])->assertSessionHasErrors('items');
        $this->assertDatabaseMissing('pos_orders', ['branch_id' => $branch]);
        $this->assertDatabaseHas('pos_menu_items', ['id' => $ownItem]);
    }

    public function test_new_orders_block_when_business_is_switched_off(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $item = $this->addItem($branch);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.businesses.accepting', $business), ['enabled' => 0])
            ->assertSessionHasNoErrors();
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id' => $item, 'quantity' => 1]],
        ])->assertSessionHasErrors('branch');
        $this->assertDatabaseHas('branches', ['id' => $branch, 'is_accepting_orders' => 0]);
    }

    public function test_kitchen_moves_received_to_preparing_to_ready_to_served(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $item = $this->addItem($branch);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id' => $item, 'quantity' => 1]],
        ])->assertSessionHasNoErrors();
        $order = DB::table('pos_orders')->where('branch_id', $branch)->firstOrFail();
        foreach (['preparing', 'ready', 'served'] as $status) {
            $this->post(route('kds.orders.status', $order->id), ['status' => $status])
                ->assertSessionHasNoErrors();
            $this->assertDatabaseHas('pos_orders', ['id' => $order->id, 'status' => $status]);
        }
        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'pos_order', 'entity_id' => $order->id,
            'action' => 'kds.order.status_changed',
        ]);
    }

    public function test_kitchen_skipping_states_is_rejected(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $item = $this->addItem($branch);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id' => $item, 'quantity' => 1]],
        ])->assertSessionHasNoErrors();
        $order = DB::table('pos_orders')->where('branch_id', $branch)->firstOrFail();
        $this->post(route('kds.orders.status', $order->id), ['status' => 'served'])
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('pos_orders', ['id' => $order->id, 'status' => 'received']);
    }

    public function test_cancelled_order_is_retained_and_audited(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $item = $this->addItem($branch);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id' => $item, 'quantity' => 1]],
        ])->assertSessionHasNoErrors();
        $order = DB::table('pos_orders')->where('branch_id', $branch)->firstOrFail();
        $this->post(route('pos.orders.cancel', $order->id), ['reason' => 'Customer changed mind'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pos_orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('pos_order_items', ['order_id' => $order->id]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'pos.order.cancelled', 'entity_id' => $order->id,
            'reason' => 'Customer changed mind',
        ]);
    }

    public function test_deactivating_last_menu_item_stops_branch(): void
    {
        [$user, , $business, $branch] = $this->setupShop();
        $item = $this->addItem($branch);
        $this->actingAs($user);
        $this->enableShop($business, $branch);
        $this->post(route('pos.menu.availability', $item), ['active' => 0])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('branches', ['id' => $branch, 'is_accepting_orders' => 0]);
    }
    public function test_free_assistant_never_mutates_orders_and_rejects_other_vendor(): void
    {
        [$owner, , $business, $branch] = $this->setupShop();
        $menu = $this->addItem($branch, 2500, 'Chapati');
        $this->actingAs($owner);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items' => [['menu_item_id'=>$menu, 'quantity'=>4]],
        ])->assertSessionHasNoErrors();
        $order = DB::table('pos_orders')->where('branch_id', $branch)->firstOrFail();
        $this->post(route('kds.orders.free-ask', $order->id), [
            'question'=>'कुल चपाती कितनी?',
        ])->assertNotFound(); // Global Free Assistant toggle OFF by default.
        $this->assertSame(4, (int) DB::table('pos_order_items')->where('order_id', $order->id)->value('quantity'));

        [$other] = $this->setupShop();
        $this->actingAs($other)->get(route('kds.branches.show', $branch))->assertNotFound();
    }

    public function test_paid_order_rejects_added_chapatis_without_changing_total(): void
    {
        [$owner, , $business, $branch] = $this->setupShop();
        $menu = $this->addItem($branch, 2000, 'Chapati');
        $this->actingAs($owner);
        $this->enableShop($business, $branch);
        $this->post(route('pos.orders.store', $branch), [
            'items'=>[['menu_item_id'=>$menu, 'quantity'=>4]],
        ])->assertSessionHasNoErrors();
        $order = DB::table('pos_orders')->where('branch_id', $branch)->firstOrFail();
        $item = DB::table('pos_order_items')->where('order_id', $order->id)->firstOrFail();
        DB::table('pos_orders')->where('id', $order->id)->update(['payment_status'=>'paid']);

        try {
            app(\App\Services\Kds\TableOrderProgress::class)->change(
                request()->setUserResolver(fn () => $owner), (int)$item->id, 'add', 2
            );
            $this->fail('A settled bill accepted new charges.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('order', $e->errors());
        }
        $this->assertDatabaseHas('pos_orders', ['id'=>$order->id,'total_paise'=>8000]);
        $this->assertDatabaseHas('pos_order_items', ['id'=>$item->id,'quantity'=>4,'added_quantity'=>0]);
    }

}
