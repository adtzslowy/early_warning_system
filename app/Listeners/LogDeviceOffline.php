<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DeviceWentOffline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogDeviceOffline
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DeviceWentOffline $event): void
    {
        Log::warning('Device terdeteksi offline', [
            'device_code' => $event->device->device_code,
            'last_seen_at' => $event->device->last_seen_at?->toDateTimeString(),
        ]);

        // TODO: trigger Notification telegram disini
    }
}
