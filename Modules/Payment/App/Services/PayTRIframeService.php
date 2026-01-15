<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class PayTRIframeService
{
    /**
     * PayTR iframe token oluştur
     * Kart bilgilerini PayTR iframe'de toplayacak
     */
    public function prepareIframePayment(Payment $payment, array $userInfo, array $orderInfo): array
    {
        // 🔥 DEBUG: Function called - write to MULTIPLE locations
        file_put_contents(storage_path('logs/paytr-debug.log'), "[" . date('Y-m-d H:i:s') . "] 🚀 prepareIframePayment CALLED: payment_id={$payment->payment_id}\n", FILE_APPEND);
        file_put_contents('/tmp/paytr-debug.txt', "[" . date('Y-m-d H:i:s') . "] 🚀 prepareIframePayment CALLED: payment_id={$payment->payment_id}\n", FILE_APPEND);
        \Log::channel('single')->emergency('🚀🚀🚀 prepareIframePayment CALLED: payment_id=' . $payment->payment_id);

        // Settings'den PayTR credentials al (tenant-aware)
        $merchantId = setting('paytr_merchant_id');
        $merchantKey = setting('paytr_merchant_key');
        $merchantSalt = setting('paytr_merchant_salt');

        if (empty($merchantId) || empty($merchantKey) || empty($merchantSalt)) {
            file_put_contents(storage_path('logs/paytr-debug.log'), "[" . date('Y-m-d H:i:s') . "] ❌ PayTR credentials missing!\n", FILE_APPEND);
            return [
                'success' => false,
                'message' => 'PayTR merchant bilgileri eksik. Lütfen admin panelden ayarları kontrol edin.'
            ];
        }

        try {
            // Test/Production mod (settings'den)
            $testMode = setting('paytr_test_mode', false) ? '1' : '0';

            // Taksit ayarları (settings'den)
            $maxInstallment = (int) setting('paytr_max_installment', 12);
            // 🔥 FIX: PayTR max_installment = 0 kabul etmiyor, minimum 1 olmalı
            if ($maxInstallment < 1) {
                $maxInstallment = 12; // Default: 12 taksit
            }
            $noInstallment = 0; // 0 = Taksit seçenekleri göster, 1 = Sadece tek çekim

            // Sepet içeriği (PayTR formatı)
            $basket = $this->prepareBasket($orderInfo);

            // Token oluşturma için hash string
            // PayTR merchant_oid sadece alfanumerik olmalı - özel karakter içeremez!
            // Tenant ID prefix ekle: T{tenant_id}{payment_number_stripped}{retry_suffix}
            $tenantId = tenant()->id ?? 1;
            $strippedPaymentNumber = str_replace(['-', '_', ' '], '', $payment->payment_number);
            // Benzersizlik için timestamp'in son 4 hanesi (her denemede farklı)
            $retrySuffix = substr(time(), -4);
            $merchantOid = 'T' . $tenantId . $strippedPaymentNumber . $retrySuffix;

            // 🔥 FIX: PayTR IPv6 desteklemiyor! IPv4'e çevir veya fallback kullan
            $userIp = request()->ip();
            if (filter_var($userIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                // IPv6 ise fallback IPv4 kullan
                $userIp = '185.125.190.39'; // Türkiye IP (fallback)
                \Log::warning('PayTR IPv6 detected, using fallback IPv4', ['original_ip' => request()->ip()]);
            }
            $email = $userInfo['email'];
            $paymentAmount = (int) ($payment->amount * 100); // Kuruş cinsinden (9.99 TL = 999)
            $currency = setting('paytr_currency', 'TL');

            // Callback URL (success ve fail)
            // NOT: Query string PayTR panelindeki URL ile uyumsuzluk yaratabilir
            $merchantOkUrl = route('payment.success');
            $merchantFailUrl = route('cart.checkout');

            // Hash string oluştur (DOĞRU SIRA!)
            // merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + no_installment + max_installment + currency + test_mode
            $hashStr = $merchantId . $userIp . $merchantOid . $email . $paymentAmount . $basket .
                       $noInstallment . $maxInstallment . $currency . $testMode;

            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

            // POST verileri (iframe token için) - Eski sistemle uyumlu
            $postData = [
                'merchant_id' => $merchantId,
                'user_ip' => $userIp,
                'merchant_oid' => $merchantOid,
                'email' => $email,
                'payment_amount' => $paymentAmount,
                'paytr_token' => $paytrToken,
                'user_basket' => $basket,
                'debug_on' => 1, // Eski sistemde hep 1
                'no_installment' => $noInstallment,
                'max_installment' => $maxInstallment,
                'user_name' => $userInfo['name'],
                'user_address' => $userInfo['address'] ?? 'Türkiye',
                'user_phone' => $userInfo['phone'] ?? '',
                'merchant_ok_url' => $merchantOkUrl,
                'merchant_fail_url' => $merchantFailUrl,
                'timeout_limit' => setting('paytr_timeout_limit', '30'),
                'currency' => $currency,
                'test_mode' => $testMode,
            ];

            // 🔥 DEBUG LOGLAMA - PayTR sorun tespiti için
            $debugData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'payment_id' => $payment->payment_id,
                'payment_number' => $payment->payment_number,
                'merchant_oid' => $merchantOid,
                'amount' => $payment->amount,
                'payment_amount_kurus' => $paymentAmount,
                'user_info' => $userInfo,
                'order_info' => $orderInfo,
                'post_data' => $postData,
            ];

            // Laravel log'a yaz
            Log::channel('daily')->info('🔍 PayTR REQUEST DEBUG', $debugData);

            // Ayrı dosyaya da yaz (kesin çalışır)
            $logFile = storage_path('logs/paytr-requests.log');
            $logContent = "\n" . str_repeat('=', 80) . "\n";
            $logContent .= "[" . date('Y-m-d H:i:s') . "] USER: " . (auth()->user()?->email ?? 'guest') . "\n";
            $logContent .= str_repeat('-', 80) . "\n";
            $logContent .= json_encode($debugData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            @file_put_contents($logFile, $logContent, FILE_APPEND);

            // PayTR API'sine token için istek gönder
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error('❌ PayTR iframe token curl error', ['error' => $error]);
                return [
                    'success' => false,
                    'message' => 'Ödeme sistemi bağlantı hatası: ' . $error
                ];
            }

            $response = json_decode($result, true);

            // 🔥 RESPONSE LOGLAMA
            $responseLog = "\n--- RESPONSE ---\n";
            $responseLog .= "Status: " . ($response['status'] ?? 'NULL') . "\n";
            $responseLog .= "Token: " . (isset($response['token']) ? substr($response['token'], 0, 20) . '...' : 'NULL') . "\n";
            $responseLog .= "Reason: " . ($response['reason'] ?? 'N/A') . "\n";
            $responseLog .= str_repeat('=', 80) . "\n";
            @file_put_contents(storage_path('logs/paytr-requests.log'), $responseLog, FILE_APPEND);

            Log::channel('daily')->info('🔍 PayTR RESPONSE DEBUG', [
                'user_email' => auth()->user()?->email,
                'response' => $response,
            ]);

            if (!$response || $response['status'] !== 'success') {
                $errorMessage = $response['reason'] ?? 'Bilinmeyen hata';
                Log::error('❌ PayTR iframe token error', [
                    'reason' => $errorMessage,
                    'full_response' => $response,
                    'payment_id' => $payment->payment_id
                ]);
                file_put_contents(storage_path('logs/paytr-error.log'),
                    "[" . date('Y-m-d H:i:s') . "] ERROR RESPONSE: " . json_encode($response, JSON_PRETTY_PRINT) . "\n\n",
                    FILE_APPEND
                );
                return [
                    'success' => false,
                    'message' => 'Ödeme token alınamadı: ' . $errorMessage
                ];
            }

            // Token başarıyla alındı
            $iframeToken = $response['token'];

            // Payment kaydına token'ı ve merchant_oid'i kaydet
            // NOT: gateway_transaction_id = PayTR'ye gönderilen merchant_oid (callback'te bu gelecek)
            $payment->update([
                'gateway_transaction_id' => $merchantOid,
                'gateway_response' => json_encode($response),
            ]);

            return [
                'success' => true,
                'token' => $iframeToken,
                'iframe_url' => 'https://www.paytr.com/odeme/guvenli/' . $iframeToken,
            ];

        } catch (\Exception $e) {
            Log::error('❌ PayTR iframe exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Ödeme hazırlama hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Sepet içeriğini PayTR formatına çevir
     *
     * 🔥 KRİTİK: PayTR sepet toplamının payment_amount ile TAM OLARAK eşleşmesini bekler!
     * KDV, kargo vs dahil TOPLAM tutarı sepette göstermeliyiz.
     */
    private function prepareBasket(array $orderInfo): string
    {
        // 🔥 FIX: Sepet toplamı = Payment amount olmalı (KDV, kargo dahil)
        // Item'ları ayrı ayrı göndermek yerine tek satır olarak total göster
        $basketItems = [
            [
                $orderInfo['description'] ?? 'Sipariş',
                number_format($orderInfo['amount'], 2, '.', ''), // Total amount (KDV dahil)
                1
            ]
        ];

        return base64_encode(json_encode($basketItems));
    }
}
