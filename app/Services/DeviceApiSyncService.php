<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\RobDevice;
use App\Enums\DeviceStatus;
use App\Events\DeviceStatusChanged;
use App\Events\SensorDataSaved;
use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class DeviceApiSyncService
{
    /**
     * Tarik data untuk semua device yang punya api aktif
     *
     * @return int jumlah device yang berhasil diproses
     * */
    public function syncAll(): int
    {
        $devices = Device::query()
            ->where('api_enabled', true)
            ->whereNotNull('api_url')
            ->get();

        $processed = 0;

        foreach ($devices as $device) {
            try {
                if ($this->syncDevice($device)) {
                    $processed++;
                }
            } catch (Throwable $e) {
                Log::warning("Sync device {$device->device_code} gagal", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $processed;
    }

    public function syncDevice(Device $device): bool
    {
        $res = Http::timeout(5)
            ->retry(2, 300)
            ->when($device->api_key, fn($http) => $http->withToken($device->api_key))
            ->get($device->api_url);

        if ($res->failed()) {
            $this->markOffline($device);
            Log::warning("API device {$device->device_code} balas {$res->status()}");
            return false;
        }

        $payload = $res->json('data') ?? $res->json() ?? [];
        $payload = $this->extractDevicePayload($payload, $device);

        $dto = RobDevice::fromArray($device->device_code, $payload);

        DB::transaction(function () use ($device, $dto) {
            $previousStatus = $device->status;

            /**
             * hanya memperbaiki status dan last_seen
             *
             **/
            $device->update([
                'status' => $dto->status,
                'last_seen_at' => $dto->status === DeviceStatus::Online ? now() : $dto->lastSeenAt,
            ]);

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

            if ($previousStatus !== $device->status) {
                event(new DeviceStatusChanged(
                    device: $device,
                    previous: $previousStatus,
                    current: $device->status,
                ));
            }

            event(new SensorDataSaved($device));
        });
        return true;
    }

    /**
     * Ambil payload satu device dari respons API, mendukung dua bentuk:
     *  1) datar          → { status, suhu, ketinggian_air, ... } (dipakai apa adanya)
     *  2) terbungkus     → { devices: { kode: { ... } } } (gaya /api/rob)
     *
     * Untuk bentuk terbungkus, dicocokkan berdasarkan device_code. Bila kode
     * tak ada DAN payload hanya berisi satu entri, itu dianggap skenario
     * "dummy satu-device" (API testing) — entri itu dipakai apa adanya. Bila
     * payload berisi BANYAK entri dan device_code tak ketemu, JANGAN menebak
     * device lain: log peringatan dan kembalikan array kosong, supaya data
     * milik device lain tidak pernah tersimpan di bawah device yang salah.
     */
    private function extractDevicePayload(array $payload, Device $device): array
    {
        if (isset($payload['devices']) && is_array($payload['devices'])) {
            $devices = $payload['devices'];

            if (isset($devices[$device->device_code])) {
                return $devices[$device->device_code];
            }

            if (count($devices) === 1) {
                return reset($devices) ?: [];
            }

            Log::warning('DeviceApiSyncService: device_code tidak ditemukan di payload terbungkus multi-device, dilewati', [
                'device_code' => $device->device_code,
                'available_codes' => array_keys($devices),
            ]);

            return [];
        }

        return $payload;
    }

    private function markOffline(Device $device): void
    {
        if ($device->status === DeviceStatus::Offline) {
            return;
        }

        $previous = $device->status;
        $device->update(['status' => DeviceStatus::Offline]);

        event(new DeviceStatusChanged(
            device: $device,
            previous: $previous,
            current: DeviceStatus::Offline,
        ));
    }
}
