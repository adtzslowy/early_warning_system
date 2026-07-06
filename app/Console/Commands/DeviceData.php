<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Device;
use App\Services\DeviceApiSyncService;
use Illuminate\Console\Command;

class DeviceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:poll {--device= : device_code tertentu saja}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarik data telemetri dari API masing-masing device';

    /**
     * Execute the console command.
     */
    public function handle(DeviceApiSyncService $service): int
    {
        if ($code = $this->option('device')) {
            $device = Device::query()->where('device_code', $code)->firstOrFail();
            $ok = $service->syncDevice($device);
            $this->info($ok ? "OK: {$code}" : "Gagal: {$code}");
            return $ok ? self::SUCCESS : self::FAILURE;
        }

        $count = $service->syncAll();
        $this->info("Selesai. {$count} device berhasil ditarik.");
        return self::SUCCESS;

    }
}
