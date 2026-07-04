<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskEvaluation extends Model
{
    use HasUuids;

    protected $fillable = [
        'device_id', 'risk_level', 'risk_score', 'water_level',
        'onshore_wind', 'rise_rate', 'evaluated_at',
    ];

    protected function casts(): array
    {
        return [
            'risk_level' => RiskLevel::class,
            'risk_score' => 'decimal:2',
            'water_level' => 'decimal:3',
            'onshore_wind' => 'decimal:3',
            'rise_rate' => 'decimal:3',
            'evaluated_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
