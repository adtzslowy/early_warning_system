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

    public function sendAlertNotification(Device $device, string $riskLevel, string $message): array
    {
        $users = $device->users;
        $notificationLogs = [];

        // Get latest predictions untuk include di notif
        $predictions = $device->predictions()
            ->whereIn('horizon_minutes', [30, 60, 120, 240])
            ->where('created_at', '>=', now()->subHours(1))
            ->orderBy('horizon_minutes')
            ->get();

        $predictions = $predictions->isNotEmpty() ? $predictions->keyBy('horizon_minutes') : collect();

        foreach ($users as $user) {
            if (empty($user->telegram_chat_id)) {
                continue;
            }

            $telegramMessage = $this->formatAlertMessage($device, $riskLevel, $message, $predictions);

            $notificationLog = NotificationLog::create([
                'device_id' => $device->id,
                'user_id' => $user->id,
                'type' => 'telegram',
                'message' => $telegramMessage,
                'recipient' => $user->telegram_chat_id,
                'status' => 'pending',
            ]);

            try {
                $response = $this->sendMessage($telegramMessage, $user->telegram_chat_id);

                if ($response['ok'] ?? false) {
                    $notificationLog->update([
                        'status' => 'sent',
                        'external_id' => $response['result']['message_id'] ?? null,
                        'sent_at' => now(),
                    ]);

                    Log::channel('telegram')->info('Alert notification sent', [
                        'device_id' => $device->id,
                        'user_id' => $user->id,
                        'message_id' => $response['result']['message_id'] ?? null,
                    ]);
                } else {
                    $this->handleError($notificationLog, 'Telegram API error: ' . json_encode($response));
                }
            } catch (\Exception $e) {
                $this->handleError($notificationLog, $e->getMessage());
            }

            $notificationLogs[] = $notificationLog->refresh();
        }

        return $notificationLogs;
    }

    public function sendMessage(string $text, ?string $chatId = null): array
    {
        $chatId = $chatId ?? $this->chatId;

        if (empty($this->botToken) || empty($chatId)) {
            return ['ok' => false, 'error' => 'Telegram credentials not configured'];
        }

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $chatId,
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

    private function formatAlertMessage(Device $device, string $riskLevel, string $message, $predictions = null): string
    {
        $riskEmoji = match ($riskLevel) {
            'aman' => '✅',
            'waspada' => '⚠️',
            'siaga' => '🔴',
            'bahaya' => '🚨',
            default => '❓',
        };

        $time = now()->timezone('Asia/Jakarta')->format('d M Y H:i');

        $currentLevel = $device->latestWaterLevel?->value ?? 'N/A';
        if ($currentLevel !== 'N/A') {
            $currentLevel = round((float) $currentLevel, 2) . 'm';
        }

        $alertMsg = "<b>{$riskEmoji} ALERT BANJIR ROB</b>\n\n" .
                   "<b>Perangkat:</b> {$device->device_code} - {$device->name}\n" .
                   "<b>Lokasi:</b> {$device->location}\n" .
                   "<b>Level Risiko Saat Ini:</b> <b>{$riskLevel}</b>\n" .
                   "<b>Ketinggian Air Saat Ini:</b> {$currentLevel}\n" .
                   "<b>Pesan:</b> {$message}\n" .
                   "<b>Waktu:</b> {$time}\n\n";

        // Add predictions jika ada
        if ($predictions && $predictions->isNotEmpty()) {
            $alertMsg .= "<b>📈 Prediksi 4 Jam ke Depan:</b>\n";

            foreach ([30, 60, 120, 240] as $horizon) {
                $pred = $predictions->get($horizon);
                if ($pred) {
                    $predValue = round((float) $pred->predicted_value, 2);
                    $predTime = $pred->predicted_at->timezone('Asia/Jakarta')->format('H:i');
                    $alertMsg .= "  • {$horizon}min ({$predTime}): <b>{$predValue}m</b>\n";
                }
            }

            $alertMsg .= "\n";
        }

        $alertMsg .= "🔗 <a href=\"" . route('dashboard', ['device' => $device->device_code]) . "\">Lihat Detail & Prediksi</a>";

        return $alertMsg;
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
