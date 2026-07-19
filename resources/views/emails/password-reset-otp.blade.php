<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Permintaan Reset Password</h1>

        <p>Halo,</p>

        <p>Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password.</p>

        <div style="background: #f5f5f5; border: 2px solid #ddd; padding: 30px; text-align: center; border-radius: 8px; margin: 30px 0;">
            <div style="font-size: 48px; font-weight: bold; letter-spacing: 8px; color: #0891b2;">
                {{ $otp }}
            </div>
        </div>

        <p style="font-size: 14px; color: #666;"><strong>Kode ini berlaku selama 15 menit.</strong></p>

        <p>Atau klik tombol di bawah untuk membuka halaman verifikasi:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background: #0891b2; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">
                Verifikasi Password Reset
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <h3 style="color: #2c3e50;">Catatan Keamanan:</h3>
        <ul style="color: #666; font-size: 14px;">
            <li>Jangan bagikan kode OTP ini kepada siapa pun</li>
            <li>Kami tidak akan pernah meminta kode ini melalui email atau chat</li>
            <li>Jika Anda tidak membuat permintaan ini, abaikan email ini</li>
        </ul>

        <p style="color: #666; font-size: 12px;">Jika tombol di atas tidak berfungsi, copy dan paste URL berikut di browser Anda:<br>
        <a href="{{ $resetUrl }}" style="color: #0891b2;">{{ $resetUrl }}</a></p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="text-align: center; color: #999; font-size: 12px;">
            Terima kasih,<br>
            <strong>Tim Early Warning System Banjir</strong>
        </p>
    </div>
</body>
</html>
