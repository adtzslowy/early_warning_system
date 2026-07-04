<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SensorType;
use App\Events\DeviceRiskUpdated;
use App\Fuzzy\Engine\FuzzyMamdaniEngine;
use App\Models\Device;
use App\Models\RiskEvaluation;
use App\Models\Sensor;
use App\Services\OnshoreWindCalculator;
use App\Services\RiseRateCalculator;
use App\ValueObjects\FuzzyInput;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class EvaluateDeviceRiskJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Device $device,
    ) {}

    public function handle(
        FuzzyMamdaniEngine $fuzzyEngine,
        RiseRateCalculator $riseRateCalculator,
        OnshoreWindCalculator $onshoreWindCalculator,
    ): void {
        $waterLevel = $this->latest(SensorType::WaterLevel);
        $windSpeed = $this->latest(SensorType::WindSpeed);
        $windDirection = $this->latest(SensorType::WindDirection);

        if ($waterLevel?->value === null) {
            return; // data belum lengkap, skip
        }

        // Nilai bertanda untuk DITAMPILKAN (positif=naik, negatif=turun);
        // versi clamp (>=0) hanya dipakai sebagai input fuzzy (risiko cuma soal kenaikan).
        $riseRate = round($riseRateCalculator->forDevice($this->device) ?? 0.0, 3);
        $riseRateForRisk = max(0.0, $riseRate);

        $onshoreWind = $onshoreWindCalculator->calculate(
            $windSpeed?->value !== null ? (float) $windSpeed->value : null,
            $windDirection?->value !== null ? (float) $windDirection->value : null,
        );

        $result = $fuzzyEngine->evaluate(new FuzzyInput(
            waterLevel: (float) $waterLevel->value,
            onshoreWind: $onshoreWind,
            riseRate: $riseRateForRisk,
        ));

        $evaluation = RiskEvaluation::query()->create([
            'device_id' => $this->device->id,
            'risk_level' => $result->level,
            'risk_score' => $result->score,
            'water_level' => $waterLevel->value,
            'onshore_wind' => round($onshoreWind, 3),
            'rise_rate' => $riseRate,
            'evaluated_at' => now(),
        ]);

        event(new DeviceRiskUpdated($this->device, $evaluation));
    }

    private function latest(SensorType $type): ?Sensor
    {
        return Sensor::query()
            ->where('device_id', $this->device->id)
            ->where('type', $type)
            ->latest('recorded_at')
            ->first();
    }
}
