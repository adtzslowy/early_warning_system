<?php

declare(strict_types=1);
namespace App\Models;

use App\Enums\SensorType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sensor extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'device_id',
        'type',
        'value',
        'unit',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => SensorType::class,
            'value' => 'decimal:3',
            'recorded_at' => 'datetime',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
