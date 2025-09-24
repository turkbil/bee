<?php

declare(strict_types=1);

namespace Modules\Page\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Services\TenantCacheService;
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
        $this->cacheTtl = TenantCacheService::TTL_HOUR;
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
        
        // Admin panelinde global cache service kullan
        if ($strategy === CacheStrategy::ADMIN_FRESH) {
            return \App\Services\GlobalCacheService::getPageWithSeo($id);
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
            $locales = $filters['locales'] ?? ['tr', 'en'];
            
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
        $sortField = $filters['sortField'] ?? 'page_id';
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
            $locales = ['tr', 'en'];
        }
        
        $searchTerm = '%' . $term . '%';
        
        return $this->model->where(function ($query) use ($searchTerm, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) LIKE ?", [$searchTerm])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.{$locale}')) LIKE ?", [$searchTerm]);
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
}