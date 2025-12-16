<?php

namespace Modules\Portfolio\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Modules\ReviewSystem\App\Traits\HasReviews;

class Portfolio extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, HasReviews;

    protected $primaryKey = 'portfolio_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'is_active',
        'portfolio_category_id',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];

    /**
     * ID accessor - portfolio_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->portfolio_id;
    }

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
     * Kategori ilişkisi
     */
    public function category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id', 'category_id');
    }

    /**
     * HasSeo trait fallback implementations
     */

    /**
     * Get fallback title for SEO
     */
    public function getSeoFallbackTitle(): ?string
    {
        return $this->getTranslated('title', app()->getLocale()) ?? $this->title;
    }

    /**
     * Get fallback description for SEO
     */
    public function getSeoFallbackDescription(): ?string
    {
        $content = $this->getTranslated('body', app()->getLocale()) ?? $this->body;

        if (is_string($content)) {
            return \Illuminate\Support\Str::limit(strip_tags($content), 160);
        }

        return null;
    }

    /**
     * Get fallback keywords for SEO
     */
    public function getSeoFallbackKeywords(): array
    {
        $title = $this->getSeoFallbackTitle();

        if ($title) {
            // Extract meaningful words from title
            $words = array_filter(explode(' ', strtolower($title)), function ($word) {
                return strlen($word) > 3; // Only words longer than 3 chars
            });

            return array_slice($words, 0, 5); // Max 5 keywords
        }

        return [];
    }

    /**
     * Get fallback canonical URL
     */
    public function getSeoFallbackCanonicalUrl(): ?string
    {
        // Use existing getUrl() method which includes correct module prefix
        return $this->getUrl();
    }

    /**
     * Get fallback image for social sharing
     */
    public function getSeoFallbackImage(): ?string
    {
        // Check if portfolio has any images in content
        $content = $this->getTranslated('body', app()->getLocale()) ?? $this->body;

        if (is_string($content) && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get fallback schema markup
     */
    public function getSeoFallbackSchemaMarkup(): ?array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
        ];

        // Add image if available
        $image = $this->getSeoFallbackImage();
        if ($image) {
            $schema['image'] = $image;
        }

        // ⭐ Aggregated Rating - HasReviews trait'inden alınır
        if (method_exists($this, 'averageRating') && method_exists($this, 'ratingsCount')) {
            $avgRating = $this->averageRating();
            $ratingCount = $this->ratingsCount();

            if ($avgRating > 0 && $ratingCount > 0) {
                $schema['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => (string) number_format($avgRating, 1),
                    'reviewCount' => $ratingCount,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ];
            }
        }

        return $schema;
    }

    /**
     * Tüm schema'ları al (CreativeWork + Breadcrumb)
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. CreativeWork Schema (Ana içerik)
        $portfolioSchema = $this->getSchemaMarkup();
        if ($portfolioSchema) {
            $schemas['creativework'] = $portfolioSchema;
        }

        // 2. Breadcrumb Schema (varsa)
        if (method_exists($this, 'getBreadcrumbSchema')) {
            $breadcrumbSchema = $this->getBreadcrumbSchema();
            if ($breadcrumbSchema) {
                $schemas['breadcrumb'] = $breadcrumbSchema;
            }
        }

        return $schemas;
    }

    /**
     * Get or create SEO setting for this portfolio
     */
    public function getOrCreateSeoSetting()
    {
        if (!$this->seoSetting) {
            $this->seoSetting()->create([
                'titles' => [],
                'descriptions' => [],
                'og_titles' => [],
                'og_descriptions' => [],
                'robots_meta' => [
                    'index' => true,
                    'follow' => true,
                    'archive' => true
                ],
                'status' => 'active'
            ]);

            // Refresh relationship
            $this->load('seoSetting');
        }

        return $this->seoSetting;
    }

    /**
     * 🌍 UNIVERSAL TRANSLATION INTERFACE METHODS
     * TranslatableEntity interface implementation
     */

    /**
     * Çevrilebilir alanları döndür
     */
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',  // Basit metin çevirisi
            'body' => 'html',   // HTML korunarak çeviri
            'slug' => 'auto'    // Otomatik oluştur (title'dan)
        ];
    }

    /**
     * SEO desteği var mı?
     */
    public function hasSeoSettings(): bool
    {
        return true;
    }

    /**
     * Çeviri sonrası ek işlemler
     */
    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        // Portfolio modülü için özel işlemler burada yapılabilir
        // Örneğin: Cache temizleme, sitemap güncelleme vb.

        \Log::info("Portfolio çevirisi tamamlandı", [
            'portfolio_id' => $this->portfolio_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData)
        ]);
    }

    /**
     * Primary key field adı
     */
    public function getPrimaryKeyName(): string
    {
        return 'portfolio_id';
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Modules\Portfolio\Database\Factories\PortfolioFactory::new();
    }

    /**
     * Media collections config
     * HasMediaManagement trait kullanır
     */
    protected function getMediaConfig(): array
    {
        return [
            'hero' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => config('modules.media.max_items.featured', 1),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => array_keys(config('modules.media.conversions', ['thumb', 'medium', 'large', 'responsive'])),
                'sortable' => false,
            ],
            'gallery' => [
                'type' => 'image',
                'single_file' => false,
                'max_items' => config('modules.media.max_items.gallery', 50),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => array_keys(config('modules.media.conversions', ['thumb', 'medium', 'large', 'responsive'])),
                'sortable' => true,
            ],
        ];
    }

    protected $mediaConfig;

    /**
     * Portfolio için locale-aware URL oluştur
     * ItemList Schema ve diğer linkler için kullanılır
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);

        // Modül slug'ını al (tenant tarafından özelleştirilebilir)
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Portfolio', 'show');

        // Varsayılan dil kontrolü
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            // Varsayılan dil için prefix yok
            return url("/{$moduleSlug}/{$slug}");
        }

        // Diğer diller için prefix ekle
        return url("/{$locale}/{$moduleSlug}/{$slug}");
    }
}
