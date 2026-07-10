<?php

declare(strict_types=1);

namespace App\Services\Bmkg;

use App\DTO\BmkgAreaForecast;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Client tipis untuk data maritim BMKG (Prakiraan Cuaca Perairan).
 *
 * Mekanik endpoint BMKG (terverifikasi):
 *  1. GET {base}/perairan_list → { files: [ { name: "D.11_Perairan Ketapang.json" } ] }
 *  2. GET {base}/perairan/{nama_file} → { code, name, issued, data: [entri...] }
 *  3. data[0] ("Hari ini") berisi wind_speed_min/max (knot), wind_from (teks),
 *     wave_desc ("0.5 - 1.25 m"), weather, valid_from/to.
 *
 * Jika apa pun gagal, kembalikan null agar pemanggil (generator sintetis)
 * memakai baseline — sistem tetap jalan tanpa koneksi.
 *
 * Atribusi sumber "BMKG" wajib dicantumkan pada tampilan yang memakai data ini.
 */
final class BmkgMaritimeClient
{
    private const KNOT_TO_MS = 0.514444;

    /** Peta nama mata angin (Indonesia) → derajat arah DATANG angin. */
    private const COMPASS = [
        'utara' => 0.0,
        'utara timur laut' => 22.5,
        'timur laut' => 45.0,
        'timur timur laut' => 67.5,
        'timur' => 90.0,
        'timur menenggara' => 112.5,
        'timur tenggara' => 112.5,
        'tenggara' => 135.0,
        'selatan menenggara' => 157.5,
        'selatan tenggara' => 157.5,
        'selatan' => 180.0,
        'selatan barat daya' => 202.5,
        'barat daya' => 225.0,
        'barat barat daya' => 247.5,
        'barat' => 270.0,
        'barat barat laut' => 292.5,
        'barat laut' => 315.0,
        'utara barat laut' => 337.5,
    ];

