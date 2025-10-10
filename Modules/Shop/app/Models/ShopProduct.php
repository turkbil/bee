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
use Illuminate\Support\Str;
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;

class ShopProduct extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable;
    use HasTranslations;
    use HasSeo;
    use HasFactory;
    use HasMediaManagement;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'brand_id',
        'sku',
        'model_number',
        'barcode',
        'title',
        'slug',
        'short_description',
        'long_description',
        'product_type',
        'condition',
        'price_on_request',
        'base_price',
        'compare_at_price',
        'cost_price',
        'currency',
        'deposit_required',
        'deposit_amount',
        'deposit_percentage',
        'installment_available',
        'max_installments',
        'stock_tracking',
        'current_stock',
        'low_stock_threshold',
        'allow_backorder',
        'lead_time_days',
        'weight',
        'dimensions',
        'technical_specs',
        'features',
        'highlighted_features',
        'media_gallery',
        'use_cases',
        'competitive_advantages',
        'target_industries',
        'faq_data',
        'video_url',
        'manual_pdf_url',
        'is_active',
        'is_featured',
        'is_bestseller',
        'view_count',
        'sales_count',
        'published_at',
        'warranty_info',
        'shipping_info',
        'tags',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'short_description' => 'array',
        'long_description' => 'array',
        'price_on_request' => 'boolean',
        'base_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'deposit_required' => 'boolean',
        'deposit_amount' => 'decimal:2',
        'deposit_percentage' => 'integer',
        'installment_available' => 'boolean',
        'max_installments' => 'integer',
        'stock_tracking' => 'boolean',
        'current_stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'allow_backorder' => 'boolean',
        'lead_time_days' => 'integer',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'technical_specs' => 'array',
        'features' => 'array',
        'highlighted_features' => 'array',
        'media_gallery' => 'array',
        'use_cases' => 'array',
        'competitive_advantages' => 'array',
        'target_industries' => 'array',
        'faq_data' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'view_count' => 'integer',
        'sales_count' => 'integer',
        'published_at' => 'datetime',
        'warranty_info' => 'array',
        'shipping_info' => 'array',
        'tags' => 'array',
    ];

    protected array $translatable = [
        'title',
        'slug',
        'short_description',
        'long_description',
    ];

    protected $appends = [
        'discount_percentage',
        'has_discount',
        'final_price',
    ];

    protected array $mediaConfig = [];

    public function getIdAttribute(): int
    {
        return (int) $this->product_id;
    }

    public function sluggable(): array
    {
        return [];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeBestseller(Builder $query): Builder
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('current_stock', '>', 0);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ShopCategory::class, 'category_id', 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ShopBrand::class, 'brand_id', 'brand_id');
    }

    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'short_description' => 'text',
            'long_description' => 'html',
            'slug' => 'auto',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return true;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info('Shop product translation completed', [
            'product_id' => $this->product_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'product_id';
    }

    protected function getSeoFallbackTitle(): ?string
    {
        $locale = app()->getLocale();

        return $this->getTranslated('title', $locale) ?? ($this->title[$locale] ?? null);
    }

    protected function getSeoFallbackDescription(): ?string
    {
        $locale = app()->getLocale();
        $content = $this->getTranslated('short_description', $locale)
            ?? ($this->short_description[$locale] ?? null);

        if (is_string($content)) {
            return Str::limit(strip_tags($content), 160);
        }

        return null;
    }

    protected function getSeoFallbackKeywords(): array
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

    protected function getSeoFallbackCanonicalUrl(): ?string
    {
        $locale = app()->getLocale();
        $slug = $this->getTranslated('slug', $locale) ?? ($this->slug[$locale] ?? null);

        if ($slug === null) {
            return null;
        }

        return url('/shop/product/' . ltrim($slug, '/'));
    }

    protected function getSeoFallbackImage(): ?string
    {
        if ($this->hasMedia('featured_image')) {
            return $this->getFirstMediaUrl('featured_image');
        }

        return null;
    }

    protected function getSeoFallbackSchemaMarkup(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $this->getSeoFallbackTitle(),
            'description' => $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'image' => $this->getSeoFallbackImage(),
            'offers' => [
                '@type' => 'Offer',
                'price' => $this->base_price,
                'priceCurrency' => $this->currency,
                'availability' => $this->current_stock > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
    }

    protected function getMediaConfig(): array
    {
        $conversions = array_keys(
            config('modules.media.conversions', ['thumb', 'medium', 'large', 'responsive'])
        );

        return [
            'featured_image' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => config('modules.media.max_items.featured', 1),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => $conversions,
                'sortable' => false,
            ],
            'gallery' => [
                'type' => 'image',
                'single_file' => false,
                'max_items' => config('modules.media.max_items.gallery', 50),
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => $conversions,
                'sortable' => true,
            ],
        ];
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->compare_at_price === null || $this->base_price === null) {
            return null;
        }

        if ((float) $this->compare_at_price <= (float) $this->base_price) {
            return null;
        }

        $discount = (($this->compare_at_price - $this->base_price) / $this->compare_at_price) * 100;

        return (int) round($discount);
    }

    public function getHasDiscountAttribute(): bool
    {
        $discount = $this->discount_percentage;

        return $discount !== null && $discount > 0;
    }

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->base_price ?? 0.0);
    }

    protected static function newFactory(): \Modules\Shop\Database\Factories\ShopProductFactory
    {
        return \Modules\Shop\Database\Factories\ShopProductFactory::new();
    }
}
