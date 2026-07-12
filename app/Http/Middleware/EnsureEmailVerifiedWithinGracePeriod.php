<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * User yang belum verifikasi email masih boleh mengakses selama masa
 * tenggang 3x24 jam sejak mendaftar. Lewat dari itu, akses dimatikan
 * (alihkan ke halaman verifikasi) sampai email diverifikasi
 */

class EnsureEmailVerifiedWithinGracePeriod
{
    private const GRACE_HOURS = 72;
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        /**
         * Sudah verifikasi (termasuk user via google) -> lolos
         */

        if (! $user || $user->hasVerifiedEmail()) {
            return $next($request);
        }

        $deadline = $user->created_at->copy()->addHours(self::GRACE_HOURS);

        if (now()->lessThanOrEqualTo($deadline)) {
            return $next($request);
        }

        return redirect()->route('verification.notice')->with('blocked', true);
    }
}
