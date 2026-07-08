<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\OnshoreWindCalculator;
use PHPUnit\Framework\TestCase;

class OnshoreWindCalculatorTest extends TestCase
{
    private OnshoreWindCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new OnshoreWindCalculator();
    }

    public function test_data_kosong_menghasilkan_nol(): void
    {
        $this->assertSame(0.0, $this->calculator->calculate(null, 270.0));
        $this->assertSame(0.0, $this->calculator->calculate(6.0, null));
    }

    public function test_bearing_default_270_angin_dari_barat_onshore_penuh(): void
    {
        // Angin dari Barat (270°), pantai default menghadap Barat → onshore penuh.
        $this->assertEqualsWithDelta(6.0, $this->calculator->calculate(6.0, 270.0), 1e-9);
    }

    public function test_bearing_default_270_angin_dari_timur_offshore_penuh(): void
    {
        // Angin dari Timur (90°) → menjauhi pantai → negatif penuh.
        $this->assertEqualsWithDelta(-6.0, $this->calculator->calculate(6.0, 90.0), 1e-9);
    }

    public function test_bearing_default_barat_daya_dan_barat_laut_onshore_parsial(): void
    {
        // Poin utama: NW (315°) & SW (225°) TIDAK diabaikan — cos 45° ≈ 0.707.
        $expected = 6.0 * cos(deg2rad(45));
        $this->assertEqualsWithDelta($expected, $this->calculator->calculate(6.0, 315.0), 1e-9);
        $this->assertEqualsWithDelta($expected, $this->calculator->calculate(6.0, 225.0), 1e-9);
    }

    public function test_bearing_per_device_menggeser_arah_onshore(): void
    {
        // Device di teluk menghadap Barat Daya (225°): angin dari 225° kini
        // onshore PENUH, sedangkan angin dari Barat (270°) jadi parsial.
        $this->assertEqualsWithDelta(6.0, $this->calculator->calculate(6.0, 225.0, 225.0), 1e-9);
        $this->assertEqualsWithDelta(
            6.0 * cos(deg2rad(45)),
            $this->calculator->calculate(6.0, 270.0, 225.0),
            1e-9,
        );
    }

    public function test_bearing_null_jatuh_ke_default(): void
    {
        // Argumen bearing null harus identik dengan default 270°.
        $this->assertEqualsWithDelta(
            $this->calculator->calculate(6.0, 315.0),
            $this->calculator->calculate(6.0, 315.0, null),
            1e-9,
        );
    }
}
