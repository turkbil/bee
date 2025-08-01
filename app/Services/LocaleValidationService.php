<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Locale Validation Service
 * 
 * Merkezi locale doğrulama servisi
 */
class LocaleValidationService
{
    private const CACHE_KEY = 'valid_locales';
    private const CACHE_TTL = 3600; // 1 saat

    /**
     * Valid locale'leri cache'le
     */
    private ?array $validLocales = null;

    /**
     * Locale geçerli mi?
     */
    public function isValidLocale(string $locale): bool
    {
        $validLocales = $this->getValidLocales();
        return in_array($locale, $validLocales, true);
    }

    /**
     * Tenant için geçerli locale mi?
     */
    public function isValidTenantLocale(string $locale): bool
    {
        $tenantLocales = $this->getTenantLocales();
        return in_array($locale, $tenantLocales, true);
    }

    /**
     * Locale regex pattern'i oluştur
     */
    public function getLocaleRegexPattern(): string
    {
        $locales = $this->getValidLocales();
        
        if (empty($locales)) {
            return '[a-z]{2}'; // Fallback
        }

        return implode('|', array_map('preg_quote', $locales));
    }

    /**
     * Default locale'i getir
     */
    public function getDefaultLocale(): string
    {
        return get_tenant_default_locale();
    }

    /**
     * Locale'i normalize et
     */
    public function normalizeLocale(?string $locale): string
    {
        if (!$locale) {
            return $this->getDefaultLocale();
        }

        // Küçük harfe çevir
        $locale = strtolower($locale);

        // Geçerli değilse default döndür
        if (!$this->isValidLocale($locale)) {
            return $this->getDefaultLocale();
        }

        return $locale;
    }

    /**
     * URL'den locale'i çıkar
     */
    public function extractLocaleFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        
        if (!$path) {
            return null;
        }

        $segments = array_filter(explode('/', trim($path, '/')));
        
        if (empty($segments)) {
            return null;
        }

        $firstSegment = reset($segments);
        
        if ($this->isValidLocale($firstSegment)) {
            return $firstSegment;
        }

        return null;
    }

    /**
     * Locale prefix'i URL'den kaldır
     */
    public function removeLocaleFromPath(string $path): string
    {
        $segments = array_filter(explode('/', trim($path, '/')));
        
        if (empty($segments)) {
            return '';
        }

        $firstSegment = reset($segments);
        
        if ($this->isValidLocale($firstSegment)) {
            array_shift($segments);
        }

        return implode('/', $segments);
    }

    /**
     * Locale bilgilerini getir
     */
    public function getLocaleInfo(string $locale): array
    {
        $localeData = $this->getAllLocaleData();
        
        return $localeData[$locale] ?? [
            'code' => $locale,
            'name' => strtoupper($locale),
            'native_name' => strtoupper($locale),
            'flag' => '🌐',
            'direction' => 'ltr'
        ];
    }

    /**
     * Cache'i temizle
     */
    public function clearCache(): void
    {
        $this->validLocales = null;
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_tenant');
        Cache::forget(self::CACHE_KEY . '_data');
    }

    // Private helper methods

    /**
     * Geçerli locale'leri getir
     */
    private function getValidLocales(): array
    {
        if ($this->validLocales !== null) {
            return $this->validLocales;
        }

        $this->validLocales = Cache::remember(self::CACHE_KEY, now()->addSeconds(self::CACHE_TTL), function () {
            // Önce tenant locale'lerini kontrol et
            if (function_exists('available_tenant_languages')) {
                $tenantLanguages = available_tenant_languages();
                if (!empty($tenantLanguages)) {
                    return array_column($tenantLanguages, 'code');
                }
            }

            // Fallback: config'den al
            return array_keys(config('app.available_locales', ['tr' => 'Türkçe', 'en' => 'English']));
        });

        return $this->validLocales;
    }

    /**
     * Tenant locale'lerini getir
     */
    private function getTenantLocales(): array
    {
        return Cache::remember(self::CACHE_KEY . '_tenant', now()->addSeconds(self::CACHE_TTL), function () {
            if (function_exists('available_tenant_languages')) {
                $languages = available_tenant_languages();
                return array_column($languages, 'code');
            }

            return $this->getValidLocales();
        });
    }

    /**
     * Tüm locale verilerini getir
     */
    private function getAllLocaleData(): array
    {
        return Cache::remember(self::CACHE_KEY . '_data', now()->addSeconds(self::CACHE_TTL), function () {
            $data = [];

            // Tenant language modelinden veri al
            if (class_exists('\Modules\LanguageManagement\app\Models\TenantLanguage')) {
                try {
                    $languages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', 1)->get();
                    
                    foreach ($languages as $lang) {
                        $data[$lang->code] = [
                            'code' => $lang->code,
                            'name' => $lang->name,
                            'native_name' => $lang->native_name ?? $lang->name,
                            'flag' => $lang->flag_icon ?? '🌐',
                            'direction' => $lang->direction ?? 'ltr'
                        ];
                    }
                } catch (\Exception $e) {
                    // Log but don't fail
                }
            }

            // Config'den eksikleri tamamla
            $configLocales = config('app.available_locales', []);
            foreach ($configLocales as $code => $name) {
                if (!isset($data[$code])) {
                    $data[$code] = [
                        'code' => $code,
                        'name' => $name,
                        'native_name' => $name,
                        'flag' => $this->getDefaultFlag($code),
                        'direction' => $this->getDirection($code)
                    ];
                }
            }

            return $data;
        });
    }

    /**
     * Default flag emoji getir
     */
    private function getDefaultFlag(string $locale): string
    {
        $flags = [
            'tr' => '🇹🇷',
            'en' => '🇺🇸',
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'fr' => '🇫🇷',
            'es' => '🇪🇸',
            'ru' => '🇷🇺',
            'zh' => '🇨🇳',
            'ja' => '🇯🇵'
        ];

        return $flags[$locale] ?? '🌐';
    }

    /**
     * Text direction getir
     */
    private function getDirection(string $locale): string
    {
        $rtlLocales = ['ar', 'he', 'fa', 'ur'];
        return in_array($locale, $rtlLocales) ? 'rtl' : 'ltr';
    }
}