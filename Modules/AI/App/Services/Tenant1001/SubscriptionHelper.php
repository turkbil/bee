<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Tenant1001;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Tenant 1001 (muzibu.com.tr) Subscription Helper
 *
 * Kullanıcının abonelik durumunu kontrol eder ve AI'ya context bilgisi sağlar.
 *
 * Subscription Status:
 * - 'guest' veya 'none': Üye değil
 * - 'free': Üye ama premium değil
 * - 'premium': Premium üye
 *
 * @package Modules\AI\App\Services\Tenant
 * @version 1.0
 */
class SubscriptionHelper
{
    /**
     * Kullanıcının abonelik durumunu döndürür
     *
     * @param User|null $user Kullanıcı (null ise guest)
     * @return array{
     *     status: string,
     *     is_premium: bool,
     *     days_remaining: int|null,
     *     plan_name: string|null,
     *     features: array
     * }
     */
    public static function getSubscriptionStatus(?User $user): array
    {
        // Kullanıcı yoksa guest
        if (!$user) {
            return [
                'status' => 'guest',
                'is_premium' => false,
                'days_remaining' => null,
                'plan_name' => null,
                'features' => [],
                'message' => 'Üye olmadan şarkıları dinleyemezsin. Hemen üye ol! 😊',
                'cta' => 'Üye Ol',
                'cta_url' => '/register',
            ];
        }

        // Kullanıcının aktif aboneliğini kontrol et
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        // Abonelik yoksa veya süresi dolmuşsa → Free user
        if (!$subscription) {
            return [
                'status' => 'free',
                'is_premium' => false,
                'days_remaining' => null,
                'plan_name' => 'Ücretsiz',
                'features' => [
                    'Sınırlı dinleme',
                    'Reklamlı',
                    'Temel kalite',
                ],
                'message' => 'Premium\'a geçersen reklamsız ve offline dinleyebilirsin! 🚀',
                'cta' => 'Premium\'a Geç',
                'cta_url' => '/pricing',
            ];
        }

        // Abonelik var → Premium user
        $daysRemaining = now()->diffInDays($subscription->ends_at, false);
        $daysRemaining = max(0, (int)$daysRemaining); // Negatif değer olmasın

        return [
            'status' => 'premium',
            'is_premium' => true,
            'days_remaining' => $daysRemaining,
            'plan_name' => $subscription->plan_name ?? 'Premium',
            'features' => [
                'Sınırsız dinleme',
                'Reklamsız',
                'Offline indirme',
                'HD kalite ses',
                'Sınırsız atlama',
            ],
            'message' => "Premium aboneliğin {$daysRemaining} gün daha geçerli! 🎉",
            'cta' => null,
            'cta_url' => null,
        ];
    }

    /**
     * AI context için abonelik bilgisini formatla
     *
     * @param User|null $user
     * @return string
     */
    public static function getContextMessage(?User $user): string
    {
        $status = self::getSubscriptionStatus($user);

        $context = "**KULLANICI ABONELİK DURUMU:**\n";
        $context .= "- Durum: {$status['status']}\n";
        $context .= "- Premium: " . ($status['is_premium'] ? 'Evet' : 'Hayır') . "\n";

        if ($status['days_remaining'] !== null) {
            $context .= "- Kalan Gün: {$status['days_remaining']} gün\n";
        }

        if ($status['plan_name']) {
            $context .= "- Plan: {$status['plan_name']}\n";
        }

        $context .= "\n**KULLANICI ÖZELLİKLERİ:**\n";
        foreach ($status['features'] as $feature) {
            $context .= "- {$feature}\n";
        }

        if ($status['message']) {
            $context .= "\n**AI MESAJI:** {$status['message']}\n";
        }

        if ($status['cta']) {
            $context .= "**ACTION:** [{$status['cta']}]({$status['cta_url']})\n";
        }

        return $context;
    }

    /**
     * Kullanıcı premium mi kontrol et (hızlı kontrol)
     *
     * @param User|null $user
     * @return bool
     */
    public static function isPremium(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();
    }

    /**
     * Kullanıcı üye mi kontrol et (hızlı kontrol)
     *
     * @param User|null $user
     * @return bool
     */
    public static function isMember(?User $user): bool
    {
        return $user !== null;
    }

