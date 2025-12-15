<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\App\Models\Order;
use Modules\Payment\App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentSuccessController extends Controller
{
    /**
     * Ödeme başarılı sayfasını göster
     */
    public function show(Request $request)
    {
        Log::info('💳 PaymentSuccessController::show', [
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

        Log::info('✅ Payment success page loaded', [
            'order_number' => $order->order_number,
            'amount' => $payment->amount,
            'items_count' => $order->items->count()
        ]);

        // Session'dan payment verilerini temizle (ama localStorage için cart_id kalsın)
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

        // Başarı sayfasını göster
        return view('payment::front.payment-success', [
            'payment' => $payment,
            'order' => $order,
            'layoutPath' => $layoutPath,
        ]);
    }
}
