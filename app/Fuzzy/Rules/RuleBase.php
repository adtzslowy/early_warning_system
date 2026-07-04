<?php

declare(strict_types=1);

namespace App\Fuzzy\Rules;

use App\Enums\RiskLevel;
use App\Fuzzy\Membership\LinguisticTerm;

final class RuleBase
{
    /** @var FuzzyRule[] */
    private array $rules;

    public function __construct()
    {
        $this->rules = $this->generate();
    }

    /** @return FuzzyRule[] */
    public function all(): array
    {
        return $this->rules;
    }

    /** @return FuzzyRule[] */
    private function generate(): array
    {
        $terms = LinguisticTerm::cases();
        $rules = [];

        foreach ($terms as $water) {
            foreach ($terms as $wind) {
                foreach ($terms as $rise) {
                    $rules[] = new FuzzyRule(
                        waterLevel: $water,
                        onshoreWind: $wind,
                        riseRate: $rise,
                        consequent: $this->consequentFor($water, $wind, $rise),
                    );
                }
            }
        }

        return $rules;
    }

    private function consequentFor(
        LinguisticTerm $water,
        LinguisticTerm $wind,
        LinguisticTerm $rise,
    ): RiskLevel {
        $severity = $this->severity($water)
            + $this->severity($wind)
            + $this->severity($rise);

        return match (true) {
            $severity <= 1 => RiskLevel::Aman,
            $severity <= 3 => RiskLevel::Waspada,
            $severity <= 5 => RiskLevel::Siaga,
            default => RiskLevel::Bahaya,
        };
    }

    private function severity(LinguisticTerm $term): int
    {
        return match ($term) {
            LinguisticTerm::Rendah => 0,
            LinguisticTerm::Sedang => 1,
            LinguisticTerm::Tinggi => 2,
        };
    }
}
