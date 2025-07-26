<?php

namespace App\Services;

use App\Models\SeoSetting;
use App\Services\SeoCacheService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Modules\LanguageManagement\App\Models\TenantLanguage;

class SeoWidgetService
{
    private const CACHE_PREFIX = 'seo_widget_';
    private const CACHE_TTL = 3600; // 1 saat

    /**
     * Herhangi bir modül için SEO widget verilerini hazırla
     */
    public static function prepareWidgetData(
        Model $model = null,
        array $customLimits = [],
        string $currentLanguage = null,
        bool $showScore = true
    ): array {
        $currentLanguage = $currentLanguage ?? self::getCurrentLanguage();
        
        return [
            'seoData' => self::getSeoData($model, $currentLanguage),
            'seoLimits' => self::getSeoLimits($customLimits),
            'seoScore' => $showScore ? self::calculateSeoScore($model, $currentLanguage) : null,
            'currentLanguage' => $currentLanguage,
            'availableLanguages' => self::getAvailableLanguages(),
            'validationRules' => self::getValidationRules()
        ];
    }

    /**
     * Model için SEO verilerini cache'li olarak getir
     */
    public static function getSeoData(Model $model = null, string $language = 'tr'): array
    {
        if (!$model || !$model->exists) {
            return self::getEmptySeoData();
        }

        $cacheKey = SeoCacheService::getDataCacheKey($model, $language);

        // ✅ GLOBAL CACHE/REDIS BYPASS - AdminNoCacheMiddleware otomatik hallediyor
        return SeoCacheService::remember($cacheKey, function () use ($model, $language) {
            // 🔍 DEBUG: Model ve language bilgisi
            Log::info('🔍 SeoWidgetService::getSeoData called', [
                'model_class' => get_class($model),
                'model_id' => $model->getKey(),
                'language' => $language,
                'app_locale' => app()->getLocale(),
                'has_seoSetting_method' => method_exists($model, 'seoSetting'),
                'has_seoSetting_property' => property_exists($model, 'seoSetting')
            ]);
            
            $seoSetting = $model->seoSetting;
            
            Log::info('🔍 SeoSetting relation', [
                'seoSetting_exists' => !!$seoSetting,
                'seoSetting_id' => $seoSetting?->id ?? 'null'
            ]);
            
            if (!$seoSetting) {
                return self::getEmptySeoData();
            }

            // 🔍 DEBUG: SEO verilerini çekme
            $seoTitle = $seoSetting->getTitle($language) ?? '';
            $seoDescription = $seoSetting->getDescription($language) ?? '';
            $seoKeywords = $seoSetting->getKeywords($language);
            
            Log::info('🔍 SEO veriler çekildi', [
                'language' => $language,
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
                'seo_keywords_count' => count($seoKeywords)
            ]);
            
            return [
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
                'seo_keywords' => implode(', ', $seoKeywords),
                'canonical_url' => $seoSetting->getCanonicalUrl($language) ?? '',
                'og_title' => $seoSetting->getOgTitle($language) ?? '',
                'og_description' => $seoSetting->getOgDescription($language) ?? '',
                'og_image' => $seoSetting->getOgImage($language) ?? '',
                'robots_meta' => $seoSetting->getRobotsMetaString(),
                'focus_keyword' => $seoSetting->focus_keyword ?? '',
                'auto_generate' => $seoSetting->auto_optimize ?? false
            ];
        });
    }

