<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DeviceStatus;
use App\Jobs\RefreshPredictionJob;
use App\Models\Device;
use Illuminate\Console\Command;

class RefreshPredictions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prediction:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job refresh kurva prediksi (grid 15–120 menit) untuk seluruh device online';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $devices = Device::query()->where('status', DeviceStatus::Online)->get();

        foreach ($devices as $device) {
            RefreshPredictionJob::dispatch($device);
        }

        $this->info("{$devices->count()} job prediksi di-dispatch.");

        return self::SUCCESS;
    }
}
