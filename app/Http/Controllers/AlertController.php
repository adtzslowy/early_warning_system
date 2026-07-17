<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(Request $request): View
    {
        $riskLevelFilter = $request->query('risk_level', '');
        $deviceFilter = $request->query('device', '');

        $alerts = Alert::query()
            ->with('device')
            ->when($riskLevelFilter, fn($q) => $q->where('risk_level', $riskLevelFilter))
            ->when($deviceFilter, fn($q) => $q->where('device_id', $deviceFilter))
            ->orderByDesc('triggered_at')
            ->paginate(25)
            ->withQueryString();

        $riskLevels = ['Waspada', 'Siaga', 'Bahaya'];
        $devices = \App\Models\Device::query()
            ->orderBy('device_code')
            ->get(['id', 'device_code', 'name']);

        return view('alert.index', [
            'alerts' => $alerts,
            'riskLevels' => $riskLevels,
            'devices' => $devices,
            'selectedRiskLevel' => $riskLevelFilter,
            'selectedDevice' => $deviceFilter,
        ]);
    }
}
