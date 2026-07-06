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
        Schema::table('devices', function (Blueprint $table) {
            $table->text('api_url')->nullable()->after('longitude');

            $table->text('api_key')->nullable()->after('api_url');
            $table->boolean('api_enabled')->default(true)->after('api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['api_url', 'api_key', 'api_enabled']);
        });
    }
};
