<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Bmkg\BmkgMaritimeClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BmkgMaritimeClientTest extends TestCase
{
    public function test_menormalkan_knot_ke_ms_arah_teks_ke_derajat_dan_tinggi_gelombang(): void
    {
        Http::fake([
            // Daftar file per area (perairan_list).
            '*perairan_list*' => Http::response([
                'files' => [['name' => 'U.04_Perairan Test.json']],
            ]),
            // File prakiraan area: bentuk asli BMKG { code, name, data: [...] }.
            '*' => Http::response([
                'code' => 'U.04',
                'name' => 'Perairan Test',
                'data' => [[
                    'wind_speed_min' => 10,   // knot
                    'wind_speed_max' => 20,   // knot → rata 15 knot
                    'wind_from' => 'Barat',   // → 270°
                    'wave_desc' => '1.0 - 1.5 m', // → 1.25 m
                    'weather' => 'Cerah Berawan',
                ]],
            ]),
        ]);

        $client = new BmkgMaritimeClient('https://example.test/perairan', 3600);
        $forecast = $client->forecastForArea('U.04');

        $this->assertNotNull($forecast);
        $this->assertEqualsWithDelta(15 * 0.514444, $forecast->windSpeedMs, 0.01);
        $this->assertSame(270.0, $forecast->windFromDeg);
        $this->assertEqualsWithDelta(1.25, $forecast->waveHeightM, 0.001);
        $this->assertSame('Cerah Berawan', $forecast->weather);
    }

    public function test_kode_area_kosong_tidak_memicu_http_dan_mengembalikan_null(): void
    {
        Http::fake();

        $client = new BmkgMaritimeClient('https://example.test/perairan', 3600);

        $this->assertNull($client->forecastForArea(''));
        Http::assertNothingSent();
    }

    public function test_fetch_gagal_mengembalikan_null_sebagai_fallback(): void
    {
        Http::fake([
            '*' => Http::response('', 500),
        ]);

        $client = new BmkgMaritimeClient('https://example.test/perairan', 3600);

        $this->assertNull($client->forecastForArea('U.04'));
    }
}
