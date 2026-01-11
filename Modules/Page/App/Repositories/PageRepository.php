<?php

declare(strict_types=1);

namespace Modules\Page\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Modules\Page\App\Contracts\PageRepositoryInterface;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Enums\CacheStrategy;

readonly class PageRepository implements PageRepositoryInterface
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;
    private readonly TenantCacheService $cache;
    
    public function __construct(
        private Page $model
    ) {
        $this->cachePrefix = TenantCacheService::PREFIX_PAGE;
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
        $this->cache = app(TenantCacheService::class);
    }
    
    public function findById(int $id): ?Page
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->where('page_id', $id)->first();
        }
        
        $cacheKey = $this->getCacheKey("find_by_id.{$id}");
        
        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->where('page_id', $id)->first()
        );
    }
    
    public function findByIdWithSeo(int $id): ?Page
    {
        $strategy = CacheStrategy::fromRequest();

        // Admin panelinde cache kullanma
        if ($strategy === CacheStrategy::ADMIN_FRESH) {
            return $this->model->with('seoSetting')->where('page_id', $id)->first();
        }

        $cacheKey = $this->getCacheKey("find_by_id_with_seo.{$id}");

        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id_with_seo.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->with('seoSetting')->where('page_id', $id)->first()
        );
    }
    
    public function findBySlug(string $slug, string $locale = 'tr'): ?Page
    {
        $strategy = CacheStrategy::PUBLIC_CACHED; // Always cache for SEO
        $cacheKey = $this->getCacheKey("find_by_slug.{$slug}.{$locale}");
        $primaryLocale = $this->sanitizeLocale($locale);
        $fallbackLocale = $this->getDefaultLocale();
        $searchLocales = array_values(array_unique([
            $primaryLocale,
            $fallbackLocale,
            'tr',
        ]));
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), function () use ($slug, $searchLocales) {
                return $this->model
                    ->where(function ($query) use ($slug, $searchLocales) {
                        foreach ($searchLocales as $index => $localeCode) {
                            $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                            $query->{$method}(
                                $this->jsonLocaleExpression('slug', $localeCode) . ' = ?',
                                [$slug]
                            );
                        }
                    })
                    ->active()
                    ->first();
            });
    }
    
    public function getActive(): Collection
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->active()->orderBy('page_id', 'desc')->get();
        }
        
        $cacheKey = $this->getCacheKey('active_pages');
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->active()->orderBy('page_id', 'desc')->get()
            );
    }
    
    public function getHomepage(): ?Page
    {
        $cacheKey = $this->getCacheKey('homepage');
        
        return Cache::tags($this->getCacheTags())->remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->homepage()->active()->first();
        });
    }
    
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $locales = $filters['locales'] ?? TenantLanguageProvider::getActiveLanguageCodes();
            $locales = array_values(array_unique(array_map(
                fn($code) => $this->sanitizeLocale((string) $code),
                $locales
            )));

            $query->where(function ($subQuery) use ($searchTerm, $locales) {
                foreach ($locales as $locale) {
                    $expressionTitle = $this->jsonLocaleExpression('title', $locale);
                    $expressionSlug = $this->jsonLocaleExpression('slug', $locale);

                    $subQuery->orWhereRaw("{$expressionTitle} LIKE ?", [$searchTerm])
                             ->orWhereRaw("{$expressionSlug} LIKE ?", [$searchTerm]);
                }
            });
        }
        
        // Status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Sorting
        $sortField = $filters['sortField'] ?? 'page_id';
        $sortDirection = $this->normalizeSortDirection($filters['sortDirection'] ?? 'desc');
        
        if ($sortField === 'title') {
            $locale = $this->sanitizeLocale($filters['currentLocale'] ?? app()->getLocale() ?? 'tr');
            $expressionTitle = $this->jsonLocaleExpression('title', $locale);
            $query->orderByRaw("{$expressionTitle} {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }
        
        // 🚀 PERFORMANCE FIX: Eager loading ile N+1 query sorununu çöz
        return $query->with(['seoSetting'])->paginate($perPage);
    }
    
    public function search(string $term, array $locales = []): Collection
    {
        if (empty($locales)) {
            $locales = TenantLanguageProvider::getActiveLanguageCodes();
        }

        $searchTerm = '%' . $term . '%';
        $locales = array_values(array_unique(array_map(
            fn($code) => $this->sanitizeLocale((string) $code),
            $locales
        )));

        return $this->model->where(function ($query) use ($searchTerm, $locales) {
            foreach ($locales as $locale) {
                $expressionTitle = $this->jsonLocaleExpression('title', $locale);
                $expressionBody = $this->jsonLocaleExpression('body', $locale);

                $query->orWhereRaw("{$expressionTitle} LIKE ?", [$searchTerm])
                      ->orWhereRaw("{$expressionBody} LIKE ?", [$searchTerm]);
            }
        })->active()->get();
    }
    
    public function create(array $data): Page
    {
        $page = $this->model->create($data);
        $this->clearCache();
        
        return $page;
    }
    
    public function update(int $id, array $data): bool
    {
        $result = $this->model->where('page_id', $id)->update($data);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function delete(int $id): bool
    {
        $result = $this->model->where('page_id', $id)->delete();
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function toggleActive(int $id): bool
    {
        // 🚨 PERFORMANCE FIX: Tek sorguda toggle yap, gereksiz findById kaldır
        $page = $this->model->where('page_id', $id)->first(['page_id', 'is_active', 'is_homepage']);
        
        if (!$page) {
            return false;
        }
        
        // Ana sayfa ise pasif yapılmasına izin verme
        if ($page->is_homepage && $page->is_active) {
            return false;
        }
        
        $result = $this->model->where('page_id', $id)->update(['is_active' => !$page->is_active]);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function bulkDelete(array $ids): int
    {
        $count = $this->model->whereIn('page_id', $ids)->delete();
        
        if ($count > 0) {
            $this->clearCache();
        }
        
        return $count;
    }
    
    public function bulkToggleActive(array $ids): int
    {
        // Ana sayfaları çıkar
        $homepageIds = $this->model->whereIn('page_id', $ids)
                                  ->where('is_homepage', true)
                                  ->pluck('page_id')
                                  ->toArray();
        
        $allowedIds = array_diff($ids, $homepageIds);
        
        if (empty($allowedIds)) {
            return 0;
        }
        
        // Önce mevcut durumları al
        $pages = $this->model->whereIn('page_id', $allowedIds)->get(['page_id', 'is_active']);
        $count = 0;
        
        foreach ($pages as $page) {
            $this->model->where('page_id', $page->page_id)
                       ->update(['is_active' => !$page->is_active]);
            $count++;
        }
        
        if ($count > 0) {
            $this->clearCache();
        }
        
        return $count;
    }
    
    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        // 🚨 PERFORMANCE FIX: Gereksiz findById kaldır, direkt güncelle
        $page = $this->model->where('page_id', $id)->first(['page_id', 'seo']);
        
        if (!$page) {
            return false;
        }
        
        $seo = $page->seo ?? [];
        $seo[$locale][$field] = $value;
        
        $result = $this->model->where('page_id', $id)->update(['seo' => $seo]);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
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

    private function jsonLocaleExpression(string $column, string $locale): string
    {
        $locale = $this->sanitizeLocale($locale);
        $connection = $this->model->getConnection();
        $driver = $connection->getDriverName();
        $wrappedColumn = $connection->getQueryGrammar()->wrap($column);
        $jsonPath = '$."' . $locale . '"';

        return match ($driver) {
            'pgsql' => "{$wrappedColumn}::jsonb ->> '{$locale}'",
            'sqlite' => "json_extract({$wrappedColumn}, '{$jsonPath}')",
            default => "JSON_UNQUOTE(JSON_EXTRACT({$wrappedColumn}, '{$jsonPath}'))",
        };
    }

    private function sanitizeLocale(string $locale): string
    {
        $clean = preg_replace('/[^a-z0-9_\-]/i', '', $locale) ?? '';

        return $clean !== '' ? $clean : 'tr';
    }

    private function getDefaultLocale(): string
    {
        $defaultLocale = null;

        if (function_exists('get_tenant_default_locale')) {
            $defaultLocale = (string) get_tenant_default_locale();
        }

        if (!$defaultLocale) {
            $defaultLocale = (string) config('app.locale', 'tr');
        }

        return $this->sanitizeLocale($defaultLocale);
    }

    private function normalizeSortDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : 'desc';
    }
}
