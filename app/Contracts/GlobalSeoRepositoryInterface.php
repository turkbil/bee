<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface GlobalSeoRepositoryInterface
{
    /**
     * SEO verilerini getir
     */
    public function getSeoData(Model $model, string $language): array;

    /**
     * SEO verilerini kaydet
     */
    public function saveSeoData(Model $model, string $language, array $seoData): bool;

    /**
     * Belirli bir SEO alanını güncelle
     */
    public function updateSeoField(Model $model, string $language, string $field, mixed $value): bool;

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData, string $module = 'default'): array;

    /**
     * SEO validation kurallarını getir
     */
    public function getValidationRules(string $module = 'default'): array;

    /**
     * SEO alanlarının limitlerini getir
     */
    public function getFieldLimits(string $module = 'default'): array;

    /**
     * Keyword'leri parse et
     */
    public function parseKeywords(?string $keywords): array;

    /**
     * SEO verilerini temizle/cache'den sil
     */
    public function clearSeoCache(Model $model): void;

    /**
     * Model için SEO ayarlarını al
     */
    public function getSeoSetting(Model $model): mixed;

    /**
     * Model için SEO ayarlarını oluştur/güncelle
     */
    public function createOrUpdateSeoSetting(Model $model, array $data): mixed;
    
    /**
     * Modül-based SEO ayarlarını getir (Page/Module index SEO'su için)
     */
    public function getSeoSettings(string $moduleName, string $pageType = 'index'): array;
    
    /**
     * Modül-based SEO ayarlarını kaydet (Page/Module index SEO'su için)
     */
    public function saveSeoSettings(string $moduleName, string $pageType, array $seoData): bool;
}