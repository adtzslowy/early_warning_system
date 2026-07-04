<?php

declare(strict_types=1);

namespace App\Fuzzy\Engine;

use App\Enums\RiskLevel;
use App\Fuzzy\Membership\FuzzySetDefinition;

final class Defuzzifier
{
    private const DOMAIN_MIN = 0;

    private const DOMAIN_MAX = 100;

    private const STEP = 1;

    /**
     * @param array<int, array{rule: \App\Fuzzy\Rules\FuzzyRule, strength: float}> $activeRules
     */
    public function centroid(array $activeRules): float
    {
        if ($activeRules === []) {
            return 0.0;
        }

        $numerator = 0.0;
        $denominator = 0.0;

        for ($x = self::DOMAIN_MIN; $x <= self::DOMAIN_MAX; $x += self::STEP) {
            $aggregated = $this->aggregatedMembershipAt((float) $x, $activeRules);

            $numerator += $x * $aggregated;
            $denominator += $aggregated;
        }

        return $denominator > 0.0 ? $numerator / $denominator : 0.0;
    }

    /**
     * Nilai keanggotaan gabungan di titik x: MAX dari seluruh rule aktif,
     * masing-masing di-clip (MIN) oleh strength rule-nya. Ini implementasi
     * standar agregasi Mamdani (clip-and-max).
     *
     * @param array<int, array{rule: \App\Fuzzy\Rules\FuzzyRule, strength: float}> $activeRules
     */
    private function aggregatedMembershipAt(float $x, array $activeRules): float
    {
        $max = 0.0;

        foreach ($activeRules as $active) {
            $outputDegree = FuzzySetDefinition::riskOutput($active['rule']->consequent)->degreeOf($x);
            $clipped = min($active['strength'], $outputDegree);
            $max = max($max, $clipped);
        }

        return $max;
    }

    public function scoreToRiskLevel(float $score): RiskLevel
    {
        return match (true) {
            $score < 30 => RiskLevel::Aman,
            $score < 55 => RiskLevel::Waspada,
            $score < 80 => RiskLevel::Siaga,
            default => RiskLevel::Bahaya,
        };
    }
}
