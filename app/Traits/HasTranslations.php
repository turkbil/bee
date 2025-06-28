<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Çevrilebilir alanlar
     * Model'de şu şekilde tanımlanır:
     * protected $translatable = ['title', 'body', 'slug', 'metakey', 'metadesc'];
     */
    
    /**
     * Belirli dilde çeviriyi al (fallback ile)
     */
    public function getTranslated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?: App::getLocale() ?: 'tr';
        
        // Alan çevrilebilir mi kontrol et
        if (!$this->isTranslatable($field)) {
            return $this->getAttribute($field);
        }
        
        $translations = $this->getAttribute($field);
        
        // JSON değilse direkt döndür
        if (!is_array($translations)) {
            return $translations;
        }
        
        // İstenen dil varsa döndür
        if (isset($translations[$locale]) && !empty($translations[$locale])) {
            return $translations[$locale];
        }
        
        // Fallback sistemi - null locale kontrolü
        return $this->getFallbackTranslation($translations, $locale ?? 'tr');
    }
    
    /**
     * Fallback çeviri sistemi
     */
    private function getFallbackTranslation(array $translations, string $requestedLocale): ?string
    {
        // 1. Varsayılan dil (tr) varsa döndür
        if (isset($translations['tr']) && !empty($translations['tr'])) {
            return $translations['tr'];
        }
        
        // 2. İlk dolu dili bul
        foreach ($translations as $locale => $content) {
            if (!empty($content)) {
                return $content;
            }
        }
        
        // 3. Hiçbiri yoksa null
        return null;
    }
    
    /**
     * Magic accessor - $page->title_en
     */
    public function __get($key)
    {
        // Translation pattern kontrolü: field_locale (title_en, body_tr, vs.)
        if (preg_match('/^(.+)_([a-z]{2})$/', $key, $matches)) {
            $field = $matches[1];
            $locale = $matches[2];
            
            if ($this->isTranslatable($field)) {
                return $this->getTranslated($field, $locale);
            }
        }
        
        return parent::__get($key);
    }
    
    /**
     * Mevcut locale'de slug al
     */
    public function getCurrentSlug(?string $locale = null): ?string
    {
        return $this->getTranslated('slug', $locale);
    }
    
    /**
     * Tüm dillerdeki slug'ları al
     */
    public function getAllSlugs(): array
    {
        $slugs = $this->getAttribute('slug');
        return is_array($slugs) ? $slugs : [];
    }
    
    /**
     * Belirli dilde içerik var mı kontrol et
     */
    public function hasTranslation(string $field, string $locale): bool
    {
        if (!$this->isTranslatable($field)) {
            return true; // Çevrilebilir değilse hep true
        }
        
        $translations = $this->getAttribute($field);
        
        if (!is_array($translations)) {
            return !empty($translations);
        }
        
        return isset($translations[$locale]) && !empty($translations[$locale]);
    }
    
    /**
     * Eksik çevirileri al
     */
    public function getMissingTranslations(array $requiredLocales = ['tr', 'en']): array
    {
        $missing = [];
        
        foreach ($this->getTranslatableFields() as $field) {
            foreach ($requiredLocales as $locale) {
                if (!$this->hasTranslation($field, $locale)) {
                    $missing[$field][] = $locale;
                }
            }
        }
        
        return $missing;
    }
    
    /**
     * Dil bazında çeviri set et
     */
    public function setTranslation(string $field, string $locale, $value): self
    {
        if (!$this->isTranslatable($field)) {
            throw new \InvalidArgumentException("Field '{$field}' is not translatable");
        }
        
        $translations = $this->getAttribute($field) ?: [];
        
        if (!is_array($translations)) {
            // String'den array'e çevir
            $translations = ['tr' => $translations];
        }
        
        $translations[$locale] = $value;
        $this->setAttribute($field, $translations);
        
        return $this;
    }
    
    /**
     * Alanın çevrilebilir olup olmadığını kontrol et
     */
    public function isTranslatable(string $field): bool
    {
        return in_array($field, $this->getTranslatableFields());
    }
    
    /**
     * Çevrilebilir alanları al
     */
    public function getTranslatableFields(): array
    {
        return $this->translatable ?? [];
    }
    
    /**
     * Mevcut dildeki tüm çevirileri al
     */
    public function getTranslationsForLocale(?string $locale = null): array
    {
        $locale = $locale ?: App::getLocale();
        $result = [];
        
        foreach ($this->getTranslatableFields() as $field) {
            $result[$field] = $this->getTranslated($field, $locale);
        }
        
        return $result;
    }
    
    /**
     * Dil durumu raporu
     */
    public function getTranslationStatus(array $requiredLocales = ['tr', 'en']): array
    {
        $status = [];
        
        foreach ($requiredLocales as $locale) {
            $status[$locale] = [
                'completed' => 0,
                'missing' => 0,
                'total' => count($this->getTranslatableFields()),
                'percentage' => 0
            ];
            
            foreach ($this->getTranslatableFields() as $field) {
                if ($this->hasTranslation($field, $locale)) {
                    $status[$locale]['completed']++;
                } else {
                    $status[$locale]['missing']++;
                }
            }
            
            $status[$locale]['percentage'] = $status[$locale]['total'] > 0 
                ? round(($status[$locale]['completed'] / $status[$locale]['total']) * 100, 1)
                : 0;
        }
        
        return $status;
    }
    
    /**
     * URL-friendly slug oluştur
     */
    public function generateSlugForLocale(string $locale, ?string $title = null): string
    {
        $title = $title ?: $this->getTranslated('title', $locale);
        
        if (!$title) {
            return '';
        }
        
        // Türkçe karakter dönüşümü
        $turkishMap = [
            'ğ' => 'g', 'Ğ' => 'G',
            'ş' => 's', 'Ş' => 'S', 
            'ı' => 'i', 'İ' => 'I',
            'ç' => 'c', 'Ç' => 'C',
            'ü' => 'u', 'Ü' => 'U',
            'ö' => 'o', 'Ö' => 'O'
        ];
        
        // Arapça/özel karakterler için basit temizlik
        $slug = strtr($title, $turkishMap);
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9\-\s]/', '', $slug);
        $slug = preg_replace('/[\s\-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
}