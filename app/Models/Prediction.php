<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasUuids;

    protected $fillable = [
        'device_id', 'horizon_minutes', 'predicted_value', 'predicted_at'
    ];

    protected function casts(): array
    {
        return [
            'predicted_value' => 'decimal:3',
            'predicted_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
