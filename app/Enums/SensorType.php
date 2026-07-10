<?php

namespace App\Enums;

enum SensorType: string
{
    case WaterLevel = 'water_level';
    case AirPressure = 'air_pressure';
    case WindSpeed = 'wind_speed';
    case WindDirection = 'wind_direction';
    case Temperature = 'temperature';
    case Humidity = 'humidity';

    public function unit(): string
    {
        return match ($this) {
            self::WaterLevel => 'cm',
            self::AirPressure => 'hPa',
            self::WindSpeed => 'm/s',
            self::WindDirection => '°',
            self::Temperature => '°C',
            self::Humidity => '%'
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::WaterLevel => 'Water Level',
            self::AirPressure => 'Air Pressure',
            self::WindSpeed => 'Wind Speed',
            self::WindDirection => 'Wind Direction',
            self::Temperature => 'Temperature',
            self::Humidity => 'Humidity'
        };
    }
}
