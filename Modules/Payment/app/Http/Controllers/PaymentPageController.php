<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\App\Models\Order;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRIframeService;

class PaymentPageController extends Controller
{
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return view('payment::front.payment-error', [
                'error' => 'Sipariş bulunamadı: ' . $orderNumber
            ]);
        }

        $payment = Payment::where('payable_type', Order::class)
            ->where('payable_id', $order->order_id)
            ->first();

        if (!$payment) {
            return view('payment::front.payment-error', [
                'error' => 'Ödeme kaydı bulunamadı.'
            ]);
        }

        // PayTR token al
        $paymentIframeUrl = null;
        $error = null;

        if ($payment->gateway_response) {
            $gatewayResponse = json_decode($payment->gateway_response, true);
            if (isset($gatewayResponse['token'])) {
                $paymentIframeUrl = 'https://www.paytr.com/odeme/guvenli/' . $gatewayResponse['token'];
            }
        }

        // Token yoksa yeni al
        if (!$paymentIframeUrl) {
            try {
                $iframeService = app(PayTRIframeService::class);

                $userInfo = session('checkout_user_info', [
                    'name' => $order->customer_name ?? 'Müşteri',
                    'email' => $order->customer_email ?? 'test@test.com',
                    'phone' => $order->customer_phone ?? '5551234567',
                    'address' => 'Türkiye',
                ]);

                $items = $order->items->map(function ($item) {
                    return [
                        'name' => $item->item_title ?? 'Ürün',
                        'price' => $item->unit_price,
                        'quantity' => $item->quantity,
                    ];
                })->toArray();

                $orderInfo = [
                    'order_number' => $order->order_number, // PayTR callback için zorunlu!
                    'amount' => $order->total_amount,
                    'description' => 'Sipariş: ' . $order->order_number,
                    'items' => $items,
                ];

                \Log::info('🔍 PayTR Token Request', compact('userInfo', 'orderInfo'));

                $result = $iframeService->prepareIframePayment($payment, $userInfo, $orderInfo);

                if ($result['success']) {
                    $payment->gateway_response = json_encode(['token' => $result['token']]);
                    $payment->save();
                    $paymentIframeUrl = 'https://www.paytr.com/odeme/guvenli/' . $result['token'];
                } else {
                    $error = 'Ödeme hazırlanamadı: ' . ($result['message'] ?? 'Bilinmeyen hata');
                }
            } catch (\Exception $e) {
                $error = 'Ödeme servisi hatası: ' . $e->getMessage();
                \Log::error('❌ PayTR Exception', ['error' => $e->getMessage()]);
            }
        }

        if ($error) {
            return view('payment::front.payment-error', compact('error', 'order'));
        }

        return view('payment::front.payment-page', [
            'order' => $order,
            'payment' => $payment,
            'paymentIframeUrl' => $paymentIframeUrl,
            'orderNumber' => $orderNumber,
        ]);
    }
}
