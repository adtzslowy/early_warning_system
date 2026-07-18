<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(): View
    {
        $devices = Device::query()
            ->with(['riskEvaluations' => function ($query) {
                $query->latest('evaluated_at')->limit(1);
            }])
            ->get()
            ->map(function ($device) {
                $latestRisk = $device->riskEvaluations->first();
                return [
                    'id' => $device->id,
                    'code' => $device->device_code,
                    'name' => $device->name,
                    'location' => $device->location,
                    'latitude' => (float) $device->latitude,
                    'longitude' => (float) $device->longitude,
                    'water_level' => $device->sensors()->latest('recorded_at')->value('value'),
                    'status' => $device->last_seen_at?->diffInMinutes(now()) < 15 ? 'online' : 'offline',
                    'risk' => $latestRisk?->risk_level?->value ?? 'aman',
                    'risk_score' => $latestRisk?->risk_score ?? 0,
                    'evaluated_at' => $latestRisk?->evaluated_at?->format('H:i'),
                ];
            });

        return view('monitoring.index', [
            'devices' => $devices,
        ]);
    }
}
