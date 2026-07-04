<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Support\Carbon;

final readonly class PredictionResult
{
    public function __construct(
        public int $horizonMinutes,
        public float $predictedValue, // domain SAMA dengan sensor water_level (jarak, cm) — bukan tinggi air langsung
        public Carbon $predictedAt,
    ) {}
}
