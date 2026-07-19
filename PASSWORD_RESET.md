# 🔐 Sistem Lupa Password dengan OTP

Dokumentasi lengkap untuk fitur forgot password dengan OTP yang dikirim via email.

## 📋 Daftar Isi
- [Gambaran Umum](#gambaran-umum)
- [Cara Kerja](#cara-kerja)
- [Setup & Konfigurasi](#setup--konfigurasi)
- [Alur Aplikasi](#alur-aplikasi)
- [Keamanan](#keamanan)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Gambaran Umum

Sistem password reset berbasis OTP (One-Time Password) yang aman:

| Aspek | Detail |
|-------|--------|
| **Tipe OTP** | 6-digit numeric (000000-999999) |
| **Delivery** | Email (Mailgun, Gmail SMTP, dll) |
| **Expiry** | 15 menit dari request |
| **Max Attempts** | 5 percobaan sebelum lock |
| **Token** | 64 karakter (SHA-256 compatible) |
| **Database** | `password_reset_tokens` table |

---

## 🔄 Cara Kerja

### Alur Singkat

```
1. User klik "Lupa Password?" di login
   ↓
2. Input email → Sistem validasi email exists
   ↓
3. Generate OTP (6 digit) + Token (64 char)
   ↓
4. Simpan ke database dengan expiry 15 menit
   ↓
5. Kirim email dengan OTP + reset link
   ↓
6. User buka email & klik link
   ↓
7. Form verifikasi: input OTP + password baru
   ↓
8. Sistem validasi: OTP cocok? Token belum expire? Attempts < 5?
   ↓
9. Update password di users table
   ↓
10. Hapus token dari database (one-time use)
   ↓
11. Redirect ke login dengan success message
```

---

## 🛠️ Setup & Konfigurasi

### Database Migration

Sudah otomatis via:
```bash
php artisan migrate
```

**Schema `password_reset_tokens`:**
```sql
CREATE TABLE password_reset_tokens (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL INDEX,
    token VARCHAR(255) NOT NULL UNIQUE,
    otp INT NOT NULL,                      -- 6 digit: 100000-999999
    expires_at TIMESTAMP NOT NULL,         -- 15 menit dari now()
    attempts INT NOT NULL DEFAULT 0,       -- Max 5 attempts
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Environment Variables

Pastikan email configuration di `.env`:

```env
# SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io        # atau Gmail, Sendgrid, dll
MAIL_PORT=465
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ews-banjir.local
MAIL_FROM_NAME="EWS Banjir Rob"
```

### Email Testing (Development)

Gunakan Mailtrap untuk test tanpa send email sebenarnya:

1. Daftar di https://mailtrap.io
2. Copy SMTP credentials
3. Paste di `.env`
4. Test dengan:
   ```bash
   php artisan tinker
   Mail::raw('Test email', fn($m) => $m->to('test@example.com'));
   ```

---

## 📱 Alur Aplikasi

### Routes

```php
// routes/web.php

// Guest only (middleware 'guest')
Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])
    ->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])
    ->name('password.email');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showVerifyForm'])
    ->name('password.verify.form');

Route::post('/reset-password', [ForgotPasswordController::class, 'verifyAndResetPassword'])
    ->name('password.update');
```

### Controller: ForgotPasswordController

**Method 1: `showRequestForm()`**
- Tampilkan form input email
- Route: `GET /forgot-password`

**Method 2: `sendOtp(Request $request)`**
- Validasi email exists
- Generate OTP & token
- Simpan ke DB dengan expiry 15 menit
- Send email dengan OTP
- Redirect ke verifikasi form
- Route: `POST /forgot-password`

**Method 3: `showVerifyForm($token)`**
- Check token valid & not expired
- Tampilkan form OTP + password baru
- Route: `GET /reset-password/{token}`

**Method 4: `verifyAndResetPassword(Request $request)`**
- Validasi OTP (6 digit)
- Check token exists & not expired
- Check OTP cocok
- Check attempts < 5
- Update password (bcrypt hashed)
- Delete token (one-time use)
- Redirect ke login dengan success
- Route: `POST /reset-password`

### Model: PasswordResetToken

```php
class PasswordResetToken extends Model {
    protected $fillable = ['email', 'token', 'otp', 'expires_at', 'attempts'];

    public function isExpired(): bool
        // Check: now() > expires_at

    public function isValid(): bool
        // Check: not expired AND attempts < 5

    public function incrementAttempts(): void
        // +1 attempts setiap OTP salah
}
```

### Mailable: SendPasswordResetOtp

File: `app/Mail/SendPasswordResetOtp.php`

```php
public function __construct(
    public string $email,
    public int $otp,
    public string $resetUrl
) {}
```

**Email Template:** `resources/views/emails/password-reset-otp.blade.php`
- Tampilkan OTP dengan font besar
- Link ke verifikasi page
- Info keamanan (15 menit expiry, jangan share OTP)

### Views

#### 1. Forgot Password Form (`resources/views/auth/forgot-password.blade.php`)
- Input email
- Submit button
- Link kembali ke login
- Display error messages

#### 2. Verify OTP Form (`resources/views/auth/verify-otp.blade.php`)
- Input OTP (6 digit, numeric only)
- Input password baru
- Confirm password field
- Toggle show/hide password
- Display error messages
- Link untuk request kode baru

---

## 🔒 Keamanan

### 1. OTP Security
```
✓ 6-digit numeric: 1 juta kombinasi
✓ Random generated: random_int(100000, 999999)
✓ Unique per request: satu token = satu OTP
✓ Expire 15 menit: prevent brute force
✓ Max 5 attempts: lock after failures
```

### 2. Token Security
```
✓ 64 karakter random string
✓ SHA-256 level entropy
✓ Unique & indexed di database
✓ Tidak ada di URL, hanya di database
✓ One-time use: delete setelah success
```

### 3. Password Security
```
✓ Minimum 8 karakter
✓ Bcrypt hashing (Laravel auto)
✓ Password confirmation required
✓ Old password tidak dicek (acceptable untuk forgot password)
```

### 4. Email Delivery Security
```
✓ HTTPS redirect untuk reset link
✓ Email dikirim via SMTP terenkripsi
✓ Token tidak di URL, user click/copy paste
✓ Server-side validation token sebelum accept
```

### 5. Rate Limiting
```
✓ Max 5 attempts per token
✓ Increment attempts counter setiap OTP salah
✓ Lock token setelah 5 gagal
✓ Timeout 15 menit (auto expire)
```

### 6. Data Privacy
```
✓ Password tidak log / tidak store plain text
✓ OTP hanya di email, tidak di SMS/chat/log
✓ Token dihapus setelah success (one-time)
✓ Expired token di-delete otomatis (optional cleanup job)
```

---

## 🧪 Testing

### Test Skenario 1: Happy Path

```bash
# 1. Di browser login page
# Klik "Lupa Password?"

# 2. Form forgot-password muncul
# Input: user@example.com

# 3. Check inbox
# Lihat email dengan OTP (mis: 123456)

# 4. Click link di email atau manual:
# Visit: http://localhost:8000/reset-password/{token}

# 5. Form verify muncul
# Input OTP: 123456
# Password: newpassword123
# Confirm: newpassword123

# 6. Submit
# Success: "Password berhasil direset"

# 7. Login dengan password baru
```

### Test Skenario 2: OTP Salah

```bash
# 1-4. Same seperti Happy Path

# 5. Form verify muncul
# Input OTP: 999999 (salah)

# 6. Submit
# Error: "Kode OTP salah. Percobaan 1 dari 5."

# 7. Coba lagi dengan OTP benar
# Success
```

### Test Skenario 3: Token Expired

```bash
# 1-2. Request password reset

# 3. Tunggu 16 menit (> 15 menit expiry)

# 4. Click link di email
# Error: "Link reset password telah kadaluarsa"

# 5. Request ulang password reset
```

### Manual Testing via Tinker

```bash
php artisan tinker

# Check pending tokens
DB::table('password_reset_tokens')->get();

# Create manual token (debug)
DB::table('password_reset_tokens')->insert([
    'email' => 'test@example.com',
    'token' => 'test-token-123',
    'otp' => 123456,
    'expires_at' => now()->addMinutes(15),
    'attempts' => 0,
    'created_at' => now(),
    'updated_at' => now(),
]);

# Delete token
DB::table('password_reset_tokens')->where('email', 'test@example.com')->delete();
```

---

## 🐛 Troubleshooting

### Email Tidak Terkirim

**Checklist:**
- [ ] `.env` email config benar
- [ ] SMTP credentials valid
- [ ] Firebase/Mailgun API key active
- [ ] Email address benar-benar terdaftar
- [ ] Check `storage/logs/laravel.log` untuk error
- [ ] Test dengan `php artisan tinker` → `Mail::raw(...)`

**Solution:**
```bash
# Test send email
php artisan tinker
use App\Mail\SendPasswordResetOtp;
use Illuminate\Support\Facades\Mail;

Mail::to('your-test@gmail.com')
    ->send(new SendPasswordResetOtp('your-test@gmail.com', 123456, 'http://localhost/reset-password/token'));
```

### OTP Salah Terus

**Kemungkinan:**
- User input salah (check OTP di email)
- Browser/email masalah copy-paste
- Database OTP tidak tersimpan dengan benar

**Debug:**
```bash
# Check token di database
DB::table('password_reset_tokens')
    ->where('email', 'user@example.com')
    ->latest()
    ->first();

# Lihat OTP value
```

### Token Tidak Valid

**Kemungkinan:**
- Token di URL sudah salah (URL manipulation)
- Token dihapus (sudah berhasil sebelumnya)
- Database inconsistency

**Solution:**
```bash
# Check token exists
DB::table('password_reset_tokens')
    ->where('token', 'the-token-from-url')
    ->first();

# Check expiry
DB::table('password_reset_tokens')
    ->where('token', 'the-token-from-url')
    ->where('expires_at', '>', now())
    ->first();
```

### Max Attempts Reached

**Error:** "Terlalu banyak percobaan salah"

**Solution:**
- User harus request kode baru
- Old token dengan attempts >= 5 tidak bisa digunakan lagi
- Atau manual: delete token dari DB
  ```bash
  DB::table('password_reset_tokens')
      ->where('email', 'user@example.com')
      ->delete();
  ```

### Password Update Gagal

**Checklist:**
- [ ] Validasi password passed (min 8 char, confirmed)
- [ ] User record exists
- [ ] Database writable

**Check Log:**
```bash
tail -f storage/logs/laravel.log | grep -i "password"
```

---

## 📊 Monitoring

### Check Pending Requests

```bash
# Lihat berapa password reset pending
SELECT COUNT(*) as pending_count 
FROM password_reset_tokens 
WHERE expires_at > NOW();

# Lihat token yang sudah expired
SELECT * FROM password_reset_tokens 
WHERE expires_at < NOW();
```

### Cleanup Expired Tokens

Buat scheduled command untuk cleanup (optional):

```bash
php artisan make:command CleanupExpiredPasswordTokens
```

```php
// app/Console/Commands/CleanupExpiredPasswordTokens.php

class CleanupExpiredPasswordTokens extends Command {
    protected $signature = 'password-tokens:cleanup';

    public function handle() {
        $deleted = DB::table('password_reset_tokens')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted {$deleted} expired tokens.");
    }
}
```

Register di `routes/console.php`:
```php
Schedule::command('password-tokens:cleanup')
    ->hourly()
    ->withoutOverlapping();
```

---

## 📚 Referensi

- [Laravel Authentication Docs](https://laravel.com/docs/authentication)
- [Laravel Password Reset](https://laravel.com/docs/authentication#resetting-passwords)
- [OWASP Password Security](https://owasp.org/www-community/authentication/Password_Reset)
- [Bcrypt Security](https://en.wikipedia.org/wiki/Bcrypt)

---

**Last Updated:** 2026-07-19  
**Maintained By:** EWS Development Team
