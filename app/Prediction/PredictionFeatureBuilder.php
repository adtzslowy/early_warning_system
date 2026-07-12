<?php

declare(strict_types=1);

namespace App\Prediction;

use App\Enums\SensorType;
use App\Models\Device;
use App\Models\Sensor;
use App\Services\LunarPhaseCalculator;
use App\Services\RiseRateCalculator;

/**
 * Membangun dataset training dari histori sensor.
 *
 * Alur DUA TAHAP supaya efisien (menghindari O(n²) & rebuild berulang):
 *  1. loadSeries()      — SEKALI per device: muat histori + precompute fitur
 *     tiap titik waktu (water_level, slope 60mnt, windX, windY, tideFactor).
 *     Fitur ini SAMA untuk semua horizon; hanya TARGET yang berbeda.
 *  2. buildForHorizon() — per horizon: cocokkan target (t+horizon) ke seri via
 *     POINTER MAJU (O(n) amortized), bukan pindai-ulang koleksi (dulu O(n²)).
 *
 * `build()` lama dipertahankan (satu horizon) sebagai pembungkus dua tahap itu.
 */
final class PredictionFeatureBuilder
{
    private const MIN_TRAINING_SAMPLES = 10;

    private const MATCH_TOLERANCE_MINUTES = 3;

    /**
     * Batasi histori training ke N hari terakhir. Menahan agar jumlah baris
     * tak tumbuh tak terbatas (yang membuat pelatihan makin lambat & lapuk),
     * sekaligus membuat model belajar dari kondisi pasang surut TERKINI.
     */
    private const TRAINING_WINDOW_DAYS = 3;

    public function __construct(
        private readonly LunarPhaseCalculator $lunarPhaseCalculator = new LunarPhaseCalculator(),
    ) {}

    /**
     * Kompatibel dengan pemakaian lama: dataset untuk SATU horizon.
     * `$windowDays = null` → pakai seluruh histori (dipakai backtest agar N besar).
     *
     * @return array{features: array<int, float[]>, targets: float[]}|null
     */
    public function build(Device $device, int $horizonMinutes, ?int $windowDays = self::TRAINING_WINDOW_DAYS): ?array
    {
        $series = $this->loadSeries($device, $windowDays);

        return $series === null ? null : $this->buildForHorizon($series, $horizonMinutes);
    }

