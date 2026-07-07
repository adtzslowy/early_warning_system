<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\DeviceStatus;
use App\Models\Device;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusChanged implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Device $device,
        public readonly DeviceStatus $previous,
        public readonly DeviceStatus $current
    ){}
}
