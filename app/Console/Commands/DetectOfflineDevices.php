<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\DeviceStatus;
use App\Events\DeviceWentOffline;
use App\Models\Device;
use Illuminate\Console\Command;

class DetectOfflineDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'devices:detect-offile {--minutes=15}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tandai device offline jika tidak ada update melewati threshold waktu';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $thresholdMinutes = (int) $this->option('minutes');
        $cutoff = now()->subMinutes($thresholdMinutes);

        $staleDevices = Device::query()
            ->where('status', DeviceStatus::Online)
            ->where(function ($query) use ($cutoff) {
                $query->where('last_seen_at', '<', $cutoff)
                    ->orWhereNull('last_seen_at');
            })
            ->get();

        foreach($staleDevices as $device) {
            $device->update(['status' => DeviceStatus::Offline]);
            event(new DeviceWentOffline($device));
        }

        $this->info("{$staleDevices->count()} device ditandai offline.");

        return self::SUCCESS;
    }
}
