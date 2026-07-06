<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Device;
use App\ValueObjects\PredictionResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event internal: menandai kurva prediksi baru selesai dihitung & tersimpan.
 * Bukan event broadcast — dashboard membaca prediksi dari DB saat load/ganti device.
 * Disediakan sebagai titik-kait bila nanti perlu efek samping tambahan.
 */
class PredictionCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * @param  list<PredictionResult>  $predictions  Kurva prediksi terurut per horizon.
     */
    public function __construct(
        public readonly Device $device,
        public readonly array $predictions,
    ){}
}
