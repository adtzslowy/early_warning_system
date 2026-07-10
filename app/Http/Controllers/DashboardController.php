<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\RiskLevel;
use App\Enums\SensorType;
use App\Models\Device;
use App\Models\Sensor;
use App\Support\DisplayPreferences;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $devices = Device::query()
            ->visibleTo($request->user())
            ->with([
                "latestWaterLevel",
                "latestTemperature",
                "latestHumidity",
                "latestAirPressure",
                "latestWindSpeed",
                "latestWindDirection",
                "latestRiskEvaluation",
            ])
            ->orderBy("device_code")
            ->get();

        if ($devices->isEmpty()) {
            return view("dashboard.index", [
                "deviceOptions" => collect(),
                "selected" => null,
                "selectedCode" => null,
            ]);
        }

        // Ringkasan tiap device untuk dropdown + strip bawah.
        $deviceOptions = $devices->map(
            fn(Device $device) => [
                "device_code" => $device->device_code,
                "name" => $device->name,
                "status" => $device->status->value,
                "water_level" =>
                    $device->latestWaterLevel?->value !== null
                        ? (float) $device->latestWaterLevel->value
                        : null,
                "risk" =>
                    $device->latestRiskEvaluation?->risk_level ??
                    RiskLevel::Aman,
            ],
        );

        // Alat terpilih: dari ?device= bila valid, jika tidak → risiko tertinggi.
        $requested = $request->query("device");
        $selectedDevice =
            $devices->firstWhere("device_code", $requested) ??
            $this->worstRiskDevice($devices);

        return view("dashboard.index", [
            "deviceOptions" => $deviceOptions,
            "deviceTable" => $this->paginate($deviceOptions, perPage: 10),
            "selected" => $this->toDeviceDetail($selectedDevice),
            "selectedCode" => $selectedDevice->device_code,
            "prefs" => DisplayPreferences::forCurrentUser(),
        ]);
    }

    public function snapshot(Request $request, Device $device): JsonResponse
    {
        abort_unless(
            Device::query()
                ->visibleTo($request->user())
                ->whereKey($device->getKey())
                ->exists(),
            403,
        );

        $device->load([
            "latestWaterLevel",
            "latestTemperature",
            "latestHumidity",
            "latestAirPressure",
            "latestWindSpeed",
            "latestWindDirection",
            "latestRiskEvaluation",
        ]);

        return response()->json($this->toRealtimePayload($device));
    }

    /**
     * Detail lengkap satu device untuk perpindahan tanpa reload halaman.
     * Mengembalikan potongan HTML region "detail device" (dirender di server)
     * + data mentah untuk chart, lalu ditukar via JS di dashboard.
     */
    public function detail(Request $request, Device $device): JsonResponse
    {
        abort_unless(
            Device::query()
                ->visibleTo($request->user())
                ->whereKey($device->getKey())
                ->exists(),
            403,
        );

        $device->load([
            "latestWaterLevel",
            "latestTemperature",
            "latestHumidity",
            "latestAirPressure",
            "latestWindSpeed",
            "latestWindDirection",
            "latestRiskEvaluation",
        ]);

        $selected = $this->toDeviceDetail($device);
        $prefs = DisplayPreferences::forCurrentUser();

        return response()->json([
            "code" => $device->device_code,
            "html" => view("dashboard.partials.device-detail", [
                "selected" => $selected,
                "prefs" => $prefs,
            ])->render(),
            "status" => $selected["status"],
            "risk" => $selected["risk"]->value,
            "water_level" => $selected["water_level"],
            "telemetry_history" => $selected["telemetry_history"],
            "prediction_curve" => $selected["prediction_curve"],
        ]);
    }

    /**
     * Paginasi manual atas koleksi yang sudah dimuat (menghindari query kedua),
     * dengan query string (mis. ?device=) tetap terbawa di link paginasi.
     */
    private function paginate(
        Collection $items,
        int $perPage,
    ): LengthAwarePaginator {
        $page = Paginator::resolveCurrentPage("page");

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ["path" => Paginator::resolveCurrentPath()],
        )->withQueryString();
    }

    private function toDeviceDetail(Device $device): array
    {
        $evaluation = $device->latestRiskEvaluation;

        return [
            "device_code" => $device->device_code,
            "name" => $device->name,
            "location" => $device->location,
            "status" => $device->status->value,
            "water_level" => $this->floatOrNull(
                $device->latestWaterLevel?->value,
            ),
            "onshore_wind" => $this->floatOrNull($evaluation?->onshore_wind),
            "rise_rate" => $this->floatOrNull($evaluation?->rise_rate),
            "risk_score" => $this->floatOrNull($evaluation?->risk_score),
            "temperature" => $this->floatOrNull(
                $device->latestTemperature?->value,
            ),
            "humidity" => $this->floatOrNull($device->latestHumidity?->value),
            "air_pressure" => $this->floatOrNull(
                $device->latestAirPressure?->value,
            ),
            "wind_speed" => $this->floatOrNull(
                $device->latestWindSpeed?->value,
            ),
            "wind_direction" => $this->floatOrNull(
                $device->latestWindDirection?->value,
            ),
            "latitude" => $this->floatOrNull($device->latitude),
            "longitude" => $this->floatOrNull($device->longitude),
            "risk" => $evaluation?->risk_level ?? RiskLevel::Aman,
            "prediction_curve" => $this->predictionCurve($device),
            "evaluated_at" => $evaluation?->evaluated_at?->diffForHumans(),
            "last_seen_at" => $device->last_seen_at?->diffForHumans(),
            "telemetry_history" => $this->telemetryHistory($device),
        ];
    }

    /**
     * Kurva prediksi terbaru sebagai deret titik (horizon → nilai → waktu),
     * siap dipakai grafik prediksi dinamis di dashboard.
     *
     * @return list<array{horizon: int, value: float, at: string, at_ts: int}>
     */
    private function predictionCurve(Device $device): array
    {
        return $device
            ->latestPredictionCurve()
            ->map(
                fn($prediction) => [
                    "horizon" => (int) $prediction->horizon_minutes,
                    "value" => (float) $prediction->predicted_value,
                    "at" => $prediction->predicted_at
                        ->timezone("Asia/Jakarta")
                        ->format("H:i"),
                    "at_ts" => $prediction->predicted_at->getTimestamp() * 1000,
                ],
            )
            ->all();
    }

    private function telemetryHistory(Device $device): array
    {
        $types = collect([
            SensorType::Temperature,
            SensorType::Humidity,
            SensorType::AirPressure,
            SensorType::WindSpeed,
            SensorType::WindDirection,
        ]);

        $rows = Sensor::query()
            ->where("device_id", $device->id)
            ->whereIn("type", $types->map->value)
            ->latest("recorded_at")
            ->limit(500)
            ->get(["type", "value", "recorded_at"])
            ->sortBy("recorded_at")
            ->groupBy(fn(Sensor $sensor) => $sensor->type->value);

        return $types
            ->mapWithKeys(
                fn(SensorType $type) => [
                    $type->value => $rows
                        ->get($type->value, collect())
                        ->map(
                            fn(Sensor $sensor) => [
                                $sensor->recorded_at->getTimestamp() * 1000,
                                $this->floatOrNull($sensor->value),
                            ],
                        )
                        ->values()
                        ->all(),
                ],
            )
            ->all();
    }

    private function toRealtimePayload(Device $device): array
    {
        $evaluation = $device->latestRiskEvaluation;

        return [
            "device_code" => $device->device_code,
            "status" => $device->status->value,
            "water_level" => $this->floatOrNull(
                $device->latestWaterLevel?->value,
            ),
            "rise_rate" => $this->floatOrNull($evaluation?->rise_rate),
            "onshore_wind" => $this->floatOrNull($evaluation?->onshore_wind),
            "risk" => ($evaluation?->risk_level ?? RiskLevel::Aman)->value,
            "risk_score" => $this->floatOrNull($evaluation?->risk_score),
            "temperature" => $this->floatOrNull(
                $device->latestTemperature?->value,
            ),
            "humidity" => $this->floatOrNull($device->latestHumidity?->value),
            "air_pressure" => $this->floatOrNull(
                $device->latestAirPressure?->value,
            ),
            "wind_speed" => $this->floatOrNull(
                $device->latestWindSpeed?->value,
            ),
            "wind_direction" => $this->floatOrNull(
                $device->latestWindDirection?->value,
            ),
            "prediction_curve" => $this->predictionCurve($device),
            "evaluated_at" => $evaluation?->evaluated_at?->toIso8601String(),
        ];
    }

    private function floatOrNull(mixed $value): ?float
    {
        return $value !== null ? (float) $value : null;
    }

    private function worstRiskDevice(Collection $devices): Device
    {
        $order = [
            RiskLevel::Bahaya->value => 3,
            RiskLevel::Siaga->value => 2,
            RiskLevel::Waspada->value => 1,
            RiskLevel::Aman->value => 0,
        ];

        return $devices
            ->sortByDesc(function (Device $device) use ($order) {
                $level =
                    $device->latestRiskEvaluation?->risk_level ??
                    RiskLevel::Aman;

                return $order[$level->value];
            })
            ->first();
    }
}
