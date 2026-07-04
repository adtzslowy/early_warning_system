<?php

declare(strict_types=1);

namespace App\Fuzzy\Engine;

use App\ValueObjects\FuzzyInput;
use App\ValueObjects\FuzzyResult;

final class FuzzyMamdaniEngine
{
    public function __construct(
        private readonly InferenceEngine $inferenceEngine = new InferenceEngine(),
        private readonly Defuzzifier $defuzzifier = new Defuzzifier(),
    ) {}

    public function evaluate(FuzzyInput $input): FuzzyResult
    {
        $activeRules = $this->inferenceEngine->evaluate($input);
        $score = $this->defuzzifier->centroid($activeRules);
        $level = $this->defuzzifier->scoreToRiskLevel($score);

        return new FuzzyResult(
            level: $level,
            score: round($score, 2),
        );
    }
}
