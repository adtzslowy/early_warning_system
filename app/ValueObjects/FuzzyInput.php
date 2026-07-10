<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class FuzzyInput
{
    public function __construct(
        public float $waterLevel,   // cm
        public float $onshoreWind,  // m/s
        public float $riseRate,     // cm/jam
    ) {}
}
