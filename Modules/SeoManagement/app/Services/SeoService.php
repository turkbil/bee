<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Services;

use Modules\SeoManagement\app\Services\Interfaces\SeoServiceInterface;
use Modules\SeoManagement\app\Models\SeoSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

readonly class SeoService implements SeoServiceInterface
{
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
        
        $seoSettings->fill($seoData);
        $seoSettings->save();

        $this->clearCache($model);

        return $seoSettings;
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
                $titles[$lang] = $data['title'];
                $seoSettings->titles = $titles;
            }
            
            if (isset($data['description'])) {
                $descriptions = $seoSettings->descriptions ?? [];
                $descriptions[$lang] = $data['description'];
                $seoSettings->descriptions = $descriptions;
            }
            
            if (isset($data['keywords'])) {
                $keywords = $seoSettings->keywords ?? [];
                $keywords[$lang] = is_array($data['keywords']) ? $data['keywords'] : explode(',', $data['keywords']);
                $seoSettings->keywords = $keywords;
            }

            if (isset($data['og_title'])) {
                $ogTitles = $seoSettings->og_title ?? [];
                $ogTitles[$lang] = $data['og_title'];
                $seoSettings->og_title = $ogTitles;
            }

            if (isset($data['og_description'])) {
                $ogDescriptions = $seoSettings->og_description ?? [];
                $ogDescriptions[$lang] = $data['og_description'];
                $seoSettings->og_description = $ogDescriptions;
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