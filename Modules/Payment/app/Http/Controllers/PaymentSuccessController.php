<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shop\App\Models\ShopOrder;
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
                'test_payment_amount' => session('test_payment_amount'),
            ]
        ]);

        // Session'dan sipariş numarasını al
        $orderNumber = session('last_order_number');

        if (!$orderNumber) {
            Log::warning('⚠️ Payment success: Order number not found in session');
            return redirect()->route('shop.index')
                ->with('error', 'Sipariş bilgisi bulunamadı.');
        }

        // Siparişi bul
        $order = ShopOrder::where('order_number', $orderNumber)
            ->with(['items.product.medias', 'payments'])
            ->first();

        if (!$order) {
            Log::warning('⚠️ Payment success: Order not found', ['order_number' => $orderNumber]);
            return redirect()->route('shop.index')
                ->with('error', 'Sipariş bulunamadı.');
        }

        // Ödeme kaydını al (en son ödeme)
        $payment = $order->payments()->latest()->first();

        if (!$payment) {
            Log::warning('⚠️ Payment success: Payment record not found', ['order_id' => $order->order_id]);

            // Ödeme kaydı yoksa test için mock payment oluştur
            $payment = new \Modules\Payment\App\Models\Payment([
                'payment_number' => $orderNumber,
                'amount' => $order->total,
                'currency' => 'TRY',
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }

        Log::info('✅ Payment success page loaded', [
            'order_number' => $order->order_number,
            'amount' => $payment->amount,
            'items_count' => $order->items->count()
        ]);

        // Session'dan payment verilerini temizle
        session()->forget([
            'last_order_number',
            'test_payment_amount',
            'test_payment_subtotal',
            'test_payment_tax',
            'test_payment_item_count',
        ]);

        // Başarı sayfasını göster
        return view('payment::front.payment-success', [
            'payment' => $payment,
            'order' => $order,
        ]);
    }
}
