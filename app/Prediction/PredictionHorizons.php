<?php

declare(strict_types=1);

namespace App\Prediction;

/**
 * Grid horizon prediksi (menit). Alih-alih dua titik tetap (30 & 60),
 * prediksi kini dihasilkan sebagai kurva ke depan — mirip forecast cuaca
 * yang menampilkan proyeksi tiap interval, lalu digambar ulang tiap kali
 * data sensor baru masuk.
 */
final class PredictionHorizons
{
    /** @var list<int> */
    public const MINUTES = [15, 30, 45, 60, 75, 90, 105, 120];
}
