<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\DTO\RobDevice;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MockRobEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Cegah panggilan nyata ke BMKG saat test; generator jatuh ke baseline.
        Http::fake();
    }

    public function test_endpoint_mock_mengembalikan_struktur_bentuk_iot(): void
    {
        $response = $this->getJson('/api/mock/rob');

        $response->assertOk()
            ->assertJsonStructure([
                'source',
                'attribution',
                'generated_at',
                'devices',
            ]);

        $devices = $response->json('devices');
        $this->assertNotEmpty($devices);

        // Setiap device online harus punya key yang dipetakan RobDevice.
        foreach ($devices as $code => $payload) {
            if (($payload['status'] ?? null) !== 'online') {
                continue;
            }

            foreach (['ketinggian_air', 'suhu', 'kelembapan', 'tekanan_udara', 'kecepatan_angin', 'arah_angin'] as $key) {
                $this->assertArrayHasKey($key, $payload, "device {$code} kehilangan {$key}");
            }

            // DTO asli harus bisa mem-parse payload sintetis → 6 bacaan.
            $dto = RobDevice::fromArray((string) $code, $payload);
            $this->assertCount(6, $dto->readings);
        }
    }

    public function test_device_offline_tidak_mengirim_bacaan_sensor(): void
    {
        $devices = $this->getJson('/api/mock/rob')->json('devices');

        $offline = collect($devices)->firstWhere('status', 'offline');
        $this->assertNotNull($offline, 'butuh minimal satu device offline untuk demo');
        $this->assertArrayNotHasKey('ketinggian_air', $offline);
    }

    public function test_endpoint_404_saat_dinonaktifkan(): void
    {
        config()->set('synthetic.enabled', false);

        $this->getJson('/api/mock/rob')->assertNotFound();
    }
}
