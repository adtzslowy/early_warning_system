<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\BmkgAreaForecast;
use App\Services\Bmkg\BmkgMaritimeClient;
use Illuminate\Support\Carbon;

/**
 * Menghasilkan payload IoT tiruan (mock) bergaya iot.ketapangkab.go.id/api/rob,
 * tetapi angkanya disintesis: model pasang-surut + diurnal, dibumbui data
 * maritim BMKG (angin & gelombang). Untuk demo & uji skripsi.
 *
 * Semua nilai adalah fungsi wall-clock time sehingga antar-poll berubah mulus
 * (kontinu) tanpa perlu menyimpan state. Sumber angin/gelombang: BMKG (atribusi).
 */
final class SyntheticRobDataGenerator
{
    public function __construct(
        private readonly BmkgMaritimeClient $bmkg,
    ) {}

    public static function make(): self
    {
        return new self(BmkgMaritimeClient::fromConfig());
    }

    /**
     * @param  string|null  $scenario  Override demo: 'badai' (cuaca ekstrem) | 'tenang'.
     * @return array<string, array<string, mixed>>  map deviceCode => payload
     */
    public function generate(?string $scenario = null): array
    {
        $now = Carbon::now();
        $devices = (array) config('synthetic.devices', []);
        $out = [];

        foreach ($devices as $device) {
            $code = (string) ($device['device_code'] ?? '');
            if ($code === '') {
                continue;
            }

            $out[$code] = $this->buildPayload($device, $now, $scenario);
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $device
     * @return array<string, mixed>
     */
    private function buildPayload(array $device, Carbon $now, ?string $scenario): array
    {
        $offline = (bool) ($device['offline'] ?? false);

        if ($offline) {
            // Device offline: tak ada bacaan baru, last_seen mundur jauh.
            return [
                'name' => $device['name'] ?? strtoupper((string) $device['device_code']),
                'status' => 'offline',
                'last_seen' => $now->copy()->subMinutes(45)->toIso8601String(),
                'latitude' => $device['latitude'] ?? null,
                'longitude' => $device['longitude'] ?? null,
            ];
        }

        $forecast = $this->forecastFor($device);
        [$windSpeed, $windFrom, $waveHeight] = $this->applyScenario($forecast, $scenario);

        $waterLevel = $this->waterLevelDistanceCm($now, $windSpeed, $windFrom, $waveHeight, $device['device_code']);
        [$temperature, $humidity, $pressure] = $this->diurnal($now, $device['device_code']);

        // Variasi kecil menit-ke-menit pada angin agar terlihat "hidup".
        $windSpeed = $this->jitter($now, $windSpeed, 0.4, $device['device_code'], 'ws');
        $windFrom = fmod($windFrom + $this->jitter($now, 0.0, 6.0, $device['device_code'], 'wd') + 360.0, 360.0);

        return [
            'name' => $device['name'] ?? strtoupper((string) $device['device_code']),
            'status' => 'online',
            'last_seen' => $now->toIso8601String(),
            'latitude' => $device['latitude'] ?? null,
            'longitude' => $device['longitude'] ?? null,
            'ketinggian_air' => round($waterLevel, 1),
            'suhu' => round($temperature, 1),
            'kelembapan' => round($humidity, 0),
            'tekanan_udara' => round($pressure, 1),
            'kecepatan_angin' => round(max(0.0, $windSpeed), 1),
            'arah_angin' => round($windFrom, 0),
        ];
    }

    /**
     * @param  array<string, mixed>  $device
     */
    private function forecastFor(array $device): ?BmkgAreaForecast
    {
        $areaCode = $device['area_code'] ?? config('services.bmkg.area_code', '');

        return $areaCode ? $this->bmkg->forecastForArea((string) $areaCode) : null;
    }

    /**
     * Terapkan override skenario demo, atau pakai forecast/baseline.
     *
     * @return array{0: float, 1: float, 2: float}  [windSpeedMs, windFromDeg, waveHeightM]
     */
    private function applyScenario(?BmkgAreaForecast $f, ?string $scenario): array
    {
        $baseline = config('synthetic.baseline');

        $windSpeed = $f?->windSpeedMs ?? (float) $baseline['wind_speed_ms'];
        $windFrom = $f?->windFromDeg ?? (float) $baseline['wind_from_deg'];
        $waveHeight = $f?->waveHeightM ?? (float) $baseline['wave_height_m'];

        return match ($scenario) {
            // Badai: angin barat kencang (onshore) + gelombang tinggi → risiko naik.
            'badai' => [12.0, 270.0, 3.0],
            'tenang' => [1.5, 90.0, 0.3],
            default => [$windSpeed, $windFrom, $waveHeight],
        };
    }

    /**
     * Jarak ultrasonik ke permukaan air (cm). KECIL = air tinggi = risiko naik.
     * = base − pasang-surut − surge(gelombang+angin onshore) + jitter, di-clamp.
     */
    private function waterLevelDistanceCm(
        Carbon $now,
        float $windSpeedMs,
        float $windFromDeg,
        float $waveHeightM,
        string $seed,
    ): float {
        $tide = config('synthetic.tide');

        // Fase pasang-surut dari detik sejak epoch (kontinu antar-poll).
        // fmod() dipakai (bukan `% (int) $periodSec`) supaya aman untuk
        // period_hours pecahan (mis. K1 = 23,93) — cast ke int akan membulatkan
        // periode sedetik lebih pendek/panjang dari pembagi float asli,
        // menyebabkan glitch fase kecil tiap pergantian siklus.
        $periodSec = (float) $tide['period_hours'] * 3600.0;
        $phase = fmod((float) $now->getTimestamp(), $periodSec) / $periodSec * 2 * M_PI;
        $tideRise = (float) $tide['amplitude_cm'] * sin($phase); // + saat pasang

        // Komponen angin onshore (270° = onshore). Hanya yang mendorong ke darat.
        $onshore = max(0.0, $windSpeedMs * cos(deg2rad($windFromDeg - 270.0)));
        $surge = $waveHeightM * (float) $tide['wave_surge_factor']
            + $onshore * (float) $tide['wind_surge_factor'];

        $distance = (float) $tide['base_distance_cm']
            - $tideRise
            - $surge
            + $this->jitter($now, 0.0, 1.5, $seed, 'wl');

        return max(
            (float) $tide['min_distance_cm'],
            min((float) $tide['max_distance_cm'], $distance),
        );
    }

    /**
     * Model diurnal suhu/kelembapan/tekanan berdasarkan jam lokal.
     *
     * @return array{0: float, 1: float, 2: float}
     */
    private function diurnal(Carbon $now, string $seed): array
    {
        $tz = (string) config('synthetic.timezone', 'Asia/Jakarta');
        $hour = (float) $now->copy()->timezone($tz)->format('H')
            + (float) $now->copy()->timezone($tz)->format('i') / 60.0;

        // Suhu puncak ~15:00, minimum ~03:00 (sinus tunggal wajib beda fase
        // 12 jam persis antara puncak & minimum).
        $tempPhase = ($hour - 9.0) / 24.0 * 2 * M_PI;
        $temperature = 27.0 + 4.0 * sin($tempPhase) + $this->jitter($now, 0.0, 0.3, $seed, 't');

        // Kelembapan berlawanan fase suhu (~70–95%).
        $humidity = 82.0 - 12.0 * sin($tempPhase) + $this->jitter($now, 0.0, 1.5, $seed, 'h');
        $humidity = max(50.0, min(100.0, $humidity));

        // Tekanan: dua puncak harian (pasang atmosfer) ~1010 hPa.
        $pressure = 1010.0 + 1.5 * sin(($hour / 12.0) * 2 * M_PI) + $this->jitter($now, 0.0, 0.4, $seed, 'p');

        return [$temperature, $humidity, $pressure];
    }

    /**
     * Jitter deterministik per menit: sama dalam satu menit (stabil untuk banyak
     * poll), berubah tiap menit. Berbasis hash(seed+channel+menit) → [-range, +range].
     *
     * PENTING: menerima $now sebagai parameter (bukan panggil Carbon::now()
     * sendiri) supaya semua fitur dalam satu generate() memakai referensi
     * waktu yang SAMA PERSIS — konsisten dgn method lain (buildPayload,
     * waterLevelDistanceCm, diurnal) dan deterministik saat waktu di-freeze
     * lewat Carbon::setTestNow() di test.
     */
    private function jitter(Carbon $now, float $center, float $range, string $seed, string $channel): float
    {
        $minute = (int) floor($now->getTimestamp() / 60);
        $h = crc32("{$seed}|{$channel}|{$minute}");
        $unit = ($h % 1000) / 1000.0; // [0,1)

        return $center + ($unit * 2 - 1) * $range;
    }
}
