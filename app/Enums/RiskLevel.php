<?php

namespace App\Enums;

enum RiskLevel: string
{
    case Aman = 'aman';
    case Siaga = 'siaga';
    case Waspada = 'waspada';
    case Bahaya = 'bahaya';

    public function label(): string
    {
        return match ($this) {
            self::Aman => 'Aman',
            self::Siaga => 'Siaga',
            self::Waspada => 'Waspada',
            self::Bahaya => 'Bahaya'
        };
    }

    /**
     * Warna marker peta (Leaflet + windy) sesuai status level risiko
     * */

    public function markerColor(): string
    {
        return match ($this) {
            self::Aman => '#22c55e',
            self::Siaga => '#eab308',
            self::Waspada => '#f97316',
            self::Bahaya => '#ef4444'
        };
    }
}
