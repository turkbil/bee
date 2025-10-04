<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Base Service Class
 *
 * Tüm modül service'lerinin extend edeceği base class.
 * Ortak metodları içerir ve DRY prensibini uygular.
 */
abstract readonly class BaseService
{
    /**
     * Title'lardan slug oluşturma
     * 
     * @param array $titles ['tr' => 'Başlık', 'en' => 'Title']
     * @param array $existingSlugs Mevcut slug'lar
     * @return array ['tr' => 'baslik', 'en' => 'title']
     */
    protected function generateSlugsFromTitles(array $titles, array $existingSlugs = []): array
    {
        $slugs = $existingSlugs;

        foreach ($titles as $locale => $title) {
            if (!empty($title) && empty($slugs[$locale])) {
                $slugs[$locale] = \Str::slug($title);
            }
        }

        return $slugs;
    }

    /**
     * SEO verilerini temizleyip hazırlama
     * 
     * Boş değerleri filtreler, sadece dolu alanları kaydeder.
     * 
     * @param array $seoData Gelen SEO verileri
     * @param array $existingSeo Mevcut SEO verileri
     * @return array Temizlenmiş SEO verileri
     */
    protected function prepareSeoData(array $seoData, array $existingSeo = []): array
    {
        $prepared = $existingSeo;

        foreach ($seoData as $locale => $data) {
            if (is_array($data)) {
                // Boş değerleri temizle
                $cleanData = array_filter($data, function($value) {
                    return !is_null($value) && $value !== '' && $value !== [];
                });

                if (!empty($cleanData)) {
                    $prepared[$locale] = array_merge($prepared[$locale] ?? [], $cleanData);
                }
            }
        }

        return $prepared;
    }
}
