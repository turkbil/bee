<?php
namespace Modules\Page\App\Models;

use App\Models\BaseModel;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;
use Modules\ReviewSystem\App\Traits\HasReviews;

class Page extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement, HasReviews;

    protected $primaryKey = 'page_id';

    /**
     * Boot method - Homepage uniqueness validation
     */
    protected static function boot()
    {
        parent::boot();

        // Kaydetmeden önce is_homepage kontrolü
        static::saving(function ($page) {
            // Eğer bu sayfa homepage olarak işaretleniyorsa
            if ($page->is_homepage) {
                // Başka homepage var mı kontrol et (kendi ID'si hariç)
                $existingHomepage = static::where('is_homepage', true)
                    ->where('page_id', '!=', $page->page_id ?? 0)
                    ->first();

                if ($existingHomepage) {
                    // Mevcut homepage'i kaldır, yeni homepage yap
                    $existingHomepage->update(['is_homepage' => false]);
                }
            }
        });
    }

    protected $fillable = [
        'title',
        'slug',
        'body',
        'css',
        'js',
        'is_active',
        'is_homepage',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body'];

    /**
     * ID accessor - page_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->page_id;
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
     * Ana sayfayı getir
     */
    public function scopeHomepage($query)
    {
        return $query->where('is_homepage', true);
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
            $words = array_filter(explode(' ', strtolower($title)), function($word) {
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
        // Check if page has any images in content
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
            '@type' => 'WebPage',
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
     * Tüm schema'ları al (WebPage + Breadcrumb)
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. WebPage Schema (Ana içerik)
        $pageSchema = $this->getSchemaMarkup();
        if ($pageSchema) {
            $schemas['webpage'] = $pageSchema;
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
     * Get or create SEO setting for this page
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
        // Page modülü için özel işlemler burada yapılabilir
        // Örneğin: Cache temizleme, sitemap güncelleme vb.
        
        \Log::info("Page çevirisi tamamlandı", [
            'page_id' => $this->page_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData)
        ]);
    }

    /**
     * Primary key field adı
     */
    public function getPrimaryKeyName(): string
    {
        return 'page_id';
    }

    /**
     * Generate full URL for the page
     *
     * @param string|null $locale Dil kodu (null ise current locale)
     * @return string Tam URL
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);

        // ModuleSlugService kullanarak dinamik route slug'ını al
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Page', 'show');
        $defaultLocale = get_tenant_default_locale();

        // Default locale ise locale prefix'i ekleme
        if ($locale === $defaultLocale) {
            return url("/{$moduleSlug}/{$slug}");
        }

        // Diğer diller için locale prefix ekle
        return url("/{$locale}/{$moduleSlug}/{$slug}");
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
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Modules\Page\Database\Factories\PageFactory::new();
    }

}