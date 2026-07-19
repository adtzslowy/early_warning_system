<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\DeviceStatus;
use App\Enums\SensorType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * @param RobSensorReading[] $readings
 */
final readonly class RobDevice
{
    public function __construct(
        public string $deviceCode,
        public string $name,
        public DeviceStatus $status,
        public ?float $latitude,
        public ?float $longitude,
        public ?Carbon $lastSeenAt,
        public array $readings,
    ) {}

    public static function fromArray(string $deviceCode, array $payload): self
    {
        $status = ($payload['status'] ?? 'offline') === 'online'
            ? DeviceStatus::Online
            : DeviceStatus::Offline;

        return new self(
            deviceCode: $deviceCode,
            // API tidak kirim "name" — pakai device code dulu, bisa
            // di-override manual nanti lewat tabel/config statis.
            name: $payload['name'] ?? strtoupper($deviceCode),
            status: $status,
            // API tidak kirim koordinat sama sekali. Lihat catatan
            // di bawah soal sumber koordinat.
            latitude: isset($payload['latitude']) ? (float) $payload['latitude'] : null,
            longitude: isset($payload['longitude']) ? (float) $payload['longitude'] : null,
            lastSeenAt: isset($payload['last_seen'])
                ? Carbon::parse($payload['last_seen'])
                : null,
            readings: self::mapReadings($payload),
        );
    }

    /**
     * @return RobSensorReading[]
     */
    private static function mapReadings(array $payload): array
    {
        // Key kiri = field asli dari API iot.ketapangkab.go.id/api/rob
        $map = [
            'ketinggian_air' => SensorType::WaterLevel,
            'suhu' => SensorType::Temperature,
            'kelembapan' => SensorType::Humidity,
            'tekanan_udara' => SensorType::AirPressure,
            'kecepatan_angin' => SensorType::WindSpeed,
            'arah_angin' => SensorType::WindDirection,
        ];

        $readings = [];

        foreach ($map as $key => $type) {
            if (! array_key_exists($key, $payload)) {
                continue;
            }

            $value = $payload[$key];

            if ($value !== null && !is_numeric($value)) {
                Log::warning("Invalid sensor value for {$key}: non-numeric value received", [
                    'type' => $type->value,
                    'value' => $value,
                ]);
                continue;
            }

            $readings[] = new RobSensorReading(
                type: $type,
                value: $value !== null ? (float) $value : null,
            );
        }

        return $readings;
    }
}
