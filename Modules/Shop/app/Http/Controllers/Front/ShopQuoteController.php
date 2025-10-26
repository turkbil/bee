<?php

namespace Modules\Shop\app\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Shop\app\Models\ShopProduct;
use Modules\Shop\app\Notifications\QuoteRequestNotification;

/**
 * Shop Quote Controller
 *
 * Landing page'lerden gelen teklif formlarını işler
 */
class ShopQuoteController extends Controller
{
    /**
     * Teklif formunu işle
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:shop_products,product_id',
            'product_title' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:2000',
        ]);

        try {
            // Product bilgilerini al
            $product = ShopProduct::findOrFail($validated['product_id']);

            // Admin'e bildirim gönder (Mail + Telegram)
            $this->sendAdminNotification($validated, $product);

            // Müşteriye onay email'i gönder
            $this->sendQuoteConfirmationToCustomer($validated, $product);

            // Log kaydet
            Log::info('Quote Request Received', [
                'product_id' => $validated['product_id'],
                'customer_email' => $validated['email'],
                'customer_name' => $validated['name'],
            ]);

            // Success mesajı ile redirect - Query parameter kullan (Tenant session bypass)
            $currentUrl = url()->previous();
            $separator = parse_url($currentUrl, PHP_URL_QUERY) ? '&' : '?';

            return redirect($currentUrl . $separator . 'quote_status=success');

        } catch (\Exception $e) {
            Log::error('Quote Submission Error', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            // Error mesajı ile redirect - Query parameter kullan (Tenant session bypass)
            $currentUrl = url()->previous();
            $separator = parse_url($currentUrl, PHP_URL_QUERY) ? '&' : '?';

            return redirect($currentUrl . $separator . 'quote_status=error');
        }
    }

    /**
     * Admin'e bildirim gönder (Mail + Telegram + WhatsApp)
     */
    private function sendAdminNotification(array $data, ShopProduct $product)
    {
        if (!config('shop.quote.send_admin_notification', true)) {
            return;
        }

        // Admin email fallback chain: config → settings → domain-based
        $adminEmail = config('shop.quote.admin_email')
            ?? get_setting('contact_email')
            ?? 'info@' . parse_url(url('/'), PHP_URL_HOST);

        // Notification gönder (Mail + Telegram)
        // Laravel'in route() metodu ile anonymous notifiable kullanımı
        Notification::route('mail', $adminEmail)
            ->route('telegram', config('services.telegram-bot-api.chat_id'))
            ->notify(new QuoteRequestNotification($data, $product));

        // WhatsApp bildirimi gönder
        try {
            $whatsappService = app(\App\Services\WhatsAppNotificationService::class);
            $whatsappService->sendCustomerLead(
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                ],
                $data['message'] ?? 'Teklif talebi',
                [
                    [
                        'title' => $product->title,
                        'url' => route('shop.show', $product->slug),
                    ]
                ],
                [
                    'site' => tenant('domain'),
                    'page_url' => url()->previous(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Müşteriye onay email'i gönder
     */
    private function sendQuoteConfirmationToCustomer(array $data, ShopProduct $product)
    {
        if (!config('shop.quote.send_customer_confirmation', true)) {
            return;
        }

        Mail::send('shop::emails.quote-customer', [
            'data' => $data,
            'product' => $product,
        ], function ($message) use ($data) {
            $message->to($data['email'], $data['name'])
                    ->subject('Teklif Talebiniz Alındı - İXTİF')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }
}
