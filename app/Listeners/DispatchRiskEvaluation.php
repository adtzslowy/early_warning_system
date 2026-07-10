<?php

namespace App\Listeners;

use App\Events\SensorDataSaved;
use App\Jobs\EvaluateDeviceRiskJob;

final class DispatchRiskEvaluation
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
    public function handle(SensorDataSaved $event): void
    {
        EvaluateDeviceRiskJob::dispatch($event->device);
    }
}
