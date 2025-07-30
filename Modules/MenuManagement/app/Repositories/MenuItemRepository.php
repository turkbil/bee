<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\MenuManagement\App\Contracts\MenuItemRepositoryInterface;
use Modules\MenuManagement\App\Models\MenuItem;
use Modules\MenuManagement\App\Enums\CacheStrategy;

readonly class MenuItemRepository implements MenuItemRepositoryInterface
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;
    
    public function __construct(
        private MenuItem $model
    ) {
        $this->cachePrefix = 'menu_item';
        $this->cacheTtl = 3600;
    }
    
    public function findById(int $id): ?MenuItem
    {
        $strategy = CacheStrategy::fromRequest();
        
        if (!$strategy->shouldCache()) {
            return $this->model->where('item_id', $id)->first();
        }
        
        $cacheKey = $this->getCacheKey("find_by_id.{$id}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('item_id', $id)->first()
            );
    }
    
    public function findByIdWithChildren(int $id): ?MenuItem
    {
        $strategy = CacheStrategy::fromRequest();
        $cacheKey = $this->getCacheKey("find_by_id_with_children.{$id}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->with(['children' => function($query) {
                    $query->orderBy('sort_order');
                }])->where('item_id', $id)->first()
            );
    }
    
    public function getByMenuId(int $menuId): Collection
    {
        $strategy = CacheStrategy::fromRequest();
        $cacheKey = $this->getCacheKey("by_menu_id.{$menuId}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('menu_id', $menuId)
                           ->orderBy('sort_order')
                           ->get()
            );
    }
    
    public function getRootItems(int $menuId): Collection
    {
        $strategy = CacheStrategy::fromRequest();
        $cacheKey = $this->getCacheKey("root_items.{$menuId}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('menu_id', $menuId)
                           ->whereNull('parent_id')
                           ->orderBy('sort_order')
                           ->get()
            );
    }
    
    public function getActiveItems(int $menuId): Collection
    {
        $strategy = CacheStrategy::fromRequest();
        $cacheKey = $this->getCacheKey("active_items.{$menuId}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('menu_id', $menuId)
                           ->where('is_active', true)
                           ->orderBy('sort_order')
                           ->get()
            );
    }
    
    public function getChildrenOf(int $parentId): Collection
    {
        $strategy = CacheStrategy::fromRequest();
        $cacheKey = $this->getCacheKey("children_of.{$parentId}");
        
        return Cache::tags($this->getCacheTags())
            ->remember($cacheKey, $strategy->getCacheTtl(), fn() => 
                $this->model->where('parent_id', $parentId)
                           ->orderBy('sort_order')
                           ->get()
            );
    }
    
    public function create(array $data): MenuItem
    {
        // Sort order otomatik hesapla
        if (!isset($data['sort_order'])) {
            $maxOrder = $this->model->where('menu_id', $data['menu_id'])
                                   ->where('parent_id', $data['parent_id'] ?? null)
                                   ->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;
        }
        
        // Depth level hesapla
        if (!isset($data['depth_level'])) {
            $data['depth_level'] = $this->calculateDepthLevel($data['parent_id'] ?? null);
        }
        
        $item = $this->model->create($data);
        $this->clearCache();
        
        return $item;
    }
    
    public function update(int $id, array $data): bool
    {
        // Parent değişmişse depth level'ı yeniden hesapla
        if (isset($data['parent_id'])) {
            $data['depth_level'] = $this->calculateDepthLevel($data['parent_id']);
        }
        
        $result = $this->model->where('item_id', $id)->update($data);
        
        if ($result) {
            $this->clearCache();
            
            // Eğer parent değiştiyse çocukların depth'ini güncelle
            if (isset($data['parent_id'])) {
                $this->updateChildrenDepth($id);
            }
        }
        
        return (bool) $result;
    }
    
    public function delete(int $id): bool
    {
        // Önce tüm çocukları sil (cascade)
        $this->deleteChildren($id);
        
        $result = $this->model->where('item_id', $id)->delete();
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function toggleActive(int $id): bool
    {
        $item = $this->model->where('item_id', $id)->first(['item_id', 'is_active']);
        
        if (!$item) {
            return false;
        }
        
        $result = $this->model->where('item_id', $id)->update(['is_active' => !$item->is_active]);
        
        if ($result) {
            $this->clearCache();
        }
        
        return (bool) $result;
    }
    
    public function updateSortOrder(array $items): bool
    {
        $updated = 0;
        
        foreach ($items as $item) {
            if (!isset($item['item_id'], $item['sort_order'])) {
                continue;
            }
            
            $this->model->where('item_id', $item['item_id'])
                       ->update(['sort_order' => $item['sort_order']]);
            $updated++;
        }
        
        if ($updated > 0) {
            $this->clearCache();
        }
        
        return $updated > 0;
    }
    
    public function moveToParent(int $itemId, ?int $parentId): bool
    {
        $depthLevel = $this->calculateDepthLevel($parentId);
        
        // Max depth kontrolü (3 seviye)
        if ($depthLevel > 3) {
            return false;
        }
        
        // Yeni parent'taki son sırayı al
        $maxOrder = $this->model->where('parent_id', $parentId)
                               ->max('sort_order') ?? 0;
        
        $result = $this->model->where('item_id', $itemId)->update([
            'parent_id' => $parentId,
            'depth_level' => $depthLevel,
            'sort_order' => $maxOrder + 1
        ]);
        
        if ($result) {
            $this->clearCache();
            $this->updateChildrenDepth($itemId);
        }
        
        return (bool) $result;
    }
    
    public function reorderItems(int $menuId, array $order): bool
    {
        $updated = 0;
        
        foreach ($order as $index => $itemData) {
            if (!isset($itemData['item_id'])) {
                continue;
            }
            
            $updateData = [
                'sort_order' => $index + 1,
                'parent_id' => $itemData['parent_id'] ?? null,
                'depth_level' => $this->calculateDepthLevel($itemData['parent_id'] ?? null)
            ];
            
            $this->model->where('item_id', $itemData['item_id'])
                       ->where('menu_id', $menuId)
                       ->update($updateData);
            $updated++;
        }
        
        if ($updated > 0) {
            $this->clearCache();
        }
        
        return $updated > 0;
    }
    
    public function bulkDelete(array $ids): int
    {
        $count = 0;
        
        foreach ($ids as $id) {
            if ($this->delete($id)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    public function clearCache(): void
    {
        Cache::tags($this->getCacheTags())->flush();
    }
    
    private function calculateDepthLevel(?int $parentId): int
    {
        if ($parentId === null) {
            return 0;
        }
        
        $parent = $this->model->where('item_id', $parentId)->first(['depth_level']);
        
        return $parent ? $parent->depth_level + 1 : 0;
    }
    
    private function updateChildrenDepth(int $parentId): void
    {
        $children = $this->model->where('parent_id', $parentId)->get(['item_id', 'depth_level']);
        
        foreach ($children as $child) {
            $newDepth = $this->calculateDepthLevel($parentId);
            $this->model->where('item_id', $child->item_id)->update(['depth_level' => $newDepth]);
            
            // Recursive olarak çocukların çocuklarını da güncelle
            $this->updateChildrenDepth($child->item_id);
        }
    }
    
    private function deleteChildren(int $parentId): void
    {
        $children = $this->model->where('parent_id', $parentId)->get(['item_id']);
        
        foreach ($children as $child) {
            $this->deleteChildren($child->item_id); // Recursive
            $this->model->where('item_id', $child->item_id)->delete();
        }
    }
    
    protected function getCacheKey(string $key): string
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        return "{$this->cachePrefix}.tenant.{$tenantId}.{$key}";
    }
    
    protected function getCacheTags(): array
    {
        $tenantId = tenant() ? tenant()->id : 'landlord';
        return ["menu_items", "tenant.{$tenantId}"];
    }
}