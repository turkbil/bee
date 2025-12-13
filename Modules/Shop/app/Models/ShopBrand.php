<?php

declare(strict_types=1);

namespace Modules\Shop\App\Models;

use App\Contracts\TranslatableEntity;
use App\Models\BaseModel;
use App\Traits\HasSeo;
use App\Traits\HasTranslations;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;

class ShopBrand extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable;
    use Searchable;
    use HasTranslations;
    use HasSeo;
    use HasFactory;
    use HasMediaManagement;

    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'logo_url',
        'website_url',
        'country_code',
        'founded_year',
        'headquarters',
        'certifications',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'certifications' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'founded_year' => 'integer',
    ];

    protected array $translatable = [
        'title',
        'slug',
        'description',
    ];

    protected array $mediaConfig = [
        'hero' => [
            'type' => 'image',
            'single_file' => true,
            'conversions' => ['thumb', 'medium', 'large'],
        ],
    ];

    public function getIdAttribute(): int
    {
        return (int) $this->brand_id;
    }

    public function sluggable(): array
    {
        return [];
    }

    /**
     * Scout index name for multi-tenant setup
     */
    public function searchableAs(): string
    {
        return tenancy()->initialized
            ? 'shop_brands_tenant_' . tenant('id')
            : 'shop_brands';
    }

    /**
     * Prepare brand payload for Meilisearch
     */
    public function toSearchableArray(): array
    {
        $locale = app()->getLocale();

        $title = $this->getTranslated('title', $locale) ?? ($this->title[$locale] ?? '');
        $slug = $this->getTranslated('slug', $locale) ?? ($this->slug[$locale] ?? '');
        $description = $this->getTranslated('description', $locale) ?? ($this->description[$locale] ?? '');

        return [
            'brand_id' => $this->brand_id,
            'title' => $title,
            'slug' => $slug,
            'description' => strip_tags($description ?? ''),
            'country_code' => $this->country_code,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'certifications' => $this->certifications ?? [],
        ];
    }

    public function getScoutKey(): mixed
    {
        return $this->brand_id;
    }

    public function getScoutKeyName(): string
    {
        return 'brand_id';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function products(): HasMany
    {
        return $this->hasMany(ShopProduct::class, 'brand_id', 'brand_id');
    }

    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'description' => 'html',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info('Shop brand translation completed', [
            'brand_id' => $this->brand_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'brand_id';
    }

    public function getSeoFallbackTitle(): ?string
    {
        $locale = app()->getLocale();

        return $this->getTranslated('title', $locale) ?? ($this->title[$locale] ?? null);
    }

    public function getSeoFallbackDescription(): ?string
    {
        $locale = app()->getLocale();
        $content = $this->getTranslated('description', $locale) ?? ($this->description[$locale] ?? null);

        if (is_string($content)) {
            return Str::limit(strip_tags($content), 160);
        }

        return null;
    }

    public function getSeoFallbackKeywords(): array
    {
        $title = $this->getSeoFallbackTitle();

        if ($title === null) {
            return [];
        }

        $words = array_filter(
            explode(' ', strtolower($title)),
            static fn(string $word): bool => strlen($word) > 3
        );

        return array_slice($words, 0, 5);
    }

    public function getSeoFallbackCanonicalUrl(): ?string
    {
        $locale = app()->getLocale();
        $slug = $this->getTranslated('slug', $locale) ?? ($this->slug[$locale] ?? null);

        if ($slug === null) {
            return null;
        }

        return url('/shop/brand/' . ltrim($slug, '/'));
    }

    public function getSeoFallbackImage(): ?string
    {
        if ($this->hasMedia('brand_logo')) {
            return $this->getFirstMediaUrl('brand_logo');
        }

        return $this->logo_url;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->website_url ?? $this->getSeoFallbackCanonicalUrl(),
            'logo' => $this->getSeoFallbackImage(),
            'sameAs' => array_filter([$this->website_url]),
        ];
    }

    protected function getMediaConfig(): array
    {
        $conversions = array_keys(
            config('modules.media.conversions', ['thumb', 'medium', 'large', 'responsive'])
        );

        return [
            'brand_logo' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => config('modules.media.max_items.featured', 1),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => $conversions,
                'sortable' => false,
            ],
        ];
    }

    protected static function newFactory(): \Modules\Shop\Database\Factories\ShopBrandFactory
    {
        return \Modules\Shop\Database\Factories\ShopBrandFactory::new();
    }
}
