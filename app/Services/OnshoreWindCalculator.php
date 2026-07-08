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
 * SUMBER ORIENTASI GARIS PANTAI: bearing onshore per titik diisi lewat
 * kolom `devices.coastline_bearing` (lihat config/coastline.php + command
 * `coastline:apply`). Kalau sebuah device belum dipetakan, kalkulator jatuh
 * ke DEFAULT 270° (Barat) — orientasi umum pantai Kabupaten Ketapang yang
 * memanjang utara-selatan menghadap Selat Karimata di sisi barat. Referensi
 * default: Wikipedia "Kabupaten Ketapang" & sumber pariwisata lokal yang
 * menyebut kota Ketapang menghadap ke arah barat/matahari terbenam.
 *
 * CATATAN: karena proyeksi cosinus, angin dari Barat Laut (315°) & Barat
 * Daya (225°) TETAP ikut terhitung sebagai onshore parsial (~0.71× kecepatan
 * pada bearing default 270°) — bukan diabaikan. Yang di-per-device-kan adalah
 * "menghadap ke mana" tiap titik (teluk/muara bisa beda), bukan penanganan
 * arah anginnya.
 */
final class OnshoreWindCalculator
{
    /**
     * Bearing kompas (derajat) dari arah mana angin onshore BERASAL, dipakai
     * sebagai fallback saat device belum punya coastline_bearing sendiri.
     * 270° = Barat.
     */
    public const DEFAULT_ONSHORE_BEARING_DEGREES = 270.0;

    /**
     * @param  float|null  $onshoreBearing  Bearing garis pantai lokal titik ini
     *                                       (derajat). null → pakai DEFAULT 270°.
     * @return float Positif = onshore (mendorong air ke pantai, risiko naik).
     *                Negatif = offshore (menjauh dari pantai, risiko turun).
     *                Satuan sama dengan wind_speed (m/s).
     */
    public function calculate(
        ?float $windSpeed,
        ?float $windDirection,
        ?float $onshoreBearing = null,
    ): float {
        if ($windSpeed === null || $windDirection === null) {
            return 0.0;
        }

        $bearing = $onshoreBearing ?? self::DEFAULT_ONSHORE_BEARING_DEGREES;
        $angleDifference = deg2rad($windDirection - $bearing);

        return $windSpeed * cos($angleDifference);
    }
}
