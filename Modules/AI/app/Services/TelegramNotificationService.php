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
            // 🔧 TENANT-AWARE: Settings Management'dan al (.env değil!)
            $botToken = setting('telegram_bot_token');
            $chatId = setting('telegram_chat_id');
            $enabled = setting('telegram_enabled');

            // Enabled kontrolü
            if (!$enabled || $enabled === '0' || $enabled === 0) {
                Log::info('ℹ️ Telegram bildirimleri devre dışı (tenant: ' . tenant('id') . ')');
                return;
            }

            if (empty($botToken) || empty($chatId)) {
                Log::warning('⚠️ Telegram ayarları eksik (tenant: ' . tenant('id') . ')', [
                    'has_token' => !empty($botToken),
                    'has_chat_id' => !empty($chatId),
                ]);
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

            $adminLink = $summaryService->generateAdminLink($conversation);
            $tenantDomain = $conversation->tenant?->domains()->first()?->domain ?? 'N/A';

            // Get last 10 messages for conversation preview
            $messages = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->reverse();

            // Build Telegram message (HTML format - temiz ve okunabilir)
            $message = "📞 <b>YENİ MÜŞTERİ İLETİŞİMİ</b>\n\n";

            // Temel bilgiler
            $message .= "<b>📱 Telefon:</b> " . implode(', ', $formattedPhones) . "\n";
            $message .= "<b>📅 Tarih:</b> " . $conversation->created_at->format('d.m.Y H:i') . "\n";
            $message .= "<b>🌐 Site:</b> {$tenantDomain}\n\n";

            // Konuşma içeriği
            $message .= "💬 <b>KONUŞMA</b> ({$conversation->message_count} mesaj)\n";
            $message .= "─────────────────────────\n";

            foreach ($messages as $msg) {
                $role = $msg->role === 'user' ? '👤 Müşteri' : '🤖 AI';
                $content = mb_strlen($msg->content) > 200
                    ? mb_substr($msg->content, 0, 200) . '...'
                    : $msg->content;

                // HTML escape
                $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

                $message .= "\n<b>{$role}:</b>\n{$content}\n";
            }

            $message .= "\n─────────────────────────\n";
            $message .= "🔗 <a href=\"{$adminLink}\">Detaylı İnceleme</a>";

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
     * Test Telegram connection
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            // 🔧 TENANT-AWARE: Settings Management'dan al
            $botToken = setting('telegram_bot_token');
            $chatId = setting('telegram_chat_id');
            $enabled = setting('telegram_enabled');

            if (empty($botToken) || empty($chatId)) {
                return [
                    'success' => false,
                    'error' => 'Telegram ayarları eksik (Admin Panel → Ayarlar → Bildirim Ayarları)',
                    'tenant_id' => tenant('id'),
                    'settings' => [
                        'telegram_enabled' => $enabled ?? '(yok)',
                        'telegram_bot_token' => $botToken ? '(set)' : '(yok)',
                        'telegram_chat_id' => $chatId ?? '(yok)',
                    ],
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
