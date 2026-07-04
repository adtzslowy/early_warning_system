<?php

declare(strict_types=1);

namespace App\Services;

final class LunarPhaseCalculator
{
    private const SYNODIC_MONTH_DAYS = 29.53058867;

    // Referensi bulan baru yang diketahui (6 Jan 2000, 18:14 UTC)
    private const KNOWN_NEW_MOON = '2000-01-06 18:14:00';

    public function ageInDays(\DateTimeImmutable $date): float
    {
        $reference = new \DateTimeImmutable(self::KNOWN_NEW_MOON, new \DateTimeZone('UTC'));
        $diffSeconds = $date->getTimestamp() - $reference->getTimestamp();
        $diffDays = $diffSeconds / 86400;

        $age = fmod($diffDays, self::SYNODIC_MONTH_DAYS);

        return $age < 0 ? $age + self::SYNODIC_MONTH_DAYS : $age;
    }

    /**
     * 1.0 = tepat bulan baru/purnama (spring tide, risiko pasang maksimal)
     * 0.0 = tepat kuartal bulan (neap tide, risiko pasang minimal)
     */
    public function springTideFactor(\DateTimeImmutable $date): float
    {
        $age = $this->ageInDays($date);
        $half = self::SYNODIC_MONTH_DAYS / 2; // ~14.77 (posisi purnama)

        $distanceToExtreme = min($age, abs($age - $half), self::SYNODIC_MONTH_DAYS - $age);
        $quarterDistance = self::SYNODIC_MONTH_DAYS / 4; // ~7.38

        return max(0.0, 1 - ($distanceToExtreme / $quarterDistance));
    }
}
