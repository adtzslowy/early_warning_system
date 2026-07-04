<?php

declare(strict_types=1);

namespace App\Prediction;

use App\Enums\SensorType;
use App\Models\Device;
use App\Models\Sensor;
use App\Services\LunarPhaseCalculator;

/**
 * Membangun dataset training dari histori sensor: tiap pasangan
 * (waktu-t, waktu-t+horizon) yang datanya tersedia jadi satu baris
 * training. Butuh minimal MIN_TRAINING_SAMPLES pasang data valid,
 * kalau belum cukup return null (bukan error).
 */
final class PredictionFeatureBuilder
{
    private const MIN_TRAINING_SAMPLES = 10;

    private const MATCH_TOLERANCE_MINUTES = 3;

    public function __construct(
        private readonly LunarPhaseCalculator $lunarPhaseCalculator = new LunarPhaseCalculator(),
    ) {}

    /**
     * @return array{features: array<int, float[]>, targets: float[]}|null
     */
    public function build(Device $device, int $horizonMinutes): ?array
    {
        $waterLevels = Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WaterLevel)
            ->orderBy('recorded_at')
            ->get(['value', 'recorded_at'])
            ->values();

        if ($waterLevels->count() < self::MIN_TRAINING_SAMPLES + 1) {
            return null;
        }

        $windSpeeds = Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WindSpeed)
            ->orderBy('recorded_at')
            ->get(['value', 'recorded_at'])
            ->keyBy(fn (Sensor $row) => $row->recorded_at->timestamp);

        $windDirections = Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WindDirection)
            ->orderBy('recorded_at')
            ->get(['value', 'recorded_at'])
            ->keyBy(fn (Sensor $row) => $row->recorded_at->timestamp);

        $features = [];
        $targets = [];

        foreach ($waterLevels as $i => $current) {
            if ($current->value === null) {
                continue;
            }

            $targetTime = $current->recorded_at->copy()->addMinutes($horizonMinutes);

            $targetReading = $waterLevels->first(
                fn (Sensor $row) => $row->value !== null
                    && abs($row->recorded_at->diffInSeconds($targetTime, false)) <= self::MATCH_TOLERANCE_MINUTES * 60
            );

            if ($targetReading === null) {
                continue;
            }

            $previous = $i > 0 ? $waterLevels->get($i - 1) : null;
            $slope = 0.0;

            if ($previous !== null && $previous->value !== null) {
                $hours = $previous->recorded_at->diffInSeconds($current->recorded_at) / 3600;
                if ($hours > 0) {
                    // Konvensi sama dengan RiseRateCalculator: jarak
                    // mengecil (previous > current) => air naik => positif.
                    $slope = ((float) $previous->value - (float) $current->value) / $hours;
                }
            }

            [$windX, $windY] = $this->windComponents(
                $windSpeeds->get($current->recorded_at->timestamp)?->value,
                $windDirections->get($current->recorded_at->timestamp)?->value,
            );

            $tideFactor = $this->lunarPhaseCalculator->springTideFactor(
                $current->recorded_at->toImmutable()
            );

            $features[] = [(float) $current->value, $slope, $windX, $windY, $tideFactor];
            $targets[] = (float) $targetReading->value;
        }

        return count($features) >= self::MIN_TRAINING_SAMPLES
            ? ['features' => $features, 'targets' => $targets]
            : null;
    }

    /**
     * Dekomposisi vektor angin jadi komponen Timur-Barat (x) dan
     * Utara-Selatan (y). Ini WAJIB dilakukan, bukan pakai arah_angin
     * mentah — karena arah angin data sirkular (359° dan 1° itu
     * hampir sama arahnya, tapi kalau dipakai mentah di regresi
     * linear dianggap jauh berbeda). Dengan dekomposisi ini, model
     * juga otomatis "belajar sendiri" dari data arah mana yang
     * berkorelasi dengan kenaikan air, tanpa perlu asumsi manual
     * soal orientasi garis pantai.
     *
     * @return array{0: float, 1: float} [windX, windY]
     */
    private function windComponents(?string $speed, ?string $direction): array
    {
        if ($speed === null || $direction === null) {
            return [0.0, 0.0];
        }

        $speed = (float) $speed;
        $radians = deg2rad((float) $direction);

        return [
            $speed * sin($radians), // komponen Timur-Barat
            $speed * cos($radians), // komponen Utara-Selatan
        ];
    }
}
