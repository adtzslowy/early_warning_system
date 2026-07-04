<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\DashboardController;
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


Route::prefix('dashboard')->group(function() {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/devices/{device:device_code}/snapshot', [DashboardController::class, 'snapshot'])
        ->name('dashboard.devices.snapshot');
});

// Preferensi tampilan per user — perlu login (auth dibangun terpisah).
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
