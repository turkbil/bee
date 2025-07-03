<?php

namespace App\Contracts\AI;

interface TokenManagerInterface
{
    /**
     * Belirtilen token miktarının kullanılabilir olup olmadığını kontrol et
     */
    public function canUseTokens(string $tenantId, int $tokensNeeded): bool;

    /**
     * Token kullanımını kaydet
     */
    public function recordTokenUsage(
        string $tenantId, 
        int $tokensUsed, 
        string $usageType = 'general',
        string $moduleContext = null,
        array $metadata = []
    ): bool;

    /**
     * Kalan token sayısını getir
     */
    public function getRemainingTokens(string $tenantId): int;

    /**
     * Günlük token kullanımını getir
     */
    public function getDailyUsage(string $tenantId): int;

    /**
     * Aylık token kullanımını getir
     */
    public function getMonthlyUsage(string $tenantId): int;

    /**
     * Token kullanım geçmişini getir
     */
    public function getUsageHistory(string $tenantId, int $limit = 50): array;

    /**
     * Token paket satın alımını kaydet
     */
    public function purchaseTokenPackage(string $tenantId, int $tokenAmount, float $price): bool;

    /**
     * Token limitlerini sıfırla (aylık reset için)
     */
    public function resetLimits(string $tenantId): bool;

    /**
     * Aylık token limitini getir
     */
    public function getMonthlyLimit(string $tenantId): int;
}