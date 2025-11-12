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
        // Settings'den PayTR credentials al (tenant-aware)
        $merchantId = setting('paytr_merchant_id');
        $merchantKey = setting('paytr_merchant_key');
        $merchantSalt = setting('paytr_merchant_salt');

        if (empty($merchantId) || empty($merchantKey) || empty($merchantSalt)) {
            return [
                'success' => false,
                'message' => 'PayTR merchant bilgileri eksik. Lütfen admin panelden ayarları kontrol edin.'
            ];
        }

        try {
            // Test/Production mod (settings'den)
            $testMode = setting('paytr_test_mode', false) ? '1' : '0';

            // Taksit ayarları (settings'den)
            $maxInstallment = (int) setting('paytr_max_installment', 0);
            $noInstallment = 0; // 0 = Taksit seçenekleri göster, 1 = Sadece tek çekim

            // Sepet içeriği (PayTR formatı)
            $basket = $this->prepareBasket($orderInfo);

            // Token oluşturma için hash string
            $merchantOid = $payment->payment_number; // Sipariş numarası (benzersiz olmalı!)
            $userIp = request()->ip();
            $email = $userInfo['email'];
            $paymentAmount = (int) ($payment->amount * 100); // Kuruş cinsinden (9.99 TL = 999)
            $currency = setting('paytr_currency', 'TL');

            // Callback URL (success ve fail)
            $merchantOkUrl = route('payment.success');
            $merchantFailUrl = route('shop.checkout') . '?payment=failed';

            // Hash string oluştur (DOĞRU SIRA!)
            // merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + no_installment + max_installment + currency + test_mode
            $hashStr = $merchantId . $userIp . $merchantOid . $email . $paymentAmount . $basket .
                       $noInstallment . $maxInstallment . $currency . $testMode;

            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

            // POST verileri (iframe token için)
            $postData = [
                'merchant_id' => $merchantId,
                'user_ip' => $userIp,
                'merchant_oid' => $merchantOid,
                'email' => $email,
                'payment_amount' => $paymentAmount,
                'paytr_token' => $paytrToken,
                'user_basket' => $basket,
                'debug_on' => '1', // Entegrasyon sürecinde 1
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
                'lang' => app()->getLocale() === 'tr' ? 'tr' : 'en',
            ];

            // Debug mode aktifse loglama yap
            if (setting('paytr_debug', false)) {
                Log::info('📦 PayTR iframe token request', [
                    'payment_id' => $payment->payment_id,
                    'merchant_oid' => $merchantOid,
                    'amount' => $payment->amount,
                    'test_mode' => $testMode,
                    'currency' => $currency,
                ]);
            }

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

            if (setting('paytr_debug', false)) {
                Log::info('📥 PayTR iframe token response', ['response' => $response]);
            }

            if (!$response || $response['status'] !== 'success') {
                $errorMessage = $response['reason'] ?? 'Bilinmeyen hata';
                Log::error('❌ PayTR iframe token error', ['reason' => $errorMessage]);
                return [
                    'success' => false,
                    'message' => 'Ödeme token alınamadı: ' . $errorMessage
                ];
            }

            // Token başarıyla alındı
            $iframeToken = $response['token'];

            // Payment kaydına token'ı kaydet
            $payment->update([
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
     */
    private function prepareBasket(array $orderInfo): string
    {
        $basketItems = [];

        if (isset($orderInfo['items']) && is_array($orderInfo['items'])) {
            foreach ($orderInfo['items'] as $item) {
                $basketItems[] = [
                    $item['name'],
                    number_format($item['price'], 2, '.', ''),
                    $item['quantity']
                ];
            }
        } else {
            // Varsayılan sepet (eğer items yoksa)
            $basketItems[] = [
                $orderInfo['description'] ?? 'Sipariş',
                number_format($orderInfo['amount'], 2, '.', ''),
                1
            ];
        }

        return base64_encode(json_encode($basketItems));
    }
}
