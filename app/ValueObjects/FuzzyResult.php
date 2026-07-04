<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\RiskLevel;

final readonly class FuzzyResult
{
    public function __construct(
        public RiskLevel $level,
        public float $score, // hasil defuzzifikasi, skala 0-100
    ) {}
}
