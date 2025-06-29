<?php

use Modules\LanguageManagement\app\Services\UrlPrefixService;
use Modules\LanguageManagement\app\Models\TenantLanguage;

if (!function_exists('getSupportedLanguageRegex')) {
    /**
     * Get regex pattern for supported languages
     * STATIC MEMORY CACHE ile bombardıman önlenir
     */
    function getSupportedLanguageRegex(): string
    {
        // Static memory cache - request boyunca sadece 1 kez hesaplanır
        static $cachedRegex = null;
        
        if ($cachedRegex !== null) {
            return $cachedRegex;
        }
        
        // Redis cache'den al veya oluştur - sadece ilk çağrıda
        $cachedRegex = \Illuminate\Support\Facades\Cache::remember('supported_language_regex', 3600, function () {
            // UrlPrefixService varsa oradan al
            if (class_exists('Modules\LanguageManagement\app\Services\UrlPrefixService')) {
                $locales = UrlPrefixService::getAvailableLocales();
                if (!empty($locales)) {
                    return implode('|', $locales);
                }
            }
            
            // Yoksa veritabanından al
            try {
                $languages = TenantLanguage::where('is_active', true)
                    ->pluck('code')
                    ->toArray();
                    
                if (!empty($languages)) {
                    return implode('|', $languages);
                }
            } catch (\Exception $e) {
                // Veritabanı yoksa
            }
            
            // Fallback - en azından temel diller
            return 'tr|en|ar|de|fr|es|ru|zh';
        });
        
        return $cachedRegex;
    }
}

if (!function_exists('clearLanguageRegexCache')) {
    /**
     * Clear language regex cache
     */
    function clearLanguageRegexCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('supported_language_regex');
    }
}

// Language fonksiyonları modül helper'ında tanımlı - çakışma önlendi

if (!function_exists('is_default_language')) {
    /**
     * Verilen dil varsayılan dil mi?
     */
    function is_default_language($locale, $context = 'site')
    {
        if ($context === 'admin') {
            return $locale === config('app.locale');
        }
        
        $tenant = tenant();
        if (!$tenant) {
            return $locale === config('app.locale');
        }
        
        return \Modules\LanguageManagement\app\Models\TenantLanguage::where('prefix', $locale)
            ->where('is_default', 1)
            ->exists();
    }
}

if (!function_exists('get_language_flag')) {
    /**
     * Dil için flag emoji döndür - Dinamik sistem
     * Önce database'den, sonra fallback listeden
     */
    function get_language_flag($locale, $context = 'site')
    {
        try {
            if ($context === 'admin') {
                // Admin dilleri için AdminLanguage tablosundan
                if (class_exists('Modules\LanguageManagement\app\Models\AdminLanguage')) {
                    $language = \Modules\LanguageManagement\app\Models\AdminLanguage::where('code', $locale)
                        ->where('is_active', true)
                        ->first();
                    
                    if ($language && $language->flag_icon) {
                        return $language->flag_icon;
                    }
                }
            } else {
                // Site dilleri için TenantLanguage tablosundan
                $tenant = tenant();
                if ($tenant && class_exists('Modules\LanguageManagement\app\Models\TenantLanguage')) {
                    $language = \Modules\LanguageManagement\app\Models\TenantLanguage::where('prefix', $locale)
                        ->where('status', 1)
                        ->first();
                    
                    if ($language && isset($language->flag_icon)) {
                        return $language->flag_icon;
                    }
                }
            }
        } catch (\Exception $e) {
            // Database hatası durumunda fallback kullan
        }
        
        // Fallback flag listesi - sadece database erişimi olmadığında
        $fallbackFlags = [
            'tr' => '🇹🇷',
            'en' => '🇺🇸', 
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'es' => '🇪🇸',
            'ru' => '🇷🇺',
            'zh' => '🇨🇳',
            'ja' => '🇯🇵',
            'ko' => '🇰🇷',
            'it' => '🇮🇹',
            'pt' => '🇵🇹',
            'nl' => '🇳🇱',
            'pl' => '🇵🇱',
            'sv' => '🇸🇪',
            'da' => '🇩🇰',
            'no' => '🇳🇴',
            'fi' => '🇫🇮'
        ];
        
        return $fallbackFlags[$locale] ?? '🌐';
    }
}