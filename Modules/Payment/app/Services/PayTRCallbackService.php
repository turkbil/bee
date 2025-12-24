<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * PayTR Callback Service
 *
 * PayTR'den gelen ödeme bildirimi callback'lerini işler.
 * Hash kontrolü, duplicate kontrolü, payment status güncelleme.
 */
class PayTRCallbackService
{
    /**
     * PayTR callback'i işle
     *
     * @param array $callbackData POST verisi
     * @return array ['success' => bool, 'message' => string]
     */
    public function handleCallback(array $callbackData): array
    {
        try {
            // 1. Gerekli alanları kontrol et
            $merchantOid = $callbackData['merchant_oid'] ?? null;
            $status = $callbackData['status'] ?? null;
            $totalAmount = $callbackData['total_amount'] ?? null;
            $hash = $callbackData['hash'] ?? null;

            if (!$merchantOid || !$status || !$hash) {
                Log::error('❌ PayTR callback: Eksik parametreler', $callbackData);
                return ['success' => false, 'message' => 'Eksik parametreler'];
            }

            // 2. Payment kaydını bul
            // Önce gateway_transaction_id ile ara (PayTR'ye gönderilen merchant_oid)
            $payment = Payment::where('gateway_transaction_id', $merchantOid)->first();

            // Bulamazsa payment_number'ı reconstruct et ve dene
            if (!$payment) {
                // merchant_oid formatı: T{tenant_id}PAY{year}{number} (örn: T2PAY202500010)
                // payment_number formatı: PAY-2025-00010
                // Tenant prefix'ini kaldır ve tireli formatı oluştur
                if (preg_match('/^T\d+PAY(\d{4})(\d+)$/', $merchantOid, $matches)) {
                    $reconstructedPaymentNumber = 'PAY-' . $matches[1] . '-' . $matches[2];
                    $payment = Payment::where('payment_number', $reconstructedPaymentNumber)->first();
                }
            }

            // Son çare: stripped payment_number ile ara
            if (!$payment) {
                // merchant_oid'den tenant prefix'ini kaldır: T2PAY202500010 -> PAY202500010
                $strippedMerchantOid = preg_replace('/^T\d+/', '', $merchantOid);
                $payment = Payment::where('status', 'pending')
                    ->whereRaw("REPLACE(REPLACE(REPLACE(payment_number, '-', ''), '_', ''), ' ', '') = ?", [$strippedMerchantOid])
                    ->first();
            }

            if (!$payment) {
                Log::error('❌ PayTR callback: Payment bulunamadı', ['merchant_oid' => $merchantOid]);
                return ['success' => false, 'message' => 'Payment bulunamadı'];
            }

            // 3. Duplicate kontrolü (payment zaten işlenmiş mi?)
            if (in_array($payment->status, ['completed', 'failed', 'refunded'])) {
                if (setting('paytr_debug', false)) {
                    Log::info('⚠️ PayTR callback: Duplicate - payment zaten işlenmiş', [
                        'payment_id' => $payment->payment_id,
                        'status' => $payment->status,
                    ]);
                }
                return ['success' => true, 'message' => 'Duplicate - zaten işlenmiş'];
            }

            // 4. Hash kontrolü (güvenlik)
            if (!$this->verifyHash($callbackData, $payment)) {
                Log::error('❌ PayTR callback: Hash doğrulama hatası', [
                    'payment_id' => $payment->payment_id,
                    'expected_hash' => $hash,
                ]);
                return ['success' => false, 'message' => 'Hash doğrulama hatası'];
            }

            // 5. Tutar kontrolü
            $expectedAmount = number_format($payment->amount, 2, '.', '');
            $receivedAmount = number_format($totalAmount / 100, 2, '.', ''); // Kuruş -> TL

            if ($expectedAmount !== $receivedAmount) {
                Log::error('❌ PayTR callback: Tutar uyumsuzluğu', [
                    'payment_id' => $payment->payment_id,
                    'expected' => $expectedAmount,
                    'received' => $receivedAmount,
                ]);
                return ['success' => false, 'message' => 'Tutar uyumsuzluğu'];
            }

            // 6. Status'e göre işlem yap
            // 🔥 FIX v3: Transaction KALDIRILDI - idempotent işlem, duplicate check var
            Log::channel('daily')->info('🔵 PayTR callback: Processing (no transaction)', [
                'payment_id' => $payment->payment_id,
                'status' => $status,
            ]);

            if ($status === 'success') {
                $this->handleSuccessPayment($payment, $callbackData);
            } else {
                $this->handleFailedPayment($payment, $callbackData);
            }

            // 🔥 DEBUG: İşlem sonrası veritabanı kontrolü
            $verifyPayment = Payment::find($payment->payment_id);
            Log::channel('daily')->info('🔵 POST-UPDATE VERIFY', [
                'payment_id' => $payment->payment_id,
                'db_status' => $verifyPayment->status ?? 'NOT_FOUND',
                'db_paid_at' => $verifyPayment->paid_at ?? 'NULL',
            ]);

            return ['success' => true, 'message' => 'İşlem başarılı'];

        } catch (\Exception $e) {
            Log::error('❌ PayTR callback exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'message' => 'İşlem hatası: ' . $e->getMessage()];
        }
    }

    /**
     * Başarılı ödemeyi işle
     */
    private function handleSuccessPayment(Payment $payment, array $callbackData): void
    {
        Log::channel('daily')->info('🔵 handleSuccessPayment START', [
            'payment_id' => $payment->payment_id,
            'current_status' => $payment->status,
            'connection' => $payment->getConnectionName(),
        ]);

        // Payment status güncelle
        // 🔥 FIX: json_encode KALDIRILDI - Model'de 'array' cast var, Laravel otomatik encode eder
        $updated = $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => $callbackData,
        ]);

