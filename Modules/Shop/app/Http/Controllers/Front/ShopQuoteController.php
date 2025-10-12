<?php

namespace Modules\Shop\app\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Modules\Shop\app\Models\ShopProduct;

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

            // Email gönder (admin'e)
            $this->sendQuoteEmailToAdmin($validated, $product);

            // Email gönder (müşteriye)
            $this->sendQuoteConfirmationToCustomer($validated, $product);

            // Log kaydet
            Log::info('Quote Request Received', [
                'product_id' => $validated['product_id'],
                'customer_email' => $validated['email'],
                'customer_name' => $validated['name'],
            ]);

            // Success mesajı ile redirect
            return redirect()->back()->with('success', 'Talebiniz başarıyla gönderildi! En kısa sürede size dönüş yapacağız.');

        } catch (\Exception $e) {
            Log::error('Quote Submission Error', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return redirect()->back()
                ->with('error', 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin veya 0216 755 3 555 numarasını arayın.')
                ->withInput();
        }
    }

    /**
     * Admin'e email gönder
     */
    private function sendQuoteEmailToAdmin(array $data, ShopProduct $product)
    {
        if (!config('shop.quote.send_admin_notification', true)) {
            return;
        }

        $adminEmail = config('shop.quote.admin_email', 'info@ixtif.com');

        Mail::send('shop::emails.quote-admin', [
            'data' => $data,
            'product' => $product,
        ], function ($message) use ($adminEmail, $data) {
            $message->to($adminEmail)
                    ->subject('Yeni Teklif Talebi: ' . $data['product_title'])
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
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
