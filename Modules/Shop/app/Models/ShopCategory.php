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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;

class ShopCategory extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable;
    use HasTranslations;
    use HasSeo;
    use HasFactory;
    use HasMediaManagement;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'description',
        'image_url',
        'icon_class',
        'level',
        'path',
        'sort_order',
        'is_active',
        'show_in_menu',
        'show_in_homepage',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'level' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'show_in_homepage' => 'boolean',
    ];

    protected array $translatable = [
        'title',
        'slug',
        'description',
    ];

    protected $appends = [
        'depth_level',
        'indent_px',
    ];

    protected array $mediaConfig = [];

    public function getIdAttribute(): int
    {
        return (int) $this->category_id;
    }

    public function sluggable(): array
    {
        return [];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeVisibleInMenu(Builder $query): Builder
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeVisibleOnHomepage(Builder $query): Builder
    {
        return $query->where('show_in_homepage', true);
    }

    public function products(): HasMany
    {
        return $this->hasMany(ShopProduct::class, 'category_id', 'category_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'category_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'category_id');
    }

    public function getDepthLevelAttribute(): int
    {
        return $this->calculateDepth();
    }

    public function getIndentPxAttribute(): int
    {
        $depth = $this->depth_level;

        return max(0, $depth) * 16;
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
        \Log::info('Shop category translation completed', [
            'category_id' => $this->category_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'category_id';
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

        return url('/shop/category/' . ltrim($slug, '/'));
    }

    public function getSeoFallbackImage(): ?string
    {
        if ($this->hasMedia('category_image')) {
            return $this->getFirstMediaUrl('category_image');
        }

        return $this->image_url;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
        ];
    }

    protected function getMediaConfig(): array
    {
        $conversions = array_keys(
            config('modules.media.conversions', ['thumb', 'medium', 'large', 'responsive'])
        );

        return [
            'category_image' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => config('modules.media.max_items.featured', 1),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => $conversions,
                'sortable' => false,
            ],
        ];
    }

    protected static function newFactory(): \Modules\Shop\Database\Factories\ShopCategoryFactory
    {
        return \Modules\Shop\Database\Factories\ShopCategoryFactory::new();
    }

    private function calculateDepth(array $visited = []): int
    {
        if (in_array($this->category_id, $visited, true)) {
            \Log::warning('Circular reference detected in shop category hierarchy', [
                'category_id' => $this->category_id,
                'visited' => $visited,
            ]);

            return 0;
        }

        if ($this->parent_id === null) {
            return 0;
        }

        $visited[] = $this->category_id;
        $parent = $this->parent()->first();

        if ($parent === null) {
            return 0;
        }

        return $parent->calculateDepth($visited) + 1;
    }
}
