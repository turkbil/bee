<?php

// __() fonksiyonu kaldırıldı - Laravel 11 standartı __() kullanın

if (!function_exists('getSystemTranslation')) {
    /**
     * Sistem dili çevirisi getir
     */
    function getSystemTranslation(string $key, array $replace = [], string $locale = 'tr'): string
    {
        // Laravel'in standart translation'ını kullan
        $translation = __($key, $replace, $locale);
        
        // Çeviri bulunamazsa fallback
        if ($translation === $key) {
            return getTranslationFallback($key, $replace);
        }
        
        return $translation;
    }
}

if (!function_exists('getModuleTranslation')) {
    /**
     * Modül dili çevirisi getir
     */
    function getModuleTranslation(string $module, string $key, array $replace = [], string $locale = 'tr'): string
    {
        $translationManager = app('App\Services\TranslationFileManager');
        
        try {
            // Modül çevirilerini al
            $moduleTranslations = $translationManager->getModuleTranslations($module, $locale);
            
            // Nested key desteği (örn: "general.title")
            $value = data_get($moduleTranslations, $key);
            
            if ($value) {
                // Replace parametrelerini uygula
                return str_replace(
                    array_map(fn($k) => ":{$k}", array_keys($replace)),
                    array_values($replace),
                    $value
                );
            }
            
            // Modülde bulunamazsa sistem çevirisine fallback
            return getSystemTranslation($key, $replace, $locale);
            
        } catch (\Exception $e) {
            \Log::warning('Module translation failed', [
                'module' => $module,
                'key' => $key,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return getTranslationFallback("{$module}::{$key}", $replace);
        }
    }
}

if (!function_exists('getTenantTranslation')) {
    /**
     * Tenant dili çevirisi getir
     */
    function getTenantTranslation(string $key, array $replace = [], ?string $tenantId = null, string $locale = 'tr'): string
    {
        $tenantId = $tenantId ?? (function_exists('tenant') && tenant() ? tenant()->id : null);
        
        if (!$tenantId) {
            return getSystemTranslation($key, $replace, $locale);
        }
        
        $translationManager = app('App\Services\TranslationFileManager');
        
        try {
            // Tenant çevirilerini al
            $tenantTranslations = $translationManager->getTenantTranslations($tenantId, $locale);
            
            // Nested key desteği
            $value = data_get($tenantTranslations, $key);
            
            if ($value) {
                // Replace parametrelerini uygula
                return str_replace(
                    array_map(fn($k) => ":{$k}", array_keys($replace)),
                    array_values($replace),
                    $value
                );
            }
            
            // Tenant'ta bulunamazsa sistem çevirisine fallback
            return getSystemTranslation($key, $replace, $locale);
            
        } catch (\Exception $e) {
            \Log::warning('Tenant translation failed', [
                'tenant_id' => $tenantId,
                'key' => $key,
                'locale' => $locale,
                'error' => $e->getMessage()
            ]);
            
            return getTranslationFallback($key, $replace);
        }
    }
}

if (!function_exists('getTranslationFallback')) {
    /**
     * Çeviri bulunamazsa fallback
     */
    function getTranslationFallback(string $key, array $replace = []): string
    {
        // Key'den anlamlı bir metin oluştur
        $parts = explode('.', $key);
        $lastPart = end($parts);
        
        // Snake_case'i title case'e çevir
        $fallback = str_replace('_', ' ', $lastPart);
        $fallback = ucwords($fallback);
        
        // Replace parametrelerini uygula
        if (!empty($replace)) {
            return str_replace(
                array_map(fn($k) => ":{$k}", array_keys($replace)),
                array_values($replace),
                $fallback
            );
        }
        
        return $fallback;
    }
}

if (!function_exists('clearTranslationCache')) {
    /**
     * Translation cache'lerini temizle
     */
    function clearTranslationCache(?string $tenantId = null, ?string $module = null): void
    {
        $translationManager = app('App\Services\TranslationFileManager');
        
        if ($tenantId) {
            $translationManager->clearTenantTranslationCache($tenantId);
        } elseif ($module) {
            $translationManager->clearModuleTranslationCache($module);
        } else {
            $translationManager->clearAllTranslationCache();
        }
    }
}

if (!function_exists('updateTenantTranslation')) {
    /**
     * Tenant çevirisini güncelle
     */
    function updateTenantTranslation(string $file, array $translations, ?string $tenantId = null, string $locale = 'tr'): bool
    {
        $tenantId = $tenantId ?? (function_exists('tenant') && tenant() ? tenant()->id : null);
        
        if (!$tenantId) {
            return false;
        }
        
        $translationManager = app('App\Services\TranslationFileManager');
        
        return $translationManager->updateTenantTranslation($tenantId, $locale, $file, $translations);
    }
}

if (!function_exists('createLanguageFiles')) {
    /**
     * Yeni dil için dosyalar oluştur
     */
    function createLanguageFiles(string $locale, array $modules = []): bool
    {
        $translationManager = app('App\Services\TranslationFileManager');
        
        return $translationManager->createLanguageFiles($locale, $modules);
    }
}

if (!function_exists('getAvailableTranslations')) {
    /**
     * Mevcut çevirileri getir
     */
    function getAvailableTranslations(string $type = 'system', ?string $identifier = null): array
    {
        $translationManager = app('App\Services\TranslationFileManager');
        $locale = app()->getLocale();
        
        switch ($type) {
            case 'system':
                return $translationManager->getSystemTranslations($locale);
            case 'module':
                return $identifier ? $translationManager->getModuleTranslations($identifier, $locale) : [];
            case 'tenant':
                $tenantId = $identifier ?? (function_exists('tenant') && tenant() ? tenant()->id : null);
                return $tenantId ? $translationManager->getTenantTranslations($tenantId, $locale) : [];
            default:
                return [];
        }
    }
}

if (!function_exists('trans_choice_smart')) {
    /**
     * Akıllı çoğul desteği
     */
    function trans_choice_smart(string $key, int $count, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // Türkçe için basit çoğul kuralı
        if ($locale === 'tr') {
            $singular = __($key . '_singular', $replace, $locale);
            $plural = __($key . '_plural', $replace, $locale);
            
            return $count === 1 ? $singular : $plural;
        }
        
        // Diğer diller için Laravel'in trans_choice'unu kullan
        return trans_choice($key, $count, $replace, $locale);
    }
}

if (!function_exists('locale_name')) {
    /**
     * Dil kodundan dil adını getir
     */
    function locale_name(string $locale): string
    {
        $names = [
            'tr' => 'Türkçe',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'pt' => 'Português',
            'ru' => 'Русский',
            'zh' => '中文',
            'ja' => '日本語',
            'ko' => '한국어',
            'ar' => 'العربية'
        ];
        
        return $names[$locale] ?? ucfirst($locale);
    }
}

if (!function_exists('is_rtl_locale')) {
    /**
     * RTL dil kontrolü
     */
    function is_rtl_locale(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        
        $rtlLocales = ['ar', 'he', 'fa', 'ur', 'ku'];
        
        return in_array($locale, $rtlLocales);
    }
}