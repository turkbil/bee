<?php

namespace Modules\Payment\App\Services\Gateways;

use Modules\Payment\App\Contracts\Payable;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * PayTR Gateway
 *
 * PayTR iFrame API entegrasyonu
 * Dokümantasyon: /public/paytr-setup/index.html
 */
class PayTRGateway implements PaymentGatewayInterface
{
    protected $config;
    protected $apiUrl = 'https://www.paytr.com/odeme/api/get-token';
    protected $testApiUrl = 'https://www.paytr.com/odeme/api/get-token';

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Ödeme işlemini başlat
     */
    public function initiatePayment(Payable $payable, PaymentMethod $paymentMethod, array $options = []): array
    {
        try {
            // Payment kaydı oluştur
            $payment = Payment::create([
                'payable_id' => $payable->getKey(),
                'payable_type' => get_class($payable),
                'payment_method_id' => $paymentMethod->payment_method_id,
                'amount' => $payable->getPaymentAmount(),
                'currency' => $payable->getPaymentCurrency(),
                'exchange_rate' => 1,
                'amount_in_base_currency' => $payable->getPaymentAmount(),
                'status' => 'pending',
                'gateway' => 'paytr',
                'payment_type' => 'purchase',
                'installment_count' => $options['installment'] ?? 1,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // PayTR parametreleri
            $merchantId = $this->config['merchant_id'];
            $merchantKey = $this->config['merchant_key'];
            $merchantSalt = $this->config['merchant_salt'];
            $merchantOid = 'PAY-' . $payment->payment_id;

            $customer = $payable->getPaymentCustomer();
            $basket = $this->formatBasket($payable->getPaymentBasket());
            $amount = intval($payable->getPaymentAmount() * 100); // Kuruş cinsinden

            // Callback URLs
            $merchantOkUrl = $options['success_url'] ?? route('payment.paytr.success');
            $merchantFailUrl = $options['fail_url'] ?? route('payment.paytr.fail');

            // Hash hesaplama
            $hashStr = $merchantId . $customer['ip'] . $merchant Oid . $customer['email'] . $amount . $basket .
                       ($options['no_installment'] ?? 0) . ($options['max_installment'] ?? 0) .
                       $payable->getPaymentCurrency() . $options['test_mode'] ?? ($paymentMethod->gateway_mode === 'test' ? 1 : 0);

            $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $merchantSalt, $merchantKey, true));

            // API request
            $response = Http::asForm()->post($this->apiUrl, [
                'merchant_id' => $merchantId,
                'user_ip' => $customer['ip'] ?? request()->ip(),
                'merchant_oid' => $merchantOid,
                'email' => $customer['email'],
                'payment_amount' => $amount,
                'paytr_token' => $paytrToken,
                'user_basket' => $basket,
                'debug_on' => $options['debug'] ?? 0,
                'no_installment' => $options['no_installment'] ?? 0,
                'max_installment' => $options['max_installment'] ?? 0,
                'user_name' => $customer['name'] ?? 'Müşteri',
                'user_address' => $customer['address'] ?? 'Adres',
                'user_phone' => $customer['phone'] ?? '5555555555',
                'merchant_ok_url' => $merchantOkUrl,
                'merchant_fail_url' => $merchantFailUrl,
                'timeout_limit' => $options['timeout'] ?? 30,
                'currency' => $payable->getPaymentCurrency(),
                'test_mode' => $options['test_mode'] ?? ($paymentMethod->gateway_mode === 'test' ? 1 : 0),
                'lang' => app()->getLocale(),
            ]);

            $result = json_decode($response->body(), true);

            if ($result['status'] === 'success') {
                // Payment güncelle
                $payment->update([
                    'gateway_transaction_id' => $merchantOid,
                    'gateway_payment_id' => $result['token'],
                    'gateway_response' => $result,
                ]);

                return [
                    'success' => true,
                    'iframe_url' => 'https://www.paytr.com/odeme/guvenli/' . $result['token'],
                    'token' => $result['token'],
                    'payment_id' => $payment->payment_id,
                ];
            } else {
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'gateway_response' => $result,
                ]);

                return [
                    'success' => false,
                    'error' => $result['reason'] ?? 'PayTR token alınamadı',
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Callback doğrulama (IPN)
     */
    public function verifyCallback(array $data): array
    {
        try {
            $merchantOid = $data['merchant_oid'] ?? null;
            $status = $data['status'] ?? null;
            $totalAmount = $data['total_amount'] ?? null;
            $hash = $data['hash'] ?? null;

            if (!$merchantOid || !$status || !$hash) {
                return ['success' => false, 'error' => 'Eksik parametreler'];
            }

            // Hash doğrulama
            $merchantKey = $this->config['merchant_key'];
            $merchantSalt = $this->config['merchant_salt'];
            $hashStr = $merchantOid . $merchantSalt . $status . $totalAmount;
            $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

            if ($hash !== $calculatedHash) {
                return ['success' => false, 'error' => 'Hash doğrulama hatası'];
            }

            // Payment bul
            $payment = Payment::where('gateway_transaction_id', $merchantOid)->first();

            if (!$payment) {
                return ['success' => false, 'error' => 'Payment bulunamadı'];
            }

            // Duplicate kontrol
            if ($payment->status === 'completed') {
                return ['success' => true, 'payment_id' => $payment->payment_id, 'status' => 'already_completed'];
            }

            // Status güncelle
            if ($status === 'success') {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], $data),
                ]);

                // Payable callback
                $payment->payable->onPaymentCompleted($payment);

                return [
                    'success' => true,
                    'payment_id' => $payment->payment_id,
                    'status' => 'completed',
                ];
            } else {
                $payment->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], $data),
                ]);

                // Payable callback
                $payment->payable->onPaymentFailed($payment);

                return [
                    'success' => false,
                    'payment_id' => $payment->payment_id,
                    'status' => 'failed',
                    'error' => $data['failed_reason_msg'] ?? 'Ödeme başarısız',
                ];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * İade işlemi
     */
    public function refund(int $paymentId, ?float $amount = null, string $reason = ''): array
    {
        // PayTR iade API'si henüz implement edilmedi
        return [
            'success' => false,
            'error' => 'PayTR iade API entegrasyonu yapılacak',
        ];
    }

    /**
     * Ödeme durumu sorgulama
     */
    public function queryPayment(string $gatewayTransactionId): array
    {
        // PayTR sorgu API'si henüz implement edilmedi
        return [
            'success' => false,
            'error' => 'PayTR sorgu API entegrasyonu yapılacak',
        ];
    }

    /**
     * Sepet formatla (PayTR basket parametresi)
     */
    protected function formatBasket(array $items): string
    {
        $basket = [];
        foreach ($items as $item) {
            $basket[] = [
                $item['name'],
                number_format($item['price'], 2, '.', ''),
                $item['quantity'] ?? 1,
            ];
        }

        return base64_encode(json_encode($basket));
    }
}
