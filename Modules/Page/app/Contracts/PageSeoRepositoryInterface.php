<?php

namespace Modules\Page\App\Contracts;

use Modules\Page\App\Models\Page;

interface PageSeoRepositoryInterface
{
    /**
     * SEO verilerini getir
     */
    public function getSeoData(Page $page, string $language): array;

    /**
     * SEO verilerini kaydet
     */
    public function saveSeoData(Page $page, string $language, array $seoData): bool;

    /**
     * Belirli bir SEO alanını güncelle
     */
    public function updateSeoField(Page $page, string $language, string $field, mixed $value): bool;

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array;

    /**
     * SEO validation kurallarını getir
     */
    public function getValidationRules(): array;

    /**
     * SEO alanlarının limitlerini getir
     */
    public function getFieldLimits(): array;

    /**
     * Keyword'leri parse et
     */
    public function parseKeywords(?string $keywords): array;

    /**
     * SEO verilerini temizle/cache'den sil
     */
    public function clearSeoCache(Page $page): void;
}