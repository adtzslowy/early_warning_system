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
            ->orderBy('device_code')
            ->get(['id', 'device_code', 'name', 'location', 'status', 'water_level', 'risk']);

        $predictor = new WaterLevelPredictor();
        
        $predictions = $devices->map(function (Device $device) use ($predictor) {
            $curve = $predictor->predictAll($device);
            
            return [
                'device' => $device,
                'curve' => $curve,
                'has_prediction' => !empty($curve),
            ];
        });

        return view('prediction.index', [
            'predictions' => $predictions,
            'total' => $devices->count(),
        ]);
    }
}
