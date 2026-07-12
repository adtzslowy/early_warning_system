<?php

declare(strict_types=1);

namespace App\Prediction;

use App\Models\Device;
use App\ValueObjects\PredictionResult;
use RuntimeException;

final class WaterLevelPredictor
{
    public function __construct(
        private readonly PredictionFeatureBuilder $featureBuilder = new PredictionFeatureBuilder(),
    ) {}

    /**
     * Prediksi seluruh horizon SEKALIGUS. Histori dimuat & fitur di-precompute
     * SEKALI (loadSeries), lalu tiap horizon hanya mencocokkan target + melatih
     * regresi — jauh lebih ringan daripada memanggil predict() 8×.
     *
     * @return list<PredictionResult>
     */
    public function predictAll(Device $device): array
    {
        $samples = $this->featureBuilder->loadSeries($device);

        if ($samples === null || $samples === []) {
            return [];
        }

        // Fitur "saat ini" untuk inferensi = fitur sampel TERBARU. Karena
        // di-precompute dengan cara yang sama seperti data training, tidak ada
        // lagi train/serve skew, dan tanpa query tambahan.
        $currentFeatures = end($samples)['features'];

        $results = [];

        foreach (PredictionHorizons::MINUTES as $horizonMinutes) {
            $result = $this->fitAndPredict($samples, $currentFeatures, $horizonMinutes);

            if ($result !== null) {
                $results[] = $result;
            }
        }

        return $results;
    }

    /**
     * Prediksi satu horizon (dipertahankan untuk pemakaian tunggal).
     */
    public function predict(Device $device, int $horizonMinutes): ?PredictionResult
    {
        $samples = $this->featureBuilder->loadSeries($device);

        if ($samples === null || $samples === []) {
            return null;
        }

        return $this->fitAndPredict($samples, end($samples)['features'], $horizonMinutes);
    }

    /**
     * @param  list<array{ts: int, value: float, features: float[]}>  $samples
     * @param  float[]  $currentFeatures
     */
    private function fitAndPredict(array $samples, array $currentFeatures, int $horizonMinutes): ?PredictionResult
    {
        $training = $this->featureBuilder->buildForHorizon($samples, $horizonMinutes);

        if ($training === null) {
            return null; // histori belum cukup untuk horizon ini
        }

        $model = new LinearRegression();

        try {
            $model->fit($training['features'], $training['targets']);
        } catch (RuntimeException) {
            return null; // data training kurang bervariasi
        }

        return new PredictionResult(
            horizonMinutes: $horizonMinutes,
            predictedValue: round($model->predict($currentFeatures), 2),
            predictedAt: now()->addMinutes($horizonMinutes),
        );
    }
}
