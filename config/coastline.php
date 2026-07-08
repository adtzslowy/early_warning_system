<?php

declare(strict_types=1);

use App\Services\OnshoreWindCalculator;

return [

    /*
    |--------------------------------------------------------------------------
    | Bearing garis pantai per device (arah datangnya angin onshore)
    |--------------------------------------------------------------------------
    |
    | Peta device_code => bearing kompas (derajat) arah garis pantai LOKAL
    | titik tersebut menghadap — yaitu dari arah mana angin dianggap onshore
    | (mendorong air ke pantai). Dipakai OnshoreWindCalculator untuk
    | memproyeksikan angin per titik, menggantikan satu konstanta global.
    |
    | Cara isi: buka koordinat device di peta (Google Earth / geoportal),
    | lihat garis pantai lokalnya menghadap ke mana, catat bearing-nya.
    |   270 = Barat, 225 = Barat Daya, 315 = Barat Laut, 180 = Selatan, dst.
    |
    | Device yang TIDAK terdaftar di sini otomatis pakai default kolom
    | (270°). Isi bertahap, lalu jalankan:  php artisan coastline:apply
    |
    | ⚠️ Angka contoh di bawah adalah PLACEHOLDER — ganti dengan hasil
    | pemetaan riil per titik sebelum dipakai untuk analisis skripsi.
    */
    'bearings' => [
        // 'ROB-001' => 270,   // menghadap Barat (Selat Karimata)
        // 'ROB-014' => 225,   // teluk, menghadap Barat Daya
        // 'ROB-032' => 315,   // muara, menghadap Barat Laut
    ],

    /*
    | Bearing default untuk device yang belum dipetakan. Sengaja disamakan
    | dengan default kolom migrasi & konstanta kalkulator agar konsisten.
    */
    'default_bearing' => OnshoreWindCalculator::DEFAULT_ONSHORE_BEARING_DEGREES,

];
