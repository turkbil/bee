<?php

namespace Modules\Payment\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\App\Models\Order;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRIframeService;

class PaymentPageController extends Controller
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
        // 🔥 DEBUG: Controller method called
        file_put_contents(storage_path('logs/paytr-debug.log'), "[" . date('Y-m-d H:i:s') . "] 🎯 PaymentPageController->show() CALLED: order={$orderNumber}\n", FILE_APPEND);

        $layoutPath = $this->getLayoutPath();

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return view('payment::front.payment-error', [
                'error' => 'Sipariş bulunamadı: ' . $orderNumber,
                'layoutPath' => $layoutPath,
            ]);
        }

        $payment = Payment::where('payable_type', Order::class)
            ->where('payable_id', $order->order_id)
            ->first();

        if (!$payment) {
            return view('payment::front.payment-error', [
                'error' => 'Ödeme kaydı bulunamadı.',
                'layoutPath' => $layoutPath,
            ]);
        }

        // PayTR token al
        $paymentIframeUrl = null;
        $error = null;

        if ($payment->gateway_response) {
            file_put_contents(storage_path('logs/paytr-debug.log'), "[" . date('Y-m-d H:i:s') . "] ✅ Token already exists in gateway_response\n", FILE_APPEND);
            $gatewayResponse = json_decode($payment->gateway_response, true);
            if (isset($gatewayResponse['token'])) {
                $paymentIframeUrl = 'https://www.paytr.com/odeme/guvenli/' . $gatewayResponse['token'];
            }
        }

        // Token yoksa yeni al
        if (!$paymentIframeUrl) {
            file_put_contents(storage_path('logs/paytr-debug.log'), "[" . date('Y-m-d H:i:s') . "] ⚠️  No token found, requesting new one...\n", FILE_APPEND);
            try {
                $iframeService = app(PayTRIframeService::class);

                // ÖNCE: pending_customer session'ını kontrol et (CheckoutPage'den gelir)
                $pendingCustomer = session('pending_customer');

                // Order'dan billing address snapshot'ını çek
                $billingAddr = is_array($order->billing_address ?? null)
                    ? $order->billing_address
                    : json_decode($order->billing_address ?? '[]', true);

                // Address string oluştur (PayTR için)
                $userAddress = 'Türkiye'; // Fallback
                if (!empty($billingAddr)) {
                    $addr = trim($billingAddr['address_line_1'] ?? '');
                    $city = trim($billingAddr['city'] ?? '');
                    $district = trim($billingAddr['district'] ?? '');

                    if (!empty($addr) && !empty($city)) {
                        $userAddress = $addr;
                        if (!empty($district)) {
                            $userAddress .= ', ' . $district;
                        }
                        $userAddress .= ', ' . $city;
                    }
                }

                // UserInfo - önce session'dan, sonra order'dan
                $userInfo = [
                    'name' => $pendingCustomer['name'] ?? $order->customer_name ?? 'Müşteri',
                    'email' => $pendingCustomer['email'] ?? $order->customer_email ?? 'test@test.com',
                    'phone' => $pendingCustomer['phone'] ?? $order->customer_phone ?? '5551234567',
                    'address' => $pendingCustomer['address'] ?? $userAddress,
                ];

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

                \Log::info('🔍 PayTR Token Request', [
                    'userInfo' => $userInfo,
                    'orderInfo' => $orderInfo,
                    'billing_address_snapshot' => $billingAddr,
                ]);

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
            return view('payment::front.payment-error', [
                'error' => $error,
                'order' => $order,
                'layoutPath' => $layoutPath,
            ]);
        }

        return view('payment::front.payment-page', [
            'order' => $order,
            'payment' => $payment,
            'paymentIframeUrl' => $paymentIframeUrl,
            'orderNumber' => $orderNumber,
            'layoutPath' => $layoutPath,
        ]);
    }
}
