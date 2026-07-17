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

Schedule::command('rob:sync')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(DetectOfflineDevices::class, ['--minutes=15'])
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command("devices:poll")
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command(RefreshPredictions::class)
    ->everyFifteenMinutes()
    ->withoutOverlapping();

// Hangatkan cache prakiraan maritim BMKG untuk generator data sintetis (demo).
Schedule::command(RefreshBmkgMaritime::class)
    ->hourly()
    ->withoutOverlapping();

Schedule::command('alerts:check')
    ->everyMinute()
    ->withoutOverlapping();

