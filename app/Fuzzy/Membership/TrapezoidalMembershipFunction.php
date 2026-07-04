<?php

declare(strict_types=1);

namespace App\Fuzzy\Membership;

/**
 * Fungsi keanggotaan trapesium: naik dari (a,b), datar di (b,c), turun di (c,d).
 * Untuk segitiga, cukup buat b == c.
 * Untuk "bahu" (shoulder) terbuka di kanan — flat selamanya ke arah ekstrem,
 * misal "laju kenaikan sangat tinggi" yang nggak ada batas atasnya — pakai
 * c == d == INF. JANGAN pakai angka finite yang sama (mis. c == d == 10),
 * karena nilai yang MELEBIHI angka itu justru dianggap 0 (bug boundary),
 * padahal seharusnya tetap dianggap maksimal/ekstrem.
 */
final readonly class TrapezoidalMembershipFunction
{
    public function __construct(
        private float $a,
        private float $b,
        private float $c,
        private float $d,
    ) {}

    public function degreeOf(float $x): float
    {
        if ($x < $this->a || $x > $this->d) {
            return 0.0;
        }

        if ($x >= $this->b && $x <= $this->c) {
            return 1.0;
        }

        if ($x < $this->b) {
            return ($x - $this->a) / ($this->b - $this->a);
        }

        // $x > $this->c && $x <= $this->d
        return ($this->d - $x) / ($this->d - $this->c);
    }
}
