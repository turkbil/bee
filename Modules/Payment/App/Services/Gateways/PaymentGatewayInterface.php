<?php

namespace Modules\Payment\App\Services\Gateways;

use Modules\Payment\App\Contracts\Payable;
use Modules\Payment\App\Models\PaymentMethod;

/**
 * Payment Gateway Interface
 *
 * Tüm gateway'ler bu interface'i implement etmelidir.
 */
interface PaymentGatewayInterface
{
    /**
     * Ödeme işlemini başlat (iframe URL veya token döndür)
     *
     * @param Payable $payable - Ödeme yapılacak model
     * @param PaymentMethod $paymentMethod - Kullanılacak ödeme yöntemi
     * @param array $options - Ek parametreler (installment, callback_url vb.)
     * @return array ['success' => bool, 'iframe_url' => string, 'token' => string, 'error' => string]
     */
    public function initiatePayment(Payable $payable, PaymentMethod $paymentMethod, array $options = []): array;

    /**
     * Callback/IPN doğrulama
     *
     * @param array $data - POST/GET verisi
     * @return array ['success' => bool, 'payment_id' => int, 'status' => string, 'error' => string]
     */
    public function verifyCallback(array $data): array;

    /**
     * İade işlemi
     *
     * @param int $paymentId - Payment ID
     * @param float $amount - İade tutarı (null ise tam iade)
     * @param string $reason - İade nedeni
     * @return array ['success' => bool, 'refund_id' => string, 'error' => string]
     */
    public function refund(int $paymentId, ?float $amount = null, string $reason = ''): array;

    /**
     * Ödeme durumu sorgulama
     *
     * @param string $gatewayTransactionId - Gateway transaction ID
     * @return array ['success' => bool, 'status' => string, 'amount' => float, 'error' => string]
     */
    public function queryPayment(string $gatewayTransactionId): array;
}
