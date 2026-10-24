<?php
namespace Tests\Unit;
use App\Services\Food\ProceedTiffinPricing as Price;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProceedTiffinPricingTest extends TestCase
{
    public function test_exact_integer_money_calculation(): void
    {
        $this->assertSame(12550, Price::rupeesToPaise('125.50'));
        $this->assertSame(8500, Price::total([
            ['unit_mrp_paise'=>1500,'quantity'=>3],
            ['unit_mrp_paise'=>2000,'quantity'=>2],
        ]));
        $this->assertSame(7900, Price::sale('79',8500));
    }
    public function test_day_and_everyday_keys_are_distinct(): void
    {
        $this->assertSame('day:2026-10-24:lunch',Price::scheduleKey('day','2026-10-24','lunch'));
        $this->assertSame('everyday:lunch',Price::scheduleKey('everyday','','lunch'));
    }
    public function test_sale_over_mrp_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        Price::sale('99',8500);
    }
    public function test_zero_quantity_is_rejected(): void
    {
        $this->expectException(ValidationException::class);
        Price::total([['unit_mrp_paise'=>1000,'quantity'=>0]]);
    }
}
