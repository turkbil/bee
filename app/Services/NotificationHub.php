<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * 🔔 Notification Hub - Universal Notification Service
 *
 * Tenant bazlı bildirim yönetimi. Sadece aktif ve dolu olan kanalları kullanır.
 *
 * @package App\Services
 */
class NotificationHub
{
    private array $settings = [];
    private bool $telegramEnabled = false;
    private bool $whatsappEnabled = false;
    private bool $emailEnabled = false;

    public function __construct()
    {
        $this->loadTenantSettings();
    }

    /**
     * 📱 Tenant ayarlarını yükle
     */
    private function loadTenantSettings(): void
    {
        try {
            // Central'dan settings tanımlarını al
            $settingsDefinitions = DB::connection('mysql')->table('settings')
                ->join('settings_groups', 'settings.group_id', '=', 'settings_groups.id')
                ->where('settings_groups.slug', 'notifications')
                ->where('settings.is_active', true)
                ->select('settings.id', 'settings.key')
                ->get()
                ->keyBy('key');

            // Tenant'tan values'ları al
            $settingValues = DB::table('settings_values')
                ->whereIn('setting_id', $settingsDefinitions->pluck('id'))
                ->get()
                ->keyBy('setting_id');

            // Ayarları birleştir
            foreach ($settingsDefinitions as $key => $definition) {
                $value = $settingValues->get($definition->id)?->value ?? '';
                $this->settings[$key] = $value;
            }

            // Aktif kanalları belirle
            $this->telegramEnabled =
                !empty($this->settings['telegram_enabled']) &&
                $this->settings['telegram_enabled'] === '1' &&
                !empty($this->settings['telegram_bot_token']) &&
                !empty($this->settings['telegram_chat_id']);

            $this->whatsappEnabled =
                !empty($this->settings['whatsapp_enabled']) &&
                $this->settings['whatsapp_enabled'] === '1' &&
                !empty($this->settings['twilio_account_sid']) &&
                !empty($this->settings['twilio_auth_token']) &&
                !empty($this->settings['twilio_whatsapp_from']) &&
                !empty($this->settings['twilio_whatsapp_to']);

            $this->emailEnabled =
                !empty($this->settings['email_enabled']) &&
                $this->settings['email_enabled'] === '1' &&
                !empty($this->settings['email']);

        } catch (Exception $e) {
            Log::error('NotificationHub.loadTenantSettings failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 🚨 Müşteri talebi bildirimi gönder (TEK SATIR KULLANIM)
     *
     * @param array $customerData Müşteri bilgileri ['name', 'phone', 'email', 'company']
     * @param string $inquiry Talep/mesaj
     * @param array $suggestedProducts Önerilen ürünler
     * @param array $context Ekstra bilgiler
     * @return array Gönderim sonuçları
     */
    public function sendCustomerLead(
        array $customerData,
        string $inquiry,
        array $suggestedProducts = [],
        array $context = []
    ): array {
        $results = [
            'telegram' => false,
            'whatsapp' => false,
            'email' => false,
            'sent_count' => 0,
        ];

        // Telegram gönder (eğer aktif ve dolu ise)
        if ($this->telegramEnabled) {
            try {
                $telegramService = new TelegramNotificationService(
                    $this->settings['telegram_bot_token'],
                    $this->settings['telegram_chat_id']
                );
                $results['telegram'] = $telegramService->sendCustomerLead(
                    $customerData,
                    $inquiry,
                    $suggestedProducts,
                    $context
                );
                if ($results['telegram']) $results['sent_count']++;
            } catch (Exception $e) {
                Log::warning('Telegram notification failed', ['error' => $e->getMessage()]);
            }
        }

        // WhatsApp gönder (eğer aktif ve dolu ise)
        if ($this->whatsappEnabled) {
            try {
                $whatsappService = new WhatsAppNotificationService(
                    $this->settings['twilio_account_sid'],
                    $this->settings['twilio_auth_token'],
                    $this->settings['twilio_whatsapp_from'],
                    $this->settings['twilio_whatsapp_to']
                );
                $results['whatsapp'] = $whatsappService->sendCustomerLead(
                    $customerData,
                    $inquiry,
                    $suggestedProducts,
                    $context
                );
                if ($results['whatsapp']) $results['sent_count']++;
            } catch (Exception $e) {
                Log::warning('WhatsApp notification failed', ['error' => $e->getMessage()]);
            }
        }

        // Email gönder (eğer aktif ve dolu ise)
        if ($this->emailEnabled) {
            // Email servisi eklenebilir
            $results['email'] = false; // Placeholder
        }

        return $results;
    }

    /**
     * 💬 Basit bildirim gönder
     *
     * @param string $message
     * @return array
     */
    public function sendSimpleNotification(string $message): array
    {
        $results = [
            'telegram' => false,
            'whatsapp' => false,
            'sent_count' => 0,
        ];

        if ($this->telegramEnabled) {
            try {
                $telegramService = new TelegramNotificationService(
                    $this->settings['telegram_bot_token'],
                    $this->settings['telegram_chat_id']
                );
                $results['telegram'] = $telegramService->sendSimpleNotification($message);
                if ($results['telegram']) $results['sent_count']++;
            } catch (Exception $e) {
                Log::warning('Telegram notification failed', ['error' => $e->getMessage()]);
            }
        }

        if ($this->whatsappEnabled) {
            try {
                $whatsappService = new WhatsAppNotificationService(
                    $this->settings['twilio_account_sid'],
                    $this->settings['twilio_auth_token'],
                    $this->settings['twilio_whatsapp_from'],
                    $this->settings['twilio_whatsapp_to']
                );
                $results['whatsapp'] = $whatsappService->sendSimpleNotification($message);
                if ($results['whatsapp']) $results['sent_count']++;
            } catch (Exception $e) {
                Log::warning('WhatsApp notification failed', ['error' => $e->getMessage()]);
            }
        }

        return $results;
    }

    /**
     * 📊 Aktif kanalları kontrol et
     *
     * @return array
     */
    public function getActiveChannels(): array
    {
        return [
            'telegram' => $this->telegramEnabled,
            'whatsapp' => $this->whatsappEnabled,
            'email' => $this->emailEnabled,
        ];
    }
}
