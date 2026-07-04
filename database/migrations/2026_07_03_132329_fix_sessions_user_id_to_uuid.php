<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * user pakai UUID, tapi sessions.user_id semula bigint → sesi auth gagal
     * tersimpan (login tidak persist). Ubah ke uuid agar cocok.
     */
    public function up(): void
    {
        // Bersihkan sesi lama (nilai bigint tak valid untuk kolom uuid).
        DB::table('sessions')->truncate();

        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('sessions')->truncate();

        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }
};
