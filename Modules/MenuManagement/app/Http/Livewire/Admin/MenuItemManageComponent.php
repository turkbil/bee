<?php

declare(strict_types=1);

namespace Modules\MenuManagement\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Computed};
use Livewire\Component;
use Modules\MenuManagement\App\Services\{MenuService, MenuUrlBuilderService};
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;
use Nwidart\Modules\Facades\Module;

#[Layout('admin.layout')]
class MenuItemManageComponent extends Component
{
    public $currentLanguage = 'tr';
    public $availableLanguages = [];
    public $multiLangInputs = [];
    public $inputs = [];
    
    // Tab Configuration
    public $tabConfig = [];
    public $tabCompletionStatus = [];
    
    // MenuItem form data
    public $title = '';
    public $url_type = '';
    public $url_data = [];
    public $parent_id = null;
    public $sort_order = 0;
    public $target = '_self';
    public $icon = '';
    public $is_active = true;
    public $editingMenuItemId = null;
    
    // Delete Modal Properties
    public $showDeleteModal = false;
    public $deleteItemId = null;
    public $deleteItemTitle = '';
    
    // Search Property
    public $search = '';
    
    // Dynamic URL options
    public $availableModules = [];
    public $moduleUrlTypes = [];
    public $moduleContent = [];
    public $selectedModule = '';
    public $selectedUrlType = '';
    
    private MenuService $menuService;
    private MenuUrlBuilderService $urlBuilder;
    private ?Menu $headerMenu = null;
    
    // SOLID Dependencies - Page pattern
    protected $pageService;
    protected $seoRepository;
    
