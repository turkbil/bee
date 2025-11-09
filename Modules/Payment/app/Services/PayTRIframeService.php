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
        $paymentMethod = $payment->paymentMethod;

        if (!$paymentMethod || $paymentMethod->gateway !== 'paytr') {
            return [
                'success' => false,
                'message' => 'Bu servis sadece PayTR için çalışır'
            ];
        }

        $config = $paymentMethod->gateway_config;

        if (empty($config['merchant_id']) || empty($config['merchant_key']) || empty($config['merchant_salt'])) {
            return [
                'success' => false,
                'message' => 'PayTR merchant bilgileri eksik'
            ];
        }

        try {
            $merchantId = $config['merchant_id'];
            $merchantKey = $config['merchant_key'];
            $merchantSalt = $config['merchant_salt'];

            // Test/Production mod
            $testMode = $paymentMethod->gateway_mode === 'test' ? '1' : '0';

            // Taksit ayarları - Sadece tek çekim
            $maxInstallment = 0; // 0 = Sistem default taksit
            $noInstallment = 0; // 0 = Taksit seçenekleri göster, 1 = Sadece tek çekim

            // Sepet içeriği (PayTR formatı)
            $basket = $this->prepareBasket($orderInfo);

            // Token oluşturma için hash string
            $merchantOid = $payment->transaction_id;
            $userIp = request()->ip();
            $email = $userInfo['email'];
            $paymentAmount = (int) ($payment->amount * 100); // Kuruş cinsinden (9.99 TL = 999)
            $currency = 'TL';

            // Callback URL (success ve fail aynı olabilir)
            $merchantOkUrl = route('payment.callback.paytr');
            $merchantFailUrl = route('payment.callback.paytr');

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
                'timeout_limit' => '30',
                'currency' => $currency,
                'test_mode' => $testMode,
            ];

            Log::info('📦 PayTR iframe token request', [
                'payment_id' => $payment->payment_id,
                'merchant_oid' => $merchantOid,
                'amount' => $payment->amount,
                'test_mode' => $testMode,
            ]);

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

            Log::info('📥 PayTR iframe token response', ['response' => $response]);

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
