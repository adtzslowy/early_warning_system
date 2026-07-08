<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bearing kompas (derajat) arah datangnya angin ONSHORE untuk titik ini —
     * yaitu arah garis pantai lokal menghadap. Dipakai OnshoreWindCalculator
     * untuk memproyeksikan angin ke komponen tegak lurus pantai per device,
     * bukan lagi satu konstanta 270° untuk seluruh kabupaten.
     *
     * Default 270° (Barat) = orientasi umum pantai Ketapang menghadap Selat
     * Karimata, sehingga device yang belum dipetakan berperilaku persis
     * seperti sebelum perubahan ini (backward-compatible).
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->decimal('coastline_bearing', 5, 2)
                ->nullable()
                ->default(270.00)
                ->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('coastline_bearing');
        });
    }
};
