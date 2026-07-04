<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Menghitung komponen angin ke arah pantai (onshore wind component) dari
 * kecepatan + arah angin mentah — dipakai sebagai input Fuzzy Mamdani
 * (beda dari Prediction Engine yang pakai wind_x/wind_y mentah, karena
 * Fuzzy tidak bisa "belajar sendiri" dari data seperti regresi).
 *
 * TEORI: angin yang bertiup KE ARAH pantai (onshore) mendorong massa air
 * menumpuk di garis pantai (wind setup), menaikkan muka air lokal. Angin
 * yang bertiup MENJAUHI pantai (offshore) menurunkan muka air.
 *
 * SUMBER ORIENTASI GARIS PANTAI: Kabupaten Ketapang memiliki garis pantai
 * yang memanjang dari selatan ke utara, menghadap Selat Karimata di sisi
 * barat. Artinya angin onshore adalah angin yang DATANG DARI arah Barat
 * (~270°). Referensi: Wikipedia "Kabupaten Ketapang" & sumber pariwisata
 * lokal yang menyebut kota Ketapang menghadap ke arah barat/matahari
 * terbenam.
 *
 * ⚠️ TODO WAJIB: ini orientasi garis pantai KABUPATEN secara umum, BUKAN
 * koordinat presisi tiap titik dari 68 device rob. Kalau device tersebar
 * di beberapa titik dengan orientasi pantai lokal yang beda-beda (teluk,
 * muara sungai, dst), konstanta ini perlu disesuaikan per device atau
 * divalidasi ulang bersama pembimbing / data survei lapangan.
 */
final class OnshoreWindCalculator
{
    /**
     * Bearing kompas (derajat) dari arah mana angin onshore BERASAL.
     * 270° = Barat.
     */
    private const COASTLINE_ONSHORE_BEARING_DEGREES = 270.0;

    /**
     * @return float Positif = onshore (mendorong air ke pantai, risiko naik).
     *                Negatif = offshore (menjauh dari pantai, risiko turun).
     *                Satuan sama dengan wind_speed (m/s).
     */
    public function calculate(?float $windSpeed, ?float $windDirection): float
    {
        if ($windSpeed === null || $windDirection === null) {
            return 0.0;
        }

        $angleDifference = deg2rad($windDirection - self::COASTLINE_ONSHORE_BEARING_DEGREES);

        return $windSpeed * cos($angleDifference);
    }
}
