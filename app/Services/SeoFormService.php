<?php

namespace App\Services;

use Modules\SeoManagement\App\Models\SeoSetting;
use App\Services\SeoLanguageManager;
use Modules\LanguageManagement\App\Models\TenantLanguage;

class SeoFormService
{
    /**
     * Model için SEO form component verilerini hazırla
     */
    public static function prepareComponentData($model)
    {
        $availableLanguages = self::getAvailableLanguages();
        $currentLanguage = self::getCurrentLanguage($availableLanguages);
        $seoData = self::loadSeoData($model);
        
        return [
            'model' => $model,
            'availableLanguages' => $availableLanguages,
            'currentLanguage' => $currentLanguage,
            'seoData' => $seoData,
            'newKeyword' => array_fill_keys($availableLanguages, ''),
        ];
    }
    
    /**
     * Aktif dilleri getir
     */
    public static function getAvailableLanguages()
    {
        $languages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
            
        return empty($languages) ? ['tr'] : $languages;
    }
    
    /**
     * Mevcut dili belirle
     */
    public static function getCurrentLanguage($availableLanguages)
    {
        $defaultLang = session('site_default_language', 'tr');
        return in_array($defaultLang, $availableLanguages) 
            ? $defaultLang 
            : $availableLanguages[0];
    }
    
    /**
     * Model için SEO verilerini yükle
     */
    public static function loadSeoData($model)
    {
        if (!$model || !$model->exists) {
            return self::getEmptySeoData();
        }
        
        $seoSettings = $model->seoSetting;
        
        if ($seoSettings) {
            return [
                'titles' => $seoSettings->titles ?? [],
                'descriptions' => $seoSettings->descriptions ?? [],
                'keywords' => $seoSettings->keywords ?? [],
                'focus_keyword' => $seoSettings->focus_keyword ?? '',
                'canonical_url' => $seoSettings->canonical_url ?? '',
                'robots_index' => $seoSettings->robots_index ?? true,
                'robots_follow' => $seoSettings->robots_follow ?? true,
                'robots_archive' => $seoSettings->robots_archive ?? true,
                'auto_generate' => $seoSettings->auto_generate ?? false,
                'og_titles' => $seoSettings->og_titles ?? '',
                'og_descriptions' => $seoSettings->og_descriptions ?? '',
                'og_image' => $seoSettings->og_image ?? '',
                'og_type' => $seoSettings->og_type ?? 'website',
                'twitter_card' => $seoSettings->twitter_card ?? 'summary',
                'twitter_site' => $seoSettings->twitter_site ?? '',
            ];
        }
        
        return self::getEmptySeoData();
    }
    
    /**
     * Boş SEO verilerini döndür
     */
    public static function getEmptySeoData()
    {
        return [
            'titles' => [],
            'descriptions' => [],
            'keywords' => [],
            'focus_keyword' => '',
            'canonical_url' => '',
            'robots_index' => true,
            'robots_follow' => true,
            'robots_archive' => true,
            'auto_generate' => false,
            'og_titles' => '',
            'og_descriptions' => '',
            'og_image' => '',
            'og_type' => 'website',
            'twitter_card' => 'summary',
            'twitter_site' => '',
        ];
    }
    
    /**
     * SEO verilerini kaydet
     */
    public static function saveSeoData($model, $seoData)
    {
        if (!$model || !$model->exists) {
            return false;
        }
        
        try {
            $seoSettings = $model->seoSetting ?? new SeoSetting();
            
            $seoSettings->fill([
                'seoable_id' => $model->getKey(),
                'seoable_type' => get_class($model),
                'titles' => $seoData['titles'] ?? [],
                'descriptions' => $seoData['descriptions'] ?? [],
                'keywords' => $seoData['keywords'] ?? [],
                'focus_keyword' => $seoData['focus_keyword'] ?? '',
                'canonical_url' => $seoData['canonical_url'] ?? '',
                'robots_index' => $seoData['robots_index'] ?? true,
                'robots_follow' => $seoData['robots_follow'] ?? true,
                'robots_archive' => $seoData['robots_archive'] ?? true,
                'auto_generate' => $seoData['auto_generate'] ?? false,
                'og_titles' => $seoData['og_titles'] ?? '',
                'og_descriptions' => $seoData['og_descriptions'] ?? '',
                'og_image' => $seoData['og_image'] ?? '',
                'og_type' => $seoData['og_type'] ?? 'website',
                'twitter_card' => $seoData['twitter_card'] ?? 'summary',
                'twitter_site' => $seoData['twitter_site'] ?? '',
            ]);
            
            $seoSettings->save();
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('SEO kaydetme hatası: ' . $e->getMessage(), [
                'model_type' => get_class($model),
                'model_id' => $model->getKey(),
                'seo_data' => $seoData
            ]);
            return false;
        }
    }
    
