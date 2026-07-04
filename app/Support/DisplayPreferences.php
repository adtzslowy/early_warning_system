<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Pusat definisi preferensi tampilan dashboard. Menyediakan nilai default,
 * daftar pilihan yang valid (aksen, rentang grafik, card), serta resolver
 * yang menggabungkan preferensi tersimpan milik user dengan default sistem.
 *
 * Sumber preferensi = kolom `preferences` (JSON) di tabel users. Kalau user
 * belum login atau belum punya preferensi, seluruh nilai jatuh ke default —
 * jadi sistem tetap berfungsi normal sebelum autentikasi dibangun.
 */
final class DisplayPreferences
{
    /**
     * Nilai default untuk semua knob.
     *
     * @var array<string, mixed>
     */
    public const DEFAULTS = [
        'accent' => 'neutral',
        'chart_range' => 360,          // menit: 0=Semua, 60, 360 (default: 6 jam, load lebih ringan), 1440
        'chart_metric' => 'temperature',
        'cards' => [
            'kpi_water' => true,
            'kpi_rise' => true,
            'kpi_onshore' => true,
            'kpi_score' => true,
            'telemetry' => true,
            'prediction' => true,
            'info' => true,
            'device_table' => true,
        ],
    ];

    /**
     * Preset warna aksen. `null` = pakai aksen bawaan tema (near-black/near-white).
     * Selain itu, nilai hex dipakai untuk override --color-accent.
     *
     * @var array<string, array{label: string, color: ?string}>
     */
    public const ACCENTS = [
        'neutral' => ['label' => 'Netral (tema)', 'color' => null],
        'blue' => ['label' => 'Biru', 'color' => '#2563eb'],
        'green' => ['label' => 'Hijau', 'color' => '#16a34a'],
        'violet' => ['label' => 'Ungu', 'color' => '#7c3aed'],
        'orange' => ['label' => 'Oranye', 'color' => '#ea580c'],
        'rose' => ['label' => 'Merah', 'color' => '#e11d48'],
    ];

    /**
     * Pilihan rentang default grafik telemetri (menit → label).
     *
     * @var array<int, string>
     */
    public const RANGES = [
        0 => 'Semua',
        60 => '1 Jam',
        360 => '6 Jam',
        1440 => '24 Jam',
    ];

    /**
     * Metrik telemetri yang bisa jadi default (key → label).
     *
     * @var array<string, string>
     */
    public const METRICS = [
        'temperature' => 'Suhu',
        'humidity' => 'Kelembapan',
        'air_pressure' => 'Tekanan Udara',
        'wind_speed' => 'Kecepatan Angin',
        'wind_direction' => 'Arah Angin',
    ];

    /**
     * Label tiap card yang bisa disembunyikan (key → label).
     *
     * @var array<string, string>
     */
    public const CARDS = [
        'kpi_water' => 'KPI · Ketinggian Air',
        'kpi_rise' => 'KPI · Laju Kenaikan',
        'kpi_onshore' => 'KPI · Onshore Wind',
        'kpi_score' => 'KPI · Skor Risiko',
        'telemetry' => 'Grafik Telemetri',
        'prediction' => 'Prediksi Ketinggian',
        'info' => 'Info Alat',
        'device_table' => 'Tabel Semua Device',
    ];

    /**
     * Preferensi efektif untuk user yang sedang login (atau default bila belum).
     *
     * @return array<string, mixed>
     */
    public static function forCurrentUser(): array
    {
        $stored = Auth::user()?->preferences ?? [];

        return self::resolve(is_array($stored) ? $stored : []);
    }

    /**
     * Gabungkan preferensi tersimpan dengan default + validasi nilai.
     *
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    public static function resolve(array $stored): array
    {
        $accent = $stored['accent'] ?? self::DEFAULTS['accent'];
        if (! array_key_exists($accent, self::ACCENTS)) {
            $accent = self::DEFAULTS['accent'];
        }

        $range = (int) ($stored['chart_range'] ?? self::DEFAULTS['chart_range']);
        if (! array_key_exists($range, self::RANGES)) {
            $range = self::DEFAULTS['chart_range'];
        }

        $metric = $stored['chart_metric'] ?? self::DEFAULTS['chart_metric'];
        if (! array_key_exists($metric, self::METRICS)) {
            $metric = self::DEFAULTS['chart_metric'];
        }

        $cards = [];
        $storedCards = is_array($stored['cards'] ?? null) ? $stored['cards'] : [];
        foreach (self::DEFAULTS['cards'] as $key => $default) {
            // Hanya key yang dikenal; nilai apa pun dinormalisasi ke boolean.
            $cards[$key] = (bool) ($storedCards[$key] ?? $default);
        }

        return [
            'accent' => $accent,
            'chart_range' => $range,
            'chart_metric' => $metric,
            'cards' => $cards,
        ];
    }

    /**
     * Warna hex aksen untuk sebuah key preset (null = pakai tema).
     */
    public static function accentColor(string $accent): ?string
    {
        return self::ACCENTS[$accent]['color'] ?? null;
    }
}
