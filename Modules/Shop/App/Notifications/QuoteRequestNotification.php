<?php

namespace Modules\Shop\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramMessage;
use Modules\Shop\app\Models\ShopProduct;

/**
 * Shop Quote Request Notification
 *
 * Ürün teklif talebi geldiğinde admin'e mail ve Telegram bildirimi gönderir
 */
class QuoteRequestNotification extends Notification
{
    use Queueable;

    public array $quoteData;
    public ShopProduct $product;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $quoteData, ShopProduct $product)
    {
        $this->quoteData = $quoteData;
        $this->product = $product;
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
        // Ürünün frontend URL'ini oluştur
        $currentLocale = app()->getLocale();
        $productUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl(
            $this->product,
            $currentLocale
        );

        return (new MailMessage)
            ->subject('🔔 Yeni Teklif Talebi: ' . $this->quoteData['product_title'])
            ->greeting('Yeni Teklif Talebi!')
            ->line('**Ürün:** ' . $this->quoteData['product_title'])
            ->line('**Müşteri Bilgileri:**')
            ->line('• Ad Soyad: ' . $this->quoteData['name'])
            ->line('• E-posta: ' . $this->quoteData['email'])
            ->line('• Telefon: ' . $this->quoteData['phone'])
            ->when($this->quoteData['message'], function ($message) {
                return $message->line('**Mesaj:**')
                    ->line($this->quoteData['message']);
            })
            ->action('Ürünü Görüntüle', $productUrl)
            ->line('Bu bildirimi shop modülü üzerinden aldınız.');
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        // Ürünün frontend URL'ini oluştur
        $currentLocale = app()->getLocale();
        $productUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl(
            $this->product,
            $currentLocale
        );

        $message = "🔔 *YENİ TEKLİF TALEBİ*\n\n";
        $message .= "📦 *Ürün:* " . $this->quoteData['product_title'] . "\n\n";
        $message .= "👤 *Müşteri Bilgileri:*\n";
        $message .= "• Ad Soyad: " . $this->quoteData['name'] . "\n";
        $message .= "• E-posta: " . $this->quoteData['email'] . "\n";
        $message .= "• Telefon: " . $this->quoteData['phone'] . "\n";

        if (!empty($this->quoteData['message'])) {
            $message .= "\n💬 *Mesaj:*\n" . $this->quoteData['message'] . "\n";
        }

        $message .= "\n🔗 [Ürünü Görüntüle](" . $productUrl . ")";

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
            'product_id' => $this->product->product_id,
            'product_title' => $this->quoteData['product_title'],
            'customer_name' => $this->quoteData['name'],
            'customer_email' => $this->quoteData['email'],
            'customer_phone' => $this->quoteData['phone'],
            'message' => $this->quoteData['message'] ?? null,
        ];
    }
}
