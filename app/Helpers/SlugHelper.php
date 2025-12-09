<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugHelper
{
    /**
     * Model için unique slug oluştur
     * 
     * @param string $modelClass Model sınıfı (örn: Modules\Page\App\Models\Page)
     * @param string $baseSlug Temel slug
     * @param string $language Dil kodu (tr, en, ar)
     * @param string $slugColumn Slug column adı (varsayılan: slug)
     * @param string $primaryKey Primary key column (varsayılan: model'in primaryKey'i)
     * @param mixed $excludeId Hariç tutulacak ID (güncelleme durumu)
     * @return string Unique slug
     */
    public static function generateUniqueSlug(
        string $modelClass,
        string $baseSlug,
        string $language,
        string $slugColumn = 'slug',
        ?string $primaryKey = null,
        $excludeId = null
    ): string {
        // Model instance oluştur
        $model = new $modelClass();
        
        // Primary key belirtilmemişse model'den al
        if (!$primaryKey) {
            $primaryKey = $model->getKeyName();
        }
        
        // Base slug'ı temizle ve normalize et
        $baseSlug = static::normalizeSlug($baseSlug);
        
        // Önce base slug'ın unique olup olmadığını kontrol et
        $query = $modelClass::where($slugColumn . '->' . $language, $baseSlug);
        
        if ($excludeId) {
            $query->where($primaryKey, '!=', $excludeId);
        }
        
        if (!$query->exists()) {
            return $baseSlug; // Zaten unique
        }
        
        // Unique değilse sayı ekle
        $counter = 1;
        
        do {
            $newSlug = $baseSlug . '-' . $counter;
            $exists = $modelClass::where($slugColumn . '->' . $language, $newSlug)
                ->when($excludeId, function($query) use ($primaryKey, $excludeId) {
                    $query->where($primaryKey, '!=', $excludeId);
                })
                ->exists();
            $counter++;
        } while ($exists && $counter <= 1000); // 1000'e kadar dene
        
        return $newSlug;
    }
    
    /**
     * Title'dan slug oluştur ve unique yap
     * 
     * @param string $modelClass Model sınıfı
     * @param string $title Başlık
     * @param string $language Dil kodu
     * @param string $slugColumn Slug column adı
     * @param string $primaryKey Primary key column
     * @param mixed $excludeId Hariç tutulacak ID
     * @return string Unique slug
     */
    public static function generateFromTitle(
        string $modelClass,
        string $title,
        string $language,
        string $slugColumn = 'slug',
        ?string $primaryKey = null,
        $excludeId = null
    ): string {
        // Title'dan slug oluştur
        $baseSlug = static::createSlugFromTitle($title);
        
        // Unique yap
        return static::generateUniqueSlug(
            $modelClass,
            $baseSlug,
            $language,
            $slugColumn,
            $primaryKey,
            $excludeId
        );
    }
    
    /**
     * Title'dan slug oluştur (Türkçe karakter desteği ile)
     * 
     * @param string $title Başlık
     * @return string Slug
     */
    public static function createSlugFromTitle(string $title): string
    {
        // normalizeSlug fonksiyonunu kullanarak tutarlılık sağla
        $slug = preg_replace('/[\s]+/', '-', $title); // Boşlukları tire yap
        return static::normalizeSlug($slug);
    }
    
    /**
     * Slug'ı normalize et
     * 
     * @param string $slug Slug
     * @return string Normalize edilmiş slug
     */
    public static function normalizeSlug(string $slug): string
    {
        $slug = strtolower(trim($slug));

        // ÖNCE Türkçe karakterleri dönüştür (ü→u, ö→o)
        // Bu Almanca dönüşüm (ü→ue) ile çakışmayı önler
        $turkishMap = [
            'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
            'Ç' => 'c', 'Ğ' => 'g', 'I' => 'i', 'İ' => 'i', 'Ö' => 'o', 'Ş' => 's', 'Ü' => 'u',
        ];
        $slug = strtr($slug, $turkishMap);

        // Diğer diller için karakter dönüşümleri (Türkçe hariç)
        $characterMaps = [
            // Arabic characters
            'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
            'د' => 'd', 'ذ' => 'dh', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's',
            'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
            'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'و' => 'w', 'ي' => 'y',
            'ى' => 'a', 'ة' => 'h', 'ء' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'a', 'ؤ' => 'w',
            'ئ' => 'y', 'ً' => '', 'ٌ' => '', 'ٍ' => '', 'َ' => '', 'ُ' => '', 'ِ' => '', 'ّ' => '', 'ْ' => '',

            // French/Accent characters (ö, ü zaten Türkçe'de dönüştürüldü)
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'ñ' => 'n', 'Ñ' => 'n',
            'ß' => 'ss',

            // Russian (Cyrillic) characters
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
            'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
            'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            // Greek characters
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h',
            'θ' => 'th', 'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'x',
            'ο' => 'o', 'π' => 'p', 'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f',
            'χ' => 'ch', 'ψ' => 'ps', 'ω' => 'w',

            // Polish characters
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ś' => 's',
            'ź' => 'z', 'ż' => 'z',
        ];

        // Apply remaining character mappings
        $slug = strtr($slug, $characterMaps);

        // Sadece a-z, 0-9 ve tire bırak
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $slug = preg_replace('/\-+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
    
    /**
     * Slug'ın unique olup olmadığını kontrol et
     * 
     * @param string $modelClass Model sınıfı
     * @param string $slug Slug
     * @param string $language Dil kodu
     * @param string $slugColumn Slug column adı
     * @param string $primaryKey Primary key column
     * @param mixed $excludeId Hariç tutulacak ID
     * @return bool Unique ise true
     */
    public static function isUnique(
        string $modelClass,
        string $slug,
        string $language,
        string $slugColumn = 'slug',
        ?string $primaryKey = null,
        $excludeId = null
    ): bool {
        // Model instance oluştur
        $model = new $modelClass();
        
        // Primary key belirtilmemişse model'den al
        if (!$primaryKey) {
            $primaryKey = $model->getKeyName();
        }
        
        $query = $modelClass::where($slugColumn . '->' . $language, $slug);
        
        if ($excludeId) {
            $query->where($primaryKey, '!=', $excludeId);
        }
        
        return !$query->exists();
    }
    
    /**
     * Çoklu dil slug'larını işle
     * 
     * @param string $modelClass Model sınıfı
     * @param array $slugs Dil bazında slug'lar ['tr' => 'slug', 'en' => 'slug']
     * @param array $titles Dil bazında başlıklar (boş slug'lar için)
     * @param string $slugColumn Slug column adı
     * @param mixed $excludeId Hariç tutulacak ID
     * @return array İşlenmiş slug'lar
     */
    public static function processMultiLanguageSlugs(
        string $modelClass,
        array $slugs,
        array $titles = [],
        string $slugColumn = 'slug',
        $excludeId = null
    ): array {
        $processedSlugs = [];
        
        foreach ($slugs as $language => $slug) {
            if (empty($slug) && !empty($titles[$language])) {
                // Boş slug - title'dan oluştur
                $processedSlugs[$language] = static::generateFromTitle(
                    $modelClass,
                    $titles[$language],
                    $language,
                    $slugColumn,
                    null,
                    $excludeId
                );
            } elseif (!empty($slug)) {
                // Dolu slug - unique yap
                $processedSlugs[$language] = static::generateUniqueSlug(
                    $modelClass,
                    $slug,
                    $language,
                    $slugColumn,
                    null,
                    $excludeId
                );
            }
        }
        
        return $processedSlugs;
    }
    
    /**
     * Slug'ı normalize et - boşsa title'dan oluştur (Otomatik Düzeltme)
     * 
     * @param string $slug Slug
     * @param string $title Title (fallback için)
     * @return string Normalize edilmiş slug
     */
    public static function normalizeSlugWithFallback(string $slug, string $title = ''): string
    {
        $normalizedSlug = static::normalizeSlug($slug);
        
        // OTOMATIK DÜZELTME: Eğer normalize edilmiş slug boşsa title'dan oluştur
        if (empty($normalizedSlug) && !empty($title)) {
            $normalizedSlug = static::normalizeSlug($title);
        }
        
        return $normalizedSlug;
    }
    
    /**
     * Model için slug validation kuralları oluştur
     * 
     * @param array $languages Dil kodları ['tr', 'en', 'ar']
     * @param string $fieldPrefix Field prefix ('multiLangInputs' gibi)
     * @param bool $required Zorunlu mu
     * @return array Validation kuralları
     */
    public static function getValidationRules(
        array $languages,
        string $fieldPrefix = 'multiLangInputs',
        bool $required = false
    ): array {
        $rules = [];
        
        foreach ($languages as $language) {
            $rules["{$fieldPrefix}.{$language}.slug"] = [
                $required ? 'required' : 'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9\-]+$/',
            ];
        }
        
        return $rules;
    }
    
    /**
     * Validation mesajları al
     * 
     * @param array $languages Dil kodları
     * @param string $fieldPrefix Field prefix
     * @return array Validation mesajları
     */
    public static function getValidationMessages(
        array $languages,
        string $fieldPrefix = 'multiLangInputs'
    ): array {
        $messages = [];
        
        foreach ($languages as $language) {
            $messages["{$fieldPrefix}.{$language}.slug.required"] = "{$language} dili için slug zorunludur";
            $messages["{$fieldPrefix}.{$language}.slug.regex"] = "{$language} dili için slug sadece küçük harf, rakam ve tire içerebilir";
            $messages["{$fieldPrefix}.{$language}.slug.max"] = "{$language} dili için slug en fazla 255 karakter olabilir";
        }
        
        return $messages;
    }
}