<?php

declare(strict_types=1);

namespace Modules\Subscription\App\Services;

use Modules\Subscription\App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Log;

/**
 * Subscription-Cart Bridge Service
 *
 * Provides integration between Subscription and Cart modules
 * Prepares subscription data for cart operations
 */
class SubscriptionCartBridge
{
    /**
     * Prepare subscription plan for adding to cart
     *
     * @param SubscriptionPlan $plan
     * @param string $cycleKey
     * @param bool $autoRenew
     * @return array Cart options array
     */
    public function prepareSubscriptionForCart(SubscriptionPlan $plan, string $cycleKey, bool $autoRenew = true): array
    {
        // Cycle bilgisini al
        $cycle = $plan->getCycle($cycleKey);

        if (!$cycle) {
            throw new \Exception("Geçersiz süre seçimi: {$cycleKey}");
        }

        // Display bilgileri
        $displayInfo = $this->getSubscriptionDisplayInfo($plan, $cycle);

        // Fiyat bilgileri
        $priceInfo = $this->getSubscriptionPriceInfo($plan, $cycle);

        // Abonelik bilgileri
        $subscriptionInfo = $this->getSubscriptionInfo($plan, $cycle, $cycleKey, $autoRenew);

        // Metadata (JSON - cart view'da cycle bilgisi için)
        $metadata = [
            'cycle_label' => $cycle['label'],
            'duration_days' => $cycle['duration_days'],
            'trial_days' => $cycle['trial_days'] ?? null,
        ];

        // Merge all data
        return array_merge($displayInfo, $priceInfo, $subscriptionInfo, ['metadata' => $metadata]);
    }

    /**
     * Get subscription display information
     *
     * @param SubscriptionPlan $plan
     * @param array $cycle
     * @return array
     */
    protected function getSubscriptionDisplayInfo(SubscriptionPlan $plan, array $cycle): array
    {
        // Cycle label
        $cycleLabel = $cycle['label']['tr'] ?? $cycle['label']['en'] ?? 'Bilinmeyen';

        // Item title (Plan adı + Cycle)
        $itemTitle = $plan->getTranslated('title', app()->getLocale()) . ' - ' . $cycleLabel;

        // Image (subscription planlarında görsel yoksa default icon kullan)
        $itemImage = null;
        if (method_exists($plan, 'hasMedia') && $plan->hasMedia('hero')) {
            $itemImage = thumb($plan->getFirstMedia('hero'), 200, 200, ['quality' => 85, 'format' => 'webp']);
        }

        Log::info('🛒 SubscriptionCartBridge - Display Info', [
            'plan_id' => $plan->subscription_plan_id,
            'item_title' => $itemTitle,
            'has_image' => !is_null($itemImage),
        ]);

        return [
            'item_title' => $itemTitle,
            'item_image' => $itemImage,
            'item_sku' => 'SUB-' . $plan->subscription_plan_id . '-' . strtoupper($cycle['key'] ?? 'unknown'),
        ];
    }

    /**
     * Get subscription price information
     *
     * @param SubscriptionPlan $plan
     * @param array $cycle
     * @return array
     */
    protected function getSubscriptionPriceInfo(SubscriptionPlan $plan, array $cycle): array
    {
        // Cycle'dan fiyat al (KDV dahil!)
        $priceWithTax = $cycle['price'] ?? 0;
        $comparePrice = $cycle['compare_price'] ?? null;

        // Compare price sadece görsel gösterim için - cart hesabına dahil edilmez!
        $discountAmount = 0;

        // Currency (default TRY)
        $currency = $plan->currency ?? 'TRY';

        // Tax rate
        $taxRate = $plan->tax_rate ?? 20;

        // Subscription fiyatları KDV DAHİL gelir!
        // KDV'yi ayrıştır: KDV hariç fiyat = KDV dahil fiyat / (1 + KDV oranı)
        $priceWithoutTax = $priceWithTax / (1 + ($taxRate / 100));

        Log::info('🛒 SubscriptionCartBridge - Price Info', [
            'plan_id' => $plan->subscription_plan_id,
            'price_with_tax' => $priceWithTax,
            'price_without_tax' => $priceWithoutTax,
            'tax_rate' => $taxRate,
            'note' => 'Subscription fiyatları KDV dahil - KDV ayrıştırıldı',
            'compare_price' => $comparePrice,
            'discount_amount' => $discountAmount,
        ]);

        return [
            'unit_price' => $priceWithoutTax, // KDV hariç fiyat
            'compare_price' => $comparePrice, // Sadece UI için
            'currency' => $currency,
            'discount_amount' => $discountAmount,
            'tax_rate' => $taxRate, // %20
        ];
    }

    /**
     * Get subscription-specific information
     *
     * @param SubscriptionPlan $plan
     * @param array $cycle
     * @param string $cycleKey
     * @param bool $autoRenew
     * @return array
     */
    protected function getSubscriptionInfo(SubscriptionPlan $plan, array $cycle, string $cycleKey, bool $autoRenew): array
    {
        return [
            'cycle_key' => $cycleKey,
            'cycle_label' => $cycle['label'],
            'duration_days' => $cycle['duration_days'],
            'trial_days' => $cycle['trial_days'] ?? null,
            'auto_renew' => (bool) $autoRenew,
            'display_description' => $plan->getTranslated('description', app()->getLocale()),
        ];
    }

    /**
     * Check if subscription can be added to cart
     *
     * @param SubscriptionPlan $plan
     * @return bool
     */
    public function canAddToCart(SubscriptionPlan $plan): bool
    {
        // Plan aktif mi?
        if (!$plan->is_active) {
            return false;
        }

        // Plan public mi?
        if (!$plan->is_public) {
            return false;
        }

        return true;
    }

    /**
     * Get cart item error messages
     *
     * @param SubscriptionPlan $plan
     * @return array
     */
    public function getCartItemErrors(SubscriptionPlan $plan): array
    {
        $errors = [];

        if (!$plan->is_active) {
            $errors[] = 'Bu üyelik planı şu anda aktif değil.';
        }

        if (!$plan->is_public) {
            $errors[] = 'Bu üyelik planı özel bir plandır.';
        }

        return $errors;
    }
}
