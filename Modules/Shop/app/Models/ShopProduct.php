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
        'body',
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
        'primary_specs',
        'use_cases',
        'competitive_advantages',
        'target_industries',
        'faq_data',
        'accessories',
        'certifications',
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
        'parent_product_id',
        'is_master_product',
        'variant_type',
    ];

    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'short_description' => 'array',
        'body' => 'array',
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
        'primary_specs' => 'array',
        'use_cases' => 'array',
        'competitive_advantages' => 'array',
        'target_industries' => 'array',
        'faq_data' => 'array',
        'accessories' => 'array',
        'certifications' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_master_product' => 'boolean',
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
        'body',
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

    public function variants()
    {
        return $this->hasMany(ShopProductVariant::class, 'product_id', 'product_id');
    }

    /**
     * Get parent product (for variants)
     */
    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(ShopProduct::class, 'parent_product_id', 'product_id');
    }

    /**
     * Get child products (variants of this product)
     */
    public function childProducts()
    {
        return $this->hasMany(ShopProduct::class, 'parent_product_id', 'product_id')
            ->where('is_active', true)
            ->orderBy('variant_type');
    }

    /**
     * Scope: Only variant products
     */
    public function scopeVariantsOnly(Builder $query): Builder
    {
        return $query->whereNotNull('parent_product_id');
    }

    /**
     * Scope: Only master products
     */
    public function scopeMasterOnly(Builder $query): Builder
    {
        return $query->where('is_master_product', true);
    }

    /**
     * Check if this product is a variant
     */
    public function isVariant(): bool
    {
        return !is_null($this->parent_product_id);
    }

    public function getTranslatableFields(): array
    {
        return [
            'title' => 'text',
            'short_description' => 'text',
            'body' => 'html',
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

    public function getSeoFallbackTitle(): ?string
    {
        $locale = app()->getLocale();

        return $this->getTranslated('title', $locale) ?? ($this->title[$locale] ?? null);
    }

    public function getSeoFallbackDescription(): ?string
    {
        $locale = app()->getLocale();

        // Meta description için zengin içerik birleştir - SMART STRATEGY
        $descriptionParts = [];
        $targetLength = 155; // Google'ın önerdiği max (160 yerine 155 güvenli)

        // 1. Short Description (Kısa ve öz - öncelikli)
        $shortDesc = $this->getTranslated('short_description', $locale)
            ?? ($this->short_description[$locale] ?? null);

        if (is_string($shortDesc) && !empty(trim(strip_tags($shortDesc)))) {
            $cleanShortDesc = strip_tags($shortDesc);
            $cleanShortDesc = html_entity_decode($cleanShortDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanShortDesc = preg_replace('/\s+/', ' ', $cleanShortDesc);
            $cleanShortDesc = trim($cleanShortDesc);

            // Short description'ı 80 karaktere sınırla (avantajlara yer bırak)
            $descriptionParts[] = Str::limit($cleanShortDesc, 80, '');
        }

        // 2. Competitive Advantages (En önemli 2 avantaj ekle)
        if (!empty($this->competitive_advantages)) {
            $advantageTexts = [];
            foreach (array_slice($this->competitive_advantages, 0, 2) as $advantage) {
                if (is_array($advantage)) {
                    $advantageText = is_array($advantage['text'] ?? null)
                        ? ($advantage['text'][$locale] ?? '')
                        : ($advantage['text'] ?? '');
                    if ($advantageText) {
                        $cleanAdvantage = strip_tags($advantageText);
                        $cleanAdvantage = html_entity_decode($cleanAdvantage, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $cleanAdvantage = preg_replace('/\s+/', ' ', $cleanAdvantage);
                        $advantageTexts[] = trim($cleanAdvantage);
                    }
                }
            }
            if (!empty($advantageTexts)) {
                // Avantajları birleştir ve kalan alana sığdır
                $advantagesText = implode(', ', $advantageTexts);
                $currentLength = strlen(implode(' ', $descriptionParts));
                $remainingSpace = $targetLength - $currentLength - 3; // 3 for " | "

                if ($remainingSpace > 30) { // En az 30 karakter varsa ekle
                    $descriptionParts[] = Str::limit($advantagesText, $remainingSpace, '');
                }
            }
        }

        // 3. Use Cases (Eğer hala yer varsa)
        if (!empty($this->use_cases)) {
            $currentLength = strlen(implode(' | ', $descriptionParts));
            $remainingSpace = $targetLength - $currentLength - 3;

            if ($remainingSpace > 40) { // En az 40 karakter varsa use case ekle
                $useCaseTexts = [];
                foreach (array_slice($this->use_cases, 0, 2) as $useCase) {
                    if (is_array($useCase)) {
                        $useCaseTitle = is_array($useCase['title'] ?? null)
                            ? ($useCase['title'][$locale] ?? '')
                            : ($useCase['title'] ?? '');
                        if ($useCaseTitle) {
                            $useCaseTexts[] = strip_tags($useCaseTitle);
                        }
                    }
                }
                if (!empty($useCaseTexts)) {
                    $useCasesText = implode(', ', $useCaseTexts);
                    $descriptionParts[] = Str::limit($useCasesText, $remainingSpace, '');
                }
            }
        }

        // Birleştir ve final kontrol
        if (!empty($descriptionParts)) {
            $fullDescription = implode(' | ', $descriptionParts);
            // Maksimum 160 karaktere kesin sınır
            return Str::limit($fullDescription, 160);
        }

        // Fallback: body (eğer short description yoksa)
        $longDesc = $this->getTranslated('body', $locale)
            ?? ($this->body[$locale] ?? null);

        if (is_string($longDesc) && !empty(trim(strip_tags($longDesc)))) {
            $cleanLongDesc = strip_tags($longDesc);
            $cleanLongDesc = html_entity_decode($cleanLongDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanLongDesc = preg_replace('/\s+/', ' ', $cleanLongDesc);
            return Str::limit(trim($cleanLongDesc), 160);
        }

        return null;
    }

    public function getSeoFallbackKeywords(): array
    {
        $keywords = [];
        $locale = app()->getLocale();

        // 1. Ürün adından anahtar kelimeler
        $title = $this->getSeoFallbackTitle();
        if ($title) {
            $titleWords = array_filter(
                explode(' ', strtolower($title)),
                static fn(string $word): bool => strlen($word) > 3
            );
            $keywords = array_merge($keywords, array_slice($titleWords, 0, 3));
        }

        // 2. Kategori adı
        if ($this->category) {
            $categoryTitle = $this->category->getTranslated('title', $locale);
            if ($categoryTitle) {
                $keywords[] = strtolower($categoryTitle);
            }
        }

        // 3. Marka adı
        if ($this->brand) {
            $brandName = $this->brand->getTranslated('name', $locale) ?? $this->brand->name;
            if ($brandName) {
                $keywords[] = strtolower($brandName);
            }
        }

        // 4. Model numarası ve SKU
        if ($this->model_number) {
            $keywords[] = strtolower($this->model_number);
        }
        if ($this->sku) {
            $keywords[] = strtolower($this->sku);
        }

        // 5. Önemli özelliklerden (features)
        if (!empty($this->features)) {
            foreach (array_slice($this->features, 0, 3) as $feature) {
                if (is_array($feature) && isset($feature['title'])) {
                    $featureTitle = is_array($feature['title'])
                        ? ($feature['title'][$locale] ?? null)
                        : $feature['title'];
                    if ($featureTitle) {
                        $keywords[] = strtolower(strip_tags($featureTitle));
                    }
                }
            }
        }

        return array_unique(array_filter($keywords));
    }

    public function getSeoFallbackCanonicalUrl(): ?string
    {
        $locale = app()->getLocale();
        $slug = $this->getTranslated('slug', $locale) ?? ($this->slug[$locale] ?? null);

        if ($slug === null) {
            return null;
        }

        return url('/shop/product/' . ltrim($slug, '/'));
    }

    public function getSeoFallbackImage(): ?string
    {
        if ($this->hasMedia('featured_image')) {
            return $this->getFirstMediaUrl('featured_image');
        }

        return null;
    }

    public function getSeoFallbackSchemaMarkup(): ?array
    {
        $locale = app()->getLocale();
        $title = $this->getTranslated('title', $locale);
        $description = $this->getTranslated('short_description', $locale);
        $longDescription = $this->getTranslated('body', $locale);

        // Base Product Schema
        $productSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $title ?? $this->getSeoFallbackTitle(),
            'description' => $description ?? $this->getSeoFallbackDescription(),
            'url' => $this->getSeoFallbackCanonicalUrl(),
        ];

        // Rich Text Content - Full Product Description (body + features + use_cases)
        $richTextParts = [];

        // 1. Long Description (Ana detaylı açıklama)
        if (!empty($longDescription)) {
            $cleanLongDesc = strip_tags($longDescription);
            $cleanLongDesc = html_entity_decode($cleanLongDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanLongDesc = preg_replace('/\s+/', ' ', $cleanLongDesc);
            $richTextParts[] = trim($cleanLongDesc);
        }

        // 2. Features (Özellikler)
        if (!empty($this->features)) {
            $featureTexts = [];
            foreach ($this->features as $feature) {
                if (is_array($feature)) {
                    $featureTitle = is_array($feature['title'] ?? null)
                        ? ($feature['title'][$locale] ?? '')
                        : ($feature['title'] ?? '');
                    $featureDesc = is_array($feature['description'] ?? null)
                        ? ($feature['description'][$locale] ?? '')
                        : ($feature['description'] ?? '');

                    if ($featureTitle || $featureDesc) {
                        $featureTexts[] = trim($featureTitle . ': ' . strip_tags($featureDesc));
                    }
                }
            }
            if (!empty($featureTexts)) {
                $richTextParts[] = 'Özellikler: ' . implode('. ', $featureTexts);
            }
        }

        // 3. Use Cases (Kullanım Alanları)
        if (!empty($this->use_cases)) {
            $useCaseTexts = [];
            foreach ($this->use_cases as $useCase) {
                if (is_array($useCase)) {
                    $useCaseTitle = is_array($useCase['title'] ?? null)
                        ? ($useCase['title'][$locale] ?? '')
                        : ($useCase['title'] ?? '');
                    if ($useCaseTitle) {
                        $useCaseTexts[] = strip_tags($useCaseTitle);
                    }
                }
            }
            if (!empty($useCaseTexts)) {
                $richTextParts[] = 'Kullanım Alanları: ' . implode(', ', $useCaseTexts);
            }
        }

        // 4. Competitive Advantages (Rekabet Avantajları)
        if (!empty($this->competitive_advantages)) {
            $advantageTexts = [];
            foreach ($this->competitive_advantages as $advantage) {
                if (is_array($advantage)) {
                    $advantageText = is_array($advantage['text'] ?? null)
                        ? ($advantage['text'][$locale] ?? '')
                        : ($advantage['text'] ?? '');
                    if ($advantageText) {
                        $advantageTexts[] = strip_tags($advantageText);
                    }
                }
            }
            if (!empty($advantageTexts)) {
                $richTextParts[] = 'Avantajlar: ' . implode('. ', $advantageTexts);
            }
        }

        // 5. Target Industries (Hedef Sektörler)
        if (!empty($this->target_industries)) {
            $industryTexts = [];
            foreach ($this->target_industries as $industry) {
                if (is_array($industry)) {
                    $industryName = is_array($industry['name'] ?? null)
                        ? ($industry['name'][$locale] ?? '')
                        : ($industry['name'] ?? '');
                    if ($industryName) {
                        $industryTexts[] = strip_tags($industryName);
                    }
                } elseif (is_string($industry)) {
                    $industryTexts[] = strip_tags($industry);
                }
            }
            if (!empty($industryTexts)) {
                $richTextParts[] = 'Hedef Sektörler: ' . implode(', ', $industryTexts);
            }
        }

        // 6. Primary Specs (Ana Özellikler)
        if (!empty($this->primary_specs)) {
            $primarySpecTexts = [];
            foreach ($this->primary_specs as $spec) {
                if (is_array($spec)) {
                    $specLabel = is_array($spec['label'] ?? null)
                        ? ($spec['label'][$locale] ?? '')
                        : ($spec['label'] ?? '');
                    $specValue = $spec['value'] ?? '';
                    if ($specLabel && $specValue) {
                        $primarySpecTexts[] = strip_tags($specLabel) . ': ' . strip_tags($specValue);
                    }
                }
            }
            if (!empty($primarySpecTexts)) {
                $richTextParts[] = 'Ana Özellikler: ' . implode(', ', $primarySpecTexts);
            }
        }

        // 7. Highlighted Features (Öne Çıkan Özellikler)
        if (!empty($this->highlighted_features)) {
            $highlightedTexts = [];
            foreach ($this->highlighted_features as $highlight) {
                if (is_array($highlight)) {
                    $highlightText = is_array($highlight['text'] ?? null)
                        ? ($highlight['text'][$locale] ?? '')
                        : ($highlight['text'] ?? '');
                    if ($highlightText) {
                        $highlightedTexts[] = strip_tags($highlightText);
                    }
                } elseif (is_string($highlight)) {
                    $highlightedTexts[] = strip_tags($highlight);
                }
            }
            if (!empty($highlightedTexts)) {
                $richTextParts[] = 'Öne Çıkan Özellikler: ' . implode(', ', $highlightedTexts);
            }
        }

        // 8. Warranty Info (Garanti Bilgisi)
        if (!empty($this->warranty_info)) {
            if (is_array($this->warranty_info)) {
                $warrantyTexts = [];
                if (isset($this->warranty_info['duration_months'])) {
                    $warrantyTexts[] = $this->warranty_info['duration_months'] . ' ay garanti';
                }
                if (isset($this->warranty_info['description'])) {
                    $warrantyDesc = is_array($this->warranty_info['description'])
                        ? ($this->warranty_info['description'][$locale] ?? '')
                        : $this->warranty_info['description'];
                    if ($warrantyDesc) {
                        $warrantyTexts[] = strip_tags($warrantyDesc);
                    }
                }
                if (!empty($warrantyTexts)) {
                    $richTextParts[] = 'Garanti: ' . implode(', ', $warrantyTexts);
                }
            }
        }

        // 9. Accessories (Aksesuarlar)
        if (!empty($this->accessories)) {
            $accessoryTexts = [];
            foreach (array_slice($this->accessories, 0, 5) as $accessory) { // İlk 5 aksesuar
                if (is_array($accessory)) {
                    $accessoryName = is_array($accessory['name'] ?? null)
                        ? ($accessory['name'][$locale] ?? '')
                        : ($accessory['name'] ?? '');
                    if ($accessoryName) {
                        $accessoryTexts[] = strip_tags($accessoryName);
                    }
                } elseif (is_string($accessory)) {
                    $accessoryTexts[] = strip_tags($accessory);
                }
            }
            if (!empty($accessoryTexts)) {
                $richTextParts[] = 'Aksesuarlar: ' . implode(', ', $accessoryTexts);
            }
        }

        // 10. Certifications (Sertifikalar)
        if (!empty($this->certifications)) {
            $certTexts = [];
            foreach ($this->certifications as $cert) {
                if (is_array($cert)) {
                    $certName = is_array($cert['name'] ?? null)
                        ? ($cert['name'][$locale] ?? '')
                        : ($cert['name'] ?? '');
                    if ($certName) {
                        $certTexts[] = strip_tags($certName);
                    }
                } elseif (is_string($cert)) {
                    $certTexts[] = strip_tags($cert);
                }
            }
            if (!empty($certTexts)) {
                $richTextParts[] = 'Sertifikalar: ' . implode(', ', $certTexts);
            }
        }

        // Rich text'i schema'ya ekle (Google için önemli)
        if (!empty($richTextParts)) {
            $fullRichText = implode(' | ', $richTextParts);
            // Google max 5000 karakter önerir
            $productSchema['text'] = Str::limit($fullRichText, 5000);
        }

        // Image/Gallery
        $images = [];
        if ($this->hasMedia('featured_image')) {
            $images[] = $this->getFirstMediaUrl('featured_image');
        }
        foreach ($this->getMedia('gallery') as $media) {
            $images[] = $media->getUrl();
        }
        if (!empty($images)) {
            $productSchema['image'] = count($images) === 1 ? $images[0] : $images;
        }

        // SKU & Identifiers
        if ($this->sku) {
            $productSchema['sku'] = $this->sku;
        }
        if ($this->model_number) {
            $productSchema['mpn'] = $this->model_number;
        }
        if ($this->barcode) {
            $productSchema['gtin13'] = $this->barcode;
        }

        // Brand
        if ($this->brand) {
            $productSchema['brand'] = [
                '@type' => 'Brand',
                'name' => $this->brand->getTranslated('name', $locale) ?? $this->brand->name,
            ];
        }

        // Category
        if ($this->category) {
            $productSchema['category'] = $this->category->getTranslated('title', $locale) ?? ($this->category->title[$locale] ?? null);
        }

        // Offers (Fiyat Bilgileri)
        $offer = [
            '@type' => 'Offer',
            'url' => $this->getSeoFallbackCanonicalUrl(),
            'priceCurrency' => $this->currency ?? 'TRY',
            'availability' => $this->getAvailabilitySchemaUrl(),
            'itemCondition' => $this->getConditionSchemaUrl(),
        ];

        // Fiyat bilgisi
        if (!$this->price_on_request && $this->base_price) {
            $offer['price'] = number_format((float) $this->base_price, 2, '.', '');

            if ($this->compare_at_price && $this->compare_at_price > $this->base_price) {
                $offer['priceValidUntil'] = now()->addMonths(6)->format('Y-m-d');
            }
        } else {
            // Fiyat talep üzerine
            $offer['priceSpecification'] = [
                '@type' => 'PriceSpecification',
                'priceCurrency' => $this->currency ?? 'TRY',
            ];
        }

        // Seller bilgisi
        $offer['seller'] = [
            '@type' => 'Organization',
            'name' => setting('site_title', 'İXTİF Forklift'),
        ];

        // Stok durumu
        if ($this->stock_tracking && $this->current_stock !== null) {
            $offer['inventoryLevel'] = [
                '@type' => 'QuantitativeValue',
                'value' => $this->current_stock,
            ];
        }

        $productSchema['offers'] = $offer;

        // Aggregated Rating (eğer varsa review sistemi)
        // Bu alan şimdilik boş, gelecekte review modülü eklenirse aktif edilecek
        /*
        if ($this->reviews_count > 0) {
            $productSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $this->average_rating,
                'reviewCount' => $this->reviews_count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }
        */

        // Weight & Dimensions
        if ($this->weight) {
            $productSchema['weight'] = [
                '@type' => 'QuantitativeValue',
                'value' => $this->weight,
                'unitCode' => 'KGM',
            ];
        }

        if (!empty($this->dimensions)) {
            if (isset($this->dimensions['length']) || isset($this->dimensions['width']) || isset($this->dimensions['height'])) {
                $productSchema['depth'] = [
                    '@type' => 'QuantitativeValue',
                    'value' => $this->dimensions['length'] ?? 0,
                    'unitCode' => 'CMT',
                ];
                $productSchema['width'] = [
                    '@type' => 'QuantitativeValue',
                    'value' => $this->dimensions['width'] ?? 0,
                    'unitCode' => 'CMT',
                ];
                $productSchema['height'] = [
                    '@type' => 'QuantitativeValue',
                    'value' => $this->dimensions['height'] ?? 0,
                    'unitCode' => 'CMT',
                ];
            }
        }

        // Additional Properties (Teknik Özellikler)
        if (!empty($this->technical_specs)) {
            $additionalProperties = [];
            foreach ($this->technical_specs as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        if ($subKey !== '_title' && $subKey !== '_icon') {
                            $additionalProperties[] = [
                                '@type' => 'PropertyValue',
                                'name' => ucfirst($key) . ' - ' . ucfirst($subKey),
                                'value' => $this->formatSpecValueForSchema($subValue),
                            ];
                        }
                    }
                } else {
                    $additionalProperties[] = [
                        '@type' => 'PropertyValue',
                        'name' => ucfirst($key),
                        'value' => $this->formatSpecValueForSchema($value),
                    ];
                }
            }
            if (!empty($additionalProperties)) {
                $productSchema['additionalProperty'] = $additionalProperties;
            }
        }

        // Warranty
        if (!empty($this->warranty_info)) {
            if (is_array($this->warranty_info) && isset($this->warranty_info['duration_months'])) {
                $productSchema['warranty'] = [
                    '@type' => 'WarrantyPromise',
                    'durationOfWarranty' => [
                        '@type' => 'QuantitativeValue',
                        'value' => $this->warranty_info['duration_months'],
                        'unitCode' => 'MON',
                    ],
                ];
            }
        }

        return $productSchema;
    }

    /**
     * Get availability schema URL
     */
    private function getAvailabilitySchemaUrl(): string
    {
        if ($this->stock_tracking) {
            if ($this->current_stock > 0) {
                return 'https://schema.org/InStock';
            }
            if ($this->allow_backorder) {
                return 'https://schema.org/BackOrder';
            }
            return 'https://schema.org/OutOfStock';
        }

        // Stok takibi yoksa hep stokta kabul et
        return 'https://schema.org/InStock';
    }

    /**
     * Get condition schema URL
     */
    private function getConditionSchemaUrl(): string
    {
        return match ($this->condition) {
            'new' => 'https://schema.org/NewCondition',
            'refurbished' => 'https://schema.org/RefurbishedCondition',
            'used' => 'https://schema.org/UsedCondition',
            'damaged' => 'https://schema.org/DamagedCondition',
            default => 'https://schema.org/NewCondition',
        };
    }

    /**
     * Format spec value for schema
     */
    private function formatSpecValueForSchema($value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }
        if (is_array($value)) {
            if (isset($value['value']) && isset($value['unit'])) {
                return $value['value'] . ' ' . $value['unit'];
            }
            return json_encode($value);
        }
        return (string) $value;
    }

    /**
     * Generate Breadcrumb Schema
     */
    public function getBreadcrumbSchema(): array
    {
        $locale = app()->getLocale();
        $breadcrumbList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        $position = 1;

        // Home
        $homeTitle = match ($locale) {
            'en' => 'Home',
            'de' => 'Startseite',
            'fr' => 'Accueil',
            'es' => 'Inicio',
            'it' => 'Home',
            'ar' => 'الرئيسية',
            default => 'Ana Sayfa',
        };

        $breadcrumbList['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $homeTitle,
            'item' => url('/'),
        ];

        // Shop Index
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $shopIndexSlug = $moduleSlugService->getMultiLangSlug('Shop', 'index', $locale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = $locale !== $defaultLocale ? '/' . $locale : '';

        $shopTitle = match ($locale) {
            'en' => 'Shop',
            'de' => 'Geschäft',
            'fr' => 'Boutique',
            'es' => 'Tienda',
            'it' => 'Negozio',
            'ar' => 'متجر',
            default => 'Mağaza',
        };

        $breadcrumbList['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $shopTitle,
            'item' => url($localePrefix . '/' . $shopIndexSlug),
        ];

        // Category (if exists)
        if ($this->category) {
            $categoryTitle = $this->category->getTranslated('title', $locale);
            $categorySlug = $this->category->getTranslated('slug', $locale);

            $breadcrumbList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $categoryTitle,
                'item' => url($localePrefix . '/shop/category/' . $categorySlug),
            ];
        }

        // Current Product
        $productTitle = $this->getTranslated('title', $locale);
        $breadcrumbList['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $productTitle,
            'item' => $this->getSeoFallbackCanonicalUrl(),
        ];

        return $breadcrumbList;
    }

    /**
     * Generate FAQ Schema from faq_data
     */
    public function getFaqSchema(): ?array
    {
        if (empty($this->faq_data) || !is_array($this->faq_data)) {
            return null;
        }

        $locale = app()->getLocale();
        $resolveLocalized = function ($data) use ($locale) {
            if (!is_array($data)) {
                return $data;
            }
            $defaultLocale = get_tenant_default_locale();
            return $data[$locale] ?? ($data[$defaultLocale] ?? ($data['en'] ?? reset($data)));
        };

        $faqEntries = collect($this->faq_data)
            ->map(fn($faq) => is_array($faq) ? [
                'question' => $resolveLocalized($faq['question'] ?? null),
                'answer' => $resolveLocalized($faq['answer'] ?? null),
            ] : null)
            ->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])
            ->values();

        if ($faqEntries->isEmpty()) {
            return null;
        }

        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        foreach ($faqEntries as $faq) {
            $faqSchema['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return $faqSchema;
    }

    /**
     * Get all schemas for this product (Product + Breadcrumb + FAQ)
     */
    public function getAllSchemas(): array
    {
        $schemas = [];

        // 1. Product Schema
        $productSchema = $this->getSchemaMarkup();
        if ($productSchema) {
            $schemas['product'] = $productSchema;
        }

        // 2. Breadcrumb Schema
        $breadcrumbSchema = $this->getBreadcrumbSchema();
        if ($breadcrumbSchema) {
            $schemas['breadcrumb'] = $breadcrumbSchema;
        }

        // 3. FAQ Schema
        $faqSchema = $this->getFaqSchema();
        if ($faqSchema) {
            $schemas['faq'] = $faqSchema;
        }

        return $schemas;
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
