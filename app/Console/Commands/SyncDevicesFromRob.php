<?php

namespace App\Console\Commands;

use App\Enums\DeviceStatus;
use App\Models\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncDevicesFromRob extends Command
{
    protected $signature = 'devices:sync-from-rob';
    protected $description = 'Sync device list & status dari ROB API (IoT atau synthetic)';

    public function handle()
    {
        $url = config('app.iot_url') ?? env('IOT_KETAPANG_URL');

        if (!$url) {
            $this->error('IOT_KETAPANG_URL not configured');
            return 1;
        }

        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->ok()) {
                $this->error("API returned status {$response->status()}");
                return 1;
            }

            $data = $response->json();
            $devices = $data['devices'] ?? [];

            if (empty($devices)) {
                $this->warn('No devices in API response');
                return 0;
            }

            $synced = 0;
            foreach ($devices as $code => $device) {
                $status = match($device['status'] ?? null) {
                    'online' => DeviceStatus::Online,
                    'offline' => DeviceStatus::Offline,
                    default => DeviceStatus::Offline,
                };

                Device::where('device_code', $code)->update([
                    'status' => $status,
                    'last_seen_at' => $device['last_seen'] ?? now(),
                    'latitude' => $device['latitude'] ?? null,
                    'longitude' => $device['longitude'] ?? null,
                ]);

                $synced++;
            }

            $this->info("✅ Synced $synced devices from ROB API");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return 1;
        }
    }
}
