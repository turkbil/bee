<?php

namespace Modules\Blog\App\Models;

use App\Models\BaseModel;
use App\Models\Tag;
use App\Traits\HasTranslations;
use App\Traits\HasSeo;
use App\Traits\HasUniversalSchemas;
use App\Contracts\TranslatableEntity;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Modules\MediaManagement\App\Traits\HasMediaManagement;

class Blog extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasUniversalSchemas, HasFactory, HasMediaManagement;

    protected $primaryKey = 'blog_id';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'excerpt',
        'published_at',
        'is_featured',
        'status',
        'is_active',
        'blog_category_id',
        'faq_data',
        'howto_data',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'excerpt' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'faq_data' => 'array',
        'howto_data' => 'array',
    ];

    protected $appends = ['tag_list'];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'body', 'excerpt'];

    /**
     * ID accessor - blog_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->blog_id;
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
     * Aktif blogları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Yayınlanmış blogları getir (Basitleştirilmiş)
     */
    public function scopePublished($query)
    {
        $now = now();

        return $query
            ->where('is_active', true) // Aktif olanlar
            ->where(function ($inner) use ($now) {
                $inner->whereNull('published_at') // Tarih yoksa yayında
                    ->orWhere('published_at', '<=', $now); // Veya geçmiş tarih
            });
    }

    /**
     * Öne çıkan blogları getir
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Belirli kategorideki blogları getir
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('blog_category_id', $categoryId);
    }

    /**
     * Belirli bir tag slug'ına göre filtrele.
     */
    public function scopeWithTagSlug($query, string $slug)
    {
        return $query->whereHas('tags', function ($tagQuery) use ($slug) {
            $tagQuery->where('slug', $slug);
        });
    }

    /**
     * Taslak blogları getir (Basitleştirilmiş)
     */
    public function scopeDraft($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Zamanlanmış blogları getir (Basitleştirilmiş)
     */
    public function scopeScheduled($query)
    {
        return $query->where('is_active', true)
                    ->where('published_at', '>', now());
    }

    /**
     * Kategori ilişkisi
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id', 'category_id');
    }

    /**
     * Etiket ilişkisi.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables', 'taggable_id', 'tag_id')->withTimestamps();
    }

    /**
     * Etiketleri isimlerine göre eşitle.
     *
     * @param  array<int, string>  $tagNames
     */
    public function syncTagsByName(array $tagNames, ?string $type = 'blog'): void
    {
        $tagIds = Tag::syncFromNames($tagNames, $type);
        $this->tags()->sync($tagIds);
        $this->load('tags');
    }

    /**
     * Etiket isimlerini koleksiyon olarak getir.
     */
    public function getTagListAttribute(): array
    {
        return $this->tags
            ->pluck('name')
            ->filter()
            ->map(fn ($name) => trim((string) $name))
            ->unique()
            ->values()
            ->all();
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
            return Str::limit(strip_tags($content), 160);
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

            $keywords = array_slice($words, 0, 5); // Max 5 keywords

            if ($this->relationLoaded('tags')) {
                $tagKeywords = $this->tags->pluck('slug')->filter()->map(fn ($slug) => str_replace('-', ' ', $slug))->all();
                $keywords = array_merge($keywords, $tagKeywords);
            }

            return array_values(array_unique($keywords));
        }

        return [];
    }

    /**
     * Get fallback canonical URL
     */
    public function getSeoFallbackCanonicalUrl(): ?string
    {
        $slug = $this->getTranslated('slug', app()->getLocale()) ?? $this->slug;

        if ($slug) {
            return url('/' . ltrim($slug, '/'));
        }

        return null;
    }

    /**
     * Get fallback image for social sharing
     */
    public function getSeoFallbackImage(): ?string
    {
        // Check if blog has any images in content
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
        $currentLocale = app()->getLocale();
        $featuredImage = $this->getFirstMedia('featured_image');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'datePublished' => $this->published_at ? $this->published_at->toISOString() : $this->created_at->toISOString(),
            'dateModified' => $this->updated_at->toISOString(),
            'author' => [
                '@type' => 'Organization',
                'name' => setting('site_title') ?? config('app.name'),
                'url' => url('/')
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('site_title') ?? config('app.name'),
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => setting('site_logo') ? cdn(setting('site_logo')) : asset('favicon.ico')
                ]
            ],
            'isPartOf' => [
                '@type' => 'Blog',
                'name' => setting('site_title') ?? config('app.name'),
                'url' => url('/')
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $this->getSeoFallbackCanonicalUrl()
            ]
        ];

        // Featured image ekle
        if ($featuredImage) {
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $featuredImage->getUrl(),
                'width' => $featuredImage->getCustomProperty('width') ?? 1200,
                'height' => $featuredImage->getCustomProperty('height') ?? 630
            ];
        }

        // Kategori ekle
        if ($this->category) {
            $categoryName = $this->category->getTranslated('name', $currentLocale);
            if ($categoryName) {
                $schema['articleSection'] = $categoryName;
            }
        }

        // Etiketler ekle
        if ($this->relationLoaded('tags') && $this->tags->isNotEmpty()) {
            $schema['keywords'] = $this->tags->pluck('name')->implode(', ');
        }

        // Okuma süresi ekle
        $readingTime = $this->calculateReadingTime($currentLocale);
        if ($readingTime) {
            $schema['timeRequired'] = 'PT' . $readingTime . 'M'; // ISO 8601 format
        }

        return $schema;
    }

    /**
     * Get or create SEO setting for this blog
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
     * Toplam kelime sayısını hesapla (Body + FAQ + HowTo)
     * SEO ve Schema için kullanılır
     */
    public function getWordCount(?string $locale = null): int
    {
        $locale = $locale ?? app()->getLocale();
        $totalWords = 0;

        // 1. Body content
        $body = $this->getTranslated('body', $locale);
        if (!empty($body)) {
            $plainBody = strip_tags($body);
            $totalWords += str_word_count($plainBody);
        }

        // 2. FAQ data
        if (!empty($this->faq_data)) {
            $faqData = is_string($this->faq_data) ? json_decode($this->faq_data, true) : $this->faq_data;
            if (is_array($faqData)) {
                foreach ($faqData as $faq) {
                    if (!empty($faq['question']) && is_string($faq['question'])) {
                        $totalWords += str_word_count($faq['question']);
                    }
                    if (!empty($faq['answer']) && is_string($faq['answer'])) {
                        $totalWords += str_word_count($faq['answer']);
                    }
                }
            }
        }

        // 3. HowTo data
        if (!empty($this->howto_data)) {
            $howtoData = is_string($this->howto_data) ? json_decode($this->howto_data, true) : $this->howto_data;
            if (is_array($howtoData)) {
                // Name
                if (!empty($howtoData['name']) && is_string($howtoData['name'])) {
                    $totalWords += str_word_count($howtoData['name']);
                }
                // Description
                if (!empty($howtoData['description']) && is_string($howtoData['description'])) {
                    $totalWords += str_word_count($howtoData['description']);
                }
                // Steps
                if (!empty($howtoData['steps']) && is_array($howtoData['steps'])) {
                    foreach ($howtoData['steps'] as $step) {
                        if (!empty($step['name']) && is_string($step['name'])) {
                            $totalWords += str_word_count($step['name']);
                        }
                        if (!empty($step['text']) && is_string($step['text'])) {
                            $totalWords += str_word_count($step['text']);
                        }
                    }
                }
            }
        }

        return $totalWords;
    }

    /**
     * Reading time hesapla (dakika)
     * Ortalama okuma hızı: 200 kelime/dakika
     */
    public function calculateReadingTime(?string $locale = null): int
    {
        $locale = $locale ?? app()->getLocale();
        $content = $this->getTranslated('body', $locale);

        if (!$content) {
            return 0;
        }

        // HTML tag'lerini temizle
        $plainText = strip_tags($content);

        // Kelime sayısını hesapla
        $wordCount = str_word_count($plainText);

        // Reading time hesapla (minimum 1 dakika)
        return max(1, ceil($wordCount / 200));
    }

    /**
     * Reading time'ı otomatik güncelle
     */
    /**
     * Excerpt oluştur (eğer yoksa)
     */
    public function generateExcerpt(?string $locale = null, int $length = 160): ?string
    {
        $locale = $locale ?? app()->getLocale();

        // Mevcut excerpt varsa onu kullan
        $excerpt = $this->getTranslated('excerpt', $locale);
        if ($excerpt) {
            return $excerpt;
        }

        // İçerikten excerpt oluştur
        $content = $this->getTranslated('body', $locale);
        if (!$content) {
            return null;
        }

        $plainText = strip_tags($content);
        return \Illuminate\Support\Str::limit($plainText, $length);
    }

    /**
     * Blog yayınlandı mı? (Basitleştirilmiş mantık)
     */
    public function isPublished(): bool
    {
        if (!$this->is_active) {
            return false; // Pasifse taslaktır
        }

        if (!$this->published_at) {
            return true; // Aktif + tarih yoksa yayında
        }

        return $this->published_at->isPast(); // Aktif + geçmiş tarih = yayında
    }

    /**
     * Blog zamanlanmış mı? (Basitleştirilmiş mantık)
     */
    public function isScheduled(): bool
    {
        return $this->is_active &&
               $this->published_at &&
               $this->published_at->isFuture();
    }

    /**
     * Blog taslak mı? (Basitleştirilmiş mantık)
     */
    public function isDraft(): bool
    {
        return !$this->is_active; // Pasifse taslaktır
    }

    /**
     * Otomatik status hesapla
     */
    public function getAutoStatus(): string
    {
        if ($this->isDraft()) {
            return 'draft';
        }

        if ($this->isScheduled()) {
            return 'scheduled';
        }

        return 'published';
    }

    /**
     * Çevrilebilir alanları döndür
     */
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',      // Basit metin çevirisi
            'body' => 'html',       // HTML korunarak çeviri
            'excerpt' => 'text',    // Basit metin çevirisi
            'slug' => 'auto'        // Otomatik oluştur (title'dan)
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
        // Blog modülü için özel işlemler burada yapılabilir
        // Örneğin: Cache temizleme, sitemap güncelleme vb.

        \Log::info("Blog çevirisi tamamlandı", [
            'blog_id' => $this->blog_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData)
        ]);
    }

    /**
     * Primary key field adı
     */
    public function getPrimaryKeyName(): string
    {
        return 'blog_id';
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Modules\Blog\Database\Factories\BlogFactory::new();
    }

    /**
     * Media collections config
     * HasMediaManagement trait kullanır
     */
    protected function getMediaConfig(): array
    {
        return [
            'featured_image' => [
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
     * Blog için locale-aware URL oluştur
     * ItemList Schema ve diğer linkler için kullanılır
     */
    public function getUrl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getTranslated('slug', $locale);

        // Modül slug'ını al (tenant tarafından özelleştirilebilir)
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'show');

        // Varsayılan dil kontrolü
        $defaultLocale = get_tenant_default_locale();

        if ($locale === $defaultLocale) {
            // Varsayılan dil için prefix yok
            return url("/{$moduleSlug}/{$slug}");
        }

        // Diğer diller için prefix ekle
        return url("/{$locale}/{$moduleSlug}/{$slug}");
    }

    /**
     * İlgili blog yazılarını getir
     */
    public function getRelatedBlogs(int $limit = 6): \Illuminate\Support\Collection
    {
        return \App\Services\RelatedContentService::getRelatedBlogs($this, $limit);
    }

    /**
     * Generate BreadcrumbList Schema for Blog
     * Override from HasUniversalSchemas trait
     *
     * Structure: Home → Blog → Category (if exists) → Current Post
     *
     * @return array|null
     */
    public function getBreadcrumbSchema(): ?array
    {
        $locale = app()->getLocale();
        $breadcrumbs = [];
        $position = 1;

        // 1. Home
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Ana Sayfa'),
            'item' => url('/')
        ];

        // 2. Blog Ana Sayfa
        $moduleSlug = \App\Services\ModuleSlugService::getSlug('Blog', 'index');
        $blogIndexUrl = $locale === get_tenant_default_locale()
            ? url("/{$moduleSlug}")
            : url("/{$locale}/{$moduleSlug}");

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => __('Blog'),
            'item' => $blogIndexUrl
        ];

        // 3. Category (varsa)
        if ($this->category) {
            $categoryName = $this->category->getTranslated('name', $locale);
            $categorySlug = $this->category->getTranslated('slug', $locale);

            if ($categoryName && $categorySlug) {
                $categoryUrl = $locale === get_tenant_default_locale()
                    ? url("/{$moduleSlug}/category/{$categorySlug}")
                    : url("/{$locale}/{$moduleSlug}/category/{$categorySlug}");

                $breadcrumbs[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $categoryName,
                    'item' => $categoryUrl
                ];
            }
        }

        // 4. Current Blog Post
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $this->getTranslated('title', $locale),
            'item' => $this->getUrl($locale)
        ];

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        ];
    }
}
