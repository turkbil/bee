<?php

declare(strict_types=1);

namespace Modules\Shop\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Modules\Shop\App\Contracts\ShopCategoryRepositoryInterface;
use Modules\Shop\App\Models\ShopCategory;
use Modules\Shop\App\Enums\CacheStrategy;

readonly class ShopCategoryRepository implements ShopCategoryRepositoryInterface
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;
    private readonly TenantCacheService $cache;

    public function __construct(
        private ShopCategory $model
    ) {
        $this->cachePrefix = 'shop_categories';
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
        $this->cache = app(TenantCacheService::class);
    }

    public function findById(int $id): ?ShopCategory
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->model->where('category_id', $id)->first();
        }

        $cacheKey = $this->getCacheKey("find_by_id.{$id}");

        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->where('category_id', $id)->first()
        );
    }

    public function findByIdWithSeo(int $id): ?ShopCategory
    {
        $strategy = CacheStrategy::fromRequest();

        // Admin panelinde global cache service kullan
        if ($strategy === CacheStrategy::ADMIN_FRESH) {
            return Cache::remember(
                "shop_category_with_seo_{$id}",
                $strategy->getCacheTtl(),
                fn() => $this->model->with('seoSetting')->where('category_id', $id)->first()
            );
        }

        $cacheKey = $this->getCacheKey("find_by_id_with_seo.{$id}");

        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id_with_seo.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->with('seoSetting')->where('category_id', $id)->first()
        );
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?ShopCategory
    {
        $strategy = CacheStrategy::PUBLIC_CACHED; // Always cache for SEO
        $cacheKey = $this->getCacheKey("find_by_slug.{$slug}.{$locale}");

        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() =>
                $this->model->where(function ($query) use ($slug, $locale) {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                          ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
                })->active()->first()
            );
    }

    public function getActive(): Collection
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->model->active()->orderBy('sort_order', 'asc')->get();
        }

        $cacheKey = $this->getCacheKey('active_categories');

        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() =>
                $this->model->active()->orderBy('sort_order', 'asc')->get()
            );
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $locales = $filters['locales'] ?? TenantLanguageProvider::getActiveLanguageCodes();

            $query->where(function ($subQuery) use ($searchTerm, $locales) {
                foreach ($locales as $locale) {
                    $subQuery->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) LIKE ?", [$searchTerm])
                            ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) LIKE ?", [$searchTerm]);
                }
            });
        }

        // Status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Sorting
        $sortField = $filters['sortField'] ?? 'category_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        if ($sortField === 'title') {
            $locale = $filters['currentLocale'] ?? 'tr';
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Eager loading ile N+1 query sorununu çöz
        return $query->with(['seoSetting'])->paginate($perPage);
    }

    public function search(string $term, array $locales = []): Collection
    {
        if (empty($locales)) {
            $locales = TenantLanguageProvider::getActiveLanguageCodes();
        }

        $searchTerm = '%' . $term . '%';

        return $this->model->where(function ($query) use ($searchTerm, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) LIKE ?", [$searchTerm])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.{$locale}')) LIKE ?", [$searchTerm]);
            }
        })->active()->get();
    }

    public function create(array $data): ShopCategory
    {
        $category = $this->model->create($data);
        $this->clearCache();

        return $category;
    }

    public function update(int $id, array $data): bool
    {
        $result = $this->model->where('category_id', $id)->update($data);

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function delete(int $id): bool
    {
        $result = $this->model->where('category_id', $id)->delete();

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function toggleActive(int $id): bool
    {
        $category = $this->model->where('category_id', $id)->first(['category_id', 'is_active']);

        if (!$category) {
            return false;
        }

        $result = $this->model->where('category_id', $id)->update(['is_active' => !$category->is_active]);

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function bulkDelete(array $ids): int
    {
        $count = $this->model->whereIn('category_id', $ids)->delete();

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function bulkToggleActive(array $ids): int
    {
        // Mevcut durumları al
        $categories = $this->model->whereIn('category_id', $ids)->get(['category_id', 'is_active']);
        $count = 0;

        foreach ($categories as $category) {
            $this->model->where('category_id', $category->category_id)
                       ->update(['is_active' => !$category->is_active]);
            $count++;
        }

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $category = $this->model->with('seoSetting')->where('category_id', $id)->first();

        if (!$category) {
            return false;
        }

        $seoSetting = $category->getOrCreateSeoSetting();

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
        $this->cache->flushByPrefix($this->cachePrefix);
    }

    protected function getCacheKey(string $key): string
    {
        return $this->cache->key($this->cachePrefix, $key);
    }

    protected function getCacheTags(): array
    {
        return $this->cache->tags([$this->cachePrefix]);
    }
}
