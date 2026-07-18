<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\Api\MockRobController;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Authentication\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Endpoint IoT TIRUAN (demo/skripsi) — publik & tanpa auth agar bisa di-poll
// oleh RobSyncService seperti API IoT asli. Nonaktif di production (lihat
// config/synthetic.php). Arahkan IOT_KETAPANG_URL ke sini HANYA untuk demo.
Route::get('/api/mock/rob', MockRobController::class)->name('api.mock.rob');

Route::prefix('sign-in')
    ->middleware('guest')
    ->group(function () {
        Route::get('/', [AuthController::class, 'index'])->name('login');
        Route::post('/verification', [AuthController::class, 'login'])->name('proses');
    });

Route::middleware('guest')->group(function () {
    Route::get('/sign-up', [RegisterController::class, 'index'])->name('register');
    Route::post('/sign-up', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function (Request $request) {
        return $request->user()->hasVerifiedEmail() ? redirect()->route('dashboard') : view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('status', 'Email berhasil diverifikasi!');
    })
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Tautan verifikasi baru sudah dikirim.');
    })
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::post('/sign-out', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('dashboard')
    ->middleware(['auth', 'verified.grace', 'permission:view dashboard'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/devices/{device:device_code}/snapshot', [DashboardController::class, 'snapshot'])->name('dashboard.devices.snapshot');
        Route::get('/devices/{device:device_code}/detail', [DashboardController::class, 'detail'])->name('dashboard.devices.detail');
    });

// Monitoring — Peta interaktif
Route::get('/monitoring', [MonitoringController::class, 'index'])
    ->middleware(['auth', 'verified.grace', 'permission:view monitoring'])
    ->name('monitoring');

// Master Data — Devices (CRUD, dilindungi permission per aksi).
Route::prefix('devices')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [DeviceController::class, 'index'])
            ->middleware('permission:view devices')
            ->name('devices');
        Route::get('/create', [DeviceController::class, 'create'])
            ->middleware('permission:view devices')
            ->name('devices.create');
        Route::post('/', [DeviceController::class, 'store'])
            ->middleware('permission:view devices')
            ->name('devices.store');
        Route::get('/{device:device_code}/edit', [DeviceController::class, 'edit'])
            ->middleware('permission:view devices')
            ->name('devices.edit');
        Route::patch('/{device:device_code}', [DeviceController::class, 'update'])
            ->middleware('permission:view devices')
            ->name('devices.update');
        Route::delete('/{device:device_code}', [DeviceController::class, 'destroy'])
            ->middleware('permission:view devices')
            ->name('devices.destroy');
    });

// Preferensi tampilan per user — perlu login (auth dibangun terpisah).
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
});

Route::prefix('users')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:view users')
            ->name('users');
        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:create users')
            ->name('users.create');
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:create users')
            ->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:edit users')
            ->name('users.edit');
        Route::patch('/{user}', [UserController::class, 'update'])
            ->middleware('permission:edit users')
            ->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:delete users')
            ->name('users.destroy');
    });

Route::prefix('sensor')
    ->middleware(['auth', 'permission:view sensors'])
    ->group(function () {
        Route::get('/', [SensorController::class, 'index'])->name('sensors');
        Route::get('/{sensor}', [SensorController::class, 'show'])->name('sensors.show');
    });

Route::prefix('alert')->middleware(['auth', 'permission:view alert'])
    ->group(function() {
        Route::get('/', [AlertController::class, 'index'])->name('alerts');
    });

Route::get('/prediction', [PredictionController::class, 'index'])
    ->middleware(['auth', 'permission:view prediction'])
    ->name('prediction');

Route::get('/notifications/log', [NotificationController::class, 'log'])
    ->middleware(['auth', 'permission:view notifications'])
    ->name('notifications.log');
