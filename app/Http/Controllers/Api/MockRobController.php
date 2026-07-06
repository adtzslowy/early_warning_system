<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyntheticRobDataGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint IoT TIRUAN untuk demo/skripsi. Meniru bentuk respons
 * iot.ketapangkab.go.id/api/rob, tetapi angkanya disintesis dari model
 * pasang-surut/diurnal + data maritim BMKG (atribusi wajib).
 *
 * DEMO-ONLY: jangan arahkan IOT_KETAPANG_URL produksi ke sini.
 * Arahkan hanya di environment non-produksi:
 *   IOT_KETAPANG_URL=http://127.0.0.1:8000/api/mock/rob
 */
final class MockRobController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        abort_unless((bool) config('synthetic.enabled'), 404);

        // Skenario opsional untuk demo: ?scenario=badai | tenang
        $scenario = $request->query('scenario');
        $scenario = is_string($scenario) ? $scenario : null;

        $devices = SyntheticRobDataGenerator::make()->generate($scenario);

        return response()->json([
            'source' => 'SYNTHETIC (data maritim: BMKG)',
            'attribution' => 'Sebagian data digerakkan oleh BMKG (Badan Meteorologi, Klimatologi, dan Geofisika).',
            'generated_at' => now()->toIso8601String(),
            'devices' => $devices,
        ]);
    }
}
