<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AIConversation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Telegram Notification Service
 *
 * AI konuşmalarda telefon numarası toplandığında Telegram'a bildirim gönderir
 */
class TelegramNotificationService
{
    /**
     * Telefon numarası toplandığında Telegram'a bildirim gönder
     *
     * @param AIConversation $conversation
     * @param array $phoneNumbers
     * @return void
     */
    public function sendPhoneNumberAlert(AIConversation $conversation, array $phoneNumbers): void
    {
        try {
            // Config kontrolü
            $botToken = config('services.telegram-bot-api.token');
            $chatId = config('services.telegram-bot-api.chat_id');

            if (empty($botToken) || empty($chatId)) {
                Log::warning('⚠️ Telegram config eksik, bildirim gönderilemedi');
                return;
            }

            // Initialize services
            $phoneService = new PhoneNumberDetectionService();
            $summaryService = new ConversationSummaryService();

            // Format phone numbers
            $formattedPhones = array_map(
                fn($p) => $phoneService->formatPhoneNumber($p),
                $phoneNumbers
            );

            // Generate summary
            $fullSummary = $summaryService->generateSummary($conversation);
            $adminLink = $summaryService->generateAdminLink($conversation);

            // Get first user message
            $firstUserMessage = $conversation->messages()
                ->where('role', 'user')
                ->orderBy('created_at', 'asc')
                ->first();

            // Build Telegram message (HTML format - daha okunabilir)
            $message = "📞 <b>YENİ TELEFON NUMARASI TOPLANDI!</b>\n\n";
            $message .= "<b>Telefon:</b> " . implode(', ', $formattedPhones) . "\n";
            $message .= "<b>Konuşma ID:</b> {$conversation->id}\n";
            $message .= "<b>Mesaj Sayısı:</b> {$conversation->message_count}\n";
            $message .= "<b>Tenant:</b> " . ($conversation->tenant_id ?? 'N/A') . "\n\n";

            if ($firstUserMessage) {
                $preview = mb_substr($firstUserMessage->content, 0, 100);
                $message .= "<b>İlk Mesaj:</b> {$preview}...\n\n";
            }

            $message .= "<b>Admin Panel:</b> {$adminLink}\n\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= $this->formatSummaryForTelegram($fullSummary);

            // Send via Telegram Bot API
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML', // Markdown yerine HTML
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('✅ Telegram notification sent', [
                    'conversation_id' => $conversation->id,
                    'phones' => $formattedPhones,
                    'chat_id' => $chatId,
                ]);
            } else {
                Log::error('❌ Telegram API failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('❌ Telegram notification failed', [
                'conversation_id' => $conversation->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Format summary for Telegram (HTML mode)
     *
     * @param string $text
     * @return string
     */
    private function formatSummaryForTelegram(string $text): string
    {
        // HTML özel karakterlerini escape et
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // Bazı formatlamaları HTML'e çevir
        $text = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $text); // **bold** → <b>bold</b>
        $text = preg_replace('/\*(.*?)\*/', '<i>$1</i>', $text);     // *italic* → <i>italic</i>

        // Uzun metni kısalt (Telegram 4096 karakter limiti)
        if (strlen($text) > 3500) {
            $text = mb_substr($text, 0, 3500) . "...\n\n(Detaylar admin panelden görülebilir)";
        }

        return $text;
    }

    /**
     * Test Telegram connection
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $botToken = config('services.telegram-bot-api.token');
            $chatId = config('services.telegram-bot-api.chat_id');

            if (empty($botToken) || empty($chatId)) {
                return [
                    'success' => false,
                    'error' => 'Telegram config eksik (.env TELEGRAM_BOT_TOKEN ve TELEGRAM_CHAT_ID)',
                ];
            }

            // Test message
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => '🔔 <b>Telegram Bildirim Testi</b>\n\nBağlantı başarılı! AI shop assistant telefon bildirimleri aktif.',
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Telegram bağlantısı başarılı!',
                    'response' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Telegram API hatası',
                    'status' => $response->status(),
                    'response' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
