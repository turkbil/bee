<?php

declare(strict_types=1);

namespace Modules\Shop\App\Repositories;

use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Shop\App\Contracts\ShopBrandRepositoryInterface;
use Modules\Shop\App\Enums\CacheStrategy;
use Modules\Shop\App\Models\ShopBrand;

readonly class ShopBrandRepository implements ShopBrandRepositoryInterface
{
    private const CACHE_PREFIX = 'shop_brands';

    private readonly TenantCacheService $cache;

    public function __construct(private ShopBrand $model)
    {
        $this->cache = app(TenantCacheService::class);
    }

    public function findById(int $id): ?ShopBrand
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->baseQuery()->where('brand_id', $id)->first();
        }

        return $this->cache->remember(
            self::CACHE_PREFIX,
            "find_by_id.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->baseQuery()->where('brand_id', $id)->first()
        );
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?ShopBrand
    {
        $strategy = CacheStrategy::PUBLIC_CACHED;
        $cacheKey = $this->cache->key(self::CACHE_PREFIX, "find_by_slug.{$slug}.{$locale}");

        return Cache::tags($this->cache->tags([self::CACHE_PREFIX]))
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
                    ->first()
            );
    }

    public function getActive(): Collection
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->baseQuery()
                ->active()
                ->orderBy('sort_order')
                ->get();
        }

        return Cache::tags($this->cache->tags([self::CACHE_PREFIX]))
            ->remember(
                $this->cache->key(self::CACHE_PREFIX, 'active'),
                $strategy->getCacheTtl(),
                fn() => $this->baseQuery()
                    ->active()
                    ->orderBy('sort_order')
                    ->get()
            );
    }

    public function getFeatured(): Collection
    {
        return $this->baseQuery()
            ->active()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        if (!empty($filters['search'])) {
            $query = $this->applySearchFilter($query, (string) $filters['search'], $filters['locales'] ?? null);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }

        if (!empty($filters['country_code'])) {
            $query->where('country_code', strtoupper((string) $filters['country_code']));
        }

        $sortField = $filters['sortField'] ?? 'sort_order';
        $sortDirection = $filters['sortDirection'] ?? 'asc';

        if ($sortField === 'title') {
            $locale = $filters['currentLocale'] ?? app()->getLocale();
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function search(string $term, array $locales = []): Collection
    {
        $locales = !empty($locales) ? $locales : TenantLanguageProvider::getActiveLanguageCodes();
        $searchTerm = '%'.$term.'%';

        return $this->baseQuery()
            ->where(function (Builder $query) use ($searchTerm, $locales): void {
                foreach ($locales as $locale) {
                    $query
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
                }
            })
            ->active()
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data): ShopBrand
    {
        $brand = $this->model->create($data);
        $this->clearCache();

        return $brand;
    }

    public function update(int $id, array $data): bool
    {
        $updated = (bool) $this->model->where('brand_id', $id)->update($data);

        if ($updated) {
            $this->clearCache();
        }

        return $updated;
    }

    public function delete(int $id): bool
    {
        $deleted = (bool) $this->model->where('brand_id', $id)->delete();

        if ($deleted) {
            $this->clearCache();
        }

        return $deleted;
    }

    public function toggleActive(int $id): bool
    {
        $brand = $this->model->where('brand_id', $id)->first(['brand_id', 'is_active']);

        if ($brand === null) {
            return false;
        }

        $toggled = (bool) $this->model
            ->where('brand_id', $id)
            ->update(['is_active' => ! $brand->is_active]);

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

        $count = $this->model->whereIn('brand_id', $ids)->delete();

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

        $brands = $this->model->whereIn('brand_id', $ids)->get(['brand_id', 'is_active']);
        $count = 0;

        foreach ($brands as $brand) {
            $this->model
                ->where('brand_id', $brand->brand_id)
                ->update(['is_active' => ! $brand->is_active]);
            $count++;
        }

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $brand = $this->model->with('seoSetting')->where('brand_id', $id)->first();

        if ($brand === null) {
            return false;
        }

        $seoSetting = $brand->getOrCreateSeoSetting();

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
        $this->cache->flushByPrefix(self::CACHE_PREFIX);
    }

    private function baseQuery(): Builder
    {
        return $this->model->newQuery();
    }

    private function applySearchFilter(Builder $query, string $term, ?array $locales = null): Builder
    {
        $locales = $locales ?: TenantLanguageProvider::getActiveLanguageCodes();
        $searchTerm = '%'.$term.'%';

        return $query->where(function (Builder $searchQuery) use ($locales, $searchTerm): void {
            foreach ($locales as $locale) {
                $searchQuery
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.\"{$locale}\"')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
            }
        });
    }
}
