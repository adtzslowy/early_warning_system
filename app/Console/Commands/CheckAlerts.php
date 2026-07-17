<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\RiskEvaluation;
use Illuminate\Console\Command;

class CheckAlerts extends Command
{
    protected $signature = 'alerts:check';

    protected $description = 'Cek RiskEvaluation terbaru dan buat Alert jika risk_level >= Waspada';

    public function handle(): int
    {
        try {
            $this->checkAndCreateAlerts();
            $this->info('Alert check selesai');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Gagal check alerts: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    private function checkAndCreateAlerts(): void
    {
        // Ambil RiskEvaluation terbaru per device
        $latestEvaluations = RiskEvaluation::query()
            ->selectRaw('DISTINCT ON (device_id) device_id, risk_level, risk_score, evaluated_at')
            ->orderByDesc('device_id')
            ->orderByDesc('evaluated_at')
            ->get();

        foreach ($latestEvaluations as $evaluation) {
            // Hanya buat alert jika risk_level >= Waspada
            if (!$this->isAlertLevel($evaluation->risk_level)) {
                continue;
            }

            // Cek apakah sudah ada alert aktif untuk device ini
            $existingAlert = Alert::query()
                ->where('device_id', $evaluation->device_id)
                ->whereNull('acknowledged_at')
                ->latest('triggered_at')
                ->first();

            // Jika sudah ada alert dengan level yang sama, skip
            if ($existingAlert && $existingAlert->risk_level === $evaluation->risk_level) {
                continue;
            }

            // Create new alert
            Alert::create([
                'device_id' => $evaluation->device_id,
                'risk_level' => $evaluation->risk_level,
                'message' => "Risk level naik ke {$evaluation->risk_level} (score: {$evaluation->risk_score})",
                'triggered_at' => now(),
            ]);
        }
    }

    private function isAlertLevel(string $riskLevel): bool
    {
        return in_array($riskLevel, ['Waspada', 'Siaga', 'Bahaya'], true);
    }
}
