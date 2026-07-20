<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegramBot extends Command
{
    protected $signature = 'telegram:test {chat_id?}';
    protected $description = 'Test Telegram bot connection with a message';

    public function handle(): int
    {
        $chatId = $this->argument('chat_id') ?? config('services.telegram.chat_id');

        if (!$chatId) {
            $this->error('❌ Chat ID not provided. Usage: php artisan telegram:test <chat_id>');
            $this->info('Or set TELEGRAM_CHAT_ID in .env');
            return self::FAILURE;
        }

        $this->info("🤖 Testing Telegram bot...");
        $this->info("Chat ID: {$chatId}");

        $service = new TelegramService();

        $testMessage = "<b>✅ EWS Banjir Rob Bot Test</b>\n\n" .
                       "Bot connection successful!\n" .
                       "Waktu: " . now()->format('d M Y H:i:s') . "\n\n" .
                       "Bot siap mengirim notifikasi alert banjir rob.";

        $response = $service->sendMessage($testMessage, $chatId);

        if ($response['ok'] ?? false) {
            $this->info("✅ Message sent successfully!");
            $this->line("Message ID: " . $response['result']['message_id'] ?? 'N/A');
            return self::SUCCESS;
        }

        $this->error("❌ Failed to send message");
        $this->error("Error: " . json_encode($response, JSON_PRETTY_PRINT));
        return self::FAILURE;
    }
}
