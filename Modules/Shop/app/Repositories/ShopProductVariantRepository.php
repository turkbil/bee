<?php

declare(strict_types=1);

namespace Modules\Shop\App\Repositories;

use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Shop\App\Contracts\ShopProductVariantRepositoryInterface;
use Modules\Shop\App\Enums\CacheStrategy;
use Modules\Shop\App\Models\ShopProductVariant;

readonly class ShopProductVariantRepository implements ShopProductVariantRepositoryInterface
{
    private const CACHE_PREFIX = 'shop_product_variants';

    private readonly TenantCacheService $cache;

    public function __construct(private ShopProductVariant $model)
    {
        $this->cache = app(TenantCacheService::class);
    }

    public function findById(int $id): ?ShopProductVariant
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->baseQuery()->where('variant_id', $id)->first();
        }

        $cacheKey = $this->cache->key(self::CACHE_PREFIX, "find_by_id.{$id}");

        return Cache::tags($this->cache->tags([self::CACHE_PREFIX]))
            ->remember(
                $cacheKey,
                $strategy->getCacheTtl(),
                fn() => $this->baseQuery()->where('variant_id', $id)->first()
            );
    }

    public function getByProduct(int $productId, array $filters = []): Collection
    {
        $query = $this->applyFilters(
            $this->baseQuery()->where('product_id', $productId),
            $filters
        );

        $query = $this->applySorting($query, $filters);

        return $query->get();
    }

    public function getPaginatedByProduct(int $productId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->applyFilters(
            $this->baseQuery()->where('product_id', $productId),
            $filters
        );

        $query = $this->applySorting($query, $filters);

        return $query->paginate($perPage)->withQueryString();
    }

    public function getActiveByProduct(int $productId): Collection
    {
        return $this->getByProduct($productId, ['is_active' => true]);
    }

    public function getDefaultVariant(int $productId): ?ShopProductVariant
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->baseQuery()
                ->where('product_id', $productId)
                ->where('is_default', true)
                ->first();
        }

        $cacheKey = $this->cache->key(self::CACHE_PREFIX, "default_variant.{$productId}");

        return Cache::tags($this->cache->tags([self::CACHE_PREFIX, "product:{$productId}"]))
            ->remember(
                $cacheKey,
                $strategy->getCacheTtl(),
                fn() => $this->baseQuery()
                    ->where('product_id', $productId)
                    ->where('is_default', true)
                    ->first()
            );
    }

    public function create(array $data): ShopProductVariant
    {
        return DB::transaction(function () use ($data) {
            /** @var ShopProductVariant $variant */
            $variant = $this->model->create($data);

            if (!empty($data['is_default'])) {
                $this->resetDefaultFlag($variant->product_id, $variant->variant_id);
            }

            $this->clearCache((int) $variant->product_id);

            return $variant;
        });
    }

    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $variant = $this->model->where('variant_id', $id)->first();

            if (!$variant) {
                return false;
            }

            $updated = (bool) $variant->update($data);

            if ($updated && array_key_exists('is_default', $data) && $data['is_default']) {
                $this->resetDefaultFlag($variant->product_id, $variant->variant_id);
            }

            if ($updated) {
                $this->clearCache((int) $variant->product_id);
            }

            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        $variant = $this->model->where('variant_id', $id)->first();

        if (!$variant) {
            return false;
        }

        $deleted = (bool) $variant->delete();

        if ($deleted) {
            $this->clearCache((int) $variant->product_id);
        }

        return $deleted;
    }

    public function bulkDelete(array $ids): int
    {
        $variants = $this->model->whereIn('variant_id', $ids)->get(['variant_id', 'product_id']);

        if ($variants->isEmpty()) {
            return 0;
        }

        $deleted = (int) $this->model->whereIn('variant_id', $ids)->delete();

        if ($deleted > 0) {
            $variants->pluck('product_id')->unique()->each(
                fn($productId) => $this->clearCache((int) $productId)
            );
        }

        return $deleted;
    }

    public function toggleActive(int $id): bool
    {
        $variant = $this->model->where('variant_id', $id)->first(['variant_id', 'product_id', 'is_active']);

        if (!$variant) {
            return false;
        }

        $toggled = (bool) $this->model
            ->where('variant_id', $id)
            ->update(['is_active' => !$variant->is_active]);

        if ($toggled) {
            $this->clearCache((int) $variant->product_id);
        }

        return $toggled;
    }

    public function setDefaultVariant(int $productId, int $variantId): bool
    {
        return DB::transaction(function () use ($productId, $variantId) {
            $variant = $this->model
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if (!$variant) {
                return false;
            }

            $this->resetDefaultFlag($productId, $variantId);

            $this->clearCache($productId);

            return true;
        });
    }

    public function updateSortOrders(int $productId, array $orderedItems): void
    {
        foreach ($orderedItems as $item) {
            if (!isset($item['id'], $item['order'])) {
                continue;
            }

            $this->model->where('product_id', $productId)
                ->where('variant_id', (int) $item['id'])
                ->update(['sort_order' => (int) $item['order']]);
        }

        $this->clearCache($productId);
    }

    public function clearCache(?int $productId = null): void
    {
        if ($productId !== null && Cache::getStore() instanceof TaggableStore) {
            Cache::tags($this->cache->tags([self::CACHE_PREFIX, "product:{$productId}"]))->flush();
            return;
        }

        $this->cache->flushByPrefix(self::CACHE_PREFIX);
    }

    private function baseQuery(): Builder
    {
        return $this->model->newQuery()
            ->orderBy('sort_order')
            ->orderBy('variant_id');
    }

    private function applyFilters(Builder $query, array $filters = []): Builder
    {
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $locales = $filters['locales'] ?? TenantLanguageProvider::getActiveLanguageCodes();

            $query->where(function (Builder $subQuery) use ($searchTerm, $locales) {
                foreach ($locales as $locale) {
                    $subQuery->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) LIKE ?", [$searchTerm]);
                }

                $subQuery->orWhere('sku', 'LIKE', $searchTerm)
                    ->orWhere('barcode', 'LIKE', $searchTerm);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['is_default'])) {
            $query->where('is_default', (bool) $filters['is_default']);
        }

        return $query;
    }

    private function applySorting(Builder $query, array $filters = []): Builder
    {
        $sortField = $filters['sortField'] ?? 'sort_order';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        if ($sortField === 'title') {
            $locale = $filters['currentLocale'] ?? app()->getLocale();
            return $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) {$sortDirection}");
        }

        if (in_array($sortField, ['sku', 'price_modifier', 'stock_quantity', 'sort_order', 'variant_id'], true)) {
            return $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }

    private function resetDefaultFlag(int $productId, int $variantId): void
    {
        $this->model->where('product_id', $productId)
            ->where('variant_id', '!=', $variantId)
            ->update(['is_default' => false]);

        $this->model->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->update(['is_default' => true]);
    }
}
