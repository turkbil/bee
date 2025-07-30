<?php

namespace Modules\MenuManagement\App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\MenuManagement\App\Models\Menu;

interface MenuRepositoryInterface
{
    public function findById(int $id): ?Menu;
    
    public function findByIdWithItems(int $id): ?Menu;
    
    public function findBySlug(string $slug): ?Menu;
    
    public function findByLocation(string $location): ?Menu;
    
    public function getActive(): Collection;
    
    public function getActiveMenus(): Collection;
    
    public function getDefault(): ?Menu;
    
    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;
    
    public function search(string $term, array $locales = []): Collection;
    
    public function create(array $data): Menu;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
    
    public function toggleActive(int $id): bool;
    
    public function bulkDelete(array $ids): int;
    
    public function bulkToggleActive(array $ids): int;
    
    public function getMenuTree(int $menuId): array;
    
    public function clearCache(): void;
}