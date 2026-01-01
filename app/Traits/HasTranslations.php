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
     * @param string $field Alan adı
     * @param string|null $locale Dil kodu
     * @param bool $useFallback Fallback kullanılsın mı? (varsayılan: true)
     */
    public function getTranslated(string $field, ?string $locale = null, bool $useFallback = true): ?string
    {
        $locale = $locale ?: App::getLocale() ?: 'tr';

        // Alan çevrilebilir mi kontrol et
        if (!$this->isTranslatable($field)) {
            // getAttribute() sonsuz döngüye girmemek için getAttributeFromArray kullan
            $value = $this->getAttributeFromArray($field);
            // Non-translatable fields için direkt değeri döndür
            if (is_array($value)) {
                // Arrays should be returned as JSON for non-translatable fields like keywords/tags
                return json_encode($value);
            }
            return is_string($value) ? $value : (string) $value;
        }

        // Raw attributes array'inden direkt al (cast/accessor atla)
        $translations = $this->getAttributeFromArray($field);

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

        // Fallback kullanılmasın denildiyse, boş dil için null döndür
        if (!$useFallback) {
            return $translations[$locale] ?? null;
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
     * Override setAttribute - Translatable field'lar için otomatik JSON encode
     * Bu, plain string değerlerin JSON formatında kaydedilmesini sağlar
     */
    public function setAttribute($key, $value)
    {
        // Eğer translatable field ise
        if ($this->isTranslatable($key)) {
            // Zaten array ise (JSON formatında) direkt kaydet
            if (is_array($value)) {
                // JSON string olarak kaydet
                $this->attributes[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                return $this;
            }

            // Zaten JSON string ise kontrol et
            if (is_string($value) && !empty($value)) {
                $decoded = json_decode($value, true);

                // Geçerli JSON ve array ise direkt kaydet
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $this->attributes[$key] = $value;
                    return $this;
                }

                // Plain string ise, mevcut çevirileri koru ve sadece aktif locale'i güncelle
                $currentValue = $this->getAttributeFromArray($key);
                $currentTranslations = [];

                if (is_string($currentValue)) {
                    $decodedCurrent = json_decode($currentValue, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedCurrent)) {
                        $currentTranslations = $decodedCurrent;
                    }
                } elseif (is_array($currentValue)) {
                    $currentTranslations = $currentValue;
                }

                // Mevcut locale için değeri güncelle
                $locale = app()->getLocale() ?: 'tr';
                $currentTranslations[$locale] = $value;

                // JSON string olarak kaydet
                $this->attributes[$key] = json_encode($currentTranslations, JSON_UNESCAPED_UNICODE);
                return $this;
            }

            // Boş veya null değer
            if (empty($value)) {
                $this->attributes[$key] = null;
                return $this;
            }
        }

        // Normal field, parent'a devret
        return parent::setAttribute($key, $value);
    }

    /**
     * Override getAttribute - Translatable field'lar için otomatik parse
     */
    public function getAttribute($key)
    {
        // Eğer translatable field ise, önce JSON parse et
        if ($this->isTranslatable($key)) {
            // Raw value al (attributes array'inden direkt)
            $value = $this->getAttributeFromArray($key);

            // JSON parse ve locale'ye göre döndür
            if (is_string($value)) {
                try {
                    // DOUBLE-ENCODE PROTECTION: Handle strings wrapped in quotes
                    // Database might have: ""{\"tr\":\"value\"}""  or  "{\"tr\":\"value\"}"
                    $value = trim($value);

                    // First attempt: direct JSON decode
                    $decoded = json_decode($value, true);

                    // If decode failed or returned string, try removing outer quotes
                    if (json_last_error() !== JSON_ERROR_NONE || is_string($decoded)) {
                        // Check for outer quotes and remove them
                        if (strlen($value) >= 2 && $value[0] === '"' && $value[strlen($value) - 1] === '"') {
                            // Use json_decode on the outer quotes to properly unescape
                            $unquoted = json_decode($value);
                            if (is_string($unquoted)) {
                                $value = $unquoted;
                            }
                        }

                        // Try decoding again
                        $decoded = json_decode($value, true);

                        // If still a string (double-encoded), decode one more time
                        if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                            $secondDecode = json_decode($decoded, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($secondDecode)) {
                                $decoded = $secondDecode;
                            }
                        }
                    }

                    if (is_array($decoded)) {
                        $locale = app()->getLocale() ?: 'tr';

                        // Mevcut locale'deki değer varsa döndür
                        if (isset($decoded[$locale]) && !empty($decoded[$locale])) {
                            return $decoded[$locale];
                        }

                        // Fallback
                        return $this->getFallbackTranslation($decoded, $locale);
                    }
                } catch (\Exception $e) {
                    // JSON parse hatası, direkt döndür
                    return $value;
                }
            }

            return $value;
        }

        // Normal field, parent'a devret (relationlar vs.)
        return parent::getAttribute($key);
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

    /**
     * Override originalIsEquivalent - Translatable field'lar için doğru karşılaştırma
     * Bu, false dirty detection'ı önler
     */
    public function originalIsEquivalent($key)
    {
        // Translatable field değilse parent'a devret
        if (!$this->isTranslatable($key)) {
            return parent::originalIsEquivalent($key);
        }

        // Original değer
        $original = $this->getOriginal($key);
        // Mevcut değer (attributes'dan direkt)
        $current = $this->attributes[$key] ?? null;

        // Her ikisi de null/boş
        if (empty($original) && empty($current)) {
            return true;
        }

        // Biri null, diğeri dolu
        if (empty($original) || empty($current)) {
            return false;
        }

        // JSON string'leri array'e çevir ve karşılaştır
        $originalArray = is_string($original) ? json_decode($original, true) : $original;
        $currentArray = is_string($current) ? json_decode($current, true) : $current;

        // JSON decode başarısız olduysa string karşılaştırma yap
        if (!is_array($originalArray) || !is_array($currentArray)) {
            return $original === $current;
        }

        // Array karşılaştırma (key sırasını görmezden gel)
        ksort($originalArray);
        ksort($currentArray);

        return $originalArray === $currentArray;
    }

    /**
     * Raw attribute değerini al (accessor'ı atla)
     * Admin form'ları için JSON formatında veri döndürür
     */
    public function getRawTranslations(string $field): array
    {
        if (!$this->isTranslatable($field)) {
            return [];
        }

        $value = $this->getAttributeFromArray($field);

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // Plain string ise mevcut locale'e ata
            return [app()->getLocale() ?: 'tr' => $value];
        }

        if (is_array($value)) {
            return $value;
        }

        return [];
    }
}