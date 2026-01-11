<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\App\Models\Order;
use Modules\Payment\App\Models\Payment;
use Modules\Cart\App\Services\CartService;
use Illuminate\Support\Facades\Log;

class PaymentSuccessController extends Controller
{
    /**
     * Ödeme başarılı sayfasını göster
     */
    public function show(Request $request)
    {
        // 🔒 AUTHENTICATION CHECK: Kullanıcı giriş yapmış olmalı
        if (!auth()->check()) {
            Log::warning('⚠️ Payment success: Unauthenticated access attempt');
            return redirect()->route('login')
                ->with('error', 'Bu sayfayı görüntülemek için giriş yapmalısınız.');
        }

        Log::info('💳 PaymentSuccessController::show', [
            'user_id' => auth()->id(),
            'query' => $request->query(),
            'session' => [
                'last_order_number' => session('last_order_number'),
            ]
        ]);

        // Önce query param'dan, sonra session'dan sipariş numarasını al
        // (PayTR redirect'inde session kaybolabiliyor)
        $orderNumber = $request->query('order') ?? session('last_order_number');

        if (!$orderNumber) {
            Log::warning('⚠️ Payment success: Order number not found in query or session');
            return redirect()->to('/')
                ->with('error', 'Sipariş bilgisi bulunamadı.');
        }

        // Siparişi bul (Cart Order)
        $order = Order::where('order_number', $orderNumber)
            ->with(['items', 'payments'])
            ->first();

        if (!$order) {
            Log::warning('⚠️ Payment success: Order not found', ['order_number' => $orderNumber]);
            return redirect()->to('/')
                ->with('error', 'Sipariş bulunamadı.');
        }

        // 🔒 OWNERSHIP CHECK: Sipariş bu kullanıcıya ait mi?
        if ($order->user_id !== auth()->id()) {
            Log::warning('⚠️ Payment success: Order ownership mismatch', [
                'order_user_id' => $order->user_id,
                'auth_user_id' => auth()->id(),
                'order_number' => $orderNumber
            ]);
            return redirect()->to('/')
                ->with('error', 'Bu siparişi görüntüleme yetkiniz yok.');
        }

        // 🛒 SEPET TEMİZLE: Ödeme başarılı olduğu için kullanıcının sepetini boşalt
        try {
            $cartService = app(CartService::class);
            $cart = $cartService->getCart(auth()->id(), session()->getId());

            if ($cart && $cart->items()->count() > 0) {
                $cartService->clearCart($cart);
                Log::info('🛒 Cart cleared after successful payment', [
                    'cart_id' => $cart->cart_id,
                    'user_id' => auth()->id(),
                    'order_number' => $orderNumber
                ]);
            }
        } catch (\Exception $e) {
            // Sepet temizleme hatası kritik değil, devam et
            Log::warning('⚠️ Cart clear failed (non-critical)', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
        }

        // Ödeme kaydını al (en son ödeme)
        $payment = $order->payments()->latest()->first();

        if (!$payment) {
            Log::warning('⚠️ Payment success: Payment record not found', ['order_id' => $order->order_id]);

            // Ödeme kaydı yoksa mock payment oluştur
            $payment = new Payment([
                'payment_number' => $orderNumber,
                'amount' => $order->total_amount,
                'currency' => 'TRY',
                'status' => 'paid',
                'created_at' => now(),
            ]);
        }

        // ⚠️ ÖDEME DURUMU KONTROLÜ
        // PayTR callback henüz gelmemiş olabilir, pending kontrolü yap
        $isPending = $order->payment_status !== 'paid';

        Log::info('✅ Payment success page loaded', [
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
            'is_pending' => $isPending,
            'amount' => $payment->amount,
            'items_count' => $order->items->count()
        ]);

        // Session'dan payment verilerini temizle (ama localStorage için cart_id kalsın)
        // NOT: Pending durumunda da temizle çünkü sepet kullanıldı
        session()->forget([
            'last_order_number',
            'checkout_user_info',
        ]);

        // Layout: Tenant temasından (header/footer için)
        $theme = tenant()->theme ?? 'simple';
        $layoutPath = "themes.{$theme}.layouts.app";

        // Tenant layout yoksa simple fallback
        if (!view()->exists($layoutPath)) {
            $layoutPath = 'themes.simple.layouts.app';
        }

        // Başarı/İşleniyor sayfasını göster
        return view('payment::front.payment-success', [
            'payment' => $payment,
            'order' => $order,
            'isPending' => $isPending,
            'layoutPath' => $layoutPath,
        ]);
    }
}
