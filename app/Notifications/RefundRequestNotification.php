<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Telegram\TelegramMessage;

/**
 * Refund Request Notification
 *
 * Cayma hakkı talebi geldiğinde admin'e mail ve Telegram bildirimi gönderir
 */
class RefundRequestNotification extends Notification
{
    use Queueable;

    public array $refundData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $refundData)
    {
        $this->refundData = $refundData;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail'];

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
        return (new MailMessage)
            ->subject('🔄 Yeni Cayma Hakkı Talebi - Sipariş: ' . $this->refundData['order_number'])
            ->greeting('Cayma Hakkı Talebi Alındı!')
            ->line('**Sipariş Bilgileri:**')
            ->line('• Sipariş No: ' . $this->refundData['order_number'])
            ->line('• Sipariş Tarihi: ' . $this->refundData['order_date'])
            ->line('• Teslim Tarihi: ' . $this->refundData['delivery_date'])
            ->when($this->refundData['invoice_number'], function ($message) {
                return $message->line('• Fatura No: ' . $this->refundData['invoice_number']);
            })
            ->line('')
            ->line('**Müşteri Bilgileri:**')
            ->line('• Ad Soyad: ' . $this->refundData['full_name'])
            ->line('• T.C. Kimlik No: ' . $this->refundData['tc_number'])
            ->line('• E-posta: ' . $this->refundData['email'])
            ->line('• Telefon: ' . $this->refundData['phone'])
            ->line('• Adres: ' . $this->refundData['address'])
            ->line('')
            ->line('**İade Edilecek Ürünler:**')
            ->line($this->refundData['products'])
            ->when($this->refundData['refund_reason'], function ($message) {
                return $message->line('')
                    ->line('**Cayma Nedeni:**')
                    ->line($this->refundData['refund_reason']);
            })
            ->action('Cayma Hakkı Sayfası', url('/page/cayma-hakki'))
            ->line('Bu bildirimi Page modülü üzerinden aldınız.');
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        $message = "🔄 *YENİ CAYMA HAKKI TALEBİ*\n\n";
        $message .= "📦 *Sipariş Bilgileri:*\n";
        $message .= "• Sipariş No: " . $this->refundData['order_number'] . "\n";
        $message .= "• Sipariş Tarihi: " . $this->refundData['order_date'] . "\n";
        $message .= "• Teslim Tarihi: " . $this->refundData['delivery_date'] . "\n";

        if (!empty($this->refundData['invoice_number'])) {
            $message .= "• Fatura No: " . $this->refundData['invoice_number'] . "\n";
        }

        $message .= "\n👤 *Müşteri Bilgileri:*\n";
        $message .= "• Ad Soyad: " . $this->refundData['full_name'] . "\n";
        $message .= "• T.C. Kimlik: " . $this->refundData['tc_number'] . "\n";
        $message .= "• E-posta: " . $this->refundData['email'] . "\n";
        $message .= "• Telefon: " . $this->refundData['phone'] . "\n";
        $message .= "• Adres: " . $this->refundData['address'] . "\n";

        $message .= "\n📦 *İade Edilecek Ürünler:*\n";
        $message .= $this->refundData['products'] . "\n";

        if (!empty($this->refundData['refund_reason'])) {
            $message .= "\n💬 *Cayma Nedeni:*\n";
            $message .= $this->refundData['refund_reason'] . "\n";
        }

        $message .= "\n🔗 [Cayma Hakkı Sayfası](" . url('/page/cayma-hakki') . ")";

        return TelegramMessage::create()
            ->content($message)
            ->options([
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_number' => $this->refundData['order_number'],
            'customer_name' => $this->refundData['full_name'],
            'customer_email' => $this->refundData['email'],
            'customer_phone' => $this->refundData['phone'],
            'products' => $this->refundData['products'],
        ];
    }
}
