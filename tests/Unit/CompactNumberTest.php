<?php

namespace Tests\Unit;

use App\Support\CompactNumber;
use PHPUnit\Framework\TestCase;

class CompactNumberTest extends TestCase
{
    public function test_it_abbreviates_millions(): void
    {
        $this->assertSame('1.5M', CompactNumber::format(1_500_000));
        $this->assertSame('7.9M', CompactNumber::format(7_893_669.98));
    }

    public function test_it_abbreviates_thousands(): void
    {
        $this->assertSame('1K', CompactNumber::format(1_000));
        $this->assertSame('450K', CompactNumber::format(450_000));
    }

    public function test_it_abbreviates_billions(): void
    {
        $this->assertSame('2.3B', CompactNumber::format(2_300_000_000));
    }

    public function test_it_keeps_full_precision_below_one_thousand(): void
    {
        $this->assertSame('850.00', CompactNumber::format(850));
        $this->assertSame('20.00', CompactNumber::format(20));
    }

    public function test_it_handles_negative_values(): void
    {
        $this->assertSame('-450K', CompactNumber::format(-450_000));
    }

    public function test_currency_prefixes_the_naira_symbol_by_default(): void
    {
        $this->assertSame('₦1.5M', CompactNumber::currency(1_500_000));
    }
}