    /**
     * SEO verilerini kaydet ve cache'i temizle
     */
    public static function saveSeoData(Model $model, array $seoData, string $language = 'tr'): bool
    {
        if (!$model || !$model->exists) {
            return false;
        }

        try {
            $seoSetting = $model->seoSetting ?? new SeoSetting();
            
            // Çoklu dil desteği ile kaydet
            $titles = $seoSetting->titles ?? [];
            $descriptions = $seoSetting->descriptions ?? [];
            $keywords = $seoSetting->keywords ?? [];

            if (!empty($seoData['seo_title'])) {
                $titles[$language] = $seoData['seo_title'];
            }

            if (!empty($seoData['seo_description'])) {
                $descriptions[$language] = $seoData['seo_description'];
            }

            if (!empty($seoData['seo_keywords'])) {
                $keywordArray = array_filter(array_map('trim', explode(',', $seoData['seo_keywords'])));
                $keywords[$language] = $keywordArray;
            }

            $seoSetting->fill([
                'seoable_id' => $model->getKey(),
                'seoable_type' => get_class($model),
                'titles' => $titles,
                'descriptions' => $descriptions,
                'keywords' => $keywords,
                'canonical_url' => $seoData['canonical_url'] ?? '',
                'og_title' => $seoData['og_title'] ?? '',
                'og_description' => $seoData['og_description'] ?? '',
                'og_image' => $seoData['og_image'] ?? '',
                'focus_keyword' => $seoData['focus_keyword'] ?? '',
                'auto_optimize' => $seoData['auto_generate'] ?? false,
            ]);

            $seoSetting->save();

            // Cache'i temizle
            SeoCacheService::forgetModelCache($model);

            return true;

        } catch (\Exception $e) {
            Log::error('SEO verisi kaydetme hatası: ' . $e->getMessage(), [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'language' => $language,
                'seo_data' => $seoData
            ]);
            
            return false;
        }
    }

    /**
     * SEO skorunu hesapla
     */
    public static function calculateSeoScore(Model $model = null, string $language = 'tr'): ?array
    {
        if (!$model || !$model->exists) {
            return null;
        }

        $seoData = self::getSeoData($model, $language);
        $score = 0;
        $maxScore = 100;
        $checks = [];

        // Title kontrolü (25 puan)
        $titleLength = mb_strlen($seoData['seo_title']);
        if ($titleLength >= 30 && $titleLength <= 60) {
            $score += 25;
            $checks['title'] = ['status' => 'good', 'message' => 'Title uzunluğu optimum'];
        } elseif ($titleLength > 0) {
            $score += 10;
            $checks['title'] = ['status' => 'warning', 'message' => 'Title çok kısa veya uzun'];
        } else {
            $checks['title'] = ['status' => 'error', 'message' => 'Title eksik'];
        }

        // Description kontrolü (25 puan)
        $descLength = mb_strlen($seoData['seo_description']);
        if ($descLength >= 120 && $descLength <= 160) {
            $score += 25;
            $checks['description'] = ['status' => 'good', 'message' => 'Description uzunluğu optimum'];
        } elseif ($descLength > 0) {
            $score += 10;
            $checks['description'] = ['status' => 'warning', 'message' => 'Description çok kısa veya uzun'];
        } else {
            $checks['description'] = ['status' => 'error', 'message' => 'Description eksik'];
        }

        // Keywords kontrolü (25 puan)
        $keywordArray = array_filter(array_map('trim', explode(',', $seoData['seo_keywords'])));
        $keywordCount = count($keywordArray);
        if ($keywordCount >= 3 && $keywordCount <= 10) {
            $score += 25;
            $checks['keywords'] = ['status' => 'good', 'message' => 'Keyword sayısı optimum'];
        } elseif ($keywordCount > 0) {
            $score += 10;
            $checks['keywords'] = ['status' => 'warning', 'message' => 'Keyword sayısı az veya fazla'];
        } else {
            $checks['keywords'] = ['status' => 'error', 'message' => 'Keyword eksik'];
        }

        // Canonical URL kontrolü (25 puan)
        if (!empty($seoData['canonical_url'])) {
            if (filter_var($seoData['canonical_url'], FILTER_VALIDATE_URL)) {
                $score += 25;
                $checks['canonical'] = ['status' => 'good', 'message' => 'Canonical URL geçerli'];
            } else {
                $score += 5;
                $checks['canonical'] = ['status' => 'warning', 'message' => 'Canonical URL geçersiz'];
            }
        } else {
            $score += 15; // Canonical isteğe bağlı
            $checks['canonical'] = ['status' => 'info', 'message' => 'Canonical URL opsiyonel'];
        }

        return [
            'score' => $score,
            'percentage' => round(($score / $maxScore) * 100),
            'checks' => $checks
        ];
    }

    /**
     * SEO limitlerini getir
     */
    public static function getSeoLimits(array $customLimits = []): array
    {
        $defaults = [
            'seo_title' => 60,
            'seo_description' => 160,
            'seo_keywords_count' => 10,
            'canonical_url' => 255
        ];

        return array_merge($defaults, $customLimits);
    }

