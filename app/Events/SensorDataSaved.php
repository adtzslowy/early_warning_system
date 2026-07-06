<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Device;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event internal: menandai satu batch data sensor baru tersimpan.
 * Dipakai untuk memicu evaluasi risiko (lihat DispatchRiskEvaluation).
 * Bukan event broadcast — hasil evaluasi disiarkan lewat DeviceRiskUpdated.
 */
class SensorDataSaved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Device $device,
    ) {}
}
