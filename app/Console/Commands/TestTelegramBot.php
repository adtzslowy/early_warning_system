<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestTelegramBot extends Command
{
    protected $signature = 'telegram:test {chat_id?}';
    protected $description = 'Test Telegram bot connection with a message';

    public function handle(TelegramService $service): int
    {
        $chatId = $this->argument('chat_id') ?? config('services.telegram.chat_id');

        if (!$chatId) {
            $this->error('❌ Chat ID not provided. Usage: php artisan telegram:test <chat_id>');
            $this->info('Or set TELEGRAM_CHAT_ID in .env');
            return self::FAILURE;
        }

        $this->info("🤖 Testing Telegram bot...");
        $this->info("Chat ID: {$chatId}");

        // Debug: Check credentials are loaded
        $token = config('services.telegram.bot_token');
        if (!$token) {
            $this->error("❌ Bot token not configured in .env");
            return self::FAILURE;
        }
        $this->line("Token loaded: " . substr($token, 0, 20) . "...");

        $testMessage = "<b>✅ EWS Banjir Rob Bot Test</b>\n\n" .
                       "Bot connection successful!\n" .
                       "Waktu: " . now()->timezone('Asia/Jakarta')->format('d M Y H:i:s') . " WIB\n\n" .
                       "Bot siap mengirim notifikasi alert banjir rob.";

        $response = $service->sendMessage($testMessage, $chatId);

        if ($response['ok'] ?? false) {
            $this->info("✅ Message sent successfully!");
            $this->info("Message ID: " . ($response['result']['message_id'] ?? 'N/A'));
            $this->info("✅ Bot is working correctly!");
            return self::SUCCESS;
        }

        $this->error("❌ Failed to send message");
        $this->error("Error: " . json_encode($response, JSON_PRETTY_PRINT));
        return self::FAILURE;
    }
}
