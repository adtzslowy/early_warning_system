<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SensorType;
use App\Models\Device;
use App\Models\Sensor;

/**
 * PENTING: sensor ultrasonik mengukur JARAK ke permukaan air (sensor
 * menggantung di atas), bukan tinggi air langsung. Jarak yang MENGECIL
 * berarti air MENDEKAT ke sensor -> air sebenarnya NAIK. Delta dihitung
 * terbalik: (nilai_lama - nilai_baru), bukan (nilai_baru - nilai_lama).
 */
final class RiseRateCalculator
{
    private const DEFAULT_LOOKBACK_MINUTES = 60;

    /**
     * @return float|null cm/jam. Positif = air NAIK (bahaya), negatif = air TURUN (aman).
     */
    public function forDevice(Device $device, int $lookbackMinutes = self::DEFAULT_LOOKBACK_MINUTES): ?float
    {
        $latest = $this->latestWaterLevel($device);

        if ($latest === null || $latest->value === null) {
            return null;
        }

        $previous = Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WaterLevel)
            ->whereNotNull('value') // API kadang kirim water_level kosong — lewati
            ->where('recorded_at', '<=', $latest->recorded_at->copy()->subMinutes($lookbackMinutes))
            ->latest('recorded_at')
            ->first();

        if ($previous === null || $previous->value === null) {
            return null;
        }

        // Carbon 3 (Laravel 12): diffInSeconds bertanda — pakai absolut (arg true)
        // agar selalu positif, berapa pun urutan tanggalnya.
        $hoursElapsed = $latest->recorded_at->diffInSeconds($previous->recorded_at, true) / 3600;

        if ($hoursElapsed <= 0.0) {
            return null;
        }

        $rawDelta = (float) $previous->value - (float) $latest->value;

        return round($rawDelta / $hoursElapsed, 3);
    }

    private function latestWaterLevel(Device $device): ?Sensor
    {
        return Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WaterLevel)
            ->whereNotNull('value') // pakai pembacaan valid terakhir, bukan yang mentah (bisa kosong)
            ->latest('recorded_at')
            ->first();
    }
}
