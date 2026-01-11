<?php

declare(strict_types=1);

namespace Modules\SeoManagement\app\Services\Interfaces;

use Modules\SeoManagement\app\Models\SeoSetting;
use Illuminate\Database\Eloquent\Model;

interface SeoServiceInterface
{
    /**
     * Get or create SEO settings for a model
     */
    public function getOrCreateSeoSettings(Model $model): SeoSetting;

    /**
     * Update SEO settings for a model
     */
    public function updateSeoSettings(Model $model, array $seoData): SeoSetting;

    /**
     * Delete SEO settings for a model
     */
    public function deleteSeoSettings(Model $model): bool;

    /**
     * Check if model has SEO settings
     */
    public function hasSeoSettings(Model $model): bool;

    /**
     * Get SEO settings by model
     */
    public function getSeoSettings(Model $model): ?SeoSetting;

    /**
     * Update multi-language SEO data
     */
    public function updateMultiLanguageSeoData(Model $model, array $languageData): SeoSetting;

    /**
     * Calculate SEO score for a model
     */
    public function calculateSeoScore(Model $model): int;
}