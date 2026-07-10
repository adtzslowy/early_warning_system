<?php

declare(strict_types=1);

namespace App\Prediction;

use App\Enums\SensorType;
use App\Models\Device;
use App\Models\Sensor;
use App\Services\LunarPhaseCalculator;
use App\Services\RiseRateCalculator;
use App\ValueObjects\PredictionResult;
use RuntimeException;

final class WaterLevelPredictor
{
    public function __construct(
        private readonly PredictionFeatureBuilder $featureBuilder = new PredictionFeatureBuilder(),
        private readonly LunarPhaseCalculator $lunarPhaseCalculator = new LunarPhaseCalculator(),
        private readonly RiseRateCalculator $riseRateCalculator = new RiseRateCalculator(),
    ) {}

    public function predict(Device $device, int $horizonMinutes): ?PredictionResult
    {
        $training = $this->featureBuilder->build($device, $horizonMinutes);

        if ($training === null) {
            return null; // histori belum cukup
        }

        $model = new LinearRegression();

        try {
            $model->fit($training['features'], $training['targets']);
        } catch (RuntimeException) {
            return null; // data training kurang bervariasi
        }

        $latestWaterLevel = Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WaterLevel)
            ->latest('recorded_at')
            ->first();

        if ($latestWaterLevel === null || $latestWaterLevel->value === null) {
            return null;
        }

        // PENTING: speed & direction dipasangkan dari timestamp yang SAMA
        // PERSIS (bukan "latest" masing-masing secara independen), konsisten
        // dengan PredictionFeatureBuilder::windComponents() yang hanya
        // memasangkan keduanya bila recorded_at identik (fallback 0,0 kalau
        // tidak). Kalau tak dipasangkan, fitur training vs inferensi bisa
        // punya distribusi berbeda saat salah satu sensor absen di satu poll.
        $latestWindSpeed = Sensor::query()
            ->where('device_id', $device->id)
            ->where('type', SensorType::WindSpeed)
            ->whereNotNull('value')
            ->latest('recorded_at')
            ->first();

        $pairedWindDirection = $latestWindSpeed !== null
            ? Sensor::query()
                ->where('device_id', $device->id)
                ->where('type', SensorType::WindDirection)
                ->where('recorded_at', $latestWindSpeed->recorded_at)
                ->whereNotNull('value')
                ->first()
            : null;

        $windX = 0.0;
        $windY = 0.0;

        if ($latestWindSpeed?->value !== null && $pairedWindDirection?->value !== null) {
            $radians = deg2rad((float) $pairedWindDirection->value);
            $windX = (float) $latestWindSpeed->value * sin($radians);
            $windY = (float) $latestWindSpeed->value * cos($radians);
        }

        $currentFeatures = [
            (float) $latestWaterLevel->value,
            $this->riseRateCalculator->forDevice($device) ?? 0.0,
            $windX,
            $windY,
            $this->lunarPhaseCalculator->springTideFactor(now()->toImmutable()),
        ];

        $predictedValue = $model->predict($currentFeatures);

        return new PredictionResult(
            horizonMinutes: $horizonMinutes,
            predictedValue: round($predictedValue, 2),
            predictedAt: now()->addMinutes($horizonMinutes),
        );
    }
}
