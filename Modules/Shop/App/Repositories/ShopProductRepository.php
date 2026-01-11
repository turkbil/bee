<?php

declare(strict_types=1);

namespace Modules\Shop\App\Repositories;

use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Modules\Shop\App\Contracts\ShopProductRepositoryInterface;
use Modules\Shop\App\Enums\CacheStrategy;
use Modules\Shop\App\Models\ShopProduct;

readonly class ShopProductRepository implements ShopProductRepositoryInterface
{
    private const DEFAULT_CACHE_PREFIX = 'shop_products';

    private readonly TenantCacheService $cache;

    public function __construct(private ShopProduct $model)
    {
        $this->cache = app(TenantCacheService::class);
    }

    public function findById(int $id): ?ShopProduct
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->baseQuery()->where('product_id', $id)->first();
        }

        return $this->cache->remember(
            self::DEFAULT_CACHE_PREFIX,
            "find_by_id.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->baseQuery()->where('product_id', $id)->first()
        );
    }

    public function findByIdWithSeo(int $id): ?ShopProduct
    {
        $strategy = CacheStrategy::fromRequest();

        if ($strategy === CacheStrategy::ADMIN_FRESH) {
            return $this->baseQuery()->with('seoSetting')->where('product_id', $id)->first();
        }

        return $this->cache->remember(
            self::DEFAULT_CACHE_PREFIX,
            "find_by_id_with_seo.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->baseQuery()->with('seoSetting')->where('product_id', $id)->first()
        );
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?ShopProduct
    {
        $strategy = CacheStrategy::PUBLIC_CACHED;
        $cacheKey = $this->cache->key(self::DEFAULT_CACHE_PREFIX, "find_by_slug.{$slug}.{$locale}");

        return Cache::tags($this->cache->tags([self::DEFAULT_CACHE_PREFIX]))
            ->remember(
                $cacheKey,
                $strategy->getCacheTtl(),
                fn() => $this->baseQuery()
                    ->where(function (Builder $query) use ($slug, $locale): void {
                        $query
                            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug])
                            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"tr\"')) = ?", [$slug]);
                    })
                    ->active()
                    ->published()
                    ->first()
            );
    }

    public function getActive(): Collection
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->baseQuery()
                ->active()
                ->orderByDesc('product_id')
                ->get();
        }

        return Cache::tags($this->cache->tags([self::DEFAULT_CACHE_PREFIX]))
            ->remember(
                $this->cache->key(self::DEFAULT_CACHE_PREFIX, 'active'),
                $strategy->getCacheTtl(),
                fn() => $this->baseQuery()
                    ->active()
                    ->orderByDesc('product_id')
                    ->get()
            );
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // Sadece master productları getir (parent_product_id NULL olanlar)
        $query = $this->baseQuery()
            ->whereNull('parent_product_id')
            ->with([
                'category',
                'brand',
                'childProducts' => function ($query) {
                    $query->where('is_active', true)->orderBy('variant_type')->orderBy('product_id');
                }
            ]);

        if (!empty($filters['search'])) {
            $searchTerm = (string) $filters['search'];
            $locales = $filters['locales'] ?? null;

            // Hem master hem child productlarda ara
            $query->where(function (Builder $q) use ($searchTerm, $locales) {
                // Master product'ta ara
                $this->applySearchFilter($q, $searchTerm, $locales);

                // VEYA child productları arasında eşleşen varsa master'ı göster
                $q->orWhereHas('childProducts', function (Builder $childQuery) use ($searchTerm, $locales) {
                    $this->applySearchFilter($childQuery, $searchTerm, $locales);
                });
            });
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', (int) $filters['brand_id']);
        }

        if (isset($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (isset($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }

        if (isset($filters['is_bestseller'])) {
            $query->where('is_bestseller', (bool) $filters['is_bestseller']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('base_price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('base_price', '<=', (float) $filters['price_max']);
        }

        $sortField = $filters['sortField'] ?? 'product_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        if ($sortField === 'title') {
            $locale = $filters['currentLocale'] ?? app()->getLocale();
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) {$sortDirection}");
        } elseif ($sortField === 'sort_order') {
            // Sort order aynı olanları product_id ASC ile sırala (eski ürünler önce, yeni ürünler sonda)
            $query->orderBy('sort_order', $sortDirection)
                  ->orderBy('product_id', 'asc');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function search(string $term, array $locales = []): Collection
    {
        $locales = !empty($locales) ? $locales : TenantLanguageProvider::getActiveLanguageCodes();
        $searchTerm = '%' . $term . '%';

        return $this->baseQuery()
            ->where(function (Builder $query) use ($searchTerm, $locales, $term): void {
                // ✅ SKU, Model Number, Barcode araması (exact ve partial)
                $query->where('sku', 'LIKE', $searchTerm)
                    ->orWhere('model_number', 'LIKE', $searchTerm)
                    ->orWhere('barcode', 'LIKE', $searchTerm)
                    ->orWhere('sku', '=', $term)
                    ->orWhere('model_number', '=', $term)
                    ->orWhere('barcode', '=', $term);

                // ✅ JSON field aramaları - Türkçe karakter uyumlu
                foreach ($locales as $locale) {
                    $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
                }
            })
            ->active()
            ->get();
    }

    public function create(array $data): ShopProduct
    {
        $product = $this->model->create($data);
        $this->clearCache();

        return $product;
    }

    public function update(int $id, array $data): bool
    {
        $updated = (bool) $this->model->where('product_id', $id)->update($data);

        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $deleted = (bool) $this->model->where('product_id', $id)->delete();

        if ($deleted) {
            $this->clearCache();
        }

        return $deleted;
    }

    public function toggleActive(int $id): bool
    {
        $product = $this->model->where('product_id', $id)->first(['product_id', 'is_active']);

        if ($product === null) {
            return false;
        }

        $toggled = (bool) $this->model
            ->where('product_id', $id)
            ->update(['is_active' => ! $product->is_active]);

        if ($toggled) {
            $this->clearCache();
        }

        return $toggled;
    }

    public function bulkDelete(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        $count = $this->model->whereIn('product_id', $ids)->delete();

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function bulkToggleActive(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        $products = $this->model->whereIn('product_id', $ids)->get(['product_id', 'is_active']);
        $count = 0;

        foreach ($products as $product) {
            $this->model
                ->where('product_id', $product->product_id)
                ->update(['is_active' => ! $product->is_active]);
            $count++;
        }

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $product = $this->model->with('seoSetting')->where('product_id', $id)->first();

        if ($product === null) {
            return false;
        }

        $seoSetting = $product->getOrCreateSeoSetting();

        match ($field) {
            'title' => $seoSetting->setTranslation('titles', $locale, (string) $value),
            'description' => $seoSetting->setTranslation('descriptions', $locale, (string) $value),
            'og_title' => $seoSetting->setTranslation('og_titles', $locale, (string) $value),
            'og_description' => $seoSetting->setTranslation('og_descriptions', $locale, (string) $value),
            default => null,
        };

        $saved = $seoSetting->save();

        if ($saved) {
            $this->clearCache();
        }

        return $saved;
    }

    public function clearCache(): void
    {
        $this->cache->flushByPrefix(self::DEFAULT_CACHE_PREFIX);
    }

    private function baseQuery(): Builder
    {
        return $this->model->newQuery();
    }

    private function applySearchFilter(Builder $query, string $term, ?array $locales = null): Builder
    {
        $locales = $locales ?: TenantLanguageProvider::getActiveLanguageCodes();
        $searchTerm = '%' . $term . '%';

        return $query->where(function (Builder $searchQuery) use ($locales, $searchTerm, $term): void {
            // ✅ SKU, Model Number, Barcode araması (case-insensitive, exact ve partial match)
            $searchQuery
                ->where('sku', 'LIKE', $searchTerm)
                ->orWhere('model_number', 'LIKE', $searchTerm)
                ->orWhere('barcode', 'LIKE', $searchTerm)
                ->orWhere('sku', '=', $term) // Exact match
                ->orWhere('model_number', '=', $term) // Exact match
                ->orWhere('barcode', '=', $term); // Exact match

            // ✅ JSON field aramaları (title, short_description) - Türkçe karakter uyumlu
            foreach ($locales as $locale) {
                $searchQuery
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
            }
        });
    }
}
