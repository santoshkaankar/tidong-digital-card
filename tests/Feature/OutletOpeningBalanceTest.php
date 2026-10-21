<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Opening\OutletOpeningService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class OutletOpeningBalanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        AccountType::firstOrCreate(['code' => 'vendor'], ['name' => 'Vendor', 'is_active' => true]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function outlet(string $type = 'restaurant'): array
    {
        $owner = User::factory()->create();
        $owner->userAccountTypes()->create([
            'account_type_id' => AccountType::where('code', 'vendor')->firstOrFail()->id,
            'status' => 'active', 'activated_at' => now(),
        ]);
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $owner->id, 'vendor_code' => 'OBV-'.Str::random(12),
            'display_name' => 'Outlet Opening Test', 'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor, 'business_type' => $type,
            'name' => 'Test outlet '.$type, 'slug' => 'ob-'.Str::lower((string) Str::ulid()),
            'status' => 'approved', 'is_accepting_orders' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'Test Counter', 'branch_code' => 'OB-'.Str::random(12),
            'status' => 'active', 'is_accepting_orders' => false,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$owner, $business, $branch];
    }

    private function group(string $nature): int
    {
        return DB::table('finance_ledger_groups')->insertGetId([
            'group_code' => 'OB-G-'.Str::random(14), 'group_name' => 'Test '.$nature,
            'nature' => $nature, 'currency' => 'INR', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function lineId(int $branch, string $ledgerName): int
    {
        return (int) DB::table('finance_accounts')->where('owner_type', 'branch')
            ->where('owner_id', $branch)->where('ledger_name', $ledgerName)->value('id');
    }

    public function test_owner_can_record_opening_date_later_and_view_full_audit(): void
    {
        [$owner, $business, $branch] = $this->outlet();
        $route = route('vendor.opening.index', [$business, $branch]);
        $this->get($route)->assertRedirect(route('login'));
        $this->actingAs($owner)->post(route('vendor.opening.begin', [$business, $branch]), [
            'opening_date' => now()->toDateString(),
        ])->assertSessionHasNoErrors();
        $this->get($route)->assertOk()->assertSee('Opening Balances')->assertSee('Audit History');
        $this->assertDatabaseHas('outlet_opening_audit_logs', [
            'branch_id' => $branch, 'action' => 'opening.started', 'actor_user_id' => $owner->id,
        ]);
    }

    public function test_other_vendor_cannot_see_or_change_outlet_openings(): void
    {
        [$owner, $business, $branch] = $this->outlet();
        [$other] = $this->outlet('sublimation');
        $this->actingAs($other)->get(route('vendor.opening.index', [$business, $branch]))->assertNotFound();
        $this->post(route('vendor.opening.begin', [$business, $branch]), [
            'opening_date' => now()->toDateString(),
        ])->assertNotFound();
        $this->assertDatabaseMissing('outlet_opening_states', ['branch_id' => $branch]);
    }

    public function test_opening_can_be_set_after_activity_and_lock_unlock_is_audited(): void
    {
        [$owner, $business, $branch] = $this->outlet();
        $service = app(OutletOpeningService::class);
        $asset = $this->group('asset');
        $equity = $this->group('equity');
        $service->createLedger($owner->id, $business, $branch, 'Cash', $asset, null);
        $service->createLedger($owner->id, $business, $branch, 'Capital', $equity, null);
        $cash = $this->lineId($branch, 'Cash');
        $capital = $this->lineId($branch, 'Capital');
        DB::table('finance_accounts')->where('id', $cash)->update(['current_balance' => '250.0000']);
        $service->begin($owner->id, $business, $branch, now()->toDateString(), null);
        $service->setLedgerOpening($owner->id, $business, $branch, $cash, 'debit', '100.00', 'Old cash counted', null);
        $service->setLedgerOpening($owner->id, $business, $branch, $capital, 'credit', '100.00', 'Capital match', null);
        $this->assertEquals('350.0000', DB::table('finance_accounts')->where('id', $cash)->value('current_balance'));
        $service->setLock($owner->id, $business, $branch, true, 'Final opening cross checked', null);
        $this->expectException(ValidationException::class);
        $service->setLedgerOpening($owner->id, $business, $branch, $cash, 'debit', '120.00', 'Late correction', null);
    }

    public function test_vendor_unlock_must_be_reasoned_and_new_stock_can_be_created_after_lock(): void
    {
        [$owner, $business, $branch] = $this->outlet('sublimation');
        $service = app(OutletOpeningService::class);
        $service->begin($owner->id, $business, $branch, now()->toDateString(), null);
        $service->createStock($owner->id, $business, $branch, 'INK-1', 'Ink bottle', 'ml', null);
        $item = (int) DB::table('outlet_stock_items')->where('branch_id', $branch)->value('id');
        $service->setStockOpening($owner->id, $business, $branch, $item, '12.500', '7.25', 'Physical count confirmed', null);
        $this->assertDatabaseHas('outlet_stock_items', ['id' => $item, 'opening_stock_value_paise' => 9063]);
        $service->setLock($owner->id, $business, $branch, true, 'Count verified and matched', null);
        $service->createStock($owner->id, $business, $branch, 'PAPER-1', 'Paper roll', 'pcs', null);
        $this->assertDatabaseHas('outlet_stock_items', ['branch_id' => $branch, 'sku' => 'PAPER-1', 'opening_quantity' => '0.000']);
        try {
            $service->setStockOpening($owner->id, $business, $branch, $item, '13.000', '7.25', 'Changing locked opening', null);
            $this->fail('Locked stock opening should be rejected');
        } catch (ValidationException $e) {
            $this->assertTrue(true);
        }
        $service->setLock($owner->id, $business, $branch, false, 'Correction requested by owner', null);
        $service->setStockOpening($owner->id, $business, $branch, $item, '13.000', '7.25', 'Physical stock rechecked', null);
        $this->assertEquals('13.000', DB::table('outlet_stock_items')->where('id', $item)->value('quantity_on_hand'));
        $this->assertDatabaseHas('outlet_opening_audit_logs', ['branch_id' => $branch, 'action' => 'opening.unlocked']);
    }

    public function test_opening_lock_rejects_unbalanced_ledgers(): void
    {
        [$owner, $business, $branch] = $this->outlet();
        $service = app(OutletOpeningService::class);
        $service->begin($owner->id, $business, $branch, now()->toDateString(), null);
        $service->createLedger($owner->id, $business, $branch, 'Cash', $this->group('asset'), null);
        $service->setLedgerOpening($owner->id, $business, $branch, $this->lineId($branch, 'Cash'),
            'debit', '10.00', 'Opening cash counted', null);
        $this->expectException(ValidationException::class);
        $service->setLock($owner->id, $business, $branch, true, 'Attempting unbalanced lock', null);
    }

    public function test_existing_outlet_ledger_can_be_registered_without_overwriting_account(): void
    {
        [$owner, $business, $branch] = $this->outlet();
        $account = DB::table('finance_accounts')->insertGetId([
            'ledger_code' => 'OB-'.Str::random(14), 'ledger_name' => 'Existing Asset',
            'ledger_group_id' => $this->group('asset'), 'currency' => 'INR',
            'account_type' => 'general', 'owner_type' => 'branch', 'owner_id' => $branch,
            'opening_balance' => '125.0000', 'current_balance' => '175.0000',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $service = app(OutletOpeningService::class);
        $service->begin($owner->id, $business, $branch, now()->toDateString(), null);
        $service->attachExistingLedger($owner->id, $business, $branch, $account, null);
        $this->assertDatabaseHas('outlet_ledger_opening_lines', [
            'branch_id' => $branch, 'ledger_id' => $account, 'direction' => 'debit', 'amount_paise' => 12500,
        ]);
        $this->assertEquals('175.0000', DB::table('finance_accounts')->where('id', $account)->value('current_balance'));
    }
}
