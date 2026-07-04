<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('risk_evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('device_id')->constrained('devices')->cascadeOnDelete();
            $table->string('risk_level');
            $table->decimal('risk_score', 5, 2);

            $table->decimal('water_level', 10, 3)->nullable();
            $table->decimal('onshore_wind', 10, 3)->nullable();
            $table->decimal('rise_rate', 10,3)->nullable();

            $table->timestamp('evaluated_at');
            $table->timestamps();

            $table->index(['device_id', 'evaluated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_evaluations');
    }
};
