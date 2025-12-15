<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\App\Models\Order;
use Modules\Payment\App\Models\Payment;

class BankTransferPageController extends Controller
{
    /**
     * Get tenant theme layout path
     */
    protected function getLayoutPath(): string
    {
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        return $layoutPath;
    }

    public function show($orderNumber)
    {
        $layoutPath = $this->getLayoutPath();

        // Havale aktif mi kontrol et
        if (!setting('bank_transfer_enabled')) {
            return redirect()->route('cart.checkout')->with('error', 'Havale/EFT şu anda aktif değil.');
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return view('payment::front.payment-error', [
                'error' => 'Sipariş bulunamadı: ' . $orderNumber,
                'layoutPath' => $layoutPath,
            ]);
        }

        // Aktif ve IBAN'ı dolu olan bankaları topla
        $banks = [];
        for ($i = 1; $i <= 3; $i++) {
            $isActive = setting("payment_bank_{$i}_active");
            $iban = setting("payment_bank_{$i}_iban");

            // Sadece aktif ve IBAN'ı dolu olanlar
            if ($isActive && !empty($iban)) {
                $banks[] = [
                    'name' => setting("payment_bank_{$i}_name"),
                    'holder' => setting("payment_bank_{$i}_holder"),
                    'branch' => setting("payment_bank_{$i}_branch"),
                    'iban' => $iban,
                ];
            }
        }

        // Hiç aktif banka yoksa
        if (empty($banks)) {
            return view('payment::front.payment-error', [
                'error' => 'Havale için aktif banka hesabı bulunamadı.',
                'order' => $order,
                'layoutPath' => $layoutPath,
            ]);
        }

        // Havale açıklaması
        $description = setting('payment_bank_transfer_description');

        return view('payment::front.payment-bank-transfer', [
            'order' => $order,
            'orderNumber' => $orderNumber,
            'banks' => $banks,
            'description' => $description,
            'layoutPath' => $layoutPath,
        ]);
    }

    public function confirm(Request $request, $orderNumber)
    {
        // Havale aktif mi kontrol et
        if (!setting('bank_transfer_enabled')) {
            return redirect()->route('cart.checkout')->with('error', 'Havale/EFT şu anda aktif değil.');
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('cart.checkout')->with('error', 'Sipariş bulunamadı.');
        }

        // Sipariş durumunu güncelle (payment_method kolonu yok, metadata'ya kaydediyoruz)
        // Status: pending = Havale onayı bekleniyor
        $order->status = 'pending';
        $metadata = $order->metadata ?? [];
        $metadata['payment_method'] = 'bank_transfer';
        $metadata['bank_transfer_confirmed_at'] = now()->toDateTimeString();
        $metadata['transfer_note'] = $request->input('transfer_note');
        $order->metadata = $metadata;
        $order->save();

        // 🛒 Kullanıcının sepetini temizle (satın alınmış sayılmalı)
        if ($order->user_id) {
            try {
                // Cart tablosunda customer_id kullanılıyor (user_id değil!)
                $cart = \Modules\Cart\App\Models\Cart::where('customer_id', $order->user_id)
                    ->where('status', 'active')
                    ->first();

                if ($cart) {
                    $cartService = app(\Modules\Cart\App\Services\CartService::class);
                    $cartService->clearCart($cart);
                    \Log::info('🛒 Havale sonrası sepet temizlendi', [
                        'customer_id' => $order->user_id,
                        'cart_id' => $cart->cart_id,
                        'order_number' => $order->order_number
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('❌ Havale sepet temizleme hatası: ' . $e->getMessage());
            }
        }

        // Bildirim gönder (Telegram + Email)
        $this->sendNotifications($order, $request->input('transfer_note'));

        return redirect()->route('payment.bank-transfer.success', $orderNumber);
    }

    public function success($orderNumber)
    {
        // Havale aktif mi kontrol et
        if (!setting('bank_transfer_enabled')) {
            return redirect()->route('cart.checkout')->with('error', 'Havale/EFT şu anda aktif değil.');
        }

        $layoutPath = $this->getLayoutPath();

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('cart.checkout');
        }

        return view('payment::front.payment-bank-transfer-success', [
            'order' => $order,
            'orderNumber' => $orderNumber,
            'layoutPath' => $layoutPath,
        ]);
    }

    protected function sendNotifications($order, $transferNote = null)
    {
        // Email bildirimi
        $notifyEmail = setting('payment_bank_transfer_notify_email');
        if ($notifyEmail) {
            try {
                \Mail::raw(
                    "Yeni Havale/EFT Bildirimi\n\n" .
                    "Sipariş No: {$order->order_number}\n" .
                    "Tutar: " . number_format($order->total_amount, 2, ',', '.') . " ₺\n" .
                    "Müşteri: {$order->customer_name}\n" .
                    "E-posta: {$order->customer_email}\n" .
                    "Telefon: {$order->customer_phone}\n" .
                    ($transferNote ? "\nNot: {$transferNote}" : ''),
                    function ($message) use ($notifyEmail, $order) {
                        $message->to($notifyEmail)
                            ->subject("Havale Bildirimi - Sipariş #{$order->order_number}");
                    }
                );
            } catch (\Exception $e) {
                \Log::error('Havale email bildirimi gönderilemedi: ' . $e->getMessage());
            }
        }

        // Telegram bildirimi (eğer Telegram modülü varsa)
        if (class_exists('\Modules\Telegram\App\Services\TelegramService')) {
            try {
                $telegram = app('\Modules\Telegram\App\Services\TelegramService');
                $telegram->sendMessage(
                    "💰 *Yeni Havale/EFT Bildirimi*\n\n" .
                    "📦 Sipariş: `{$order->order_number}`\n" .
                    "💵 Tutar: *" . number_format($order->total_amount, 2, ',', '.') . " ₺*\n" .
                    "👤 Müşteri: {$order->customer_name}\n" .
                    "📧 E-posta: {$order->customer_email}\n" .
                    ($transferNote ? "\n📝 Not: {$transferNote}" : '')
                );
            } catch (\Exception $e) {
                \Log::error('Havale Telegram bildirimi gönderilemedi: ' . $e->getMessage());
            }
        }
    }
}
