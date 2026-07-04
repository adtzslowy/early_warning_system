<?php

declare(strict_types=1);

namespace App\Fuzzy\Rules;

use App\Enums\RiskLevel;
use App\Fuzzy\Membership\LinguisticTerm;

final readonly class FuzzyRule
{
    public function __construct(
        public LinguisticTerm $waterLevel,
        public LinguisticTerm $onshoreWind,
        public LinguisticTerm $riseRate,
        public RiskLevel $consequent,
    ) {}

    public function strength(float $waterDegree, float $windDegree, float $riseDegree): float
    {
        return min($waterDegree, $windDegree, $riseDegree);
    }
}