    // Livewire Listeners - Page pattern ile aynı
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'tab-changed' => 'handleTabChange',
        'switchLanguage' => 'switchLanguage',
        'js-language-sync' => 'handleJavaScriptLanguageSync',
        'handleTestEvent' => 'handleTestEvent',
        'simple-test' => 'handleSimpleTest',
        'handleJavaScriptLanguageSync' => 'handleJavaScriptLanguageSync',
        'debug-test' => 'handleDebugTest',
        'set-js-language' => 'setJavaScriptLanguage',
        'set-continue-mode' => 'setContinueMode',
        'updateOrder' => 'updateOrder',
        'itemDeleted' => 'handleItemDeleted'
    ];
    
    public function boot(MenuService $menuService, MenuUrlBuilderService $urlBuilder): void
    {
        $this->menuService = $menuService;
        $this->urlBuilder = $urlBuilder;
    }
    
    public function mount($id = null): void
    {
        // Initialize available languages for global language switcher
        $this->availableLanguages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
            
        // Set default current language
        $this->currentLanguage = $this->availableLanguages[0] ?? 'tr';
        
        // Initialize tab configuration
        $this->tabConfig = [
            ['name' => __('menumanagement::admin.add_menu_item'), 'icon' => 'fas fa-plus']
        ];
        
        $this->tabCompletionStatus = [0 => true];
        
        // Get menu by ID or default header menu
        if ($id) {
            $this->headerMenu = $this->menuService->getMenu($id);
            if (!$this->headerMenu) {
                session()->flash('error', __('menumanagement::admin.menu_not_found'));
                $this->redirect(route('admin.menumanagement.menu.index'));
                return;
            }
        } else {
            // Default behavior for main page (ID=1)
            $this->headerMenu = $this->menuService->getDefaultMenu();
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
    public function currentHeaderMenu()
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
        if (!$this->currentHeaderMenu) {
            logger('❌ currentHeaderMenu null');
            return collect();
        }
        
        // CACHE BYPASS: Doğrudan MenuItem modelinden çek - relation cache sorunu için
        // Sort order'a göre sırala - parent-child ilişkisi zaten doğru sort_order ile yönetiliyor
        $query = MenuItem::where('menu_id', $this->currentHeaderMenu->menu_id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('item_id', 'asc');
        
        // Search filter
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $query->where(function($q) use ($search) {
                // JSON title alanında arama
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr'))) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en'))) LIKE ?", ["%{$search}%"])
                  // URL data arama
                  ->orWhere('url_data', 'LIKE', "%{$search}%")
                  // Icon arama
                  ->orWhere('icon', 'LIKE', "%{$search}%")
                  // Target arama
                  ->orWhere('target', 'LIKE', "%{$search}%");
            });
        }
        
        $items = $query->get();
        
        // DETAILED DEBUG - Temporarily
        foreach ($items as $index => $item) {
            $title = $item->getTranslated('title', app()->getLocale());
            $urlData = is_array($item->url_data) ? $item->url_data : json_decode($item->url_data, true);
            $module = $urlData['module'] ?? 'N/A';
            $parentInfo = $item->parent_id ? "Child of: {$item->parent_id}" : 'MAIN ITEM';
            
            // logger("🔍 ITEM #{$index}", [
            //     'id' => $item->item_id,
            //     'parent_id' => $item->parent_id,
            //     'title' => $title,
            //     'module' => $module,
            //     'sort_order' => $item->sort_order,
            //     'relationship' => $parentInfo
            // ]);
        }
            
        // logger('🔍 headerMenuItems DEBUG', [
        //     'menu_id' => $this->currentHeaderMenu->menu_id ?? 'null',
        //     'items_count' => $items->count(),
        //     'search_term' => $this->search,
        //     'filtered' => !empty($this->search)
        // ]);

        return $items;
    }

    /**
     * Dropdown için hiyerarşik menü listesi
     * Depth level'a göre "─" prefix ekler
     */
    #[Computed]
    public function hierarchicalMenuItems()
    {
        if (!$this->currentHeaderMenu) {
            return collect();
        }

        $items = MenuItem::where('menu_id', $this->currentHeaderMenu->menu_id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('item_id', 'asc')
            ->get();

        return $items->map(function($item) {
            $depth = $item->depth_level ?? 0;
            $prefix = str_repeat('─', $depth);
            if ($depth > 0) {
                $prefix .= ' ';
            }

            return [
                'id' => $item->item_id,
                'title' => $prefix . $item->getTranslated('title', app()->getLocale()),
                'depth' => $depth,
                'parent_id' => $item->parent_id
            ];
        });
    }

    private function loadHeaderMenu(): void
    {
        $this->headerMenu = $this->menuService->getDefaultMenu();
    }
    
    
    /**
     * Menü öğesi kaydetme ana metodu - hem ekle hem güncelle
     */
    public function saveMenuItem(): void
    {
        if ($this->editingMenuItemId) {
            $this->updateMenuItem();
        } else {
            $this->addMenuItem();
        }
    }

    public function addMenuItem(): void
    {
        // DEBUG: url_data içeriğini kontrol et
        \Log::info('🔍 addMenuItem BAŞLANGIÇ', [
            'url_type' => $this->url_type,
            'url_data' => $this->url_data,
            'selectedModule' => $this->selectedModule,
            'selectedUrlType' => $this->selectedUrlType
        ]);

        // Validation rules
        $validationRules = [
            'url_type' => 'required|in:internal,external,module,url',
            'target' => 'required|in:_self,_blank,_parent,_top',
        ];

        // Multi-language title validation
        foreach ($this->multiLangInputs as $lang => $data) {
            if (!empty($data['title'])) {
                $validationRules["multiLangInputs.{$lang}.title"] = 'required|string|max:255';
            }
        }

        // En az bir dil dolu olmalı
        $hasContent = false;
        foreach ($this->multiLangInputs as $data) {
            if (!empty($data['title'])) {
                $hasContent = true;
                break;
            }
        }

        if (!$hasContent) {
            $this->addError('multiLangInputs', 'En az bir dil için başlık girilmelidir.');
            return;
        }

        // URL data validation based on type
        if ($this->url_type === 'internal' || $this->url_type === 'external' || $this->url_type === 'url') {
            $validationRules['url_data.url'] = 'required|string';
        } elseif ($this->url_type === 'module') {
            // KRİTİK: Validation öncesi url_data'yı güncelle
            if (!empty($this->selectedModule)) {
                $this->url_data['module'] = $this->selectedModule;
            }
            if (!empty($this->selectedUrlType)) {
                $this->url_data['type'] = $this->selectedUrlType;
            }

            $validationRules['url_data.module'] = 'required|string';
            $validationRules['url_data.type'] = 'required|string';
        }

        \Log::info('🔍 VALIDATION ÖNCESI url_data', [
            'url_data' => $this->url_data,
            'validation_rules' => $validationRules
        ]);

        $this->validate($validationRules);

        try {
            // Title array oluştur
            $titleArray = [];
            foreach ($this->multiLangInputs as $lang => $data) {
                if (!empty($data['title'])) {
                    $titleArray[$lang] = $data['title'];
                }
            }

            $data = [
                'menu_id' => $this->currentHeaderMenu->menu_id,
                'title' => $titleArray,
                'url_type' => $this->url_type,
                'url_data' => $this->url_data,
                'parent_id' => $this->parent_id,
                'target' => $this->target,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
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

                // Manuel sortable refresh
                $this->dispatch('refresh-sortable');
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
                'message' => __('admin.operation_failed') . ': ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updateMenuItem(): void
    {
        if (!$this->editingMenuItemId) {
            return;
        }

        // Validation rules - same as addMenuItem
        $validationRules = [
            'url_type' => 'required|in:internal,external,module,url',
            'target' => 'required|in:_self,_blank,_parent,_top',
        ];

        // Multi-language title validation
        foreach ($this->multiLangInputs as $lang => $data) {
            if (!empty($data['title'])) {
                $validationRules["multiLangInputs.{$lang}.title"] = 'required|string|max:255';
            }
        }

        // En az bir dil dolu olmalı
        $hasContent = false;
        foreach ($this->multiLangInputs as $data) {
            if (!empty($data['title'])) {
                $hasContent = true;
                break;
            }
        }

        if (!$hasContent) {
            $this->addError('multiLangInputs', 'En az bir dil için başlık girilmelidir.');
            return;
        }

        // URL data validation based on type
        if ($this->url_type === 'internal' || $this->url_type === 'external' || $this->url_type === 'url') {
            $validationRules['url_data.url'] = 'required|string';
        } elseif ($this->url_type === 'module') {
            // KRİTİK: Validation öncesi url_data'yı güncelle
            if (!empty($this->selectedModule)) {
                $this->url_data['module'] = $this->selectedModule;
            }
            if (!empty($this->selectedUrlType)) {
                $this->url_data['type'] = $this->selectedUrlType;
            }

            $validationRules['url_data.module'] = 'required|string';
            $validationRules['url_data.type'] = 'required|string';
        }

        $this->validate($validationRules);

        try {
            // Title array oluştur
            $titleArray = [];
            foreach ($this->multiLangInputs as $lang => $data) {
                if (!empty($data['title'])) {
                    $titleArray[$lang] = $data['title'];
                }
            }

            $data = [
                'title' => $titleArray,
                'url_type' => $this->url_type,
                'url_data' => $this->url_data,
                'parent_id' => $this->parent_id,
                'target' => $this->target,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ];

            $result = $this->menuService->updateMenuItem($this->editingMenuItemId, $data);

            if ($result->success) {
                $this->dispatch('toast', [
                    'title' => __('admin.success'),
                    'message' => $result->message,
                    'type' => 'success'
                ]);

                $this->resetForm();
                $this->editingMenuItemId = null;
                $this->headerMenu = null; // Reset cache

                // Manuel sortable refresh
                $this->dispatch('refresh-sortable');
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
                'message' => __('admin.operation_failed') . ': ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function editMenuItem(int $menuItemId): void
    {
        try {
            $menuItem = MenuItem::findOrFail($menuItemId);
            
            // Form alanlarını doldur
            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang]['title'] = $menuItem->getTranslated('title', $lang) ?? '';
            }
            
            $this->url_type = $menuItem->url_type;
            $this->url_data = $menuItem->url_data ?? [];
            $this->target = $menuItem->target;
            $this->sort_order = $menuItem->sort_order;
            $this->parent_id = $menuItem->parent_id;
            $this->icon = $menuItem->icon;
            $this->is_active = $menuItem->is_active;
            $this->editingMenuItemId = $menuItemId;
            
            // URL tipine göre form hazırlığı
            if ($this->url_type === 'module' && isset($this->url_data['module'])) {
                $this->loadAvailableModules();

                // selectedModule property'sini set et
                $this->selectedModule = $this->url_data['module'];

                \Log::info('🔍 EditMenuItem - Module Set', [
                    'selectedModule' => $this->selectedModule,
                    'url_data[module]' => $this->url_data['module']
                ]);

                $this->moduleSelected($this->url_data['module']);

                if (isset($this->url_data['type'])) {
                    // selectedUrlType property'sini set et
                    $this->selectedUrlType = $this->url_data['type'];

                    \Log::info('🔍 EditMenuItem - URL Type Set', [
                        'selectedUrlType' => $this->selectedUrlType,
                        'url_data[type]' => $this->url_data['type'],
                        'moduleUrlTypes' => $this->moduleUrlTypes
                    ]);

                    $this->urlTypeSelected($this->url_data['type']);

                    // Eğer slug yoksa ama ID varsa, slug'ı bul
                    if (!isset($this->url_data['slug']) && isset($this->url_data['id'])) {
                        $contents = $this->moduleContent;
                        $selectedContent = collect($contents)->firstWhere('id', $this->url_data['id']);
                        if ($selectedContent && isset($selectedContent['slug'])) {
                            $this->url_data['slug'] = $selectedContent['slug'];
                        }
                    }

                    \Log::info('🔍 EditMenuItem - Final State', [
                        'moduleContent' => $this->moduleContent,
                        'url_data' => $this->url_data
                    ]);
                }
            } elseif ($this->url_type === 'internal' || $this->url_type === 'external') {
                // Internal/External için url_data'da url varsa direkt kullan
                // Form zaten wire:model ile bağlı
            }
            
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
                $this->dispatch('refresh-sortable');
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
                $this->dispatch('refresh-sortable');
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
        $this->dispatch('refresh-sortable');
    }
    
    /**
     * Circular reference problemini düzelt
     */
    public function fixCircularReference(): void
    {
        try {
            // ID: 15'i ana kategoriye çevir
            MenuItem::where('item_id', 15)->update([
                'parent_id' => null,
                'depth_level' => 0
            ]);
            
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => 'Circular reference problemi düzeltildi',
                'type' => 'success'
            ]);
            
            $this->loadHeaderMenu();
            $this->dispatch('refresh-sortable');
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => 'Düzeltme başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Widget pattern'ından uyarlanan updateOrder metodu - JavaScript drag-drop için
     */
    public function updateOrder($list = null)
    {
        try {
            // KRİTİK DEBUG: Bu metod çağrılıyor mu?
            \Log::info('🚨 UPDATEORDER METODU ÇAĞRILDI!', [
                'received_list' => $list,
                'list_type' => gettype($list),
                'timestamp' => now()->format('H:i:s'),
                'user_id' => auth()->id()
            ]);
            
            logger('🔄 MenuManagement drag-drop updateOrder başladı', [
                'received_list' => $list,
                'list_type' => gettype($list)
            ]);

            // JavaScript'ten gelen veri formatını kontrol et: list array direkt geliyor
            $items = $list;
            
            if (!is_array($items) || empty($items)) {
                logger('❌ updateOrder: Geçersiz items parametresi', [
                    'items' => $items
                ]);
                return;
            }

            logger('🎯 Tam item verisi alındı', [
                'items' => $items,
                'count' => count($items)
            ]);

            // Service metodunu çağır - tam item array'i gönder (id, parentId dahil)
            $result = $this->menuService->updateMenuItemOrder($items);
            
            if ($result->success) {
                // Anlık güncelleme için cache'i temizle
                $this->headerMenu = null;
                unset($this->headerMenuItems);

                logger('✅ MenuManagement drag-drop başarılı', [
                    'updated_items' => count($items)
                ]);
                
                // Başarı bildirimi - diğer sistemlerdeki gibi görünür bildirim
                $this->dispatch('toast', [
                    'title' => __('admin.success'),
                    'message' => __('menumanagement::admin.menu_items_order_updated'),
                    'type' => 'success',
                    'duration' => 3000
                ]);
                
                // Manuel sortable refresh - sonsuz döngü önlemi
                $this->dispatch('refresh-sortable');
            } else {
                logger('❌ MenuManagement drag-drop başarısız', [
                    'error' => $result->message
                ]);
                
                $this->dispatch('toast', [
                    'title' => __('admin.error'),
                    'message' => $result->message,
                    'type' => 'error'
                ]);
            }
            
        } catch (\Exception $e) {
            logger('🚨 MenuManagement updateOrder exception', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Show delete modal for menu item
     */
    public function openDeleteModal(int $itemId, string $title)
    {
        $this->deleteItemId = $itemId;
        $this->deleteItemTitle = $title;
        $this->showDeleteModal = true;
    }
    
    /**
     * Close delete modal
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteItemId = null;
        $this->deleteItemTitle = '';
    }
    
    /**
     * Confirm delete action
     */
    public function confirmDelete()
    {
        if (!$this->deleteItemId) {
            $this->closeDeleteModal();
            return;
        }
        
        try {
            $result = $this->menuService->deleteMenuItem($this->deleteItemId);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.error'),
                'message' => $result->message,
                'type' => $result->success ? 'success' : 'error'
            ]);
            
            if ($result->success) {
                $this->loadHeaderMenu();
                $this->dispatch('refresh-sortable');
            }
            
            $this->closeDeleteModal();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error'
            ]);
            $this->closeDeleteModal();
        }
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
        
        foreach ($languages as $languageCode) {
            $menuData['name'][$languageCode] = $languageCode === 'tr' ? 'Ana Menü' : 'Main Menu';
        }
        
        $result = $this->menuService->createMenu($menuData);
        if ($result->success) {
            $this->headerMenu = $result->data;
        }
    }
    
    private function initializeFormData(): void
    {
        $this->multiLangInputs = [];
        foreach ($this->availableLanguages as $languageCode) {
            $this->multiLangInputs[$languageCode] = [
                'title' => ''
            ];
        }
        
        $this->resetForm();
    }
    
    private function resetForm(): void
    {
        foreach ($this->availableLanguages as $languageCode) {
            $this->multiLangInputs[$languageCode] = [
                'title' => ''
            ];
        }

        $this->url_type = '';
        $this->url_data = [];
        $this->parent_id = null;
        $this->sort_order = 0;
        $this->target = '_self';
        $this->icon = '';
        $this->is_active = true;
        $this->selectedModule = '';
        $this->selectedUrlType = '';
        $this->moduleUrlTypes = [];
        $this->moduleContent = [];
        $this->editingMenuItemId = null;
    }

    /**
     * Düzenlemeyi iptal et ve formu temizle
     */
    public function cancelEdit(): void
    {
        $this->resetForm();

        $this->dispatch('toast', [
            'title' => __('admin.info'),
            'message' => 'Düzenleme iptal edildi',
            'type' => 'info'
        ]);
    }
    
    /**
     * Language switcher method - same as Page pattern
     */
    public function switchLanguage($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $oldLanguage = $this->currentLanguage;
            $this->currentLanguage = $language;
            
            // Session'a kaydet - save sonrası dil koruması için
            session(['menu_manage_language' => $language]);
            
            // KRİTİK FİX: Dil değişince form verilerini kontrol et ve initialize et
            if (!isset($this->multiLangInputs[$language]) || empty($this->multiLangInputs[$language])) {
                $this->multiLangInputs[$language] = [
                    'title' => '',
                    'url_value' => ''
                ];
                \Log::info('🔧 Dil değişince boş form verileri initialize edildi', [
                    'language' => $language,
                    'initialized_fields' => ['title', 'url_value']
                ]);
            }
            
            \Log::info('🎯 MenuItemManageComponent switchLanguage çağrıldı', [
                'old_language' => $oldLanguage,
                'new_language' => $language,
                'current_language' => $this->currentLanguage,
                'is_successfully_changed' => $this->currentLanguage === $language,
                'form_data_ready' => isset($this->multiLangInputs[$language])
            ]);
            
            // JavaScript'e dil değişikliğini bildir (TinyMCE için)
            $this->dispatch('language-switched', [
                'language' => $language,
                'editorId' => "editor_{$language}",
                'content' => $this->multiLangInputs[$language]['title'] ?? ''
            ]);
            
            // KRİTİK: $refresh kaldırıldı - dil değişiminde validation tetiklenmemeli
        }
    }

    // ===== JAVASCRIPT SYNC METHODS - PAGE PATTERN =====
    
    // JavaScript Language Sync Handler
    public function handleJavaScriptLanguageSync($data)
    {
        $jsLanguage = $data['language'] ?? '';
        $oldLanguage = $this->currentLanguage;
        
        \Log::info('🚨 KRİTİK: MenuManagement handleJavaScriptLanguageSync çağrıldı', [
            'js_language' => $jsLanguage,
            'current_language' => $this->currentLanguage,
            'data' => $data,
            'will_change' => in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage
        ]);
        
        if (in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage) {
            $this->currentLanguage = $jsLanguage;
            
            // JavaScript'e confirmation gönder
            $this->dispatch('language-sync-completed', [
                'language' => $jsLanguage,
                'oldLanguage' => $oldLanguage,
                'success' => true
            ]);
            
            \Log::info('🔄 MenuManagement JavaScript Language Sync - Livewire güncellendi', [
                'old_language' => $oldLanguage,
                'new_language' => $jsLanguage,
                'current_language' => $this->currentLanguage,
                'sync_successful' => true
            ]);
        } else {
            // Değişiklik yoksa da confirmation gönder
            $this->dispatch('language-sync-completed', [
                'language' => $this->currentLanguage,
                'oldLanguage' => $oldLanguage,
                'success' => false,
                'reason' => 'no_change_needed'
            ]);
        }
    }

    // Test event handler
    public function handleTestEvent($data)
    {
        \Log::info('🧪 MenuManagement TEST EVENT ALINDI! Livewire listener calisiyor!', [
            'data' => $data,
            'timestamp' => now(),
            'component' => 'MenuItemManageComponent',  
            'event_working' => 'YES - JavaScript to Livewire works!'
        ]);
    }

    // Simple test handler
    public function handleSimpleTest($data)
    {
        \Log::info('🎯 MenuManagement SIMPLE TEST EVENT ALINDI! jQuery + Livewire 3.6.3 calisiyor!', [
            'data' => $data,
            'timestamp' => now(),
            'message' => $data['message'] ?? 'no message',
            'language' => $data['language'] ?? 'no language',
            'test_successful' => true
        ]);
    }

    // Debug Test Handler
    public function handleDebugTest($data)
    {
        \Log::info('🔥 MenuManagement DEBUG TEST EVENT ALINDI!', [
            'data' => $data,
            'current_language' => $this->currentLanguage,
            'message' => $data['message'] ?? 'no message',
            'language' => $data['language'] ?? 'no language',
            'timestamp' => $data['timestamp'] ?? 'no timestamp',
            'livewire_working' => true
        ]);
    }

    // JavaScript Language Session Handler
    public function setJavaScriptLanguage($data)
    {
        $jsLanguage = $data['language'] ?? '';
        
        // Session'a JavaScript currentLanguage'i kaydet
        session(['js_current_language' => $jsLanguage]);
        
        \Log::info('📝 MenuManagement JavaScript language session\'a kaydedildi', [
            'js_language' => $jsLanguage,
            'session_set' => true,
            'current_livewire_language' => $this->currentLanguage
        ]);
    }

    // Kaydet ve Devam Et Handler
    public function setContinueMode($data)
    {
        session([
            'menu_continue_mode' => $data['continue_mode'] ?? false,
            'js_saved_language' => $data['saved_language'] ?? 'tr'
        ]);

        \Log::info('✅ MenuManagement Kaydet ve Devam Et - session verileri kaydedildi', [
            'continue_mode' => $data['continue_mode'] ?? false,
            'saved_language' => $data['saved_language'] ?? 'tr',
            'session_set' => true
        ]);
    }

    // Tab Change Handler  
    public function handleTabChange($data)
    {
        \Log::info('🔄 MenuManagement Tab değişti', [
            'tab_data' => $data,
            'current_language' => $this->currentLanguage
        ]);
    }
    
    /**
     * Property değişimlerini dinle
     */
    public function updated($propertyName)
    {
        if ($propertyName === 'url_type') {
            $this->urlTypeChanged();
        }

        // selectedModule değiştiğinde url_data'ya map et
        if ($propertyName === 'selectedModule') {
            $this->moduleSelected($this->selectedModule);
        }

        // selectedUrlType değiştiğinde url_data'ya map et
        if ($propertyName === 'selectedUrlType') {
            $this->urlTypeSelected($this->selectedUrlType);
        }
    }
    
    /**
     * URL tipi değiştiğinde
     */
    public function urlTypeChanged()
    {
        // Reset selections
        $this->url_data = [];
        $this->selectedModule = '';
        $this->selectedUrlType = '';
        $this->moduleUrlTypes = [];
        $this->moduleContent = [];
        
        // Module seçimi için hazırla
        if ($this->url_type === 'module') {
            $this->loadAvailableModules();
        }
        
        $this->dispatch('url-type-changed', ['url_type' => $this->url_type]);
    }
    
    /**
     * Mevcut modülleri yükle (sadece type=content olanlar)
     */
    public function loadAvailableModules()
    {
        // MenuUrlBuilderService artık sadece content tipindeki modülleri döndürüyor
        $this->availableModules = $this->urlBuilder->getAvailableModules();
        
        \Log::info('🔍 MenuManagement - Available modules loaded', [
            'modules' => $this->availableModules,
            'count' => count($this->availableModules)
        ]);
        
        $this->dispatch('modules-loaded', ['modules' => $this->availableModules]);
    }
    
    /**
     * Modül seçildiğinde URL tiplerini yükle
     */
    public function moduleSelected($moduleSlug)
    {
        // Boş değeri handle et
        if (empty($moduleSlug)) {
            $this->selectedModule = '';
            $this->selectedUrlType = '';
            $this->moduleContent = [];
            $this->moduleUrlTypes = [];
            unset($this->url_data['module'], $this->url_data['type']);
            return;
        }

        $this->selectedModule = $moduleSlug;
        $this->selectedUrlType = '';
        $this->moduleContent = [];

        // url_data'yı koru, sadece module ve type'ı güncelle
        if (!isset($this->url_data) || !is_array($this->url_data)) {
            $this->url_data = [];
        }
        $this->url_data['module'] = $moduleSlug;
        // Type'ı kaldır çünkü yeni modül seçildi
        unset($this->url_data['type']);

        // Bu modülün desteklediği URL tiplerini al
        $module = collect($this->availableModules)->firstWhere('slug', $moduleSlug);

        if ($module) {
            $this->moduleUrlTypes = $module['url_types'];
            $this->dispatch('module-url-types-loaded', [
                'module' => $moduleSlug,
                'types' => $this->moduleUrlTypes
            ]);
        }
    }

    /**
     * URL tipi seçildiğinde içerik yükle
     */
    public function urlTypeSelected($urlType)
    {
        // Boş değeri handle et
        if (empty($urlType)) {
            $this->selectedUrlType = '';
            $this->moduleContent = [];
            unset($this->url_data['type']);
            return;
        }

        $this->selectedUrlType = $urlType;

        // url_data'yı koru, sadece type'ı ekle
        if (!isset($this->url_data) || !is_array($this->url_data)) {
            $this->url_data = [];
        }
        $this->url_data['type'] = $urlType;

        // Bu tip için içerik seçimi gerekiyorsa yükle
        $typeConfig = collect($this->moduleUrlTypes)->firstWhere('type', $urlType);

        if ($typeConfig && ($typeConfig['needs_selection'] ?? false)) {
            $this->moduleContent = $this->urlBuilder->getModuleContent($this->selectedModule, $urlType);
            $this->dispatch('module-content-loaded', [
                'module' => $this->selectedModule,
                'type' => $urlType,
                'content' => $this->moduleContent
            ]);
        }
    }
    
    /**
     * İçerik seçildiğinde
     */
    public function contentSelected($contentId)
    {
        if (!$contentId) {
            $this->url_data = [];
            return;
        }

        // Module content listesinden seçilen içeriği bul
        $contents = $this->moduleContent;
        $selectedContent = collect($contents)->firstWhere('id', $contentId);

        if ($selectedContent) {
            $this->url_data = [
                'id' => $contentId,
                'slug' => $selectedContent['slug'] ?? null
            ];
        } else {
            $this->url_data['id'] = $contentId;
        }
    }
    
    /**
     * Manuel URL girişi (internal/external)
     */
    public function updateUrlData($url)
    {
        $this->url_data = ['url' => $url];
    }
    
    public function render()
    {
        return view('menumanagement::admin.livewire.menu-item-manage-component', [
            'headerMenu' => $this->headerMenu,
            'headerMenuItems' => $this->headerMenuItems,
            'availableLanguages' => $this->availableLanguages,
            'showDeleteModal' => $this->showDeleteModal,
            'deleteItemTitle' => $this->deleteItemTitle
        ]);
    }
}