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

class PortfolioCategory extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable, HasTranslations, HasSeo, HasFactory, HasMediaManagement;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
        'sort_order',
        'parent_id',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'slug', 'description'];

    /**
     * ID accessor - category_id'yi id olarak döndür
     */
    public function getIdAttribute()
    {
        return $this->category_id;
    }

    /**
     * Sluggable Ayarları - JSON çoklu dil desteği için devre dışı
     */
    public function sluggable(): array
    {
        return [];
    }

    /**
     * Aktif kategorileri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Portfolios ilişkisi
     */
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class, 'portfolio_category_id', 'category_id');
    }

    /**
     * Parent category ilişkisi
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'category_id');
    }

    /**
     * Children categories ilişkisi
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'category_id');
    }

    /**
     * Calculate depth level based on parent (recursive)
     * Circular reference korumalı
     */
    public function getDepthLevelAttribute(): int
    {
        return $this->calculateDepth();
    }

    private function calculateDepth(array $visited = []): int
    {
        // Circular reference kontrolü
        if (in_array($this->category_id, $visited)) {
            \Log::warning("Circular reference detected in category hierarchy", [
                'category_id' => $this->category_id,
                'visited' => $visited
            ]);
            return 0;
        }

        if (!$this->parent_id) {
            return 0;
        }

        $visited[] = $this->category_id;
        $parent = $this->parent()->first();

        if (!$parent) {
            return 0;
        }

        return $parent->calculateDepth($visited) + 1;
    }

    /**
     * Get indent pixels for display
     */
    public function getIndentPxAttribute(): int
    {
        return $this->depth_level * 30; // 30px per level
    }

    /**
     * HasSeo trait fallback implementations
     */

    protected function getSeoFallbackTitle(): ?string
    {
        return $this->getTranslated('title', app()->getLocale()) ?? $this->title;
    }

    protected function getSeoFallbackDescription(): ?string
    {
        $description = $this->getTranslated('description', app()->getLocale()) ?? $this->description;

        if (is_string($description)) {
            return \Illuminate\Support\Str::limit(strip_tags($description), 160);
        }

        return null;
    }

    protected function getSeoFallbackKeywords(): array
    {
        $title = $this->getSeoFallbackTitle();

        if ($title) {
            $words = array_filter(explode(' ', strtolower($title)), function($word) {
                return strlen($word) > 3;
            });

            return array_slice($words, 0, 5);
        }

        return [];
    }

    protected function getSeoFallbackCanonicalUrl(): ?string
    {
        $slug = $this->getTranslated('slug', app()->getLocale()) ?? $this->slug;

        if ($slug) {
            return url('/portfolio/category/' . ltrim($slug, '/'));
        }

        return null;
    }

    protected function getSeoFallbackImage(): ?string
    {
        // Kategori görseli varsa onu kullan
        if ($this->hasMedia('category_image')) {
            return $this->getFirstMediaUrl('category_image');
        }

        return null;
    }

    protected function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
        ];
    }

    /**
     * Get or create SEO setting
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

            $this->load('seoSetting');
        }

        return $this->seoSetting;
    }

    /**
     * TranslatableEntity interface implementation
     */
    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'description' => 'html',
            'slug' => 'auto'
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info("Portfolio Category çevirisi tamamlandı", [
            'category_id' => $this->category_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData)
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'category_id';
    }

    protected static function newFactory()
    {
        return \Modules\Portfolio\Database\Factories\PortfolioCategoryFactory::new();
    }

    /**
     * Media collections config
     * HasMediaManagement trait kullanır
     */
    protected function getMediaConfig(): array
    {
        return [
            'category_image' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => config('modules.media.max_items.featured', 1),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => array_keys(config('modules.media.conversions', ['thumb', 'medium', 'large', 'responsive'])),
                'sortable' => false,
            ],
        ];
    }

    protected $mediaConfig;
}
