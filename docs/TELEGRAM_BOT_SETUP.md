# Setup Telegram Bot untuk EWS Banjir Rob

Bot Telegram untuk sistem notifikasi alert banjir rob sudah dikonfigurasi. Berikut cara setup lengkap.

## Status Konfigurasi

✅ Bot token sudah tersimpan di `.env`
```
TELEGRAM_BOT_TOKEN=8705095314:AAHKcrdqv-sY2XoYkk7yajfO8x9Y9w12Ugw
```

✅ Konfigurasi sudah di `config/services.php`

⏳ User perlu menambahkan Chat ID mereka masing-masing

---

## Cara User Mendapatkan Chat ID

### Opsi 1: Menggunakan Bot @getidsbot (Recommended)

1. Buka Telegram
2. Cari bot `@getidsbot`
3. Kirim `/start`
4. Bot akan menampilkan Chat ID Anda (contoh: `123456789`)
5. Copy Chat ID tersebut
6. Masuk ke EWS → Settings → Profil
7. Paste Chat ID di field "Chat ID Telegram"
8. Klik "Simpan Profil"

### Opsi 2: Via Direct Message ke Bot EWS

1. Buka Telegram
2. Cari bot `@ews_banjir_rob_bot` (atau sesuai nama bot Anda)
3. Kirim pesan apapun
4. Chat ID Anda akan disimpan otomatis di database

---

## Alur Notifikasi

```
Alert Terdeteksi (Fuzzy Logic)
    ↓
AlertController atau Scheduler
    ↓
TelegramService::sendAlertNotification()
    ↓
Loop semua user assigned ke device
    ↓
Cek apakah user punya telegram_chat_id
    ├─ Ya → Kirim notifikasi
    └─ Tidak → Skip
    ↓
Catat di NotificationLog (sent/failed)
```

---

## Testing Bot

### Test Manual via Tinker

```php
php artisan tinker

// Ganti 123456789 dengan Chat ID Anda
$service = new App\Services\TelegramService();
$response = $service->sendMessage(
    "🤖 Test message dari EWS Bot",
    "123456789"
);

dd($response);
```

Expected response (success):
```json
{
    "ok": true,
    "result": {
        "message_id": 12345,
        "date": 1626000000,
        ...
    }
}
```

### Test via Settings Profile

1. Login ke aplikasi
2. Settings → Profil
3. Input Chat ID Telegram Anda
4. Klik "Simpan Profil"
5. Tunggu alert → lihat apakah notifikasi masuk di Telegram

---

## Format Notifikasi Alert

Notifikasi yang dikirim format:

```
🚨 ALERT BANJIR ROB

Perangkat: TEST001 - Test Device
Lokasi: Ketapang
Level Risiko: Bahaya
Pesan: Water level melebihi threshold
Waktu: 20 Jul 2026 14:30

🔗 Lihat Detail
```

Warna emoji berdasarkan risk level:
- ✅ Aman (green)
- ⚠️ Waspada (yellow)
- 🔴 Siaga (orange)
- 🚨 Bahaya (red)

---

## Troubleshooting

### "Bad Request: chat not found"
- Chat ID salah atau tidak ada
- Pastikan Chat ID benar (hanya angka)
- User harus sudah start chat dengan bot terlebih dahulu

### "Unauthorized"
- Bot token expired atau salah
- Regenerate token dari BotFather

### Notifikasi tidak masuk
1. Check di Settings → Profil → apakah Chat ID sudah disimpan
2. Check NotificationLog di `/notifications/log` → lihat status message
3. Check server logs: `tail storage/logs/telegram.log`

---

## Troubleshooting Log

Semua attempt notifikasi di-log di channel `telegram`:

```php
Log::channel('telegram')->info('Alert sent', [
    'device_id' => $device->id,
    'user_id' => $user->id,
    'message_id' => $response['result']['message_id']
]);

Log::channel('telegram')->error('Failed to send', [
    'notification_log_id' => $notificationLog->id,
    'error' => $errorMessage
]);
```

View logs:
```
storage/logs/telegram.log
```

---

## Production Checklist

- [ ] Bot token di `.env` (tidak hardcode)
- [ ] TELEGRAM_CHAT_ID di `.env` kosong (per user, bukan global)
- [ ] TelegramService error handling tested
- [ ] NotificationLog schema verified
- [ ] Telegram API timeout = 10 detik
- [ ] Rate limiting dipahami (Telegram: ~30 msg/detik per bot)
- [ ] Logging enabled untuk monitoring
- [ ] Documentation shared dengan users

---

## Reference

- **Telegram Bot API:** https://core.telegram.org/bots/api
- **BotFather:** @BotFather (di Telegram)
- **Get Chat ID:** @getidsbot (di Telegram)
- **Service Implementation:** `app/Services/TelegramService.php`
- **Notification Log:** `app/Models/NotificationLog.php`