    public function __construct(
        private readonly string $url,
        private readonly int $cacheTtl,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            url: (string) config('services.bmkg.perairan_url'),
            cacheTtl: (int) config('services.bmkg.cache_ttl', 10800),
        );
    }

    /**
     * Prakiraan untuk satu kode area perairan (mis. "D.11"), di-cache.
     * Mengembalikan null bila kode kosong atau fetch/parse gagal.
     */
    public function forecastForArea(string $areaCode): ?BmkgAreaForecast
    {
        $areaCode = trim($areaCode);
        if ($areaCode === '' || $this->url === '') {
            return null;
        }

        return Cache::remember(
            "bmkg:perairan:{$areaCode}",
            $this->cacheTtl,
            fn () => $this->fetch($areaCode),
        );
    }

    /** Paksa ambil ulang dari BMKG dan perbarui cache (dipakai bmkg:refresh). */
    public function refreshArea(string $areaCode): ?BmkgAreaForecast
    {
        $areaCode = trim($areaCode);
        if ($areaCode === '' || $this->url === '') {
            return null;
        }

        Cache::forget("bmkg:perairan:{$areaCode}");
        $forecast = $this->fetch($areaCode);

        if ($forecast !== null) {
            Cache::put("bmkg:perairan:{$areaCode}", $forecast, $this->cacheTtl);
        }

        return $forecast;
    }

    private function fetch(string $areaCode): ?BmkgAreaForecast
    {
        try {
            // BMKG menyajikan tiap area sebagai file JSON terpisah bernama
            // "{KODE}_Nama.json"; nama file diambil dari daftar perairan_list.
            $file = $this->resolveFileName($areaCode);
            if ($file === null) {
                Log::warning('BMKG maritim: file area tak ditemukan di perairan_list', [
                    'area' => $areaCode,
                ]);

                return null;
            }

            $fileUrl = rtrim($this->url, '/').'/'.rawurlencode($file);
            $response = Http::timeout(8)->retry(2, 500)->acceptJson()->get($fileUrl);

            if ($response->failed()) {
                Log::warning('BMKG maritim: fetch gagal', [
                    'url' => $fileUrl,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $entry = $this->pickEntry($response->json(), $areaCode);
            if ($entry === null) {
                return null;
            }

            return $this->normalize($areaCode, $entry);
        } catch (Throwable $e) {
            Log::warning('BMKG maritim: exception saat fetch', [
                'area' => $areaCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Cari nama file JSON untuk kode area dari endpoint perairan_list
     * (mis. "D.11" → "D.11_Perairan Ketapang.json"). Daftar di-cache.
     */
    private function resolveFileName(string $areaCode): ?string
    {
        $listUrl = Str::beforeLast($this->url, '/').'/perairan_list';

        // Cache key menyertakan hash pendek dari $this->url supaya tidak ada
        // tabrakan cache lintas environment kalau BMKG_PERAIRAN_URL pernah
        // beda tapi berbagi cache store yang sama (mis. Redis).
        $cacheKey = 'bmkg:perairan_list:'.substr(md5($this->url), 0, 8);

        $files = Cache::remember($cacheKey, $this->cacheTtl, function () use ($listUrl) {
            $res = Http::timeout(8)->retry(2, 500)->acceptJson()->get($listUrl);

            return $res->ok() ? ($res->json('files') ?? []) : [];
        });

        foreach ((array) $files as $f) {
            $name = is_array($f) ? ($f['name'] ?? '') : '';
            if (is_string($name) && str_starts_with($name, $areaCode.'_')) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Ambil satu entri prakiraan dari respons file area. Bentuk asli:
     * { code, name, data: [entri "Hari ini", "Besok", ...] } → ambil data[0].
     * Dibuat toleran terhadap variasi lain (object keyed by code / entri datar).
     *
     * @param  mixed  $json
     * @return array<string, mixed>|null
     */
    private function pickEntry(mixed $json, string $areaCode): ?array
    {
        if (! is_array($json)) {
            return null;
        }

        // Bentuk asli BMKG: { code, name, data: [ ... ] } → entri pertama (Hari ini).
        if (isset($json['data']) && is_array($json['data']) && array_is_list($json['data'])) {
            $first = $json['data'][0] ?? null;
            if (is_array($first)) {
                return $first;
            }
        }

        // object keyed by code
        if (isset($json[$areaCode]) && is_array($json[$areaCode])) {
            return $this->pickEntry($json[$areaCode], $areaCode) ?? $json[$areaCode];
        }

        // entri datar yang sudah berisi field angin/gelombang
        if (array_key_exists('wind_speed_min', $json) || array_key_exists('wind_from', $json)) {
            return $json;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $e
     */
    private function normalize(string $areaCode, array $e): BmkgAreaForecast
    {
        $baseline = config('synthetic.baseline');

        // Kecepatan angin: rata-rata min/max (knot) → m/s.
        $min = $this->num($e['wind_speed_min'] ?? null);
        $max = $this->num($e['wind_speed_max'] ?? null);
        $knots = match (true) {
            $min !== null && $max !== null => ($min + $max) / 2,
            $min !== null => $min,
            $max !== null => $max,
            default => null,
        };
        $windSpeedMs = $knots !== null
            ? $knots * self::KNOT_TO_MS
            : (float) $baseline['wind_speed_ms'];

        // Arah angin datang (wind_from) teks → derajat.
        $windFromDeg = $this->compassToDegrees($e['wind_from'] ?? null)
            ?? (float) $baseline['wind_from_deg'];

        // Tinggi gelombang: titik tengah rentang "1.0 - 1.5 m".
        $waveHeightM = $this->parseWave($e['wave_desc'] ?? null)
            ?? (float) $baseline['wave_height_m'];

        return new BmkgAreaForecast(
            areaCode: $areaCode,
            windSpeedMs: round($windSpeedMs, 2),
            windFromDeg: $windFromDeg,
            waveHeightM: round($waveHeightM, 2),
            weather: isset($e['weather']) ? (string) $e['weather'] : null,
            validFrom: $this->date($e['valid_from'] ?? null),
            validTo: $this->date($e['valid_to'] ?? null),
        );
    }

    private function compassToDegrees(mixed $text): ?float
    {
        if (! is_string($text) || trim($text) === '') {
            return null;
        }

        $key = strtolower(trim(preg_replace('/\s+/', ' ', $text) ?? ''));

        return self::COMPASS[$key] ?? null;
    }

    private function parseWave(mixed $desc): ?float
    {
        if (! is_string($desc)) {
            return null;
        }

        // Ambil semua angka (mis. "1.0 - 1.5 m") lalu rata-ratakan.
        preg_match_all('/\d+(?:[.,]\d+)?/', $desc, $m);
        $nums = array_map(fn ($n) => (float) str_replace(',', '.', $n), $m[0] ?? []);

        if ($nums === []) {
            return null;
        }

        return array_sum($nums) / count($nums);
    }

    private function num(mixed $v): ?float
    {
        return is_numeric($v) ? (float) $v : null;
    }

    private function date(mixed $v): ?Carbon
    {
        if (! is_string($v) || trim($v) === '') {
            return null;
        }

        try {
            return Carbon::parse($v);
        } catch (Throwable) {
            return null;
        }
    }
}
