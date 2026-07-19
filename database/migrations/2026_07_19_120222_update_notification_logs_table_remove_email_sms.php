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
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropColumn(['type', 'recipient']);
            $table->string('telegram_chat_id')->nullable()->after('device_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->string('type')->default('telegram')->after('device_id');
            $table->string('recipient')->nullable()->after('message');
            $table->dropColumn('telegram_chat_id');
        });
    }
};
