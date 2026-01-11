<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\MenuManagement\App\Contracts\{MenuRepositoryInterface, MenuItemRepositoryInterface};
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\MenuManagement\App\Models\{Menu, MenuItem};
use Modules\MenuManagement\App\DataTransferObjects\{MenuOperationResult, MenuItemOperationResult};
use Modules\MenuManagement\App\Exceptions\{MenuNotFoundException, MenuCreationException, MenuItemNotFoundException};
use Throwable;

readonly class MenuService
{
    public function __construct(
        private MenuRepositoryInterface $menuRepository,
        private MenuItemRepositoryInterface $menuItemRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}
    
    public function getMenu(int $id): Menu
    {
        return $this->menuRepository->findById($id) 
            ?? throw MenuNotFoundException::withId($id);
    }
    
    public function getMenuWithItems(int $id): Menu
    {
        return $this->menuRepository->findByIdWithItems($id)
            ?? throw MenuNotFoundException::withId($id);
    }
    
    public function getMenuBySlug(string $slug): Menu
    {
        return $this->menuRepository->findBySlug($slug)
            ?? throw MenuNotFoundException::withSlug($slug);
    }
    
    public function getMenuByLocation(string $location): ?Menu
    {
        return $this->menuRepository->findByLocation($location);
    }
    
    
    public function getDefaultMenu(): ?Menu
    {
        return $this->menuRepository->getDefault();
    }
    
    public function getPaginatedMenus(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->menuRepository->getPaginated($filters, $perPage);
    }
    
    public function searchMenus(string $term, array $locales = []): Collection
    {
        return $this->menuRepository->search($term, $locales);
    }
    
    public function createMenu(array $data): MenuOperationResult
    {
        try {
            // Slug oluştur
            if (isset($data['name']) && is_array($data['name'])) {
                $data['slug'] = $this->generateSlugFromName($data['name']);
            }
            
            // Default menü kontrolü
            if (!empty($data['is_default'])) {
                $existingDefault = $this->menuRepository->getDefault();
                if ($existingDefault) {
                    throw MenuCreationException::duplicateDefaultMenu();
                }
            }
            
            $menu = $this->menuRepository->create($data);
            
            // Clear all menu caches
            clearMenuCaches();
            
            return MenuOperationResult::success(
                message: __('menumanagement::admin.menu_created_successfully'),
                data: $menu
            );
            
        } catch (Throwable $e) {
            throw MenuCreationException::withDatabaseError($e->getMessage());
        }
    }
    
    public function updateMenu(int $id, array $data): MenuOperationResult
    {
        try {
            $menu = $this->menuRepository->findById($id)
                ?? throw MenuNotFoundException::withId($id);
            
            // Default menü kontrolü
            if (!empty($data['is_default']) && !$menu->is_default) {
                $existingDefault = $this->menuRepository->getDefault();
                if ($existingDefault && $existingDefault->menu_id !== $id) {
                    throw MenuCreationException::duplicateDefaultMenu();
                }
            }
            
            $updated = $this->menuRepository->update($id, $data);
            
            if (!$updated) {
                return MenuOperationResult::error(__('menumanagement::admin.menu_update_failed'));
            }
            
            // Clear menu caches
            clearMenuCaches($id);
            
            return MenuOperationResult::success(
                message: __('menumanagement::admin.menu_updated_successfully'),
                data: $this->menuRepository->findById($id)
            );
            
        } catch (Throwable $e) {
            return MenuOperationResult::error($e->getMessage());
        }
    }
    
    public function deleteMenu(int $id): MenuOperationResult
    {
        try {
            $menu = $this->menuRepository->findById($id)
                ?? throw MenuNotFoundException::withId($id);
            
            if ($menu->is_default) {
                return MenuOperationResult::warning(
                    __('menumanagement::admin.default_menu_cannot_be_deleted')
                );
            }
            
            $deleted = $this->menuRepository->delete($id);
            
            if (!$deleted) {
                return MenuOperationResult::error(__('menumanagement::admin.menu_delete_failed'));
            }
            
            // Clear menu caches
            clearMenuCaches($id);
            
            return MenuOperationResult::success(
                message: __('menumanagement::admin.menu_deleted_successfully')
            );
            
        } catch (Throwable $e) {
            return MenuOperationResult::error($e->getMessage());
        }
    }
    
    public function toggleMenuStatus(int $id): MenuOperationResult
    {
        try {
            $menu = $this->menuRepository->findById($id)
                ?? throw MenuNotFoundException::withId($id);
            
            if ($menu->is_default && $menu->is_active) {
                return MenuOperationResult::warning(
                    __('menumanagement::admin.default_menu_cannot_be_deactivated')
                );
            }
            
            $toggled = $this->menuRepository->toggleActive($id);
            
            if (!$toggled) {
                return MenuOperationResult::error(__('menumanagement::admin.menu_toggle_failed'));
            }
            
            $newStatus = !$menu->is_active;
            
            // Clear menu caches
            clearMenuCaches($id);
            
            return MenuOperationResult::success(
                message: $newStatus 
                    ? __('menumanagement::admin.menu_activated_successfully')
                    : __('menumanagement::admin.menu_deactivated_successfully'),
                data: $this->menuRepository->findById($id),
                meta: ['new_status' => $newStatus]
            );
            
        } catch (Throwable $e) {
            return MenuOperationResult::error($e->getMessage());
        }
    }
    
    public function getMenuTree(int $menuId): array
    {
        return $this->menuRepository->getMenuTree($menuId);
    }
    
    // Menu Item Methods
    public function getMenuItem(int $id): MenuItem
    {
        return $this->menuItemRepository->findById($id)
            ?? throw MenuItemNotFoundException::withId($id);
    }
    
    public function getMenuItemsCollection(int $menuId): Collection
    {
        return $this->menuItemRepository->getByMenuId($menuId);
    }
    
    public function getRootMenuItems(int $menuId): Collection
    {
        return $this->menuItemRepository->getRootItems($menuId);
    }
    
    public function getActiveMenuItems(int $menuId): Collection
    {
        return $this->menuItemRepository->getActiveItems($menuId);
    }
    
    public function createMenuItem(array $data): MenuItemOperationResult
    {
        try {
            // Menu varlık kontrolü
            $menu = $this->menuRepository->findById($data['menu_id'])
                ?? throw MenuNotFoundException::withId($data['menu_id']);
            
            // Parent kontrolü
            if (!empty($data['parent_id'])) {
                $parent = $this->menuItemRepository->findById($data['parent_id']);
                if (!$parent || $parent->menu_id !== $data['menu_id']) {
                    throw MenuItemNotFoundException::inMenu($data['menu_id'], $data['parent_id']);
                }
            }
            
            $menuItem = $this->menuItemRepository->create($data);
            
            // Clear menu caches
            clearMenuCaches($data['menu_id']);
            
            return MenuItemOperationResult::success(
                message: __('menumanagement::admin.menu_item_created_successfully'),
                data: $menuItem
            );
            
        } catch (Throwable $e) {
            return MenuItemOperationResult::error($e->getMessage());
        }
    }
    
    public function updateMenuItem(int $id, array $data): MenuItemOperationResult
    {
        try {
            $menuItem = $this->menuItemRepository->findById($id)
                ?? throw MenuItemNotFoundException::withId($id);
            
            $updated = $this->menuItemRepository->update($id, $data);
            
            if (!$updated) {
                return MenuItemOperationResult::error(__('menumanagement::admin.menu_item_update_failed'));
            }
            
            // Clear menu caches
            clearMenuCaches($menuItem->menu_id);
            
            return MenuItemOperationResult::success(
                message: __('menumanagement::admin.menu_item_updated_successfully'),
                data: $this->menuItemRepository->findById($id)
            );
            
        } catch (Throwable $e) {
            return MenuItemOperationResult::error($e->getMessage());
        }
    }
    
    public function toggleMenuItemStatus(int $id): MenuItemOperationResult
    {
        try {
            $menuItem = $this->menuItemRepository->findById($id)
                ?? throw MenuItemNotFoundException::withId($id);
            
            $toggled = $this->menuItemRepository->toggleActive($id);
            
            if (!$toggled) {
                return MenuItemOperationResult::error(__('menumanagement::admin.menu_item_status_toggle_failed'));
            }
            
            $newStatus = !$menuItem->is_active;
            
            // Clear menu caches
            clearMenuCaches($menuItem->menu_id);
            
            return MenuItemOperationResult::success(
                message: $newStatus 
                    ? __('menumanagement::admin.menu_item_activated_successfully')
                    : __('menumanagement::admin.menu_item_deactivated_successfully'),
                data: $this->menuItemRepository->findById($id),
                meta: ['new_status' => $newStatus]
            );
            
        } catch (Throwable $e) {
            return MenuItemOperationResult::error($e->getMessage());
        }
    }
    
    public function updateMenuItemOrder(array $itemIds): MenuItemOperationResult
    {
        try {
            $updated = $this->menuItemRepository->updateOrder($itemIds);
            
            if (!$updated) {
                return MenuItemOperationResult::error(__('menumanagement::admin.menu_item_order_update_failed'));
            }
            
            // Clear all menu caches as order affects all items
            clearMenuCaches();
            
            return MenuItemOperationResult::success(
                message: __('menumanagement::admin.menu_item_order_updated_successfully')
            );
            
        } catch (Throwable $e) {
            return MenuItemOperationResult::error($e->getMessage());
        }
    }

    public function deleteMenuItem(int $id): MenuItemOperationResult
    {
        try {
            $menuItem = $this->menuItemRepository->findById($id)
                ?? throw MenuItemNotFoundException::withId($id);
            
            $deleted = $this->menuItemRepository->delete($id);
            
            if (!$deleted) {
                return MenuItemOperationResult::error(__('menumanagement::admin.menu_item_delete_failed'));
            }
            
            // Clear menu caches
            clearMenuCaches($menuItem->menu_id);
            
            return MenuItemOperationResult::success(
                message: __('menumanagement::admin.menu_item_deleted_successfully')
            );
            
        } catch (Throwable $e) {
            return MenuItemOperationResult::error($e->getMessage());
        }
    }
    
    public function reorderMenuItems(int $menuId, array $order): MenuItemOperationResult
    {
        try {
            $reordered = $this->menuItemRepository->reorderItems($menuId, $order);
            
            if (!$reordered) {
                return MenuItemOperationResult::error(__('menumanagement::admin.menu_items_reorder_failed'));
            }
            
            // Clear menu caches
            clearMenuCaches($menuId);
            
            return MenuItemOperationResult::success(
                message: __('menumanagement::admin.menu_items_reordered_successfully')
            );
            
        } catch (Throwable $e) {
            return MenuItemOperationResult::error($e->getMessage());
        }
    }
    
    public function prepareMenuForForm(int $id, string $currentLanguage): array
    {
        $menu = $this->getMenuWithItems($id);
        
        // SEO verilerini yükle
        $seoData = $this->seoRepository->getSeoDataForModel($menu, $currentLanguage) ?? [];
        
        // Tab completion durumunu hesapla
        $allData = array_merge(
            $menu->toArray(),
            $seoData
        );
        
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'menu');
        $seoLimits = $this->seoRepository->getSeoLimits('menu');
        
        return [
            'menu' => $menu,
            'seoData' => $seoData,
            'tabCompletion' => $tabCompletion,
            'seoLimits' => $seoLimits
        ];
    }
    
    public function clearCache(): void
    {
        $this->menuRepository->clearCache();
        $this->menuItemRepository->clearCache();
    }
    
    private function generateSlugFromName(array $names): string
    {
        $defaultLang = 'tr';
        $name = $names[$defaultLang] ?? $names[array_key_first($names)] ?? 'menu';
        
        return \Illuminate\Support\Str::slug($name);
    }
    
    /**
     * Frontend Helper: Get menu by location for rendering
     */
    public function getMenuForLocation(string $location, string $language = null): ?array
    {
        try {
            $language = $language ?? app()->getLocale();
            
            $menu = $this->menuRepository->findByLocation($location);
            if (!$menu || !$menu->is_active) {
                return null;
            }
            
            return [
                'id' => $menu->menu_id,
                'name' => $menu->getTranslated('name', $language),
                'location' => $menu->location,
                'items' => $this->getMenuItems($menu->menu_id, $language)
            ];
            
        } catch (\Exception $e) {
            logger('MenuService::getMenuForLocation error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Frontend Helper: Get all active menus
     */
    public function getActiveMenus(string $language = null): array
    {
        try {
            $language = $language ?? app()->getLocale();
            $menus = $this->menuRepository->getActiveMenus();
            
            return $menus->map(function($menu) use ($language) {
                return [
                    'id' => $menu->menu_id,
                    'name' => $menu->getTranslated('name', $language),
                    'location' => $menu->location,
                    'items' => $this->getMenuItems($menu->menu_id, $language)
                ];
            })->toArray();
            
        } catch (\Exception $e) {
            logger('MenuService::getActiveMenus error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get menu items (placeholder - will be implemented with MenuItem model)
     */
    private function getMenuItems(int $menuId, string $language): array
    {
        // MenuItem model henüz oluşturulmadı, implement edilecek
        return [];
    }
}