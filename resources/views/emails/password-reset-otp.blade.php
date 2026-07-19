<x-mail::message>
# Permintaan Reset Password

Halo,

Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password.

<x-mail::panel>
    <div style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; padding: 20px;">
        {{ $otp }}
    </div>
</x-mail::panel>

**Kode ini berlaku selama 15 menit.**

Atau klik tombol di bawah untuk membuka halaman verifikasi:

<x-mail::button :url="$resetUrl" color="primary">
    Verifikasi Password Reset
</x-mail::button>

**Catatan keamanan:**
- Jangan bagikan kode OTP ini kepada siapa pun
- Kami tidak akan pernah meminta kode ini melalui email atau chat
- Jika Anda tidak membuat permintaan ini, abaikan email ini

Jika tombol di atas tidak berfungsi, copy dan paste URL berikut di browser Anda:
{{ $resetUrl }}

---

Terima kasih,
**Tim Early Warning System Banjir**
</x-mail::message>