    /**
     * Muat histori & precompute baris fitur SEKALI. Dipakai bersama untuk
     * seluruh horizon (lihat WaterLevelPredictor::predictAll).
     * `$windowDays = null` → tanpa batas jendela (seluruh histori).
     *
     * @return list<array{ts: int, value: float, features: float[]}>|null
     */
    public function loadSeries(Device $device, ?int $windowDays = self::TRAINING_WINDOW_DAYS): ?array
    {
        $applyWindow = fn ($query) => $windowDays !== null
            ? $query->where('recorded_at', '>=', now()->subDays($windowDays))
            : $query;

        $waterLevels = $applyWindow(
            Sensor::query()
                ->where('device_id', $device->id)
                ->where('type', SensorType::WaterLevel)
        )
            ->orderBy('recorded_at')
            ->get(['value', 'recorded_at'])
            ->values();

        if ($waterLevels->count() < self::MIN_TRAINING_SAMPLES + 1) {
            return null;
        }

        $windSpeeds = $applyWindow(
            Sensor::query()
                ->where('device_id', $device->id)
                ->where('type', SensorType::WindSpeed)
        )
            ->get(['value', 'recorded_at'])
            ->keyBy(fn (Sensor $row) => $row->recorded_at->timestamp);

        $windDirections = $applyWindow(
            Sensor::query()
                ->where('device_id', $device->id)
                ->where('type', SensorType::WindDirection)
        )
            ->get(['value', 'recorded_at'])
            ->keyBy(fn (Sensor $row) => $row->recorded_at->timestamp);

        $samples = [];

        // Pointer geser untuk mencari bacaan >=60 menit sebelum sampel saat ini
        // (definisi SAMA dengan RiseRateCalculator::DEFAULT_LOOKBACK_MINUTES).
        // Aman O(n) karena threshold bergerak monoton seiring $i naik.
        $lookbackPointer = 0;

        foreach ($waterLevels as $i => $current) {
            if ($current->value === null) {
                continue;
            }

            $threshold = $current->recorded_at->copy()->subMinutes(RiseRateCalculator::DEFAULT_LOOKBACK_MINUTES);

            while (
                $lookbackPointer + 1 < $i
                && $waterLevels->get($lookbackPointer + 1)->recorded_at->lessThanOrEqualTo($threshold)
            ) {
                $lookbackPointer++;
            }

            $previous = $lookbackPointer < $i ? $waterLevels->get($lookbackPointer) : null;
            $slope = 0.0;

            if (
                $previous !== null
                && $previous->value !== null
                && $previous->recorded_at->lessThanOrEqualTo($threshold)
            ) {
                $slope = RiseRateCalculator::rate(
                    (float) $previous->value,
                    $previous->recorded_at,
                    (float) $current->value,
                    $current->recorded_at,
                ) ?? 0.0;
            }

            [$windX, $windY] = $this->windComponents(
                $windSpeeds->get($current->recorded_at->timestamp)?->value,
                $windDirections->get($current->recorded_at->timestamp)?->value,
            );

            $tideFactor = $this->lunarPhaseCalculator->springTideFactor(
                $current->recorded_at->toImmutable()
            );

            $samples[] = [
                'ts' => $current->recorded_at->timestamp,
                'value' => (float) $current->value,
                'features' => [(float) $current->value, $slope, $windX, $windY, $tideFactor],
            ];
        }

        return count($samples) >= self::MIN_TRAINING_SAMPLES + 1 ? $samples : null;
    }

    /**
     * Cocokkan target (t + horizon) untuk tiap sampel via pointer maju O(n).
     *
     * @param  list<array{ts: int, value: float, features: float[]}>  $samples
     * @return array{features: array<int, float[]>, targets: float[]}|null
     */
    public function buildForHorizon(array $samples, int $horizonMinutes): ?array
    {
        $features = [];
        $targets = [];

        $n = count($samples);
        $tolSec = self::MATCH_TOLERANCE_MINUTES * 60;
        $horizonSec = $horizonMinutes * 60;
        $j = 0; // pointer maju ke kandidat target (monoton naik seiring $i)

        foreach ($samples as $i => $row) {
            $targetTs = $row['ts'] + $horizonSec;

            while ($j < $n && $samples[$j]['ts'] < $targetTs) {
                $j++;
            }

            // Kandidat terdekat: samples[$j] (>= targetTs) atau samples[$j-1] (< targetTs).
            $best = null;
            $bestDiff = PHP_INT_MAX;
            foreach ([$j - 1, $j] as $k) {
                if ($k < $i + 1 || $k >= $n) {
                    continue; // target harus SETELAH sampel saat ini
                }
                $diff = abs($samples[$k]['ts'] - $targetTs);
                if ($diff <= $tolSec && $diff < $bestDiff) {
                    $bestDiff = $diff;
                    $best = $samples[$k];
                }
            }

            if ($best === null) {
                continue;
            }

            $features[] = $row['features'];
            $targets[] = $best['value'];
        }

        return count($features) >= self::MIN_TRAINING_SAMPLES
            ? ['features' => $features, 'targets' => $targets]
            : null;
    }

    /**
     * Dekomposisi vektor angin jadi komponen Timur-Barat (x) dan Utara-Selatan
     * (y). WAJIB, bukan pakai arah_angin mentah — karena arah angin sirkular
     * (359° dan 1° hampir sama, tapi di regresi linear dianggap jauh berbeda).
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
