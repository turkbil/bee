<?php

namespace App\Helpers;

use App\Services\TokenService;
use App\Models\Tenant;

class TokenHelper
{
    /**
     * Token servisi instance
     */
    private static function getTokenService(): TokenService
    {
        return TokenService::getInstance();
    }
    
    /**
     * Token miktarını formatla
     */
    public static function format(int $amount): string
    {
        return self::getTokenService()->formatTokenAmount($amount);
    }
    
    /**
     * Kalan token miktarını al
     */
    public static function remaining(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getRemainingTokens($tenant);
    }
    
    /**
     * Formatlanmış kalan token miktarını al
     */
    public static function remainingFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::remaining($tenant));
    }
    
    /**
     * Bugün kullanılan token miktarını al
     */
    public static function todayUsage(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getTodayUsage($tenant);
    }
    
    /**
     * Formatlanmış bugün kullanılan token miktarını al
     */
    public static function todayUsageFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::todayUsage($tenant));
    }
    
    /**
     * Günlük ortalama kullanımı al
     */
    public static function dailyAverage(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getDailyAverage($tenant);
    }
    
    /**
     * Formatlanmış günlük ortalama kullanımı al
     */
    public static function dailyAverageFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::dailyAverage($tenant));
    }
    
    /**
     * Toplam satın alınan token miktarını al
     */
    public static function totalPurchased(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getTotalPurchasedTokens($tenant);
    }
    
    /**
     * Formatlanmış toplam satın alınan token miktarını al
     */
    public static function totalPurchasedFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::totalPurchased($tenant));
    }
    
    /**
     * Toplam kullanılan token miktarını al
     */
    public static function totalUsed(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getTotalUsedTokens($tenant);
    }
    
    /**
     * Formatlanmış toplam kullanılan token miktarını al
     */
    public static function totalUsedFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::totalUsed($tenant));
    }
    
    /**
     * Aylık kullanım limitini al
     */
    public static function monthlyLimit(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getTenantMonthlyLimit($tenant);
    }
    
    /**
     * Formatlanmış aylık kullanım limitini al
     */
    public static function monthlyLimitFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::monthlyLimit($tenant));
    }
    
    /**
     * Bu ay kullanılan token miktarını al
     */
    public static function monthlyUsage(?Tenant $tenant = null): int
    {
        return self::getTokenService()->getTenantMonthlyUsage($tenant);
    }
    
    /**
     * Formatlanmış bu ay kullanılan token miktarını al
     */
    public static function monthlyUsageFormatted(?Tenant $tenant = null): string
    {
        return self::format(self::monthlyUsage($tenant));
    }
    
    /**
     * Kullanım yüzdesini al
     */
    public static function usagePercentage(?Tenant $tenant = null): float
    {
        return self::getTokenService()->getUsagePercentage($tenant);
    }
    
    /**
     * Bugünkü kullanım yüzdesini al
     */
    public static function todayUsagePercentage(?Tenant $tenant = null): float
    {
        return self::getTokenService()->getTodayUsagePercentage($tenant);
    }
    
    /**
     * Günlük ortalama kullanım yüzdesini al
     */
    public static function dailyAveragePercentage(?Tenant $tenant = null): float
    {
        return self::getTokenService()->getDailyAveragePercentage($tenant);
    }
    
    /**
     * Kampanya durumunu kontrol et
     */
    public static function isCampaignActive(): bool
    {
        return self::getTokenService()->isCampaignActive();
    }
    
    /**
     * Kampanya bilgisini al
     */
    public static function campaignInfo(): array
    {
        return self::getTokenService()->getCampaignInfo();
    }
    
    /**
     * Kampanya çarpanını al
     */
    public static function campaignMultiplier(): float
    {
        return self::getTokenService()->getCampaignMultiplier();
    }
    
    /**
     * Token miktarını kampanya çarpanıyla hesapla
     */
    public static function withCampaign(int $amount): int
    {
        return self::getTokenService()->getRawTokenAmount($amount);
    }
    
    /**
     * Formatlanmış kampanya token miktarını al
     */
    public static function withCampaignFormatted(int $amount): string
    {
        return self::format(self::withCampaign($amount));
    }
    
    /**
     * Cache'leri temizle
     */
    public static function clearCaches(?Tenant $tenant = null): void
    {
        self::getTokenService()->clearCaches($tenant);
    }
    
    /**
     * Token durumu özeti al
     */
    public static function getSummary(?Tenant $tenant = null): array
    {
        return [
            'remaining' => self::remaining($tenant),
            'remaining_formatted' => self::remainingFormatted($tenant),
            'today_usage' => self::todayUsage($tenant),
            'today_usage_formatted' => self::todayUsageFormatted($tenant),
            'daily_average' => self::dailyAverage($tenant),
            'daily_average_formatted' => self::dailyAverageFormatted($tenant),
            'total_purchased' => self::totalPurchased($tenant),
            'total_purchased_formatted' => self::totalPurchasedFormatted($tenant),
            'total_used' => self::totalUsed($tenant),
            'total_used_formatted' => self::totalUsedFormatted($tenant),
            'monthly_limit' => self::monthlyLimit($tenant),
            'monthly_limit_formatted' => self::monthlyLimitFormatted($tenant),
            'monthly_usage' => self::monthlyUsage($tenant),
            'monthly_usage_formatted' => self::monthlyUsageFormatted($tenant),
            'usage_percentage' => self::usagePercentage($tenant),
            'today_usage_percentage' => self::todayUsagePercentage($tenant),
            'daily_average_percentage' => self::dailyAveragePercentage($tenant),
            'campaign_info' => self::campaignInfo()
        ];
    }
}