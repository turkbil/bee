<?php

namespace App\Traits;

use App\Helpers\SlugHelper;

/**
 * Livewire Component'ler için slug yönetim trait'i
 * Çoklu dil slug sistemini kolaylaştırır
 */
trait HasSlugManagement
{
    /**
     * Slug validation kurallarını al
     * 
     * @param array $languages Dil kodları
     * @param string $fieldPrefix Field prefix
     * @param bool $required Zorunlu mu
     * @return array
     */
    protected function getSlugValidationRules(
        array $languages,
        string $fieldPrefix = 'multiLangInputs',
        bool $required = false
    ): array {
        return SlugHelper::getValidationRules($languages, $fieldPrefix, $required);
    }
    
    /**
     * Slug validation mesajlarını al
     * 
     * @param array $languages Dil kodları
     * @param string $fieldPrefix Field prefix
     * @return array
     */
    protected function getSlugValidationMessages(
        array $languages,
        string $fieldPrefix = 'multiLangInputs'
    ): array {
        return SlugHelper::getValidationMessages($languages, $fieldPrefix);
    }
    
    /**
     * Çoklu dil slug'larını işle
     * 
     * @param string $modelClass Model sınıfı
     * @param array $multiLangInputs Çoklu dil inputları
     * @param array $languages Dil kodları
     * @param mixed $excludeId Hariç tutulacak ID
     * @param string $slugColumn Slug column adı
     * @return array İşlenmiş slug'lar
     */
    protected function processMultiLanguageSlugs(
        string $modelClass,
        array $multiLangInputs,
        array $languages,
        $excludeId = null,
        string $slugColumn = 'slug'
    ): array {
        $slugs = [];
        $titles = [];
        
        // Slug'ları ve title'ları ayır
        foreach ($languages as $lang) {
            $slugs[$lang] = $multiLangInputs[$lang]['slug'] ?? '';
            $titles[$lang] = $multiLangInputs[$lang]['title'] ?? '';
        }
        
        return SlugHelper::processMultiLanguageSlugs(
            $modelClass,
            $slugs,
            $titles,
            $slugColumn,
            $excludeId
        );
    }
    
    /**
     * Tek bir slug'ı unique yap
     * 
     * @param string $modelClass Model sınıfı
     * @param string $slug Slug
     * @param string $language Dil kodu
     * @param mixed $excludeId Hariç tutulacak ID
     * @param string $slugColumn Slug column adı
     * @param string $primaryKey Primary key column
     * @return string Unique slug
     */
    protected function makeSlugUnique(
        string $modelClass,
        string $slug,
        string $language,
        $excludeId = null,
        string $slugColumn = 'slug',
        ?string $primaryKey = null
    ): string {
        return SlugHelper::generateUniqueSlug(
            $modelClass,
            $slug,
            $language,
            $slugColumn,
            $primaryKey,
            $excludeId
        );
    }
    
    /**
     * Title'dan slug oluştur ve unique yap
     * 
     * @param string $modelClass Model sınıfı
     * @param string $title Başlık
     * @param string $language Dil kodu
     * @param mixed $excludeId Hariç tutulacak ID
     * @param string $slugColumn Slug column adı
     * @param string $primaryKey Primary key column
     * @return string Unique slug
     */
    protected function generateSlugFromTitle(
        string $modelClass,
        string $title,
        string $language,
        $excludeId = null,
        string $slugColumn = 'slug',
        ?string $primaryKey = null
    ): string {
        return SlugHelper::generateFromTitle(
            $modelClass,
            $title,
            $language,
            $slugColumn,
            $primaryKey,
            $excludeId
        );
    }
}