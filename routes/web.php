<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('sign-in')->middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/verification', [AuthController::class, 'login'])->name('proses');
});

Route::post('/sign-out', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::prefix('dashboard')->middleware(['auth', 'permission:view dashboard'])->group(function() {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/devices/{device:device_code}/snapshot', [DashboardController::class, 'snapshot'])
        ->name('dashboard.devices.snapshot');
});

// Master Data — Devices (CRUD, dilindungi permission per aksi).
Route::prefix('devices')->middleware('auth')->group(function () {
    Route::get('/', [DeviceController::class, 'index'])
        ->middleware('permission:view devices')->name('devices');
    Route::get('/create', [DeviceController::class, 'create'])
        ->middleware('permission:create devices')->name('devices.create');
    Route::post('/', [DeviceController::class, 'store'])
        ->middleware('permission:create devices')->name('devices.store');
    Route::get('/{device:device_code}/edit', [DeviceController::class, 'edit'])
        ->middleware('permission:edit devices')->name('devices.edit');
    Route::patch('/{device:device_code}', [DeviceController::class, 'update'])
        ->middleware('permission:edit devices')->name('devices.update');
    Route::delete('/{device:device_code}', [DeviceController::class, 'destroy'])
        ->middleware('permission:delete devices')->name('devices.destroy');
});

// Preferensi tampilan per user — perlu login (auth dibangun terpisah).
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/profile', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
});
