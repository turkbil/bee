<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\MenuManagement\App\Services\MenuService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;

#[Layout('admin.layout')]
class MenuItemManageComponent extends Component
{
    public $currentLanguage = 'tr';
    public $multiLangInputs = [];
    public $inputs = [];
    
    // MenuItem form data
    public $title = '';
    public $url_value = '';
    public $url_type = 'custom';
    public $parent_id = null;
    public $sort_order = 0;
    public $target = '_self';
    public $css_class = '';
    public $icon = '';
    public $is_active = true;
    public $editingMenuItemId = null;
    
    private MenuService $menuService;
    private ?Menu $headerMenu = null;
    
    public function boot(MenuService $menuService): void
    {
        $this->menuService = $menuService;
    }
    
    public function mount(): void
    {
        // Get default header menu
        $this->headerMenu = $this->menuService->getDefaultMenu();
        
        if (!$this->headerMenu) {
            // Create default menu if doesn't exist
            $this->createDefaultMenu();
        }
        
        $this->initializeFormData();
    }
    
    #[Computed]
    public function availableLanguages()
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
    
    #[Computed] 
    public function headerMenu()
    {
        return $this->headerMenu ??= $this->menuService->getDefaultMenu();
    }
    
    #[Computed]
    public function availableSiteLanguages()
    {
        return TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }

    #[Computed]
    public function headerMenuItems()
    {
        if (!$this->headerMenu) {
            return collect();
        }
        
        return $this->headerMenu->items()
            ->with(['children' => function ($query) {
                $query->orderBy('sort_order');
            }])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    private function loadHeaderMenu(): void
    {
        $this->headerMenu = $this->menuService->getDefaultMenu();
    }
    
    public function switchLanguage($language): void
    {
        $this->currentLanguage = $language;
    }
    
    public function addMenuItem(): void
    {
        $this->validate([
            'multiLangInputs.*.title' => 'required|string|max:255',
            'multiLangInputs.*.url_value' => 'required|string|max:255',
            'url_type' => 'required|in:custom,page,module,external',
            'target' => 'required|in:_self,_blank,_parent,_top',
            'sort_order' => 'required|integer|min:0',
        ]);
        
        try {
            $data = [
                'menu_id' => $this->headerMenu->menu_id,
                'title' => $this->multiLangInputs,
                'url_value' => $this->multiLangInputs,
                'url_type' => $this->url_type,
                'parent_id' => $this->parent_id,
                'sort_order' => $this->sort_order,
                'target' => $this->target,
                'css_class' => $this->css_class,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
                'visibility' => 'public',
                'depth_level' => 0
            ];
            
            $result = $this->menuService->createMenuItem($data);
            
            if ($result->success) {
                $this->dispatch('toast', [
                    'title' => __('admin.success'),
                    'message' => $result->message,
                    'type' => 'success'
                ]);
                
                $this->resetForm();
                $this->headerMenu = null; // Reset cache
            } else {
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => $result->message,
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }
    
    public function editMenuItem(int $menuItemId): void
    {
        try {
            $menuItem = MenuItem::findOrFail($menuItemId);
            
            // Form alanlarını doldur
            $this->multiLangInputs = $menuItem->title;
            $this->url_type = $menuItem->url_type;
            $this->target = $menuItem->target;
            $this->sort_order = $menuItem->sort_order;
            $this->parent_id = $menuItem->parent_id;
            $this->css_class = $menuItem->css_class;
            $this->icon = $menuItem->icon;
            $this->is_active = $menuItem->is_active;
            $this->editingMenuItemId = $menuItemId;
            
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('menumanagement::admin.menu_item_loaded_for_editing'),
                'type' => 'info',
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('menumanagement::admin.menu_item_load_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function deleteMenuItem($itemId): void
    {
        try {
            $result = $this->menuService->deleteMenuItem($itemId);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.error'),
                'message' => $result->message,
                'type' => $result->success ? 'success' : 'error'
            ]);
            
            if ($result->success) {
                $this->loadHeaderMenu();
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function toggleMenuItemStatus(int $menuItemId): void
    {
        try {
            $result = $this->menuService->toggleMenuItemStatus($menuItemId);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.error'),
                'message' => $result->message,
                'type' => $result->type,
            ]);
            
            if ($result->success) {
                $this->loadHeaderMenu();
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('menumanagement::admin.menu_item_status_update_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function updateMenuItemOrder(array $itemIds): void
    {
        try {
            $result = $this->menuService->updateMenuItemOrder($itemIds);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.error'),
                'message' => $result->message,
                'type' => $result->type,
            ]);
            
            if ($result->success) {
                $this->loadHeaderMenu();
            }
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('menumanagement::admin.menu_item_order_update_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function refreshMenuItems(): void
    {
        $this->loadHeaderMenu();
    }
    
    private function createDefaultMenu(): void
    {
        // Create default header menu if it doesn't exist
        $languages = $this->availableLanguages;
        
        $menuData = [
            'name' => [],
            'location' => 'header',
            'is_active' => true,
            'is_default' => true,
            'slug' => 'header-menu'
        ];
        
        foreach ($languages as $language) {
            $menuData['name'][$language->code] = $language->code === 'tr' ? 'Ana Menü' : 'Main Menu';
        }
        
        $result = $this->menuService->createMenu($menuData);
        if ($result->success) {
            $this->headerMenu = $result->data;
        }
    }
    
    private function initializeFormData(): void
    {
        $this->multiLangInputs = [];
        foreach ($this->availableLanguages as $language) {
            $this->multiLangInputs[$language->code] = [
                'title' => '',
                'url_value' => ''
            ];
        }
        
        $this->resetForm();
    }
    
    private function resetForm(): void
    {
        foreach ($this->availableLanguages as $language) {
            $this->multiLangInputs[$language->code] = [
                'title' => '',
                'url_value' => ''
            ];
        }
        
        $this->url_type = 'custom';
        $this->parent_id = null;
        $this->sort_order = 0;
        $this->target = '_self';
        $this->css_class = '';
        $this->icon = '';
        $this->is_active = true;
    }
    
    public function render()
    {
        return view('menumanagement::admin.livewire.menu-item-manage-component', [
            'headerMenu' => $this->headerMenu,
            'headerMenuItems' => $this->headerMenuItems,
            'availableLanguages' => $this->availableLanguages
        ]);
    }
}