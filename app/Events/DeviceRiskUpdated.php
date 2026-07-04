<?php

namespace App\Events;

use App\Enums\SensorType;
use App\Models\Device;
use App\Models\RiskEvaluation;
use App\Models\Sensor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceRiskUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Device $device,
        public readonly RiskEvaluation $evaluation,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('rob-monitoring')
        ];
    }

    public function broadcastAs(): string
    {
        return 'device.risk.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'device_code' => $this->device->device_code,
            'status' => $this->device->status->value,
            'water_level' => $this->floatOrNull($this->evaluation->water_level),
            'rise_rate' => $this->floatOrNull($this->evaluation->rise_rate),
            'onshore_wind' => $this->floatOrNull($this->evaluation->onshore_wind),
            'risk' => $this->evaluation->risk_level->value,
            'risk_score' => (float) $this->evaluation->risk_score,
            'temperature' => $this->latestReading(SensorType::Temperature),
            'humidity' => $this->latestReading(SensorType::Humidity),
            'air_pressure' => $this->latestReading(SensorType::AirPressure),
            'wind_speed' => $this->latestReading(SensorType::WindSpeed),
            'wind_direction' => $this->latestReading(SensorType::WindDirection),
            'evaluated_at' => $this->evaluation->evaluated_at->toIso8601String(),
        ];
    }

    /** Nilai sensor terbaru (by recorded_at) untuk satu jenis — samakan dgn tampilan dashboard. */
    private function latestReading(SensorType $type): ?float
    {
        $value = Sensor::query()
            ->where('device_id', $this->device->id)
            ->where('type', $type)
            ->latest('recorded_at')
            ->value('value');

        return $this->floatOrNull($value);
    }

    private function floatOrNull(mixed $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }
}
