<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentMethod;
use Illuminate\Support\Facades\Log;

class PayTRPaymentService
{
    /**
     * PayTR iframe token oluştur ve ödeme başlat
     */
    public function initiatePayment(Payment $payment, array $userInfo, array $orderInfo): array
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
            // Ödeme bilgilerini hazırla
            $merchantId = $config['merchant_id'];
            $merchantKey = $config['merchant_key'];
            $merchantSalt = $config['merchant_salt'];

            // Test/Production mod
            $testMode = $paymentMethod->gateway_mode === 'test' ? '1' : '0';
            $noInstallment = $payment->installment_count <= 1 ? '1' : '0';
            $maxInstallment = $noInstallment === '1' ? 0 : $payment->installment_count;

            // Sepet içeriği (PayTR formatı)
            $basket = $this->prepareBasket($orderInfo);

            // PayTR'ye gönderilecek parametreler
            $merchantOid = $payment->payment_number; // payment_number kullan (transaction_id yok)
            $userIp = request()->ip();
            $emailDomain = explode('@', $userInfo['email'])[1] ?? 'example.com';

            // Callback URL'leri
            $merchantOkUrl = route('payment.callback.success', ['payment' => $payment->payment_id]);
            $merchantFailUrl = route('payment.callback.fail', ['payment' => $payment->payment_id]);

            // Tutar (kuruş cinsine çevir)
            $paymentAmount = intval($payment->amount * 100);

            // Hash oluştur
            $hashStr = $merchantId . $userIp . $merchantOid . $userInfo['email'] . $paymentAmount .
                       $basket . $noInstallment . $maxInstallment . $payment->currency . $testMode;
            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

            // POST parametreleri
            $postData = [
                'merchant_id' => $merchantId,
                'user_ip' => $userIp,
                'merchant_oid' => $merchantOid,
                'email' => $userInfo['email'],
                'payment_amount' => $paymentAmount,
                'paytr_token' => $paytrToken,
                'user_basket' => $basket,
                'debug_on' => $testMode,
                'no_installment' => $noInstallment,
                'max_installment' => $maxInstallment,
                'user_name' => $userInfo['name'],
                'user_address' => $userInfo['address'] ?? 'N/A',
                'user_phone' => $userInfo['phone'] ?? 'N/A',
                'merchant_ok_url' => $merchantOkUrl,
                'merchant_fail_url' => $merchantFailUrl,
                'timeout_limit' => '30',
                'currency' => $payment->currency,
                'test_mode' => $testMode,
                'lang' => app()->getLocale() === 'tr' ? 'tr' : 'en',
            ];

            // PayTR API'ye istek gönder
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 90);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);

                Log::error('PayTR Payment API Hatası', [
                    'error' => $error,
                    'payment_id' => $payment->payment_id
                ]);

                return [
                    'success' => false,
                    'message' => 'PayTR API bağlantı hatası: ' . $error
                ];
            }

            curl_close($ch);

            $result = json_decode($result, true);

            if (empty($result) || !isset($result['status'])) {
                Log::error('PayTR Payment API Geçersiz Yanıt', [
                    'response' => $result
                ]);

                return [
                    'success' => false,
                    'message' => 'PayTR API geçersiz yanıt döndü'
                ];
            }

            if ($result['status'] === 'success') {
                // Token alındı, iframe URL'i oluştur
                $iframeToken = $result['token'];

                // Payment kaydını güncelle
                $payment->update([
                    'gateway_response' => $result,
                    'status' => 'pending',
                ]);

                Log::info('PayTR Ödeme Başlatıldı', [
                    'payment_id' => $payment->payment_id,
                    'transaction_id' => $payment->transaction_id,
                    'token' => substr($iframeToken, 0, 20) . '...'
                ]);

                return [
                    'success' => true,
                    'message' => 'Ödeme başarıyla başlatıldı',
                    'iframe_token' => $iframeToken,
                    'iframe_url' => 'https://www.paytr.com/odeme/guvenli/' . $iframeToken,
                ];
            } else {
                $errorMsg = $result['reason'] ?? 'Bilinmeyen hata';

                Log::error('PayTR Payment API Hatası', [
                    'error' => $errorMsg,
                    'result' => $result,
                    'payment_id' => $payment->payment_id
                ]);

                return [
                    'success' => false,
                    'message' => 'PayTR hatası: ' . $errorMsg
                ];
            }

        } catch (\Exception $e) {
            Log::error('PayTR Payment Exception', [
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
     * PayTR callback doğrula
     */
    public function verifyCallback(array $postData): array
    {
        if (empty($postData['merchant_oid']) || empty($postData['status']) || empty($postData['hash'])) {
            return [
                'success' => false,
                'message' => 'Eksik callback parametreleri'
            ];
        }

        // merchant_oid ile payment bul (gateway_transaction_id veya payment_number)
        $payment = Payment::where('gateway_transaction_id', $postData['merchant_oid'])
            ->orWhere('payment_number', $postData['merchant_oid'])
            ->first();

        if (!$payment) {
            return [
                'success' => false,
                'message' => 'Ödeme kaydı bulunamadı'
            ];
        }

        $paymentMethod = $payment->paymentMethod;

        if (!$paymentMethod || $paymentMethod->gateway !== 'paytr') {
            return [
                'success' => false,
                'message' => 'Geçersiz ödeme yöntemi'
            ];
        }

        $config = $paymentMethod->gateway_config;
        $merchantKey = $config['merchant_key'];
        $merchantSalt = $config['merchant_salt'];

        // Hash doğrula
        $hashStr = $postData['merchant_oid'] . $merchantSalt . $postData['status'] . $postData['total_amount'];
        $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

        if ($calculatedHash !== $postData['hash']) {
            Log::error('PayTR Callback Hash Doğrulama Hatası', [
                'payment_id' => $payment->payment_id,
                'calculated_hash' => $calculatedHash,
                'received_hash' => $postData['hash']
            ]);

            return [
                'success' => false,
                'message' => 'Hash doğrulama hatası'
            ];
        }

        // Ödeme durumunu güncelle
        if ($postData['status'] === 'success') {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_response' => $postData,
            ]);

            // Payable model'e bildir (ShopOrder, Subscription vs.)
            if ($payment->payable) {
                $payment->payable->markAsPaid($payment);
            }

            Log::info('PayTR Ödeme Başarılı', [
                'payment_id' => $payment->payment_id,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount
            ]);

            return [
                'success' => true,
                'message' => 'Ödeme başarılı',
                'payment' => $payment
            ];
        } else {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => $postData,
            ]);

            Log::warning('PayTR Ödeme Başarısız', [
                'payment_id' => $payment->payment_id,
                'transaction_id' => $payment->transaction_id,
                'failed_reason' => $postData['failed_reason_msg'] ?? 'Bilinmeyen'
            ]);

            return [
                'success' => false,
                'message' => 'Ödeme başarısız: ' . ($postData['failed_reason_msg'] ?? 'Bilinmeyen hata'),
                'payment' => $payment
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

        return base64_encode(json_encode($basketItems));
    }
}
