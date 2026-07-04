<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\RobDevice;
use App\Events\DeviceStatusChanged;
use App\Events\SensorDataSaved;
use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class RobSyncService
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = config('services.iot.base_url');
    }

    /**
     * Ambil data terbaru dari API, kemudian simpan ke Device dan histori sensor.
     *
     * @return int Jumlah device yang berhasil diproses
     */
   public function sync(): int
    {
        $payload = $this->fetch();
        $processed = 0;

        foreach ($payload as $deviceCode => $devicePayload) {
            if (! is_array($devicePayload)) {
                continue;
            }

            $dto = RobDevice::fromArray((string) $deviceCode, $devicePayload);

            DB::transaction(function () use ($dto, &$processed) {
                $previousStatus = Device::query()
                    ->where('device_code', $dto->deviceCode)
                    ->value('status');

                $device = $this->upsertDevice($dto);
                $this->storeReadings($device, $dto);

                if ($previousStatus !== null && $previousStatus !== $device->status) {
                    event(new DeviceStatusChanged(
                        device: $device,
                        previous: $previousStatus,
                        current: $device->status,
                    ));
                }

                event(new SensorDataSaved($device));

                $processed++;
            });
        }

        return $processed;
    }

    private function fetch(): array
    {
        if (empty($this->endpoint))
        {
            Log::error("RobSyncService: endpoint API belum dikonfigurasi");
            return [];
        }

        $response = Http::timeout(5)->retry(2, 500)->get($this->endpoint);
        if ($response->failed()) {
            Log::error('Gagal fetch data dari API IoT', [
                'endpoint' => $this->endpoint,
                'status' => $response->status(),
            ]);

            throw new RuntimeException(
                "Fetch api iot gagal dengan status {$response->status()}"
            );
        }

        return $response->json('devices') ?? [];
    }

    private function upsertDevice(RobDevice $dto): Device
    {
        return Device::query()->updateOrCreate(
            ['device_code' => $dto->deviceCode],
            [
                'name' => $dto->name,
                'latitude' => $dto->latitude,
                'longitude' => $dto->longitude,
                'status' => $dto->status,
                'last_seen_at' => $dto->status->value === 'online'
                    ? now()
                    : $dto->lastSeenAt,
            ]
        );
    }

    private function storeReadings(Device $device, RobDevice $dto): void
    {
        $recordedAt = $dto->lastSeenAt ?? now();

        foreach ($dto->readings as $reading) {
            Sensor::query()->create([
                'device_id' => $device->id,
                'type' => $reading->type,
                'value' => $reading->value,
                'unit' => $reading->type->unit(),
                'recorded_at' => $recordedAt,
            ]);
        }
    }
}
