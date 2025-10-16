<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * 📱 Telegram Notification Service
 *
 * Müşteri talebi ve bilgilerini Telegram'a gönderir
 *
 * @package App\Services
 */
class TelegramNotificationService
{
    private string $botToken;
    private string $chatId;

    public function __construct(?string $botToken = null, ?string $chatId = null)
    {
        $this->botToken = $botToken ?? config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = $chatId ?? config('services.telegram.chat_id') ?? env('TELEGRAM_CHAT_ID', '');
    }

    /**
     * 🚨 Müşteri talebi bildirimi gönder
     *
     * @param array $customerData Müşteri bilgileri (ad, telefon, email)
     * @param string $inquiry Kullanıcının sorusu/talebi
     * @param array $suggestedProducts AI'ın önerdiği ürünler
     * @param array $context Ek context bilgileri
     * @return bool
     */
    public function sendCustomerLead(
        array $customerData,
        string $inquiry,
        array $suggestedProducts = [],
        array $context = []
    ): bool {
        try {
            if (empty($this->botToken) || empty($this->chatId)) {
                Log::warning('Telegram credentials not configured', [
                    'has_token' => !empty($this->botToken),
                    'has_chat_id' => !empty($this->chatId),
                ]);
                return false;
            }

            // Build message
            $message = $this->buildLeadMessage($customerData, $inquiry, $suggestedProducts, $context);

            // Send to Telegram
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => false,
                ]);

            if ($response->successful()) {
                Log::info('✅ Telegram notification sent successfully', [
                    'customer_name' => $customerData['name'] ?? 'N/A',
                    'inquiry_preview' => mb_substr($inquiry, 0, 50),
                ]);
                return true;
            }

            Log::error('❌ Telegram API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;

        } catch (Exception $e) {
            Log::error('TelegramNotificationService.sendCustomerLead failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * 📝 Build formatted lead message
     */
    private function buildLeadMessage(
        array $customerData,
        string $inquiry,
        array $suggestedProducts,
        array $context
    ): string {
        $lines = [];

        // Header
        $lines[] = "🚨 <b>YENİ MÜŞTERİ TALEBİ</b>";
        $lines[] = "";

        // Customer info
        $lines[] = "👤 <b>Müşteri Bilgileri:</b>";

        if (!empty($customerData['name'])) {
            $lines[] = "• Ad Soyad: " . htmlspecialchars($customerData['name']);
        }

        if (!empty($customerData['phone'])) {
            $phone = htmlspecialchars($customerData['phone']);
            $lines[] = "• Telefon: <a href=\"tel:{$phone}\">{$phone}</a>";
        }

        if (!empty($customerData['email'])) {
            $email = htmlspecialchars($customerData['email']);
            $lines[] = "• E-posta: <a href=\"mailto:{$email}\">{$email}</a>";
        }

        if (!empty($customerData['company'])) {
            $lines[] = "• Şirket: " . htmlspecialchars($customerData['company']);
        }

        $lines[] = "";

        // Inquiry
        $lines[] = "🛒 <b>Talep:</b>";
        $lines[] = htmlspecialchars($inquiry);
        $lines[] = "";

        // Suggested products (if any)
        if (!empty($suggestedProducts)) {
            $lines[] = "📊 <b>AI Tarafından Önerilen Ürünler:</b>";

            $count = 0;
            foreach ($suggestedProducts as $product) {
                if ($count >= 5) break; // Max 5 ürün

                $title = $product['title'] ?? $product['name'] ?? 'Ürün';
                $url = $product['url'] ?? null;

                if ($url) {
                    $lines[] = "• <a href=\"{$url}\">" . htmlspecialchars($title) . "</a>";
                } else {
                    $lines[] = "• " . htmlspecialchars($title);
                }

                $count++;
            }

            $lines[] = "";
        }

        // Context info
        if (!empty($context['site'])) {
            $lines[] = "🌐 Site: " . htmlspecialchars($context['site']);
        }

        if (!empty($context['page_url'])) {
            $lines[] = "📄 Sayfa: <a href=\"{$context['page_url']}\">" . htmlspecialchars($context['page_url']) . "</a>";
        }

        if (!empty($context['device'])) {
            $lines[] = "📱 Cihaz: " . htmlspecialchars($context['device']);
        }

        $lines[] = "";
        $lines[] = "⏰ " . now()->timezone('Europe/Istanbul')->format('d.m.Y H:i');

        return implode("\n", $lines);
    }

    /**
     * 💬 Basit bildirim gönder
     *
     * @param string $message
     * @return bool
     */
    public function sendSimpleNotification(string $message): bool
    {
        try {
            if (empty($this->botToken) || empty($this->chatId)) {
                return false;
            }

            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

            return $response->successful();

        } catch (Exception $e) {
            Log::error('TelegramNotificationService.sendSimpleNotification failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * ✅ Test Telegram connection
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            if (empty($this->botToken) || empty($this->chatId)) {
                return [
                    'success' => false,
                    'error' => 'Telegram credentials not configured',
                ];
            }

            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                    'chat_id' => $this->chatId,
                    'text' => '✅ Telegram bildirim sistemi test edildi - ' . now()->format('Y-m-d H:i:s'),
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Telegram connection successful',
                ];
            }

            return [
                'success' => false,
                'error' => 'Telegram API error: ' . $response->status(),
                'body' => $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
