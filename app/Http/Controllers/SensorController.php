<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SensorType;
use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $deviceId = trim((string) $request->query('device_id', ''));
        $type = trim((string) $request->query('type', ''));

        $total = Sensor::whereHas('device', fn ($q) => $q->visibleTo($request->user()))->count();

        $sensors = Sensor::query()
            ->with('device')
            ->whereHas('device', fn ($q) => $q->visibleTo($request->user()))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('type', 'like', "%{$search}%")
                        ->orWhereHas('device', function ($deviceQuery) use ($search) {
                            $deviceQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('device_code', 'like', "%{$search}%")
                                ->orWhere('location', 'like', "%{$search}%");
                        });
                });
            })
            ->when($deviceId !== '', fn ($query) => $query->where('device_id', $deviceId))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $devices = Device::query()
            ->visibleTo($request->user())
            ->orderBy('device_code')
            ->get(['id', 'device_code', 'name']);

        $types = collect(SensorType::cases())->map(fn ($t) => $t->value);

        $view = view('sensors.index', compact(
            'sensors', 'search', 'deviceId', 'type', 'devices', 'types', 'total'
        ));

        return $request->ajax() ? $view->fragment('results') : $view;
    }

    public function show(Request $request, Sensor $sensor)
    {
        abort_unless(
            Device::query()
                ->visibleTo($request->user())
                ->whereKey($sensor->device_id)
                ->exists(),
            403,
        );

        return view('sensors.show', [
            'sensor' => $sensor,
        ]);
    }
}