    /**
     * Premium özellikleri listele (AI'ya context için)
     *
     * @return array
     */
    public static function getPremiumFeatures(): array
    {
        return [
            '✅ Sınırsız dinleme',
            '✅ Reklamsız deneyim',
            '✅ Offline indirme',
            '✅ HD kalite ses (320kbps)',
            '✅ Sınırsız atlama',
            '✅ Özel playlist\'ler',
            '✅ Öncelikli destek',
        ];
    }

    /**
     * Free özellikleri listele (AI'ya context için)
     *
     * @return array
     */
    public static function getFreeFeatures(): array
    {
        return [
            '🎵 Sınırlı dinleme (günde 10 şarkı)',
            '📢 Reklamlı',
            '🎧 Standart kalite (128kbps)',
            '⏭️ 5 atlama/saat',
        ];
    }

    /**
     * Abonelik paketlerini listele (AI'ya context için)
     * Database'den gerçek fiyatları çeker (tenant-aware)
     *
     * @return array
     */
    public static function getAvailablePlans(): array
    {
        $plans = [];

        // Ücretsiz plan (her zaman var)
        $plans[] = [
            'name' => 'Ücretsiz',
            'price' => '0 TL',
            'duration' => 'Süresiz',
            'features' => self::getFreeFeatures(),
            'price_with_tax' => '0 TL',
            'price_without_tax' => '0 TL',
            'tax_info' => 'KDV yok',
        ];

        try {
            // Database'den aktif ve public planları çek (tenant-aware)
            $dbPlans = \Modules\Subscription\App\Models\SubscriptionPlan::active()
                ->public()
                ->ordered()
                ->get();

            foreach ($dbPlans as $plan) {
                // Trial planları atla (deneme üyeliği AI'da gösterilmez)
                if ($plan->is_trial) {
                    continue;
                }

                // Her cycle için ayrı plan göster
                $sortedCycles = $plan->getSortedCycles();

                foreach ($sortedCycles as $cycleKey => $cycle) {
                    // Fiyat bilgilerini al
                    $basePrice = $plan->getCycleBasePrice($cycleKey); // KDV Hariç
                    $priceWithTax = $plan->getCyclePriceWithTax($cycleKey); // KDV Dahil
                    $taxRate = $plan->tax_rate ?? 20.0;

                    // Cycle adını Türkçeleştir
                    $cycleName = $cycleKey;
                    $duration = $cycle['duration_days'] ?? 30;

                    if ($cycleKey === 'aylik') {
                        $cycleName = 'Aylık';
                    } elseif ($cycleKey === 'yillik') {
                        $cycleName = 'Yıllık';
                    } elseif ($cycleKey === '15-gunluk') {
                        $cycleName = '15 Günlük';
                    } elseif ($cycleKey === '6-aylik') {
                        $cycleName = '6 Aylık';
                    } else {
                        // Genel format: "30 Günlük", "90 Günlük"
                        $cycleName = $duration . ' Günlük';
                    }

                    // Plan adı: "Premium Aylık", "Premium Yıllık"
                    $planName = $plan->titleText . ' - ' . $cycleName;

                    // Özellikler
                    $features = self::getPremiumFeatures();

                    // Yıllık plan için ekstra bonus
                    if ($cycleKey === 'yillik') {
                        $features[] = '🎁 En avantajlı seçenek!';
                    }

                    $plans[] = [
                        'name' => $planName,
                        'price' => number_format($basePrice, 0, '', '') . ' TRY',  // AI için: "4000 TRY" (binlik ayraç YOK!)
                        'price_with_tax' => number_format($priceWithTax, 0, '', '') . ' TRY',
                        'price_without_tax' => number_format($basePrice, 0, '', '') . ' TRY',
                        'tax_info' => 'KDV %' . $taxRate . ' (' . number_format($priceWithTax - $basePrice, 0, '', '') . ' TRY)',
                        'duration' => $duration . ' gün',
                        'features' => $features,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda fallback (statik bilgi - gerçek fiyatlar!)
            \Log::error('Tenant1001SubscriptionHelper::getAvailablePlans error: ' . $e->getMessage());

            $plans[] = [
                'name' => 'Premium - Aylık',
                'price' => '600 TRY',  // Gerçek fiyat (binlik ayraç YOK!)
                'price_with_tax' => '720 TRY',
                'price_without_tax' => '600 TRY',
                'tax_info' => 'KDV %20 (120 TRY)',
                'duration' => '30 gün',
                'features' => self::getPremiumFeatures(),
            ];

            $plans[] = [
                'name' => 'Premium - Yıllık',
                'price' => '4000 TRY',  // Gerçek fiyat (binlik ayraç YOK!)
                'price_with_tax' => '4800 TRY',
                'price_without_tax' => '4000 TRY',
                'tax_info' => 'KDV %20 (800 TRY)',
                'duration' => '365 gün',
                'features' => array_merge(self::getPremiumFeatures(), ['🎁 En avantajlı seçenek!']),
            ];
        }

        return $plans;
    }

    /**
     * Abonelik paketlerini AI context formatında döndür (Card Format)
     *
     * @return string
     */
    public static function getPricingContext(): string
    {
        $plans = self::getAvailablePlans();
        $context = "**ABONELİK PAKETLERİ:**\n\n";
        $context .= "*Tüm paketlerimiz aşağıda card formatında listelenmiştir. Fiyatlar KDV dahil ve KDV hariç olarak ayrıca belirtilmiştir.*\n\n";
        $context .= "---\n\n";

        foreach ($plans as $plan) {
            // Card başlık
            $context .= "### 🎵 {$plan['name']}\n\n";

            // Fiyat bilgisi (KDV Dahil vurgulanır)
            if (isset($plan['price_with_tax']) && $plan['price'] !== '0 TL') {
                $context .= "**💰 Fiyat:**\n";
                $context .= "- **KDV Dahil:** {$plan['price_with_tax']}\n";
                $context .= "- KDV Hariç: {$plan['price_without_tax']}\n";
                $context .= "- {$plan['tax_info']}\n\n";
            } else {
                $context .= "**💰 Fiyat:** {$plan['price']}\n\n";
            }

            // Süre
            $context .= "**⏱️ Süre:** {$plan['duration']}\n\n";

            // Özellikler
            $context .= "**✨ Özellikler:**\n";
            foreach ($plan['features'] as $feature) {
                $context .= "- {$feature}\n";
            }

            $context .= "\n---\n\n";
        }

        $context .= "*Premium paketlerimiz hakkında daha fazla bilgi için [buraya tıklayın](/pricing).*\n";

        return $context;
    }

    /**
     * Kullanıcının cihaz bilgilerini döndür
     *
     * @param User|null $user
     * @return array{device_count: int, device_limit: int, can_add: bool}
     */
    public static function getDeviceInfo(?User $user): array
    {
        if (!$user) {
            return [
                'device_count' => 0,
                'device_limit' => 0,
                'can_add' => false,
            ];
        }

        // Aktif abonelik kontrolü
        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if (!$subscription || !$subscription->plan) {
            // Free user - limit 1
            $deviceCount = $user->devices()->count();
            return [
                'device_count' => $deviceCount,
                'device_limit' => 1,
                'can_add' => $deviceCount < 1,
            ];
        }

        // Premium user - plan'dan limit al
        $deviceLimit = $subscription->plan->device_limit ?? 3;
        $deviceCount = $user->devices()->count();

        return [
            'device_count' => $deviceCount,
            'device_limit' => $deviceLimit,
            'can_add' => $deviceCount < $deviceLimit,
        ];
    }

    /**
     * Kullanıcının kalan gün sayısını döndür
     *
     * @param User|null $user
     * @return int|null Null if no active subscription
     */
    public static function getDaysRemaining(?User $user): ?int
    {
        if (!$user) {
            return null;
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if (!$subscription) {
            return null;
        }

        $daysRemaining = now()->diffInDays($subscription->ends_at, false);
        return max(0, (int)$daysRemaining);
    }

    /**
     * Tüm planları fiyatlarıyla birlikte getir (alias for getAvailablePlans)
     *
     * @return array
     */
    public static function getPlansWithPrices(): array
    {
        return self::getAvailablePlans();
    }
}
