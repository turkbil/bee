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
use Modules\MediaManagement\App\Traits\HasMediaManagement;
use Spatie\MediaLibrary\HasMedia;

class ShopProductVariant extends BaseModel implements TranslatableEntity, HasMedia
{
    use Sluggable;
    use HasTranslations;
    use HasSeo;
    use HasFactory;
    use HasMediaManagement;

    protected $primaryKey = 'variant_id';

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'title',
        'option_values',
        'price_modifier',
        'cost_price',
        'stock_quantity',
        'reserved_quantity',
        'weight',
        'dimensions',
        'image_url',
        'images',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'option_values' => 'array',
        'price_modifier' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'images' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected array $translatable = ['title'];

    protected array $mediaConfig = [];

    public function getIdAttribute(): int
    {
        return (int) $this->variant_id;
    }

    public function sluggable(): array
    {
        return [];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'product_id');
    }

    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
        ];
    }

    public function hasSeoSettings(): bool
    {
        return false;
    }

    public function afterTranslation(string $targetLanguage, array $translatedData): void
    {
        \Log::info('Shop product variant translation completed', [
            'variant_id' => $this->variant_id,
            'target_language' => $targetLanguage,
            'translated_fields' => array_keys($translatedData),
        ]);
    }

    public function getPrimaryKeyName(): string
    {
        return 'variant_id';
    }

    public function getSeoFallbackTitle(): ?string
    {
        return null;
    }

    public function getSeoFallbackDescription(): ?string
    {
        return null;
    }

    public function getSeoFallbackKeywords(): array
    {
        return [];
    }

    public function getSeoFallbackCanonicalUrl(): ?string
    {
        return null;
    }

    public function getSeoFallbackImage(): ?string
    {
        return null;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        return null;
    }

    protected function getMediaConfig(): array
    {
        return [
            'variant_image' => [
                'type' => 'image',
                'single_file' => true,
                'max_items' => 1,
                'max_size' => config('modules.media.max_file_size', 10240),
                'conversions' => array_keys(config('modules.media.conversions', ['thumb', 'medium'])),
                'sortable' => false,
            ],
        ];
    }

    protected static function newFactory()
    {
        return \Modules\Shop\Database\Factories\ShopProductVariantFactory::new();
    }
}

