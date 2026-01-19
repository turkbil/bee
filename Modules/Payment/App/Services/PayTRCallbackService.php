<?php

namespace Modules\Payment\App\Services;

use Modules\Payment\App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Modules\Mail\App\Services\MailTemplateService;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentMail;

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
            // gateway_transaction_id ile ara (PayTR'ye gönderilen merchant_oid)
            $payment = Payment::where('gateway_transaction_id', $merchantOid)->first();

            if (!$payment) {
                Log::error('❌ PayTR callback: Payment bulunamadı', [
                    'merchant_oid' => $merchantOid,
                ]);
                return ['success' => false, 'message' => 'Payment bulunamadı'];
            }

            // 3. Duplicate kontrolü (payment zaten işlenmiş mi?)
            // NOT: Kullanıcı birden fazla ödeme denemesi yapabilir (iframe yenileme)
            // Success callback her zaman işlenmeli (failed durumunu override eder)
            // Failed callback sadece pending durumunda işlenmeli
            if ($payment->status === 'completed' || $payment->status === 'refunded') {
                // Completed veya refunded ise kesinlikle atla
                Log::info('⚠️ PayTR callback: Duplicate - payment zaten tamamlanmış', [
                    'payment_id' => $payment->payment_id,
                    'status' => $payment->status,
                ]);
                return ['success' => true, 'message' => 'Duplicate - zaten işlenmiş'];
            }

            if ($payment->status === 'failed' && $status === 'failed') {
                // Zaten failed ve yeni gelen de failed ise atla
                Log::info('⚠️ PayTR callback: Duplicate - payment zaten başarısız', [
                    'payment_id' => $payment->payment_id,
                ]);
                return ['success' => true, 'message' => 'Duplicate - zaten başarısız'];
            }

            // ÖNEMLI: Eğer payment failed ama yeni callback success ise, işlemeye devam et!
            // Bu, kullanıcının birden fazla deneme yaptığı ve sonunda başardığı durumdur.
            if ($payment->status === 'failed' && $status === 'success') {
                Log::info('✅ PayTR callback: Failed payment için success callback geldi - işlenecek', [
                    'payment_id' => $payment->payment_id,
                    'merchant_oid' => $merchantOid,
                ]);
                // Devam et, success işlenecek
            }

            // 4. Hash kontrolü (güvenlik)
            if (!$this->verifyHash($callbackData, $payment)) {
                Log::error('❌ PayTR callback: Hash doğrulama hatası', [
                    'payment_id' => $payment->payment_id,
                    'expected_hash' => $hash,
                ]);
                return ['success' => false, 'message' => 'Hash doğrulama hatası'];
            }

            // 5. Başarısız ödeme kontrolü (tutar kontrolünden ÖNCE!)
            // PayTR failed callback'lerinde total_amount=0 gelir, bu normal
            if ($status === 'failed') {
                Log::info('⚠️ PayTR callback: Ödeme başarısız', [
                    'payment_id' => $payment->payment_id,
                    'reason' => $callbackData['failed_reason_msg'] ?? 'Bilinmiyor',
                ]);
                $this->handleFailedPayment($payment, $callbackData);
                return ['success' => true, 'message' => 'Failed payment processed'];
            }

            // 6. Tutar kontrolü (sadece başarılı ödemeler için)
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

            // 7. Status'e göre işlem yap (artık sadece success gelir)
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

        // Send payment success email to customer
        $this->sendPaymentSuccessEmail($payment);

        // Send admin notification email
        $this->sendAdminPaymentNotification($payment, $callbackData);

        // Event dispatch (gelecekte: sms, notification)
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

        // Send payment failed email
        $this->sendPaymentFailedEmail($payment, $callbackData);

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

    /**
     * Send payment success email to user
     */
    private function sendPaymentSuccessEmail(Payment $payment): void
    {
        try {
            // Get user from payable (ShopOrder, Subscription, etc.)
            $user = $this->getUserFromPayment($payment);
            if (!$user || !$user->email) {
                Log::warning('⚠️ Payment success email: User not found', ['payment_id' => $payment->payment_id]);
                return;
            }

            // Get mail template
            $templateService = app(MailTemplateService::class);
            $template = $templateService->getTemplate('payment_success');

            if (!$template) {
                Log::warning('⚠️ Payment success email: Template not found');
                return;
            }

            // Prepare variables
            $variables = [
                'user_name' => $user->name,
                'payment_number' => $payment->payment_number,
                'amount' => number_format($payment->amount, 2),
                'currency' => $payment->currency ?? 'TL',
                'paid_at' => $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i'),
                'payable_description' => $this->getPayableDescription($payment),
                'site_name' => setting('site_name', config('app.name')),
            ];

            // Get locale and render content
            $locale = app()->getLocale();
            $subject = $templateService->renderContent($template->getSubjectForLocale($locale), $variables);
            $content = $templateService->renderContent($template->getContentForLocale($locale), $variables);

            // Send email using PaymentMail Mailable
            Mail::to($user->email)->send(new PaymentMail($subject, $content, $user->email));

            Log::channel('daily')->info('✅ Payment success email sent', [
                'payment_id' => $payment->payment_id,
                'user_email' => $user->email,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Payment success email error', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment failed email to user
     */
    private function sendPaymentFailedEmail(Payment $payment, array $callbackData): void
    {
        try {
            // Get user from payable (ShopOrder, Subscription, etc.)
            $user = $this->getUserFromPayment($payment);
            if (!$user || !$user->email) {
                Log::warning('⚠️ Payment failed email: User not found', ['payment_id' => $payment->payment_id]);
                return;
            }

            // Get mail template
            $templateService = app(MailTemplateService::class);
            $template = $templateService->getTemplate('payment_failed');

            if (!$template) {
                Log::warning('⚠️ Payment failed email: Template not found');
                return;
            }

            // Get failure reason from callback
            $failureReason = $callbackData['failed_reason_msg'] ??
                            $callbackData['failed_reason_code'] ??
                            'Bilinmeyen hata';

            // Prepare variables
            $variables = [
                'user_name' => $user->name,
                'payment_number' => $payment->payment_number,
                'amount' => number_format($payment->amount, 2),
                'currency' => $payment->currency ?? 'TL',
                'failed_at' => now()->format('d.m.Y H:i'),
                'payable_description' => $this->getPayableDescription($payment),
                'failure_reason' => $failureReason,
                'site_name' => setting('site_name', config('app.name')),
            ];

            // Get locale and render content
            $locale = app()->getLocale();
            $subject = $templateService->renderContent($template->getSubjectForLocale($locale), $variables);
            $content = $templateService->renderContent($template->getContentForLocale($locale), $variables);

            // Send email using PaymentMail Mailable
            Mail::to($user->email)->send(new PaymentMail($subject, $content, $user->email));

            Log::channel('daily')->info('✅ Payment failed email sent', [
                'payment_id' => $payment->payment_id,
                'user_email' => $user->email,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Payment failed email error', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send admin notification email about new payment
     */
    private function sendAdminPaymentNotification(Payment $payment, array $callbackData): void
    {
        try {
            // Get admin email from settings or fallback
            $adminEmail = setting('admin_email') ?: setting('site_email') ?: 'ferhat@turkbilisim.com.tr';
            if (!$adminEmail) {
                Log::warning('⚠️ Admin payment notification: Admin email not configured');
                return;
            }

            // Get mail template
            $templateService = app(MailTemplateService::class);
            $template = $templateService->getTemplate('payment_admin_notification');

            if (!$template) {
                Log::warning('⚠️ Admin payment notification: Template not found');
                return;
            }

            // Get user and billing info
            $user = $this->getUserFromPayment($payment);
            $billingInfo = $this->getBillingInfoHtml($user);

            // Prepare variables
            $variables = [
                'merchant_oid' => $callbackData['merchant_oid'] ?? $payment->gateway_transaction_id ?? '-',
                'amount' => number_format($payment->amount, 2),
                'currency' => $payment->currency ?? 'TL',
                'paid_at' => $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i'),
                'billing_info' => $billingInfo,
                'site_name' => setting('site_name', config('app.name')),
            ];

            // Get locale and render content
            $locale = app()->getLocale();
            $subject = $templateService->renderContent($template->getSubjectForLocale($locale), $variables);
            $content = $templateService->renderContent($template->getContentForLocale($locale), $variables);

            // Send email
            Mail::to($adminEmail)->send(new PaymentMail($subject, $content, $adminEmail));

            Log::channel('daily')->info('✅ Admin payment notification sent', [
                'payment_id' => $payment->payment_id,
                'admin_email' => $adminEmail,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Admin payment notification error', [
                'payment_id' => $payment->payment_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get billing info HTML based on user's billing profile
     */
    private function getBillingInfoHtml($user): string
    {
        if (!$user) {
            return '<p style="color: #666; font-size: 14px;">Kullanıcı bilgisi bulunamadı</p>';
        }

        // Try to get billing profile from tenant database (where billing_profiles are stored)
        $billingProfile = DB::table('billing_profiles')
            ->where('user_id', $user->id)
            ->where('is_default', true)
            ->whereNull('deleted_at')
            ->first();

        // If no default, get any
        if (!$billingProfile) {
            $billingProfile = DB::table('billing_profiles')
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->first();
        }

        $html = '<table style="width: 100%;">';

        // Ad Soyad
        $name = $billingProfile->contact_name ?? $user->name ?? '-';
        $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px; width: 40%;">Ad Soyad:</td>';
        $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($name) . '</td></tr>';

        if ($billingProfile && $billingProfile->type === 'corporate') {
            // Kurumsal
            $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">Müşteri Tipi:</td>';
            $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">🏢 Kurumsal</td></tr>';

            $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">Şirket:</td>';
            $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($billingProfile->company_name ?? '-') . '</td></tr>';

            $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">Vergi No:</td>';
            $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($billingProfile->tax_number ?? '-') . '</td></tr>';

            $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">Vergi Dairesi:</td>';
            $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($billingProfile->tax_office ?? '-') . '</td></tr>';
        } else {
            // Bireysel
            $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">Müşteri Tipi:</td>';
            $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">👤 Bireysel</td></tr>';

            $identityNumber = $billingProfile->identity_number ?? '-';
            $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">TC Kimlik No:</td>';
            $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($identityNumber) . '</td></tr>';
        }

        // Telefon
        $phone = $billingProfile->contact_phone ?? $user->phone ?? '-';
        $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">Telefon:</td>';
        $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($phone) . '</td></tr>';

        // Email
        $email = $billingProfile->contact_email ?? $user->email ?? '-';
        $html .= '<tr><td style="padding: 5px 0; color: #166534; font-size: 14px;">E-posta:</td>';
        $html .= '<td style="padding: 5px 0; color: #15803d; font-size: 14px; font-weight: 600;">' . e($email) . '</td></tr>';

        $html .= '</table>';

        return $html;
    }

    /**
     * Get user from payment's payable model
     */
    private function getUserFromPayment(Payment $payment)
    {
        $payable = $payment->payable;

        if (!$payable) {
            return null;
        }

        // Try to get user from payable
        if (method_exists($payable, 'user') && $payable->user) {
            return $payable->user;
        }

        // If payable has user_id property
        if (property_exists($payable, 'user_id') && $payable->user_id) {
            return \App\Models\User::find($payable->user_id);
        }

        return null;
    }

    /**
     * Get human-readable description of payable
     */
    private function getPayableDescription(Payment $payment): string
    {
        $payable = $payment->payable;

        if (!$payable) {
            return 'Ödeme';
        }

        // ShopOrder için
        if (method_exists($payable, 'order_number')) {
            return 'Sipariş No: ' . $payable->order_number;
        }

        // Subscription için
        if (method_exists($payable, 'subscription_plan')) {
            $plan = $payable->subscription_plan;
            return $plan ? $plan->name : 'Abonelik';
        }

        // Fallback
        return class_basename(get_class($payable));
    }
}
