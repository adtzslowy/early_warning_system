# Data IoT Sintetis Bertenaga BMKG (Demo & Uji Skripsi)

Endpoint IoT **tiruan** yang meniru bentuk respons `iot.ketapangkab.go.id/api/rob`,
tetapi angkanya disintesis dari model **pasang-surut** + **diurnal** dan dibumbui
data maritim **BMKG** asli (angin & gelombang). Berguna untuk demo/pengujian tanpa
perangkat IoT asli.

> **Atribusi:** Sebagian data digerakkan oleh **BMKG (Badan Meteorologi,
> Klimatologi, dan Geofisika)**. Wajib dicantumkan pada tampilan/laporan.

> **Demo-only:** JANGAN arahkan `IOT_KETAPANG_URL` produksi ke endpoint ini.
> Nonaktif otomatis saat `APP_ENV=production` (lihat `config/synthetic.php`).

## Arsitektur

```
bmkg:refresh (tiap jam) ─▶ Cache (bmkg:perairan:{code})
                                  │
rob:sync ─Http::get─▶ GET /api/mock/rob (MockRobController)
                                  │
                   SyntheticRobDataGenerator (BMKG cache + model waktu)
                                  ▼
                   {devices:{...}} → RobDevice → sensors → Fuzzy → dashboard
```

Pipeline asli (`RobSyncService`, DTO, kalkulator, Fuzzy, dashboard) **tidak
diubah** — mock hanya menggantikan sumber HTTP.

## Mekanik endpoint BMKG (terverifikasi)

1. `GET {base}/perairan_list` → `{ files: [ { name: "D.11_Perairan Ketapang.json" } ] }`
2. `GET {base}/perairan/{nama_file}` → `{ code, name, issued, data: [entri...] }`
3. `data[0]` ("Hari ini") berisi `wind_speed_min/max` (knot), `wind_from` (teks),
   `wave_desc` ("0.5 - 1.25 m"), `weather`, `valid_from/to`.

Kode area relevan: **D.11**=Perairan Ketapang, **D.12**=Kendawangan,
**D.13**=Kep. Karimata, **F.01/F.05**=Selat Karimata utara/selatan.

## Setup

1. `.env` (lokal/demo):
   ```env
   IOT_KETAPANG_URL=http://127.0.0.1:8000/api/mock/rob
   BMKG_DEFAULT_AREA_CODE=D.11      # Perairan Ketapang; kosong = baseline
   ```
   Lalu `php artisan config:clear`.
2. (Opsional) hangatkan cache BMKG: `php artisan bmkg:refresh`.
   Terjadwal otomatis tiap jam di `routes/console.php`.
3. Jalankan seperti biasa (`composer dev`, atau `php artisan serve` + worker).
   `rob:sync` (terjadwal tiap menit) akan meng-upsert device demo & menyimpan
   sensor.

> Catatan mesin dev: `php` hanya ada di `~/.luna/bin/php` — jalankan
> `export PATH="$HOME/.luna/bin:$PATH"` dulu sebelum `composer dev`.

Device demo, amplitudo pasang-surut, dan baseline diatur di
[`config/synthetic.php`](../config/synthetic.php).

## Skenario demo

Paksa kondisi cuaca lewat query string:

- `GET /api/mock/rob?scenario=badai` → angin barat kencang (onshore) + gelombang
  tinggi → water_level turun (jarak ultrasonik kecil) → risiko naik
  (Waspada→Siaga→Bahaya).
- `GET /api/mock/rob?scenario=tenang` → angin lemah offshore → risiko rendah.

## Sumber data per parameter

| Parameter        | Sumber                                             |
|------------------|----------------------------------------------------|
| `kecepatan_angin`| BMKG (knot → m/s) + jitter                          |
| `arah_angin`     | BMKG (`wind_from` teks → derajat) + jitter          |
| `ketinggian_air` | Model pasang-surut M2 + surge(gelombang+angin BMKG) |
| `suhu`/`kelembapan`/`tekanan_udara` | Model diurnal (jam lokal)        |

Jika fetch BMKG gagal / kode area kosong → pakai baseline di
`config/synthetic.php` (sistem tetap jalan offline).

## Uji

```bash
php artisan test tests/Unit/BmkgMaritimeClientTest.php tests/Feature/MockRobEndpointTest.php
```
