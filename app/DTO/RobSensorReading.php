<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\SensorType;

final readonly class RobSensorReading
{
    public function __construct(
        public SensorType $type,
        public ?float $value,
    ) {}
}
