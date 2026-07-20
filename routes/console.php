<?php

use App\Console\Commands\DetectOfflineDevices;
use App\Console\Commands\RefreshBmkgMaritime;
use App\Console\Commands\RefreshPredictions;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * EARLY WARNING SYSTEM - SCHEDULER CONFIGURATION
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Scheduler orchestrates automated tasks untuk sistem platform.
 * Semua commands di-run dengan withoutOverlapping() untuk prevent duplicate execution.
 *
 * Setup cronjob di server:
 *   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
 */

// ─────────────────────────────────────────────────────────────────────────
// 1. REAL-TIME DATA SYNC
// ─────────────────────────────────────────────────────────────────────────

/**
 * Sync data dari ROB IoT API (IoT atau synthetic mock)
 * Interval: Setiap 1 menit
 * Purpose: Fetch device list & sensor data dari API endpoint
 */
Schedule::command('rob:sync')
    ->everyMinute()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ rob:sync completed');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->error('❌ rob:sync failed');
    });

/**
 * Poll data dari device endpoint
 * Interval: Setiap 1 menit
 * Purpose: Fetch & store data sensor ke database
 */
Schedule::command('devices:poll')
    ->everyMinute()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ devices:poll completed');
    });

// ─────────────────────────────────────────────────────────────────────────
// 2. ALERT & DETECTION
// ─────────────────────────────────────────────────────────────────────────

/**
 * Check kondisi device & trigger alert
 * Interval: Setiap 1 menit
 * Purpose: Evaluasi risk level & kirim notifikasi jika ada perubahan
 */
Schedule::command('alerts:check')
    ->everyMinute()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ alerts:check completed');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->error('❌ alerts:check failed');
    });

/**
 * Detect device yang offline
 * Interval: Setiap 1 menit
 * Purpose: Check last_seen_at & update status jadi offline jika lebih dari 15 menit
 */
Schedule::command(DetectOfflineDevices::class, ['--minutes=15'])
    ->everyMinute()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ detect:offline completed');
    });

// ─────────────────────────────────────────────────────────────────────────
// 3. PREDICTION & FORECASTING
// ─────────────────────────────────────────────────────────────────────────

/**
 * Refresh prediction model untuk semua device
 * Interval: Setiap 15 menit
 * Purpose: Rebuild linear regression model & generate forecast untuk 2 jam ke depan
 */
Schedule::command(RefreshPredictions::class)
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ predictions:refresh completed');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->error('❌ predictions:refresh failed');
    });

/**
 * Sync device status dari ROB API
 * Interval: Setiap 1 menit
 * Purpose: Update device status (online/offline) dari IoT endpoint
 */
Schedule::command('devices:sync-from-rob')
    ->everyMinute()
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ devices:sync-from-rob completed');
    });

/**
 * Refresh cache data prakiraan maritim BMKG
 * Interval: Setiap 1 jam
 * Purpose: Hangatkan cache BMKG untuk generator data sintetis (demo/dev only)
 * Note: Disable di production jika tidak perlu synthetic data
 * Status: DISABLED - BMKG API sering 403, gunakan real sensor data
 */
// Schedule::command(RefreshBmkgMaritime::class)
//     ->hourly()
//     ->withoutOverlapping()
//     ->onSuccess(function () {
//         \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ bmkg:maritime completed');
//     });

// ─────────────────────────────────────────────────────────────────────────
// 4. MAINTENANCE & CLEANUP (Optional - Uncomment jika perlu)
// ─────────────────────────────────────────────────────────────────────────

/**
 * Cleanup old sensor readings (keep only 7 days)
 * Interval: Setiap hari pukul 2 pagi
 * Purpose: Archive/delete sensor data lama untuk optimize database size
 * Status: COMMENTED - Uncomment jika ingin enable
 */
// Schedule::command('sensor:cleanup')
//     ->dailyAt('02:00')
//     ->withoutOverlapping()
//     ->onSuccess(function () {
//         \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ sensor:cleanup completed');
//     });

/**
 * Archive old notification logs (keep only 30 days)
 * Interval: Setiap hari pukul 3 pagi
 * Purpose: Archive notification history untuk comply dengan data retention policy
 * Status: COMMENTED - Uncomment jika ingin enable
 */
// Schedule::command('notifications:archive')
//     ->dailyAt('03:00')
//     ->withoutOverlapping()
//     ->onSuccess(function () {
//         \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ notifications:archive completed');
//     });

// ─────────────────────────────────────────────────────────────────────────
// 5. MONITORING & REPORTING (Optional - Uncomment jika perlu)
// ─────────────────────────────────────────────────────────────────────────

/**
 * Generate daily system health report
 * Interval: Setiap hari pukul 6 pagi
 * Purpose: Cek kesehatan sistem & email report ke admin
 * Status: COMMENTED - Uncomment jika ingin enable
 */
// Schedule::command('system:health-report')
//     ->dailyAt('06:00')
//     ->withoutOverlapping()
//     ->onSuccess(function () {
//         \Illuminate\Support\Facades\Log::channel('scheduler')->info('✅ system:health-report completed');
//     });

