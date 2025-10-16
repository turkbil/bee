<?php

declare(strict_types=1);

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * 📱 WhatsApp Notification Service (Twilio)
 *
 * Müşteri talebi ve bilgilerini WhatsApp'a gönderir
 *
 * @package App\Services
 */
class WhatsAppNotificationService
{
    private string $accountSid;
    private string $authToken;
    private string $fromNumber;
    private string $toNumber;

    public function __construct(
        ?string $accountSid = null,
        ?string $authToken = null,
        ?string $fromNumber = null,
        ?string $toNumber = null
    ) {
        $this->accountSid = $accountSid ?? config('services.twilio.account_sid') ?? env('TWILIO_ACCOUNT_SID', '');
        $this->authToken = $authToken ?? config('services.twilio.auth_token') ?? env('TWILIO_AUTH_TOKEN', '');
        $this->fromNumber = $fromNumber ?? config('services.twilio.whatsapp_from') ?? env('TWILIO_WHATSAPP_FROM', '');
        $this->toNumber = $toNumber ?? config('services.twilio.whatsapp_to') ?? env('TWILIO_WHATSAPP_TO', '');
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
            if (empty($this->accountSid) || empty($this->authToken) || empty($this->fromNumber) || empty($this->toNumber)) {
                Log::warning('Twilio WhatsApp credentials not configured', [
                    'has_account_sid' => !empty($this->accountSid),
                    'has_auth_token' => !empty($this->authToken),
                    'has_from_number' => !empty($this->fromNumber),
                    'has_to_number' => !empty($this->toNumber),
                ]);
                return false;
            }

            // Build message
            $message = $this->buildLeadMessage($customerData, $inquiry, $suggestedProducts, $context);

            // Send via Twilio
            $client = new Client($this->accountSid, $this->authToken);

            $result = $client->messages->create(
                $this->toNumber, // To
                [
                    'from' => $this->fromNumber,
                    'body' => $message,
                ]
            );

            if ($result->sid) {
                Log::info('✅ WhatsApp notification sent successfully', [
                    'sid' => $result->sid,
                    'customer_name' => $customerData['name'] ?? 'N/A',
                    'inquiry_preview' => mb_substr($inquiry, 0, 50),
                ]);
                return true;
            }

            Log::error('❌ Twilio WhatsApp API error', [
                'result' => $result,
            ]);

            return false;

        } catch (Exception $e) {
            Log::error('WhatsAppNotificationService.sendCustomerLead failed', [
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
        $lines[] = "🚨 *YENİ MÜŞTERİ TALEBİ*";
        $lines[] = "";

        // Customer info
        $lines[] = "👤 *Müşteri Bilgileri:*";

        if (!empty($customerData['name'])) {
            $lines[] = "• Ad Soyad: " . $customerData['name'];
        }

        if (!empty($customerData['phone'])) {
            $lines[] = "• Telefon: " . $customerData['phone'];
        }

        if (!empty($customerData['email'])) {
            $lines[] = "• E-posta: " . $customerData['email'];
        }

        if (!empty($customerData['company'])) {
            $lines[] = "• Şirket: " . $customerData['company'];
        }

        $lines[] = "";

        // Inquiry
        $lines[] = "🛒 *Talep:*";
        $lines[] = $inquiry;
        $lines[] = "";

        // Suggested products (if any)
        if (!empty($suggestedProducts)) {
            $lines[] = "📊 *AI Tarafından Önerilen Ürünler:*";

            $count = 0;
            foreach ($suggestedProducts as $product) {
                if ($count >= 5) break; // Max 5 ürün

                $title = $product['title'] ?? $product['name'] ?? 'Ürün';
                $url = $product['url'] ?? null;

                if ($url) {
                    $lines[] = "• {$title}: {$url}";
                } else {
                    $lines[] = "• {$title}";
                }

                $count++;
            }

            $lines[] = "";
        }

        // Context info
        if (!empty($context['site'])) {
            $lines[] = "🌐 Site: " . $context['site'];
        }

        if (!empty($context['page_url'])) {
            $lines[] = "📄 Sayfa: " . $context['page_url'];
        }

        if (!empty($context['device'])) {
            $lines[] = "📱 Cihaz: " . $context['device'];
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
            if (empty($this->accountSid) || empty($this->authToken) || empty($this->fromNumber) || empty($this->toNumber)) {
                return false;
            }

            $client = new Client($this->accountSid, $this->authToken);

            $result = $client->messages->create(
                $this->toNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $message,
                ]
            );

            return $result->sid !== null;

        } catch (Exception $e) {
            Log::error('WhatsAppNotificationService.sendSimpleNotification failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * ✅ Test WhatsApp connection
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            if (empty($this->accountSid) || empty($this->authToken) || empty($this->fromNumber) || empty($this->toNumber)) {
                return [
                    'success' => false,
                    'error' => 'Twilio WhatsApp credentials not configured',
                ];
            }

            $client = new Client($this->accountSid, $this->authToken);

            $result = $client->messages->create(
                $this->toNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => '✅ WhatsApp bildirim sistemi test edildi - ' . now()->format('Y-m-d H:i:s'),
                ]
            );

            if ($result->sid) {
                return [
                    'success' => true,
                    'message' => 'WhatsApp connection successful',
                    'sid' => $result->sid,
                ];
            }

            return [
                'success' => false,
                'error' => 'Twilio WhatsApp API error',
                'result' => $result,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
