<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasUuids;

    protected $fillable = [
        'device_id',
        'risk_level',
        'message',
        'triggered_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }
}
