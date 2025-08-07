<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\LanguageManagement\app\Models\TenantLanguage;

/**
 * Dynamic Language Provider Service
 * 
 * Eliminates ALL hardcoded language structures across the system.
 * Provides tenant-specific, database-driven language management.
 */
readonly class TenantLanguageProvider
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'tenant_lang_';

    /**
     * Get all active languages for current tenant
     */
    public static function getActiveLanguages(): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'active_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return TenantLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get language codes only (for loops, validation, etc.)
     */
    public static function getActiveLanguageCodes(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'codes_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return TenantLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->toArray();
        });
    }

    /**
     * Get RTL languages
     */
    public static function getRtlLanguages(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'rtl_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return TenantLanguage::where('is_active', true)
                ->where('direction', 'rtl')
                ->pluck('code')
                ->toArray();
        });
    }

    /**
     * Get language native names (for display)
     */
    public static function getLanguageNativeNames(): array
    {
        $cacheKey = self::CACHE_PREFIX . 'native_names_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return TenantLanguage::where('is_active', true)
                ->pluck('native_name', 'code')
                ->toArray();
        });
    }

    /**
     * Get default language
     */
    public static function getDefaultLanguageCode(): string
    {
        $cacheKey = self::CACHE_PREFIX . 'default_' . self::getTenantCacheKey();
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $defaultLang = TenantLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->first();
            return $defaultLang?->code ?? 'tr';
        });
    }

    /**
     * Validate language code against active languages
     */
    public static function isValidLanguageCode(string $code): bool
    {
        return in_array($code, self::getActiveLanguageCodes());
    }

    /**
     * Check if language is RTL
     */
    public static function isRtl(string $languageCode): bool
    {
        return in_array($languageCode, self::getRtlLanguages());
    }

    /**
     * Clear all language caches for current tenant
     */
    public static function clearCache(): void
    {
        $tenantKey = self::getTenantCacheKey();
        $patterns = [
            self::CACHE_PREFIX . 'active_' . $tenantKey,
            self::CACHE_PREFIX . 'codes_' . $tenantKey,
            self::CACHE_PREFIX . 'rtl_' . $tenantKey,
            self::CACHE_PREFIX . 'native_names_' . $tenantKey,
            self::CACHE_PREFIX . 'default_' . $tenantKey,
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get current tenant cache key
     */
    private static function getTenantCacheKey(): string
    {
        // Try to get tenant from current context
        if (function_exists('tenant') && tenant()) {
            return (string) tenant()->getTenantKey();
        }
        
        return 'central';
    }
}