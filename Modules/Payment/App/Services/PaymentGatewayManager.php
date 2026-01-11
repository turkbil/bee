<?php

namespace Modules\Payment\App\Services;

// BankAccount model artık kullanılmıyor - Settings tabanlı sistem

/**
 * Payment Gateway Manager
 *
 * Checkout sayfasında hangi ödeme yöntemlerinin gösterileceğini yönetir.
 * Her gateway'in aktiflik durumu, tutar limitleri, credentials kontrolü burada yapılır.
 */
class PaymentGatewayManager
{
    /**
     * Checkout'ta gösterilecek aktif gateway listesi
     *
     * @param float $amount Sipariş tutarı
     * @return array Gateway listesi (sort_order'a göre sıralı)
     */
    public function getAvailableGateways(float $amount): array
    {
        $gateways = [];

        // PayTR kontrolü
        if ($this->isPayTRAvailable($amount)) {
            $gateways[] = [
                'code' => 'paytr',
                'name' => setting('paytr_display_name', 'Kredi Kartı ile Ödeme'),
                'description' => setting('paytr_description', 'Kredi kartı ile güvenli ödeme yapabilirsiniz.'),
                'logo_url' => setting('paytr_logo_url', '/images/payment/paytr-logo.png'),
                'sort_order' => (int) setting('paytr_sort_order', 1),
                'type' => 'online', // online, manual
            ];
        }

        // Havale/EFT kontrolü
        if ($this->isBankTransferAvailable($amount)) {
            $gateways[] = [
                'code' => 'bank_transfer',
                'name' => setting('bank_transfer_display_name', 'Havale / EFT'),
                'description' => setting('bank_transfer_description', 'Banka hesabımıza havale veya EFT yaparak ödeyebilirsiniz.'),
                'logo_url' => setting('bank_transfer_logo_url', '/images/payment/bank-icon.png'),
                'sort_order' => (int) setting('bank_transfer_sort_order', 10),
                'type' => 'manual',
            ];
        }

        // Sıralama (sort_order'a göre)
        usort($gateways, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        return $gateways;
    }

    /**
     * PayTR kullanılabilir mi?
     */
    public function isPayTRAvailable(float $amount): bool
    {
        // 1. Aktif mi?
        if (!setting('paytr_enabled', false)) {
            return false;
        }

        // 2. Credentials dolu mu?
        $merchantId = setting('paytr_merchant_id');
        $merchantKey = setting('paytr_merchant_key');
        $merchantSalt = setting('paytr_merchant_salt');

        if (empty($merchantId) || empty($merchantKey) || empty($merchantSalt)) {
            return false;
        }

        // 3. Tutar limiti uygun mu?
        $minAmount = (float) setting('paytr_min_amount', 10);
        $maxAmount = (float) setting('paytr_max_amount', 50000);

        if ($amount < $minAmount || $amount > $maxAmount) {
            return false;
        }

        return true;
    }

    /**
     * Havale/EFT kullanılabilir mi?
     */
    public function isBankTransferAvailable(float $amount): bool
    {
        // 1. Aktif mi?
        if (!setting('bank_transfer_enabled', false)) {
            return false;
        }

        // 2. En az 1 aktif banka hesabı var mı? (Settings tabanlı)
        $hasActiveBankAccount = false;
        for ($i = 1; $i <= 3; $i++) {
            $isActive = setting("payment_bank_{$i}_active");
            $iban = setting("payment_bank_{$i}_iban");

            if ($isActive && !empty($iban)) {
                $hasActiveBankAccount = true;
                break;
            }
        }

        if (!$hasActiveBankAccount) {
            return false;
        }

        return true;
    }

    /**
     * Belirli bir gateway kullanılabilir mi? (Form validation için)
     */
    public function isGatewayAvailable(string $gatewayCode, float $amount): bool
    {
        return match ($gatewayCode) {
            'paytr' => $this->isPayTRAvailable($amount),
            'bank_transfer' => $this->isBankTransferAvailable($amount),
            default => false,
        };
    }

    /**
     * Gateway'e göre service döndür (Factory Pattern)
     */
    public function getGatewayService(string $gatewayCode)
    {
        return match ($gatewayCode) {
            'paytr' => app(PayTRIframeService::class),
            'bank_transfer' => null, // Manuel ödeme, service yok
            default => throw new \Exception("Bilinmeyen gateway: {$gatewayCode}"),
        };
    }

    /**
     * Gateway display adı al (admin panelde gösterim için)
     */
    public function getGatewayName(string $gatewayCode): string
    {
        return match ($gatewayCode) {
            'paytr' => setting('paytr_display_name', 'PayTR'),
            'bank_transfer' => setting('bank_transfer_display_name', 'Havale/EFT'),
            default => ucfirst($gatewayCode),
        };
    }
}
