<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class PayTRDirectService
{
    /**
     * PayTR Direct API ile ödeme başlat
     * Kart bilgilerini direkt PayTR'a POST eder
     */
    public function prepareDirectPayment(Payment $payment, array $userInfo, array $orderInfo, array $cardInfo): array
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

            // Taksit ayarları
            $installmentCount = $payment->installment_count > 1 ? $payment->installment_count : 0;

            // Sepet içeriği (PayTR formatı)
            $basket = $this->prepareBasket($orderInfo);

            // Token oluşturma için hash string
            $merchantOid = $payment->payment_number; // payment_number kullan (transaction_id yok)
            $userIp = request()->ip();
            $email = $userInfo['email'];
            $paymentAmount = number_format($payment->amount, 2, '.', ''); // 100.99 formatında
            $currency = 'TL';
            $paymentType = 'card';

            // Non-3D mod (0 = 3D Secure, 1 = Non-3D)
            $non3d = '0'; // Varsayılan 3D Secure

            // Hash string oluştur (token için)
            $hashStr = $merchantId . $userIp . $merchantOid . $email . $paymentAmount .
                       $paymentType . $installmentCount . $currency . $testMode . $non3d;

            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

            // Callback URL'leri
            $merchantOkUrl = route('payment.callback.success', ['payment' => $payment->payment_id]);
            $merchantFailUrl = route('payment.callback.fail', ['payment' => $payment->payment_id]);

            // POST verileri (PayTR'a gönderilecek)
            $postData = [
                // Merchant bilgileri
                'merchant_id' => $merchantId,
                'paytr_token' => $paytrToken,

                // Müşteri bilgileri
                'user_ip' => $userIp,
                'merchant_oid' => $merchantOid,
                'email' => $email,
                'user_name' => $userInfo['name'],
                'user_address' => !empty($userInfo['address']) ? $userInfo['address'] : 'Türkiye',
                'user_phone' => !empty($userInfo['phone']) ? $userInfo['phone'] : '05000000000',

                // Ödeme bilgileri
                'payment_type' => $paymentType,
                'payment_amount' => $paymentAmount,
                'currency' => $currency,
                'installment_count' => $installmentCount,

                // Kart bilgileri
                'cc_owner' => $cardInfo['cc_owner'],
                'card_number' => $cardInfo['card_number'],
                'expiry_month' => $cardInfo['expiry_month'],
                'expiry_year' => $cardInfo['expiry_year'],
                'cvv' => $cardInfo['cvv'],

                // Sepet
                'user_basket' => $basket,

                // Callback URL'ler
                'merchant_ok_url' => $merchantOkUrl,
                'merchant_fail_url' => $merchantFailUrl,

                // Ayarlar
                'test_mode' => $testMode,
                'non_3d' => $non3d,
                'client_lang' => app()->getLocale() === 'tr' ? 'tr' : 'en',
                'debug_on' => '1', // Hata mesajları için
            ];

            // Taksit varsa card_type ekle (opsiyonel)
            if ($installmentCount > 1 && !empty($cardInfo['card_type'])) {
                $postData['card_type'] = $cardInfo['card_type'];
            }

            Log::info('PayTR Direct Payment Hazırlandı', [
                'payment_id' => $payment->payment_id,
                'transaction_id' => $merchantOid,
                'amount' => $paymentAmount,
                'installment' => $installmentCount,
            ]);

            return [
                'success' => true,
                'message' => 'Ödeme hazır',
                'post_url' => 'https://www.paytr.com/odeme',
                'post_data' => $postData,
            ];

        } catch (\Exception $e) {
            Log::error('PayTR Direct Payment Hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payment_id' => $payment->payment_id
            ]);

            return [
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
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
                    $item['quantity'] ?? 1
                ];
            }
        } else {
            // Fallback: Tek ürün
            $basketItems[] = [
                $orderInfo['description'] ?? 'Ödeme',
                number_format($orderInfo['amount'], 2, '.', ''),
                1
            ];
        }

        return htmlentities(json_encode($basketItems));
    }
}
