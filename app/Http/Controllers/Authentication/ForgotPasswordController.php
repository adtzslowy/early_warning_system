<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Mail\SendPasswordResetOtp;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem.',
        ]);

        $email = $request->email;

        PasswordResetToken::where('email', $email)->delete();

        $otp = random_int(100000, 999999);
        $token = Str::random(64);

        $reset = PasswordResetToken::create([
            'email' => $email,
            'token' => $token,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(15),
        ]);

        $resetUrl = route('password.verify.form', ['token' => $token]);
        Mail::to($email)->send(new SendPasswordResetOtp($email, $otp, $resetUrl));

        session(['password_reset_email' => $email]);

        return redirect()->route('password.verify.form', ['token' => $token])
            ->with('success', 'Kode reset password telah dikirim ke email Anda.');
    }

    public function showVerifyForm($token)
    {
        $reset = PasswordResetToken::where('token', $token)->first();

        if (!$reset || $reset->isExpired()) {
            return redirect()->route('password.request')
                ->withErrors('Link reset password telah kadaluarsa. Silakan coba lagi.');
        }

        return view('auth.verify-otp', ['token' => $token, 'email' => $reset->email]);
    }

    public function verifyAndResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ], [
            'otp.digits' => 'Kode OTP harus 6 digit.',
            'password.confirmed' => 'Password tidak cocok.',
        ]);

        $reset = PasswordResetToken::where('token', $request->token)->first();

        if (!$reset) {
            throw ValidationException::withMessages([
                'otp' => 'Token tidak valid.',
            ]);
        }

        if ($reset->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP telah kadaluarsa.',
            ]);
        }

        if (!$reset->isValid()) {
            throw ValidationException::withMessages([
                'otp' => 'Terlalu banyak percobaan salah. Silakan minta kode baru.',
            ]);
        }

        if ((int) $request->otp !== $reset->otp) {
            $reset->incrementAttempts();
            throw ValidationException::withMessages([
                'otp' => 'Kode OTP salah. Percobaan ' . $reset->attempts . ' dari 5.',
            ]);
        }

        $user = User::where('email', $reset->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'otp' => 'Email tidak ditemukan di sistem.',
            ]);
        }

        $user->update(['password' => $request->password]);
        $reset->delete();

        return redirect()->route('login')
            ->with('status', '✅ Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
