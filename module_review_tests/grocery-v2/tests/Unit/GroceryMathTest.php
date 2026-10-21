<?php

namespace Tests\Unit;

use App\Services\Grocery\GroceryMath;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GroceryMathTest extends TestCase
{
    public function test_money_and_weight_decimal_scaling(): void
    {
        $this->assertSame(12550, GroceryMath::paise('125.50'));
        $this->assertSame(750, GroceryMath::milli('0.750'));
        $this->assertSame('125.50', GroceryMath::formatPaise(12550));
    }

    public function test_inclusive_gst_and_intra_state_split(): void
    {
        $line = GroceryMath::line(11800, 1000, 1800, true, false);
        $this->assertSame(10000, $line['taxable_paise']);
        $this->assertSame(900, $line['cgst_paise']);
        $this->assertSame(900, $line['sgst_paise']);
        $this->assertSame(0, $line['igst_paise']);
        $this->assertSame(11800, $line['total_paise']);
    }

    public function test_exclusive_gst_interstate_tax(): void
    {
        $line = GroceryMath::line(10000, 1500, 500, false, true);
        $this->assertSame(15000, $line['taxable_paise']);
        $this->assertSame(750, $line['igst_paise']);
        $this->assertSame(15750, $line['total_paise']);
        $this->assertSame(0, $line['cgst_paise']);
    }

    public function test_invalid_overprecise_money_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GroceryMath::paise('13.333');
    }
}
