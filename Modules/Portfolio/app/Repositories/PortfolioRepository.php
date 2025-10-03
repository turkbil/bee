<?php

declare(strict_types=1);

namespace Modules\Portfolio\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Page\App\Enums\CacheStrategy;

readonly class PortfolioRepository implements PortfolioRepositoryInterface
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;
    private readonly TenantCacheService $cache;
    
    public function __construct(
        private Portfolio $model
    ) {
        $this->cachePrefix = 'portfolio';
        $this->cacheTtl = TenantCacheService::TTL_HOUR;
        $this->cache = app(TenantCacheService::class);
    }
    
    public function findById(int $id): ?Portfolio
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->where('portfolio_id', $id)->first();
        }
        
        $cacheKey = $this->getCacheKey("find_by_id.{$id}");
        
        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->where('portfolio_id', $id)->first()
        );
    }
    
    public function findByIdWithSeo(int $id): ?Portfolio
    {
        $strategy = CacheStrategy::fromRequest();
        
        // Admin panelinde global cache service kullan
        if ($strategy === CacheStrategy::ADMIN_FRESH) {
            return \App\Services\GlobalCacheService::getPageWithSeo($id);
        }
        
        $cacheKey = $this->getCacheKey("find_by_id_with_seo.{$id}");
        
        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id_with_seo.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->with('seoSetting')->where('portfolio_id', $id)->first()
        );
    }
    
    public function findBySlug(string $slug, string $locale = 'tr'): ?Portfolio
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
            return $this->model->active()->orderBy('portfolio_id', 'desc')->get();
        }
        
        $cacheKey = $this->getCacheKey('active_pages');
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->active()->orderBy('portfolio_id', 'desc')->get()
            );
    }
    
    public function getHomepage(): ?Portfolio
    {
        return null; // Portfolios don't have homepage
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
        $sortField = $filters['sortField'] ?? 'portfolio_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        
        if ($sortField === 'title') {
            $locale = $filters['currentLocale'] ?? 'tr';
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) {$sortDirection}");
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

        return $this->model->where(function ($query) use ($searchTerm, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) LIKE ?", [$searchTerm])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.{$locale}')) LIKE ?", [$searchTerm]);
            }
        })->active()->get();
    }
    
    public function create(array $data): Portfolio
    {
        $portfolio = $this->model->create($data);
        $this->clearCache();
        
        return $portfolio;
    }
    
    public function update(int $id, array $data): bool
    {
        $result = $this->model->where('portfolio_id', $id)->update($data);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function delete(int $id): bool
    {
        $result = $this->model->where('portfolio_id', $id)->delete();
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function toggleActive(int $id): bool
    {
        // 🚨 PERFORMANCE FIX: Tek sorguda toggle yap, gereksiz findById kaldır
        $portfolio = $this->model->where('portfolio_id', $id)->first(['portfolio_id', 'is_active', 'is_homepage']);
        
        if (!$portfolio) {
            return false;
        }
        
        // Ana sayfa ise pasif yapılmasına izin verme
        if ($portfolio->is_homepage && $portfolio->is_active) {
            return false;
        }
        
        $result = $this->model->where('portfolio_id', $id)->update(['is_active' => !$portfolio->is_active]);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function bulkDelete(array $ids): int
    {
        $count = $this->model->whereIn('portfolio_id', $ids)->delete();
        
        if ($count > 0) {
            $this->clearCache();
        }
        
        return $count;
    }
    
    public function bulkToggleActive(array $ids): int
    {
        // Ana sayfaları çıkar
        $homepageIds = $this->model->whereIn('portfolio_id', $ids)
                                  ->where('is_homepage', true)
                                  ->pluck('portfolio_id')
                                  ->toArray();
        
        $allowedIds = array_diff($ids, $homepageIds);
        
        if (empty($allowedIds)) {
            return 0;
        }
        
        // Önce mevcut durumları al
        $portfolios = $this->model->whereIn('portfolio_id', $allowedIds)->get(['portfolio_id', 'is_active']);
        $count = 0;
        
        foreach ($portfolios as $portfolio) {
            $this->model->where('portfolio_id', $portfolio->portfolio_id)
                       ->update(['is_active' => !$portfolio->is_active]);
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
        $portfolio = $this->model->where('portfolio_id', $id)->first(['portfolio_id', 'seo']);
        
        if (!$portfolio) {
            return false;
        }
        
        $seo = $portfolio->seo ?? [];
        $seo[$locale][$field] = $value;
        
        $result = $this->model->where('portfolio_id', $id)->update(['seo' => $seo]);
        
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
}