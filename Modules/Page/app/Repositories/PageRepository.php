<?php
namespace Modules\Page\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Page\App\Contracts\PageRepositoryInterface;
use Modules\Page\App\Models\Page;

class PageRepository implements PageRepositoryInterface
{
    protected string $cachePrefix = 'page';
    protected int $cacheTtl = 3600; // 1 saat
    
    public function __construct(protected Page $model)
    {
    }
    
    public function findById(int $id): ?Page
    {
        $cacheKey = $this->getCacheKey("find_by_id.{$id}");
        
        return Cache::tags($this->getCacheTags())->remember($cacheKey, $this->cacheTtl, function () use ($id) {
            return $this->model->where('page_id', $id)->first();
        });
    }
    
    public function findBySlug(string $slug, string $locale = 'tr'): ?Page
    {
        $cacheKey = $this->getCacheKey("find_by_slug.{$slug}.{$locale}");
        
        return Cache::tags($this->getCacheTags())->remember($cacheKey, $this->cacheTtl, function () use ($slug, $locale) {
            return $this->model->where(function ($query) use ($slug, $locale) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]); // Fallback
            })->active()->first();
        });
    }
    
    public function getActive(): Collection
    {
        $cacheKey = $this->getCacheKey('active_pages');
        
        return Cache::tags($this->getCacheTags())->remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->active()->orderBy('page_id', 'desc')->get();
        });
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
        
        return $query->paginate($perPage);
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
        $page = $this->findById($id);
        
        if (!$page) {
            return false;
        }
        
        // Ana sayfa ise pasif yapılmasına izin verme
        if ($page->is_homepage && $page->is_active) {
            return false;
        }
        
        $result = $this->update($id, ['is_active' => !$page->is_active]);
        
        return $result;
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
        $page = $this->findById($id);
        
        if (!$page) {
            return false;
        }
        
        $seo = $page->seo ?? [];
        $seo[$locale][$field] = $value;
        
        $result = $this->update($id, ['seo' => $seo]);
        
        return $result;
    }
    
    public function clearCache(): void
    {
        Cache::tags($this->getCacheTags())->flush();
    }
    
    protected function getCacheKey(string $key): string
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        return "{$this->cachePrefix}.tenant.{$tenantId}.{$key}";
    }
    
    protected function getCacheTags(): array
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        return ["pages", "tenant.{$tenantId}"];
    }
}