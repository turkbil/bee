<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\MenuManagement\App\Contracts\MenuRepositoryInterface;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Enums\CacheStrategy;

readonly class MenuRepository implements MenuRepositoryInterface
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;
    
    public function __construct(
        private Menu $model
    ) {
        $this->cachePrefix = 'menu';
        $this->cacheTtl = 3600;
    }
    
    public function findById(int $id): ?Menu
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->where('menu_id', $id)->first();
        }
        
        $cacheKey = $this->getCacheKey("find_by_id.{$id}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('menu_id', $id)->first()
            );
    }
    
    public function findByIdWithItems(int $id): ?Menu
    {
        $strategy = CacheStrategy::fromRequest();
        $cacheKey = $this->getCacheKey("find_by_id_with_items.{$id}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->with(['items' => function($query) {
                    $query->orderBy('sort_order');
                }])->where('menu_id', $id)->first()
            );
    }
    
    public function findBySlug(string $slug): ?Menu
    {
        $strategy = CacheStrategy::PUBLIC_CACHED; // Always cache for performance
        $cacheKey = $this->getCacheKey("find_by_slug.{$slug}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('slug', $slug)->active()->first()
            );
    }
    
    public function findByLocation(string $location): ?Menu
    {
        $strategy = CacheStrategy::PUBLIC_CACHED;
        $cacheKey = $this->getCacheKey("find_by_location.{$location}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('location', $location)->active()->first()
            );
    }
    
    public function getActive(): Collection
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->active()->orderBy('menu_id', 'desc')->get();
        }
        
        $cacheKey = $this->getCacheKey('active_menus');
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->active()->orderBy('menu_id', 'desc')->get()
            );
    }
    
    public function getActiveMenus(): Collection
    {
        return $this->getActive();
    }
    
    public function getDefault(): ?Menu
    {
        $cacheKey = $this->getCacheKey('default_menu');
        
        return Cache::tags($this->getCacheTags())->remember($cacheKey, $this->cacheTtl, function () {
            return $this->model->where('is_default', true)->active()->first();
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
                $subQuery->where('slug', 'like', $searchTerm);
                foreach ($locales as $locale) {
                    $subQuery->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
                }
            });
        }
        
        // Status filter
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('is_active', $filters['status'] === 'active');
        } elseif (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Location filter
        if (!empty($filters['location']) && $filters['location'] !== 'all') {
            $query->where('location', $filters['location']);
        }
        
        // Sorting
        $sortField = $filters['sortField'] ?? 'menu_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        
        if ($sortField === 'name') {
            $locale = $filters['currentLocale'] ?? 'tr';
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) {$sortDirection}");
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
            $query->where('slug', 'like', $searchTerm);
            foreach ($locales as $locale) {
                $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.{$locale}')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
            }
        })->active()->get();
    }
    
    public function create(array $data): Menu
    {
        $menu = $this->model->create($data);
        $this->clearCache();
        
        return $menu;
    }
    
    public function update(int $id, array $data): bool
    {
        $result = $this->model->where('menu_id', $id)->update($data);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function delete(int $id): bool
    {
        // Default menü silinemez
        $menu = $this->model->where('menu_id', $id)->first(['menu_id', 'is_default']);
        
        if (!$menu || $menu->is_default) {
            return false;
        }
        
        $result = $this->model->where('menu_id', $id)->delete();
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function toggleActive(int $id): bool
    {
        $menu = $this->model->where('menu_id', $id)->first(['menu_id', 'is_active', 'is_default']);
        
        if (!$menu) {
            return false;
        }
        
        // Default menü pasif yapılamaz
        if ($menu->is_default && $menu->is_active) {
            return false;
        }
        
        $result = $this->model->where('menu_id', $id)->update(['is_active' => !$menu->is_active]);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function bulkDelete(array $ids): int
    {
        // Default menüleri çıkar
        $defaultIds = $this->model->whereIn('menu_id', $ids)
                                 ->where('is_default', true)
                                 ->pluck('menu_id')
                                 ->toArray();
        
        $allowedIds = array_diff($ids, $defaultIds);
        
        if (empty($allowedIds)) {
            return 0;
        }
        
        $count = $this->model->whereIn('menu_id', $allowedIds)->delete();
        
        if ($count > 0) {
            $this->clearCache();
        }
        
        return $count;
    }
    
    public function bulkToggleActive(array $ids): int
    {
        // Default menüleri çıkar (pasifleştirme için)
        $defaultIds = $this->model->whereIn('menu_id', $ids)
                                 ->where('is_default', true)
                                 ->where('is_active', true)
                                 ->pluck('menu_id') 
                                 ->toArray();
        
        $allowedIds = array_diff($ids, $defaultIds);
        
        if (empty($allowedIds)) {
            return 0;
        }
        
        $menus = $this->model->whereIn('menu_id', $allowedIds)->get(['menu_id', 'is_active']);
        $count = 0;
        
        foreach ($menus as $menu) {
            $this->model->where('menu_id', $menu->menu_id)
                       ->update(['is_active' => !$menu->is_active]);
            $count++;
        }
        
        if ($count > 0) {
            $this->clearCache();
        }
        
        return $count;
    }
    
    public function getMenuTree(int $menuId): array
    {
        $cacheKey = $this->getCacheKey("menu_tree.{$menuId}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $this->cacheTtl, function () use ($menuId) {
                $menu = $this->model->with(['activeItems' => function($query) {
                    $query->with('activeChildren')->orderBy('sort_order');
                }])->find($menuId);
                
                return $menu ? $menu->getTreeStructure() : [];
            });
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
        return ["menus", "tenant.{$tenantId}"];
    }
}