    /**
     * Anahtar kelime ekle
     */
    public static function addKeyword($seoData, $language, $keyword)
    {
        $keyword = trim($keyword);
        
        if (empty($keyword)) {
            return $seoData;
        }
        
        if (!isset($seoData['keywords'][$language])) {
            $seoData['keywords'][$language] = [];
        }
        
        if (!in_array($keyword, $seoData['keywords'][$language])) {
            $seoData['keywords'][$language][] = $keyword;
        }
        
        return $seoData;
    }
    
    /**
     * Anahtar kelime sil
     */
    public static function removeKeyword($seoData, $language, $index)
    {
        if (isset($seoData['keywords'][$language][$index])) {
            unset($seoData['keywords'][$language][$index]);
            $seoData['keywords'][$language] = array_values($seoData['keywords'][$language]);
        }
        
        return $seoData;
    }
    
    /**
     * SEO verilerini validate et
     */
    public static function validateSeoData($seoData)
    {
        $rules = [
            'focus_keyword' => 'nullable|string|max:100',
            'canonical_url' => 'nullable|url|max:255',
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
            'robots_archive' => 'boolean',
            'auto_generate' => 'boolean',
            'og_titles' => 'nullable|string|max:60',
            'og_descriptions' => 'nullable|string|max:160',
            'og_image' => 'nullable|url|max:500',
            'og_type' => 'nullable|string|in:website,article,product',
            'twitter_card' => 'nullable|string|in:summary,summary_large_image,app,player',
            'twitter_site' => 'nullable|string|max:50',
        ];
        
        // Dil bazlı validasyon kuralları
        $availableLanguages = self::getAvailableLanguages();
        foreach ($availableLanguages as $lang) {
            $rules["titles.{$lang}"] = 'nullable|string|max:60';
            $rules["descriptions.{$lang}"] = 'nullable|string|max:160';
            $rules["keywords.{$lang}"] = 'nullable|array';
            $rules["keywords.{$lang}.*"] = 'string|max:50';
        }
        
        return \Validator::make($seoData, $rules);
    }
    
    /**
     * Model'in SEO skorunu hesapla
     */
    public static function calculateSeoScore($model, $language = null)
    {
        if (!$model || !$model->exists) {
            return 0;
        }
        
        $language = $language ?? self::getCurrentLanguage(self::getAvailableLanguages());
        $seoSettings = $model->seoSetting;
        $score = 0;
        
        if (!$seoSettings) {
            return 0;
        }
        
        // Title kontrolü (25 puan)
        if (isset($seoSettings->titles[$language]) && !empty($seoSettings->titles[$language])) {
            $titleLength = strlen($seoSettings->titles[$language]);
            if ($titleLength >= 30 && $titleLength <= 60) {
                $score += 25;
            } elseif ($titleLength > 0) {
                $score += 15;
            }
        }
        
        // Description kontrolü (25 puan)
        if (isset($seoSettings->descriptions[$language]) && !empty($seoSettings->descriptions[$language])) {
            $descLength = strlen($seoSettings->descriptions[$language]);
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 25;
            } elseif ($descLength > 0) {
                $score += 15;
            }
        }
        
        // Keywords kontrolü (20 puan)
        if (isset($seoSettings->keywords[$language]) && !empty($seoSettings->keywords[$language])) {
            $keywordCount = count($seoSettings->keywords[$language]);
            if ($keywordCount >= 3 && $keywordCount <= 10) {
                $score += 20;
            } elseif ($keywordCount > 0) {
                $score += 10;
            }
        }
        
        // Focus keyword kontrolü (10 puan)
        if (!empty($seoSettings->focus_keyword)) {
            $score += 10;
        }
        
        // Open Graph kontrolü (10 puan)
        if (!empty($seoSettings->og_titles) && !empty($seoSettings->og_descriptions)) {
            $score += 10;
        }
        
        // Robots ayarları (10 puan)
        if ($seoSettings->robots_index && $seoSettings->robots_follow) {
            $score += 10;
        }
        
        return min($score, 100);
    }
}