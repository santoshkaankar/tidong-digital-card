<?php

namespace Tests\Unit;

use App\Services\Billing\InvoiceAmountCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InvoiceAmountCalculatorTest extends TestCase
{
    public function test_exact_integer_amounts_with_discount_and_tax(): void
    {
        $value = (new InvoiceAmountCalculator())->calculate([
            ['quantity_milli'=>2000, 'unit_price_paise'=>10000, 'discount_paise'=>1000, 'tax_rate_bps'=>1800],
        ]);
        $this->assertSame(20000, $value['subtotal_paise']);
        $this->assertSame(19000, $value['taxable_paise']);
        $this->assertSame(3420, $value['tax_paise']);
        $this->assertSame(22420, $value['grand_total_paise']);
    }

    public function test_fractional_quantity_rounds_in_minor_units(): void
    {
        $value = (new InvoiceAmountCalculator())->calculate([
            ['quantity_milli'=>1500,'unit_price_paise'=>101,'discount_paise'=>0,'tax_rate_bps'=>0],
        ]);
        $this->assertSame(152, $value['grand_total_paise']);
    }

    public function test_excess_discount_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new InvoiceAmountCalculator())->calculate([
            ['quantity_milli'=>1000,'unit_price_paise'=>100,'discount_paise'=>101,'tax_rate_bps'=>0],
        ]);
    }
}
