<?php

declare(strict_types=1);

namespace App\Fuzzy\Membership;

use App\Enums\RiskLevel;

/**
 * TODO: Seluruh titik (a,b,c,d) di bawah ini masih ASUMSI awal.
 * Sesuaikan dengan data historis riil & validasi pembimbing (Bapak
 * Ar-Razy / Bapak Eka) sebelum dipakai untuk BAB IV pengujian.
 */
final class FuzzySetDefinition
{
    /**
     * Ketinggian air (cm) — TAPI sensor ultrasonik dipasang MENGGANTUNG
     * di atas air dan mengukur JARAK sensor-ke-permukaan-air, bukan
     * tinggi air langsung. Relasinya TERBALIK: jarak KECIL = air DEKAT
     * ke sensor = air TINGGI = risiko TINGGI. Jarak BESAR = air JAUH
     * dari sensor = air RENDAH = risiko RENDAH.
     */
    public static function waterLevel(LinguisticTerm $term): TrapezoidalMembershipFunction
    {
        return match ($term) {
            LinguisticTerm::Tinggi => new TrapezoidalMembershipFunction(0, 0, 20, 50),     // jarak kecil = air tinggi
            LinguisticTerm::Sedang => new TrapezoidalMembershipFunction(30, 50, 70, 100),
            LinguisticTerm::Rendah => new TrapezoidalMembershipFunction(80, 120, INF, INF), // jarak besar = air rendah
        };
    }

    /**
     * Komponen angin onshore (m/s) — hasil kalkulasi OnshoreWindCalculator,
     * BUKAN wind_speed/wind_direction mentah. Domain bisa negatif (angin
     * offshore/aman) sampai positif (angin onshore kuat/bahaya).
     *
     * TODO: rentang -5..5 (transisi) masih ASUMSI awal berdasarkan skala
     * kecepatan angin umum di data (~0-15 m/s). Validasi dengan data
     * historis kejadian rob riil sebelum dipakai di BAB IV.
     */
    public static function onshoreWind(LinguisticTerm $term): TrapezoidalMembershipFunction
    {
        return match ($term) {
            LinguisticTerm::Rendah => new TrapezoidalMembershipFunction(-INF, -INF, -2, 0),  // offshore = risiko rendah
            LinguisticTerm::Sedang => new TrapezoidalMembershipFunction(-2, 0, 2, 5),
            LinguisticTerm::Tinggi => new TrapezoidalMembershipFunction(2, 5, INF, INF),       // onshore kuat = risiko tinggi
        };
    }

    /** Laju kenaikan air sebenarnya (cm/jam) — sudah dikonversi dari jarak, lihat RiseRateCalculator */
    public static function riseRate(LinguisticTerm $term): TrapezoidalMembershipFunction
    {
        return match ($term) {
            LinguisticTerm::Rendah => new TrapezoidalMembershipFunction(0, 0, 1, 2),
            LinguisticTerm::Sedang => new TrapezoidalMembershipFunction(1, 2.5, 3.5, 5),
            LinguisticTerm::Tinggi => new TrapezoidalMembershipFunction(4, 6, INF, INF),
        };
    }

    /** Output: skor risiko 0-100, dipetakan ke RiskLevel */
    public static function riskOutput(RiskLevel $level): TrapezoidalMembershipFunction
    {
        return match ($level) {
            RiskLevel::Aman => new TrapezoidalMembershipFunction(0, 0, 15, 30),
            RiskLevel::Waspada => new TrapezoidalMembershipFunction(20, 35, 45, 60),
            RiskLevel::Siaga => new TrapezoidalMembershipFunction(50, 65, 75, 90),
            RiskLevel::Bahaya => new TrapezoidalMembershipFunction(80, 90, INF, INF),
        };
    }
}