        Log::channel('daily')->info('🔵 Payment update result', [
            'payment_id' => $payment->payment_id,
            'updated' => $updated,
            'new_status' => $payment->fresh()->status ?? 'FRESH_FAILED',
        ]);

        // Payable modeli güncelle (ShopOrder, Membership vb.)
        $payable = $payment->payable;

        Log::channel('daily')->info('🔵 Payable check', [
            'payable_exists' => $payable ? true : false,
            'payable_type' => $payable ? get_class($payable) : null,
            'has_method' => $payable && method_exists($payable, 'onPaymentCompleted'),
        ]);

        if ($payable && method_exists($payable, 'onPaymentCompleted')) {
            $payable->onPaymentCompleted($payment);
            Log::channel('daily')->info('✅ onPaymentCompleted called');
        }

        // Event dispatch (gelecekte: email, sms, notification)
        // event(new OrderPaid($payment));
    }

    /**
     * Başarısız ödemeyi işle
     */
    private function handleFailedPayment(Payment $payment, array $callbackData): void
    {
        // Payment status güncelle
        // 🔥 FIX: json_encode KALDIRILDI - Model'de 'array' cast var
        $payment->update([
            'status' => 'failed',
            'gateway_response' => $callbackData,
        ]);

        // Payable modeli güncelle
        $payable = $payment->payable;

        if ($payable && method_exists($payable, 'onPaymentFailed')) {
            $payable->onPaymentFailed($payment);
        }

        // Event dispatch
        // event(new PaymentFailed($payment));
    }

    /**
     * Hash doğrulama (PayTR güvenlik)
     */
    private function verifyHash(array $callbackData, Payment $payment): bool
    {
        // Settings'den merchant bilgilerini al
        $merchantKey = setting('paytr_merchant_key');
        $merchantSalt = setting('paytr_merchant_salt');

        if (empty($merchantKey) || empty($merchantSalt)) {
            Log::error('❌ PayTR callback: Merchant key/salt bulunamadı');
            return false;
        }

        // Hash string oluştur (PayTR formatı)
        $merchantOid = $callbackData['merchant_oid'];
        $status = $callbackData['status'];
        $totalAmount = $callbackData['total_amount'];

        $hashStr = $merchantOid . $merchantSalt . $status . $totalAmount;
        $calculatedHash = base64_encode(hash_hmac('sha256', $hashStr, $merchantKey, true));

        return hash_equals($calculatedHash, $callbackData['hash']);
    }
}
