<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Device;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = config('services.telegram.chat_id') ?? env('TELEGRAM_CHAT_ID', '');
    }

    public function sendAlertNotification(Device $device, string $riskLevel, string $message): NotificationLog
    {
        $telegramMessage = $this->formatAlertMessage($device, $riskLevel, $message);

        $notificationLog = NotificationLog::create([
            'device_id' => $device->id,
            'type' => 'telegram',
            'message' => $telegramMessage,
            'recipient' => $this->chatId,
            'status' => 'pending',
        ]);

        try {
            $response = $this->sendMessage($telegramMessage);

            if ($response['ok'] ?? false) {
                $notificationLog->update([
                    'status' => 'sent',
                    'external_id' => $response['result']['message_id'] ?? null,
                    'sent_at' => now(),
                ]);

                Log::channel('telegram')->info('Alert notification sent', [
                    'device_id' => $device->id,
                    'message_id' => $response['result']['message_id'] ?? null,
                ]);
            } else {
                $this->handleError($notificationLog, 'Telegram API error: ' . json_encode($response));
            }
        } catch (\Exception $e) {
            $this->handleError($notificationLog, $e->getMessage());
        }

        return $notificationLog->refresh();
    }

    public function sendMessage(string $text): array
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            return ['ok' => false, 'error' => 'Telegram credentials not configured'];
        }

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $this->chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ])
                ->json();

            return $response;
        } catch (\Exception $e) {
            Log::error('Telegram API error: ' . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function formatAlertMessage(Device $device, string $riskLevel, string $message): string
    {
        $riskEmoji = match ($riskLevel) {
            'aman' => '✅',
            'waspada' => '⚠️',
            'siaga' => '🔴',
            'bahaya' => '🚨',
            default => '❓',
        };

        $time = now()->timezone('Asia/Jakarta')->format('d M Y H:i');

        return "<b>{$riskEmoji} ALERT BANJIR ROB</b>\n\n" .
               "<b>Perangkat:</b> {$device->device_code} - {$device->name}\n" .
               "<b>Lokasi:</b> {$device->location}\n" .
               "<b>Level Risiko:</b> <b>{$riskLevel}</b>\n" .
               "<b>Pesan:</b> {$message}\n" .
               "<b>Waktu:</b> {$time}\n\n" .
               "🔗 <a href=\"" . route('dashboard', ['device' => $device->device_code]) . "\">Lihat Detail</a>";
    }

    private function handleError(NotificationLog $notificationLog, string $errorMessage): void
    {
        $notificationLog->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);

        Log::channel('telegram')->error('Failed to send notification', [
            'notification_log_id' => $notificationLog->id,
            'error' => $errorMessage,
        ]);
    }
}
