<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Carbon;

/**
 * Prakiraan maritim BMKG yang sudah dinormalisasi untuk satu area perairan.
 * Sumber: BMKG (Prakiraan Cuaca Perairan). Atribusi wajib.
 */
final readonly class BmkgAreaForecast
{
    public function __construct(
        public string $areaCode,
        public float $windSpeedMs,   // m/s (dikonversi dari knot)
        public float $windFromDeg,   // derajat, arah datang angin (0=Utara)
        public float $waveHeightM,   // meter, titik tengah rentang gelombang
        public ?string $weather = null,
        public ?Carbon $validFrom = null,
        public ?Carbon $validTo = null,
    ) {}
}
