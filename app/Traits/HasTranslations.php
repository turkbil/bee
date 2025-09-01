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
            $value = $this->getAttribute($field);
            // Non-translatable fields için direkt değeri döndür
            if (is_array($value)) {
                // Arrays should be returned as JSON for non-translatable fields like keywords/tags
                return json_encode($value);
            }
            return is_string($value) ? $value : (string) $value;
        }
        
        $translations = $this->getAttribute($field);
        
        // Double-encoded JSON kontrolü ve düzeltme
        if (is_string($translations)) {
            try {
                $decoded = json_decode($translations, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $translations = $decoded;
                } else {
                    // JSON decode edilemedi, direkt string döndür
                    return $translations;
                }
            } catch (\Exception $e) {
                // JSON decode hatası, direkt string döndür
                return $translations;
            }
        }
        
        // Array değilse direkt döndür
        if (!is_array($translations)) {
            return is_string($translations) ? $translations : (string) $translations;
        }
        
        // İstenen dil varsa döndür
        if (isset($translations[$locale]) && !empty($translations[$locale])) {
            $value = $translations[$locale];
            return is_string($value) ? $value : (string) $value;
        }
        
        
        // Fallback yazı sistemi - çevirisi olmayan dillerde fallback dildeki YAZIYI göster
        return $this->getFallbackTranslation($translations, $locale);
    }
    
    /**
     * Fallback çeviri sistemi - Tenant varsayılan dili öncelikli
     */
    private function getFallbackTranslation(array $translations, string $requestedLocale): ?string
    {
        // 1. Tenant varsayılan dilini bul ve kullan
        $defaultLocale = $this->getTenantDefaultLanguage();
        if (isset($translations[$defaultLocale]) && !empty($translations[$defaultLocale])) {
            $value = $translations[$defaultLocale];
            return is_string($value) ? $value : (string) $value;
        }
        
        // 2. Sistem varsayılanı (tr) varsa döndür
        if ($defaultLocale !== 'tr' && isset($translations['tr']) && !empty($translations['tr'])) {
            $value = $translations['tr'];
            return is_string($value) ? $value : (string) $value;
        }
        
        // 3. İlk dolu dili bul
        foreach ($translations as $locale => $content) {
            if (!empty($content)) {
                return is_string($content) ? $content : (string) $content;
            }
        }
        
        // 4. Hiçbiri yoksa null
        \Log::warning('⚠️ Fallback translation bulunamadı', [
            'requested_locale' => $requestedLocale,
            'tenant_default_locale' => $defaultLocale,
            'available_translations' => array_keys($translations)
        ]);
        
        return null;
    }
    
    /**
     * Tenant varsayılan dilini al - Her tenant'ın kendi varsayılanı
     */
    private function getTenantDefaultLanguage(): string
    {
        try {
            // Tenant'tan varsayılan dili al
            if (function_exists('tenant') && tenant()) {
                $currentTenant = tenant();
                
                // Tenant'ın tenant_default_locale alanı varsa onu kullan
                if (isset($currentTenant->tenant_default_locale) && !empty($currentTenant->tenant_default_locale)) {
                    return $currentTenant->tenant_default_locale;
                }
            }
            
            // Tenant yoksa veya tenant_default_locale yoksa sistem varsayılanı
            return config('app.locale', 'tr');
            
        } catch (\Exception $e) {
            // Hata durumunda sistem varsayılanı
            return config('app.locale', 'tr');
        }
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
        
        // Double-encoded JSON kontrolü ve düzeltme
        if (is_string($translations)) {
            try {
                $decoded = json_decode($translations, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $translations = $decoded;
                } else {
                    return !empty($translations);
                }
            } catch (\Exception $e) {
                return !empty($translations);
            }
        }
        
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
        $fields = $this->getTranslatableFields();
        
        // If getTranslatableFields returns associative array (from TranslatableEntity interface)
        if (is_array($fields) && count($fields) > 0 && !is_numeric(array_keys($fields)[0])) {
            return array_key_exists($field, $fields);
        }
        
        // If it returns simple array (from $translatable property)
        return in_array($field, $fields);
    }
    
    /**
     * Çevrilebilir alanları al
     */
    public function getTranslatableFields(): array
    {
        // If the model implements TranslatableEntity interface, it should override this method
        // Otherwise, use the $translatable property
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
    
    /**
     * Alias metod - getTranslated() ile aynı işlevi görür
     * Eski kod uyumluluğu için
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        return $this->getTranslated($field, $locale);
    }
}