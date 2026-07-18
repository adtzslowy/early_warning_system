<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Device;
use App\Prediction\WaterLevelPredictor;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function index(): View
    {
        $devices = Device::query()
            ->with(['latestWaterLevel', 'latestRiskEvaluation'])
            ->orderBy('device_code')
            ->get(['id', 'device_code', 'name', 'location', 'status']);

        $predictor = new WaterLevelPredictor();

        $predictions = $devices->map(function (Device $device) use ($predictor) {
            $curve = $predictor->predictAll($device);
            $riskLevel = $device->latestRiskEvaluation?->risk_level;

            return [
                'device' => $device,
                'curve' => $curve,
                'has_prediction' => !empty($curve),
                'risk_level' => $riskLevel?->value ?? 'aman',
            ];
        });

        return view('prediction.index', [
            'predictions' => $predictions,
            'total' => $devices->count(),
        ]);
    }
}
