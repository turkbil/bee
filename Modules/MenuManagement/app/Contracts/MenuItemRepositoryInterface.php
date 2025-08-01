<?php

namespace Modules\MenuManagement\App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\MenuManagement\App\Models\MenuItem;

interface MenuItemRepositoryInterface
{
    public function findById(int $id): ?MenuItem;
    
    public function findByIdWithChildren(int $id): ?MenuItem;
    
    public function getByMenuId(int $menuId): Collection;
    
    public function getRootItems(int $menuId): Collection;
    
    public function getActiveItems(int $menuId): Collection;
    
    public function getChildrenOf(int $parentId): Collection;
    
    public function create(array $data): MenuItem;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function toggleActive(int $id): bool;
    
    public function updateSortOrder(array $items): bool;
    
    public function updateOrder(array $itemIds): bool;
    
    public function moveToParent(int $itemId, ?int $parentId): bool;
    
    public function reorderItems(int $menuId, array $order): bool;
    
    public function bulkDelete(array $ids): int;
    
    public function clearCache(): void;
}