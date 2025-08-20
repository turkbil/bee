<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Services;

use Modules\SeoManagement\app\Services\Interfaces\SeoServiceInterface;
use Modules\SeoManagement\app\Models\SeoSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

readonly class SeoService implements SeoServiceInterface
{
    /**
     * GLOBAL SEO CONTENT TEMİZLEME SİSTEMİ
     * HTML, Enter ve gereksiz boşlukları temizler
     */
    private function cleanSeoContent(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        
        // 1. HTML etiketlerini tamamen kaldır
        $text = strip_tags($content);
        
        // 2. HTML entity'leri decode et
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // 3. Enter'ları kaldır (\r\n, \r, \n)
        $text = preg_replace('/\r\n|\r|\n/', ' ', $text);
        
        // 4. Çoklu boşlukları tek boşluğa çevir
        $text = preg_replace('/\s+/', ' ', $text);
        
        // 5. Başlangıç ve bitiş boşluklarını temizle
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Keywords array temizleme
     */
    private function cleanKeywordsArray(array $keywords): array
    {
        return array_map(function($keyword) {
            return $this->cleanSeoContent($keyword);
        }, array_filter($keywords));
    }
    
    public function getOrCreateSeoSettings(Model $model): SeoSetting
    {
        $seoSettings = $this->getSeoSettings($model);
        
        if ($seoSettings) {
            return $seoSettings;
        }

        return SeoSetting::create([
            'seoable_id' => $model->getKey(),
            'seoable_type' => get_class($model),
            'available_languages' => config('app.available_languages', ['tr', 'en', 'ar']),
            'default_language' => config('app.locale', 'tr')
        ]);
    }

    public function updateSeoSettings(Model $model, array $seoData): SeoSetting
    {
        $seoSettings = $this->getOrCreateSeoSettings($model);
        
        // SEO verilerini temizle
        $cleanedData = $this->cleanSeoData($seoData);
        
        
        $seoSettings->fill($cleanedData);
        $seoSettings->save();

        $this->clearCache($model);

        return $seoSettings;
    }
    
    /**
     * Tüm SEO verilerini temizle
     */
    private function cleanSeoData(array $seoData): array
    {
        $cleanedData = $seoData;
        
        // Tekil string alanları temizle
        $stringFields = [
            'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
            'og_image', 'og_type', 'twitter_card', 'twitter_title', 
            'twitter_description', 'twitter_image', 'focus_keyword'
        ];
        
        foreach ($stringFields as $field) {
            if (isset($cleanedData[$field])) {
                $cleanedData[$field] = $this->cleanSeoContent($cleanedData[$field]);
            }
        }
        
        // Array alanları temizle
        if (isset($cleanedData['additional_keywords']) && is_array($cleanedData['additional_keywords'])) {
            $cleanedData['additional_keywords'] = $this->cleanKeywordsArray($cleanedData['additional_keywords']);
        }
        
        return $cleanedData;
    }

    public function deleteSeoSettings(Model $model): bool
    {
        $seoSettings = $this->getSeoSettings($model);
        
        if (!$seoSettings) {
            return false;
        }

        $this->clearCache($model);
        
        return $seoSettings->delete();
    }

    public function hasSeoSettings(Model $model): bool
    {
        return $this->getSeoSettings($model) !== null;
    }

    public function getSeoSettings(Model $model): ?SeoSetting
    {
        $cacheKey = $this->getCacheKey($model);
        
        return Cache::remember($cacheKey, 3600, function () use ($model) {
            return SeoSetting::where('seoable_id', $model->getKey())
                ->where('seoable_type', get_class($model))
                ->first();
        });
    }

    public function updateMultiLanguageSeoData(Model $model, array $languageData): SeoSetting
    {
        $seoSettings = $this->getOrCreateSeoSettings($model);
        
        foreach ($languageData as $lang => $data) {
            if (isset($data['title'])) {
                $titles = $seoSettings->titles ?? [];
                $titles[$lang] = $this->cleanSeoContent($data['title']);
                $seoSettings->titles = $titles;
            }
            
            if (isset($data['description'])) {
                $descriptions = $seoSettings->descriptions ?? [];
                $descriptions[$lang] = $this->cleanSeoContent($data['description']);
                $seoSettings->descriptions = $descriptions;
            }
            
            if (isset($data['keywords'])) {
                $keywords = $seoSettings->keywords ?? [];
                $keywordArray = is_array($data['keywords']) ? $data['keywords'] : explode(',', $data['keywords']);
                $keywords[$lang] = $this->cleanKeywordsArray($keywordArray);
                $seoSettings->keywords = $keywords;
            }

            if (isset($data['og_title'])) {
                $ogTitles = $seoSettings->og_titles ?? [];
                $ogTitles[$lang] = $this->cleanSeoContent($data['og_title']);
                $seoSettings->og_titles = $ogTitles;
            }

            if (isset($data['og_description'])) {
                $ogDescriptions = $seoSettings->og_descriptions ?? [];
                $ogDescriptions[$lang] = $this->cleanSeoContent($data['og_description']);
                $seoSettings->og_descriptions = $ogDescriptions;
            }
            
            if (isset($data['focus_keywords'])) {
                $focusKeywords = $seoSettings->focus_keywords ?? [];
                $focusKeywordArray = is_array($data['focus_keywords']) ? $data['focus_keywords'] : explode(',', $data['focus_keywords']);
                $focusKeywords[$lang] = $this->cleanKeywordsArray($focusKeywordArray);
                $seoSettings->focus_keywords = $focusKeywords;
            }
        }

        $seoSettings->save();
        $this->clearCache($model);

        return $seoSettings;
    }

    public function calculateSeoScore(Model $model): int
    {
        $seoSettings = $this->getSeoSettings($model);
        
        if (!$seoSettings) {
            return 0;
        }

        $score = 0;
        $maxScore = 100;

        // Title check (20 points)
        if ($seoSettings->getTitle()) {
            $titleLength = strlen($seoSettings->getTitle());
            if ($titleLength >= 30 && $titleLength <= 60) {
                $score += 20;
            } elseif ($titleLength > 0) {
                $score += 10;
            }
        }

        // Description check (25 points)
        if ($seoSettings->getDescription()) {
            $descLength = strlen($seoSettings->getDescription());
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 25;
            } elseif ($descLength > 0) {
                $score += 15;
            }
        }

        // Keywords check (15 points)
        $keywords = $seoSettings->getKeywords();
        if (!empty($keywords) && count($keywords) >= 3 && count($keywords) <= 10) {
            $score += 15;
        } elseif (!empty($keywords)) {
            $score += 8;
        }

        // Open Graph check (15 points)
        if ($seoSettings->getOgTitle() && $seoSettings->getOgDescription()) {
            $score += 15;
        } elseif ($seoSettings->getOgTitle() || $seoSettings->getOgDescription()) {
            $score += 8;
        }

        // Canonical URL check (10 points)
        if ($seoSettings->getCanonicalUrl()) {
            $score += 10;
        }

        // Multi-language support check (15 points)
        $languages = $seoSettings->available_languages ?? [];
        if (count($languages) > 1) {
            $hasMultiLangContent = false;
            foreach (['titles', 'descriptions', 'keywords'] as $field) {
                if ($seoSettings->$field && count($seoSettings->$field) > 1) {
                    $hasMultiLangContent = true;
                    break;
                }
            }
            if ($hasMultiLangContent) {
                $score += 15;
            }
        }

        // Update score in database
        $seoSettings->seo_score = min($score, $maxScore);
        $seoSettings->last_analyzed = now();
        $seoSettings->save();

        return $seoSettings->seo_score;
    }

    private function getCacheKey(Model $model): string
    {
        return "seo_settings_" . class_basename($model) . "_{$model->getKey()}";
    }

    private function clearCache(Model $model): void
    {
        Cache::forget($this->getCacheKey($model));
    }
}