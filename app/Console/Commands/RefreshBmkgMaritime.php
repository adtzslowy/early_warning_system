<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Bmkg\BmkgMaritimeClient;
use Illuminate\Console\Command;

/**
 * Hangatkan cache prakiraan maritim BMKG untuk semua kode area yang dipakai
 * generator data sintetis. Dijadwalkan tiap jam (lihat routes/console.php).
 * Sumber: BMKG (atribusi wajib).
 */
class RefreshBmkgMaritime extends Command
{
    protected $signature = 'bmkg:refresh';

    protected $description = 'Perbarui cache prakiraan maritim BMKG (angin & gelombang) untuk data sintetis';

    public function handle(): int
    {
        // Dibangun via factory karena constructor butuh primitif (url/ttl) dari
        // config yang tak bisa di-autowire container.
        $client = BmkgMaritimeClient::fromConfig();

        foreach ($this->areaCodes() as $code) {
            $forecast = $client->refreshArea($code);

            if ($forecast === null) {
                $this->warn("Area {$code}: gagal / kosong — generator akan pakai baseline.");
                continue;
            }

            $this->info(sprintf(
                'Area %s: angin %.1f m/s dari %.0f°, gelombang %.1f m.',
                $code,
                $forecast->windSpeedMs,
                $forecast->windFromDeg,
                $forecast->waveHeightM,
            ));
        }

        return self::SUCCESS;
    }

    /**
     * Kumpulkan kode area unik dari config global + per-device.
     *
     * @return list<string>
     */
    private function areaCodes(): array
    {
        $codes = [];

        $default = (string) config('services.bmkg.area_code', '');
        if ($default !== '') {
            $codes[] = $default;
        }

        foreach ((array) config('synthetic.devices', []) as $device) {
            $code = $device['area_code'] ?? null;
            if (is_string($code) && trim($code) !== '') {
                $codes[] = trim($code);
            }
        }

        return array_values(array_unique($codes));
    }
}
