<?php

namespace Modules\Shop\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Services\PayTRIframeService;
use Illuminate\Support\Facades\Log;

class PaymentPageController extends Controller
{
    public function show($orderNumber)
    {
        Log::info('💳 PaymentPageController::show', ['order' => $orderNumber]);

        try {
            // TEST MOD - Session'dan gelen gerçek fiyat bilgilerini kullan
            $amount = session('test_payment_amount', 100.00); // Session'dan al, yoksa 100.00
            $subtotal = session('test_payment_subtotal', 0);
            $tax = session('test_payment_tax', 0);
            $itemCount = session('test_payment_item_count', 0);

            Log::info('💰 Payment amounts from session', [
                'amount' => $amount,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'itemCount' => $itemCount
            ]);

            // Test için PayTR iframe token oluştur
            $paytrService = app(PayTRIframeService::class);

            // Test payment kaydı oluştur
            $testPayment = new Payment([
                'payment_number' => $orderNumber,  // PayTR merchant_oid olarak kullanılacak
                'amount' => $amount, // Session'dan gelen gerçek tutar
                'status' => 'pending',
                'currency' => 'TRY',
                'gateway' => 'paytr',
            ]);

            // Test user ve order bilgileri
            $userInfo = [
                'name' => 'Test Kullanıcı',
                'email' => setting('contact_email') ?: 'test@example.com',
                'phone' => '05551234567',
                'address' => 'Test Adres, İstanbul, Türkiye',
            ];

            $orderInfo = [
                'description' => 'Test Siparişi - ' . $itemCount . ' ürün',
                'amount' => $amount,
                'items' => [
                    [
                        'name' => 'Sepet Toplamı',
                        'price' => $amount,
                        'quantity' => 1,
                    ]
                ]
            ];

            // PayTR iframe token al
            $result = $paytrService->prepareIframePayment($testPayment, $userInfo, $orderInfo);

            if (!$result['success']) {
                // Hata durumunda checkout'a geri döndür
                return redirect()->route('shop.checkout')
                    ->with('error', $result['message']);
            }

            Log::info('✅ PayTR iframe token alındı', [
                'token' => substr($result['token'], 0, 20) . '...',
                'url' => $result['iframe_url']
            ]);

            // İframe sayfasını göster
            return view('shop::front.payment-page', [
                'orderNumber' => $orderNumber,
                'iframeUrl' => $result['iframe_url'],
                'amount' => $amount,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'itemCount' => $itemCount,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ PaymentPageController error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('shop.checkout')
                ->with('error', 'Ödeme sayfası yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }
}
