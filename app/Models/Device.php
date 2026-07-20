<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeviceStatus;
use App\Enums\SensorType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        "device_code",
        "name",
        "location",
        "latitude",
        "longitude",
        "coastline_bearing",
        "api_url",
        "api_key",
        "api_enabled",
        "status",
        "last_seen_at",
    ];

    public function casts(): array
    {
        return [
            "latitude" => "decimal:7",
            "longitude" => "decimal:7",
            "coastline_bearing" => "decimal:2",
            "status" => DeviceStatus::class,
            "last_seen_at" => "datetime",
            "api_key" => "encrypted",
            "api_enabled" => 'boolean',
        ];
    }

    public function sensors(): HasMany
    {
        return $this->hasMany(Sensor::class);
    }

    /**
     * Pembacaan terbaru per jenis sensor untuk device ini.
     * Berguna untuk dashboard tanpa perlu N+1 query manual.
     * */
    public function latestSensors(): HasMany
    {
        return $this->hasMany(Sensor::class)->latest("recorded_at");
    }

    public function latestWaterLevel(): HasOne
    {
        return $this->latestSensorOfType(SensorType::WaterLevel);
    }

    public function latestTemperature(): HasOne
    {
        return $this->latestSensorOfType(SensorType::Temperature);
    }

    public function latestHumidity(): HasOne
    {
        return $this->latestSensorOfType(SensorType::Humidity);
    }

    public function latestAirPressure(): HasOne
    {
        return $this->latestSensorOfType(SensorType::AirPressure);
    }

    public function latestWindSpeed(): HasOne
    {
        return $this->latestSensorOfType(SensorType::WindSpeed);
    }

    /**
     * Pembacaan sensor terbaru untuk satu jenis tertentu.
     *
     * PENTING: filter `type` HARUS masuk ke closure ofMany agar ikut ke subquery
     * agregat. Kalau hanya di ->where() luar (seperti latestOfMany biasa), MAX
     * recorded_at + tiebreaker MAX(id) dihitung LINTAS semua jenis sensor yang
     * ber-recorded_at sama, lalu filter type di luar membuang barisnya → null.
     */
    private function latestSensorOfType(SensorType $type): HasOne
    {
        return $this->hasOne(Sensor::class)->ofMany(
            ["recorded_at" => "max", "id" => "max"],
            fn(Builder $query) => $query->where("type", $type),
        );
    }

    // public function latestWindDirection(): HasOne
    // {
    //     return $this->hasOne(Sensor::class)
    //         ->where('type', SensorType::WindDirection)
    //         ->latestOfMany('recorded_at');
    // }

    public function riskEvaluations(): HasMany
    {
        return $this->hasMany(RiskEvaluation::class);
    }

    // public function latestRiskEvaluation(): HasOne
    // {
    //     return $this->hasOne(RiskEvaluation::class)->latestOfMany('evaluated_at');
    // }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    /**
     * Kurva prediksi terbaru: seluruh titik horizon dari satu run terakhir,
     * terurut dari horizon terpendek ke terpanjang. Dipakai untuk grafik
     * prediksi dinamis (mirip forecast cuaca).
     *
     * @return \Illuminate\Support\Collection<int, Prediction>
     */
    public function latestPredictionCurve(): \Illuminate\Support\Collection
    {
        $latestRun = $this->predictions()->max("created_at");

        if ($latestRun === null) {
            return collect();
        }

        return $this->predictions()
            ->where("created_at", $latestRun)
            ->orderBy("horizon_minutes")
            ->get();
    }

    public function latestPrediction30(): HasOne
    {
        return $this->latestPredictionForHorizon(30);
    }

    public function latestPrediction60(): HasOne
    {
        return $this->latestPredictionForHorizon(60);
    }

    /** Prediksi terbaru untuk satu horizon (sama masalahnya dgn latestSensorOfType). */
    private function latestPredictionForHorizon(int $minutes): HasOne
    {
        return $this->hasOne(Prediction::class)->ofMany(
            ["predicted_at" => "max", "id" => "max"],
            fn(Builder $query) => $query->where("horizon_minutes", $minutes),
        );
    }

    public function latestWindDirection(): HasOne
    {
        return $this->latestSensorOfType(SensorType::WindDirection);
    }

    public function riskEvaluation(): HasMany
    {
        return $this->hasMany(RiskEvaluation::class);
    }

    public function latestRiskEvaluation(): HasOne
    {
        return $this->hasOne(RiskEvaluation::class)->latestOfMany(
            "evaluated_at",
        );
    }

    public function prediction(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function prediction30(): HasOne
    {
        return $this->hasOne(Prediction::class)
            ->where("horizon_minutes", 30)
            ->latestOfMany("predicted_at");
    }

    public function prediction60(): HasOne
    {
        return $this->hasOne(Prediction::class)
            ->where("horizon_minutes", 60)
            ->latestOfMany("predicted_at");
    }

    public function operators(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->operators();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole("admin")) {
            return $query;
        }

        return $query->whereHas("operators", fn($q) => $q->whereKey($user->id));
    }

    public function isOnline(): bool
    {
        return $this->status === DeviceStatus::Online;
    }
}
