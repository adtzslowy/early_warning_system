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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('device_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->string('type')->default('telegram'); // telegram, email, sms
            $table->text('message');
            $table->string('recipient')->nullable(); // phone number, email, etc
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending')->index();
            $table->string('external_id')->nullable(); // Telegram message ID or similar
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['created_at', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
