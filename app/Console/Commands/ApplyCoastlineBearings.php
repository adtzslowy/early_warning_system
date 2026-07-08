<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;

/**
 * Menyalin bearing garis pantai per titik dari config/coastline.php ke kolom
 * devices.coastline_bearing.
 *
 * Idempotent & aman diulang: device yang tidak terdaftar di config dibiarkan
 * apa adanya (tetap pakai default kolom 270°). Jalankan tiap kali peta bearing
 * di config diperbarui, atau setelah device baru di-import.
 */
class ApplyCoastlineBearings extends Command
{
    protected $signature = 'coastline:apply
        {--dry-run : Tampilkan perubahan tanpa menyimpan}';

    protected $description = 'Terapkan bearing garis pantai per device dari config/coastline.php';

    public function handle(): int
    {
        /** @var array<string, int|float> $bearings */
        $bearings = (array) config('coastline.bearings', []);

        if ($bearings === []) {
            $this->warn('config/coastline.php belum berisi bearing apa pun — semua device pakai default 270°.');

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $applied = 0;
        $missing = [];

        foreach ($bearings as $deviceCode => $bearing) {
            $device = Device::query()->where('device_code', $deviceCode)->first();

            if ($device === null) {
                $missing[] = $deviceCode;

                continue;
            }

            $newBearing = (float) $bearing;
            $oldBearing = $device->coastline_bearing !== null
                ? (float) $device->coastline_bearing
                : null;

            $this->line(sprintf(
                '  %s: %s → %.2f°',
                $deviceCode,
                $oldBearing !== null ? sprintf('%.2f°', $oldBearing) : '(default)',
                $newBearing,
            ));

            if (! $dryRun) {
                $device->update(['coastline_bearing' => $newBearing]);
            }

            $applied++;
        }

        if ($missing !== []) {
            $this->warn('device_code tidak ditemukan di DB: ' . implode(', ', $missing));
        }

        $this->info(sprintf(
            '%s %d device.',
            $dryRun ? '[dry-run] akan memperbarui' : 'Memperbarui',
            $applied,
        ));

        return self::SUCCESS;
    }
}
