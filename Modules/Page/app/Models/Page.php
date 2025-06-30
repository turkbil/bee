<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends BaseModel
{
    use Sluggable, HasTranslations;

    protected $primaryKey = 'page_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'css',
        'js',
        'seo',
        'is_active',
        'is_homepage',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'seo' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     * Artık HasTranslations trait'inde generateSlugForLocale() kullanılacak
     */
    public function sluggable(): array
    {
        return [
            // JSON column çalışmadığı için devre dışı
            // 'slug' => [
            //     'source' => 'title',
            //     'unique' => true,
            //     'onUpdate' => false,
            // ],
        ];
    }

    /**
     * Aktif sayfaları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Ana sayfayı getir
     */
    public function scopeHomepage($query)
    {
        return $query->where('is_homepage', true);
    }
    
    // SEO Helper Methods
    
    /**
     * SEO verilerini varsayılan değerlerle birleştir
     */
    public function getSeoAttribute($value)
    {
        $decoded = json_decode($value, true) ?: [];
        $defaults = $this->getSeoDefaults();
        
        // Her dil için default değerleri merge et
        foreach ($this->getAvailableLocales() as $locale) {
            $decoded[$locale] = array_merge($defaults, $decoded[$locale] ?? []);
        }
        
        return $decoded;
    }
    
    /**
     * SEO verilerini kaydet - boş değerleri temizle
     */
    public function setSeoAttribute($value)
    {
        if (!is_array($value)) {
            $this->attributes['seo'] = json_encode([]);
            return;
        }
        
        $cleaned = [];
        foreach ($value as $locale => $seoData) {
            if (is_array($seoData)) {
                $cleaned[$locale] = array_filter($seoData, function($v) {
                    return !is_null($v) && $v !== '' && $v !== [];
                });
            }
        }
        
        $this->attributes['seo'] = json_encode($cleaned);
    }
    
    /**
     * Belirli bir locale ve field için SEO değeri al
     */
    public function getSeoField(string $locale, string $field, $default = null)
    {
        return $this->seo[$locale][$field] ?? $default;
    }
    
    /**
     * Belirli bir locale ve field için SEO değeri güncelle
     */
    public function updateSeoField(string $locale, string $field, $value)
    {
        $seo = $this->seo;
        $seo[$locale][$field] = $value;
        $this->update(['seo' => $seo]);
    }
    
    /**
     * Meta title al - fallback hierarchy ile
     */
    public function getMetaTitle(string $locale = 'tr'): string
    {
        // 1. SEO meta_title
        $metaTitle = $this->getSeoField($locale, 'meta_title');
        if (!empty($metaTitle)) {
            return $metaTitle;
        }
        
        // 2. Sayfa title
        $pageTitle = $this->getTranslated('title', $locale);
        if (!empty($pageTitle)) {
            return $pageTitle;
        }
        
        // 3. Fallback
        return config('app.name', 'Site');
    }
    
    /**
     * Meta description al
     */
    public function getMetaDescription(string $locale = 'tr'): string
    {
        return $this->getSeoField($locale, 'meta_description', '');
    }
    
    /**
     * Keywords al
     */
    public function getKeywords(string $locale = 'tr'): array
    {
        return $this->getSeoField($locale, 'keywords', []);
    }
    
    /**
     * OG Image al
     */
    public function getOgImage(string $locale = 'tr'): ?string
    {
        return $this->getSeoField($locale, 'og_image');
    }
    
    /**
     * Robots meta al
     */
    public function getRobots(string $locale = 'tr'): string
    {
        return $this->getSeoField($locale, 'robots', 'index,follow');
    }
    
    /**
     * Canonical URL al
     */
    public function getCanonicalUrl(string $locale = 'tr'): ?string
    {
        $canonical = $this->getSeoField($locale, 'canonical_url');
        
        if (empty($canonical)) {
            // Auto-generate from slug
            $slug = $this->getTranslated('slug', $locale);
            if ($slug) {
                return url('/' . $slug);
            }
        }
        
        return $canonical;
    }
    
    /**
     * SEO varsayılan değerleri
     */
    protected function getSeoDefaults(): array
    {
        return [
            'meta_title' => null,
            'meta_description' => null,
            'keywords' => [],
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
            'canonical_url' => null,
            'robots' => 'index,follow',
            'schema_markup' => null,
        ];
    }
    
    /**
     * Mevcut dilleri al
     */
    protected function getAvailableLocales(): array
    {
        // TenantLanguage'dan al veya fallback
        try {
            return \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_active', true)
                ->pluck('code')
                ->toArray();
        } catch (\Exception $e) {
            return ['tr', 'en']; // Fallback
        }
    }
    
}