<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramMessage;

/**
 * Call Me Back Notification
 *
 * "Sizi Arayalım" formu geldiğinde admin'e mail ve Telegram bildirimi gönderir
 */
class CallMeBackNotification extends Notification
{
    use Queueable;

    public array $customerData;
    public string $referrer;
    public string $landingPage;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $customerData, string $referrer = '', string $landingPage = '')
    {
        $this->customerData = $customerData;
        $this->referrer = $referrer;
        $this->landingPage = $landingPage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        // Mail her zaman gönder
        $channels[] = 'mail';

        // Telegram bot token varsa Telegram'a da gönder
        if (config('services.telegram-bot-api.token')) {
            $channels[] = 'telegram';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('📞 Sizi Arayalım Talebi - ' . $this->customerData['name'])
            ->greeting('Yeni Geri Arama Talebi!')
            ->line('**Müşteri Bilgileri:**')
            ->line('• Ad Soyad: ' . $this->customerData['name'])
            ->line('• Telefon: ' . $this->customerData['phone']);

        if (!empty($this->customerData['email'])) {
            $message->line('• E-posta: ' . $this->customerData['email']);
        }

        if (!empty($this->referrer)) {
            $message->line('')
                ->line('**Nereden Geldi:**')
                ->line($this->referrer);
        }

        if (!empty($this->landingPage)) {
            $message->action('Sayfayı Görüntüle', $this->landingPage);
        }

        return $message->line('Bu bildirimi "Sizi Arayalım" formu üzerinden aldınız.');
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        $message = "📞 *SİZİ ARAYALIM TALEBİ*\n\n";
        $message .= "👤 *Müşteri Bilgileri:*\n";
        $message .= "• Ad Soyad: " . $this->customerData['name'] . "\n";
        $message .= "• Telefon: " . $this->customerData['phone'] . "\n";

        if (!empty($this->customerData['email'])) {
            $message .= "• E-posta: " . $this->customerData['email'] . "\n";
        }

        if (!empty($this->referrer)) {
            $message .= "\n🔗 *Nereden geldi:*\n" . $this->referrer . "\n";
        }

        if (!empty($this->landingPage)) {
            $message .= "\n📄 [Sayfayı Görüntüle](" . $this->landingPage . ")";
        }

        return TelegramMessage::create()
            ->content($message)
            ->options([
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'customer_name' => $this->customerData['name'],
            'customer_phone' => $this->customerData['phone'],
            'customer_email' => $this->customerData['email'] ?? null,
            'referrer' => $this->referrer,
            'landing_page' => $this->landingPage,
        ];
    }
}
