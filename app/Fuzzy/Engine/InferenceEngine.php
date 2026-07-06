<?php

declare(strict_types=1);

namespace App\Fuzzy\Engine;

use App\Fuzzy\Membership\FuzzySetDefinition;
use App\Fuzzy\Membership\LinguisticTerm;
use App\Fuzzy\Rules\FuzzyRule;
use App\Fuzzy\Rules\RuleBase;
use App\ValueObjects\FuzzyInput;

final class InferenceEngine
{
    public function __construct(
        private readonly RuleBase $ruleBase = new RuleBase(),
    ) {}

    /**
     * @return array<int, array{rule: FuzzyRule, strength: float}>
     */
    public function evaluate(FuzzyInput $input): array
    {
        $waterDegrees = $this->fuzzify(
            fn(LinguisticTerm $t) => FuzzySetDefinition::waterLevel($t),
            $input->waterLevel,
        );
        $windDegrees = $this->fuzzify(
            fn(LinguisticTerm $t) => FuzzySetDefinition::onshoreWind($t),
            $input->onshoreWind,
        );
        $riseDegrees = $this->fuzzify(
            fn(LinguisticTerm $t) => FuzzySetDefinition::riseRate($t),
            $input->riseRate,
        );

        $active = [];

        foreach ($this->ruleBase->all() as $rule) {
            $strength = $rule->strength(
                $waterDegrees[$rule->waterLevel->value],
                $windDegrees[$rule->onshoreWind->value],
                $riseDegrees[$rule->riseRate->value],
            );

            if ($strength > 0.0) {
                $active[] = ["rule" => $rule, "strength" => $strength];
            }
        }

        return $active;
    }

    private function fuzzify(\Closure $membershipFor, float $x): array
    {
        $degrees = [];

        foreach (LinguisticTerm::cases() as $term) {
            $degrees[$term->value] = $membershipFor($term)->degreeOf($x);
        }

        return $degrees;
    }
}
