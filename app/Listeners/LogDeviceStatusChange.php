<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DeviceStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogDeviceStatusChange
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
    public function handle(DeviceStatusChanged $event): void
    {
        Log::info('Status device berubah', [
            'device_code' => $event->device->device_code,
            'previous' => $event->previous->value,
            'current' => $event->current->value,
        ]);

        // TODO: trigger Notification Telegram di sini nanti.
    }
}
