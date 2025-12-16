<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Tenant;

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
class Tenant1001SubscriptionHelper
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
     *
     * @return array
     */
    public static function getAvailablePlans(): array
    {
        // Database'den subscription plans çek (tenant-aware)
        // Şimdilik static, sonra DB'den çekilecek
        return [
            [
                'name' => 'Ücretsiz',
                'price' => '0 TL',
                'duration' => 'Süresiz',
                'features' => self::getFreeFeatures(),
            ],
            [
                'name' => 'Aylık Premium',
                'price' => '29.90 TL',
                'duration' => '1 Ay',
                'features' => self::getPremiumFeatures(),
            ],
            [
                'name' => 'Yıllık Premium',
                'price' => '299 TL',
                'duration' => '12 Ay',
                'features' => array_merge(self::getPremiumFeatures(), ['🎁 2 ay hediye!']),
            ],
        ];
    }

    /**
     * Abonelik paketlerini AI context formatında döndür
     *
     * @return string
     */
    public static function getPricingContext(): string
    {
        $plans = self::getAvailablePlans();
        $context = "**ABONELİK PAKETLERİ:**\n\n";

        foreach ($plans as $plan) {
            $context .= "### {$plan['name']} - {$plan['price']}/{$plan['duration']}\n";
            foreach ($plan['features'] as $feature) {
                $context .= "- {$feature}\n";
            }
            $context .= "\n";
        }

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
