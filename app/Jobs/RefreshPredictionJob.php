<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\PredictionCompleted;
use App\Models\Device;
use App\Models\Prediction;
use App\Prediction\WaterLevelPredictor;
use App\ValueObjects\PredictionResult;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RefreshPredictionJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Device $device
    ){}

    /**
     * Execute the job.
     */
    public function handle(WaterLevelPredictor $predictor): void
    {
        // Semua horizon sekaligus: histori dimuat & fitur di-precompute SEKALI
        // (bukan 8× seperti dulu), jadi jauh lebih cepat untuk histori besar.
        $results = $predictor->predictAll($this->device);

        if ($results === []) {
            return; // histori belum cukup untuk horizon manapun
        }

        // Satu waktu-run bersama untuk seluruh titik kurva, supaya kurva
        // terbaru bisa diambil utuh via max(created_at) tanpa jitter antar-baris.
        $runAt = now();

        Prediction::query()->insert(array_map(fn (PredictionResult $result) => [
            'id' => (string) Str::orderedUuid(),
            'device_id' => $this->device->id,
            'horizon_minutes' => $result->horizonMinutes,
            'predicted_value' => $result->predictedValue,
            'predicted_at' => $result->predictedAt,
            'created_at' => $runAt,
            'updated_at' => $runAt,
        ], $results));

        event(new PredictionCompleted($this->device, $results));
    }
}
