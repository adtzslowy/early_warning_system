<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Data IoT Sintetis (Mock) — untuk Demo & Uji Skripsi
|--------------------------------------------------------------------------
|
| Konfigurasi generator data sensor tiruan yang disajikan lewat endpoint
| GET /api/mock/rob. Angka disintesis dari model pasang-surut + diurnal dan
| dibumbui data maritim BMKG asli (angin & gelombang). HANYA untuk demo —
| jangan arahkan IOT_KETAPANG_URL produksi ke mock ini.
|
| Daftar `devices` bersifat self-seeding: saat `rob:sync` menarik dari mock,
| RobSyncService akan meng-upsert device ini secara otomatis.
|
*/

return [

    // Aktifkan endpoint mock. Default: hanya saat APP bukan production.
    'enabled' => (bool) env('SYNTHETIC_ROB_ENABLED', env('APP_ENV') !== 'production'),

    // Zona waktu untuk model diurnal (suhu/kelembapan/tekanan).
    'timezone' => 'Asia/Jakarta',

    // Amplitudo pasang-surut (cm) & periode dominan (jam, konstituen M2 ~12.42).
    'tide' => [
        'amplitude_cm' => 35.0,
        'period_hours' => 12.42,
        // Jarak ultrasonik dasar saat air "netral" (cm). Kecil = air tinggi.
        'base_distance_cm' => 90.0,
        // Batas clamp jarak ultrasonik.
        'min_distance_cm' => 20.0,
        'max_distance_cm' => 130.0,
        // Faktor konversi tinggi gelombang (m) → tambahan "air" (cm).
        'wave_surge_factor' => 12.0,
        // Faktor komponen angin onshore (m/s) → tambahan "air" (cm).
        'wind_surge_factor' => 1.5,
    ],

    // Baseline dipakai bila cache BMKG kosong / gagal fetch.
    'baseline' => [
        'wind_speed_ms' => 3.0,
        'wind_from_deg' => 250.0, // barat-barat daya (cenderung onshore ringan)
        'wave_height_m' => 0.8,
    ],

    /*
    | Device demo. `area_code` = kode wilayah perairan BMKG dari perairan_list
    | (D.11=Perairan Ketapang, D.12=Kendawangan, D.13=Kep. Karimata,
    | F.01/F.05=Selat Karimata utara/selatan). Bila null, pakai
    | services.bmkg.area_code (default global). Koordinat sekitar Ketapang.
    */
    'devices' => [
        [
            'device_code' => 'ROB-KTP-01',
            'name' => 'Rob Ketapang - Muara',
            'location' => 'Muara Sungai Pawan, Ketapang',
            'latitude' => -1.8500,
            'longitude' => 109.9700,
            'area_code' => 'D.11', // Perairan Ketapang (BMKG)
            'offline' => false,
        ],
        [
            'device_code' => 'ROB-KTP-02',
            'name' => 'Rob Ketapang - Pelabuhan',
            'location' => 'Pelabuhan Ketapang',
            'latitude' => -1.8200,
            'longitude' => 109.9800,
            'area_code' => 'D.11', // Perairan Ketapang (BMKG)
            'offline' => false,
        ],
        [
            'device_code' => 'ROB-KTP-03',
            'name' => 'Rob Kendawangan',
            'location' => 'Kendawangan',
            'latitude' => -2.3300,
            'longitude' => 110.2100,
            'area_code' => 'D.12', // Perairan Kendawangan (BMKG)
            // Satu device sengaja offline untuk mendemokan jalur status realtime.
            'offline' => true,
        ],
    ],
];