    /**
     * Validasyon kurallarını getir
     */
    public static function getValidationRules(): array
    {
        return [
            'seo_title' => 'required|string|max:60',
            'seo_description' => 'required|string|max:160',
            'seo_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|url|max:500',
            'focus_keyword' => 'nullable|string|max:100'
        ];
    }

    /**
     * Mevcut aktif dilleri getir
     */
    public static function getAvailableLanguages(): array
    {
        try {
            $languages = TenantLanguage::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->toArray();
                
            return empty($languages) ? ['tr'] : $languages;
        } catch (\Exception $e) {
            return ['tr'];
        }
    }

    /**
     * Mevcut dili belirle
     */
    public static function getCurrentLanguage(): string
    {
        $availableLanguages = self::getAvailableLanguages();
        $defaultLang = session('admin_locale', session('site_locale', 'tr'));
        
        return in_array($defaultLang, $availableLanguages) 
            ? $defaultLang 
            : $availableLanguages[0];
    }

    /**
     * Boş SEO verilerini döndür
     */
    public static function getEmptySeoData(): array
    {
        return [
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'canonical_url' => '',
            'og_title' => '',
            'og_description' => '',
            'og_image' => '',
            'robots_meta' => 'index, follow',
            'focus_keyword' => '',
            'auto_generate' => false
        ];
    }

    /**
     * Model için SEO cache'ini temizle
     */
    public static function clearSeoCache(Model $model): void
    {
        if (!$model || !$model->exists) {
            return;
        }

        $languages = self::getAvailableLanguages();
        $baseKey = self::CACHE_PREFIX . get_class($model) . '_' . $model->getKey();

        foreach ($languages as $language) {
            Cache::forget($baseKey . '_' . $language);
        }

        // Genel cache anahtarlarını da temizle
        Cache::forget($baseKey);
        Cache::forget("seo_settings_" . get_class($model) . "_" . $model->getKey());
    }

    /**
     * Tüm SEO cache'ini temizle
     */
    public static function clearAllSeoCache(): void
    {
        SeoCacheService::flush();
    }

    /**
     * Modül için SEO API verilerini hazırla
     */
    public static function prepareApiResponse(Model $model, string $language): array
    {
        $seoData = self::getSeoData($model, $language);
        $seoScore = self::calculateSeoScore($model, $language);

        return [
            'success' => true,
            'seoData' => $seoData,
            'seoScore' => $seoScore,
            'language' => $language,
            'limits' => self::getSeoLimits()
        ];
    }

    /**
     * Slug benzersizlik kontrolü
     */
    public static function checkSlugUniqueness(string $slug, string $moduleClass, int $excludeId = null): bool
    {
        try {
            $query = $moduleClass::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            return !$query->exists();
        } catch (\Exception $e) {
            Log::error('Slug benzersizlik kontrolü hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Otomatik SEO önerisi oluştur
     */
    public static function generateAutoSeoSuggestion(Model $model, string $language = 'tr'): array
    {
        $suggestions = [
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
        ];

        try {
            // Model'den title çıkarma
            if (isset($model->title)) {
                $suggestions['seo_title'] = mb_substr($model->title, 0, 60);
            } elseif (isset($model->name)) {
                $suggestions['seo_title'] = mb_substr($model->name, 0, 60);
            }

            // Model'den description çıkarma
            if (isset($model->description)) {
                $suggestions['seo_description'] = mb_substr(strip_tags($model->description), 0, 160);
            } elseif (isset($model->content)) {
                $suggestions['seo_description'] = mb_substr(strip_tags($model->content), 0, 160);
            }

            // Basit keyword önerisi
            if ($suggestions['seo_title']) {
                $words = explode(' ', strtolower($suggestions['seo_title']));
                $words = array_filter($words, function($word) {
                    return strlen($word) > 3; // 3 karakterden uzun kelimeler
                });
                $suggestions['seo_keywords'] = implode(', ', array_slice($words, 0, 5));
            }

        } catch (\Exception $e) {
            Log::error('SEO önerisi oluşturma hatası: ' . $e->getMessage());
        }

        return $suggestions;
    }
}