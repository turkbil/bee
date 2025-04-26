<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class BlockService
{
    /**
     * Tüm blokları al
     *
     * @return array
     */
    public function getAllBlocks(): array
    {
        // Önbellekle blokları saklayarak arka arkaya aynı istekleri önleyelim
        static $cachedBlocks = null;
        
        // Eğer bloklar daha önce yüklendiyse tekrar yüklemeye gerek yok
        if ($cachedBlocks !== null) {
            return $cachedBlocks;
        }
        
        $blocks = [];
        
        // 1. Aktif tenant widget'larını yükle (en öncelikli)
        $tenantWidgetBlocks = $this->loadTenantWidgets();
        
        // 2. WidgetManagement'dan widget'ları kategorilerine göre yükle
        $widgetBlocks = $this->loadWidgetsFromWidgetManagementHierarchical();
        
        // Tüm blokları birleştir
        $blocks = array_merge($tenantWidgetBlocks, $widgetBlocks);
        
        // Log dosyasına detayları yaz
        Log::info('Yüklenen bloklar:', ['count' => count($blocks)]);
        
        // Blokları önbelleğe al ve döndür
        $cachedBlocks = $blocks;
        return $blocks;
    }
    
    /**
     * WidgetManagement'dan widgetları hiyerarşik kategorilere göre yükle
     *
     * @return array
     */
    protected function loadWidgetsFromWidgetManagementHierarchical(): array
    {
        $blocks = [];
        
        try {
            // WidgetManagement modülünün yüklü olup olmadığını kontrol et
            if (!class_exists('Modules\WidgetManagement\App\Models\Widget') ||
                !class_exists('Modules\WidgetManagement\App\Models\WidgetCategory')) {
                Log::info('WidgetManagement modülü bulunamadı veya yüklenmedi.');
                return [];
            }
            
            // Önce ana kategorileri al (parent_id=null)
            $rootCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->get();
                
            Log::info('Ana widget kategorileri yüklendi:', ['count' => $rootCategories->count()]);
            
            // Her ana kategori için blokları al
            foreach ($rootCategories as $rootCategory) {
                // Ana kategori widgetlarını ekle (önce module tipindekiler)
                $this->addCategoryWidgetsToBlocks($rootCategory, $blocks, 'module');
                $this->addCategoryWidgetsToBlocks($rootCategory, $blocks, 'file');
                $this->addCategoryWidgetsToBlocks($rootCategory, $blocks, null); // Diğer tipler
                
                // Alt kategorileri al
                $childCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                    ->where('parent_id', $rootCategory->widget_category_id)
                    ->orderBy('order')
                    ->get();
                
                Log::info("Kategori {$rootCategory->title} alt kategorileri:", ['count' => $childCategories->count()]);
                
                // Her alt kategori için blokları al
                foreach ($childCategories as $childCategory) {
                    // Alt kategori widgetlarını ekle (önce module tipindekiler)
                    $this->addCategoryWidgetsToBlocks($childCategory, $blocks, 'module');
                    $this->addCategoryWidgetsToBlocks($childCategory, $blocks, 'file');
                    $this->addCategoryWidgetsToBlocks($childCategory, $blocks, null); // Diğer tipler
                    
                    // Üçüncü seviye kategoriler (iç içe kategoriler) için kontrol et
                    $grandchildCategories = \Modules\WidgetManagement\App\Models\WidgetCategory::where('is_active', true)
                        ->where('parent_id', $childCategory->widget_category_id)
                        ->orderBy('order')
                        ->get();
                    
                    foreach ($grandchildCategories as $grandchildCategory) {
                        // Üçüncü seviye kategorilerin widgetlarını ekle (önce module tipindekiler)
                        $this->addCategoryWidgetsToBlocks($grandchildCategory, $blocks, 'module');
                        $this->addCategoryWidgetsToBlocks($grandchildCategory, $blocks, 'file');
                        $this->addCategoryWidgetsToBlocks($grandchildCategory, $blocks, null); // Diğer tipler
                    }
                }
            }
            
            Log::info('WidgetManagement widget blokları hiyerarşik olarak yüklendi:', ['count' => count($blocks)]);
        } catch (\Exception $e) {
            Log::error('Widget blokları hiyerarşik olarak yüklenirken hata oluştu: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
        
        return $blocks;
    }
    
    /**
     * Belirli bir kategorinin widgetlarını bloklar dizisine ekle
     * 
     * @param \Modules\WidgetManagement\App\Models\WidgetCategory $category
     * @param array &$blocks
     * @param string $typeFilter Widget tipi filtresi (module, file, vs.)
     * @return void
     */
    protected function addCategoryWidgetsToBlocks($category, &$blocks, $typeFilter = null)
    {
        try {
            // Kategoriye ait widget'ları getir
            $widgetsQuery = \Modules\WidgetManagement\App\Models\Widget::where('widget_category_id', $category->widget_category_id)
                ->where('is_active', true);
                
            // Eğer tip filtresi belirtilmişse ekle
            if ($typeFilter) {
                $widgetsQuery->where('type', $typeFilter);
            } elseif ($typeFilter === null) {
                // null ile çağrıldıysa, module ve file dışındakileri getir
                $widgetsQuery->whereNotIn('type', ['module', 'file']);
            }
            
            $widgets = $widgetsQuery->orderBy('name')->get();
            
            $logType = $typeFilter ?: 'diğer';
            Log::info("Kategori {$category->title} widget'ları {$logType} tipine göre:", ['count' => $widgets->count()]);
            
            // Widget'ları block formatına çevir
            foreach ($widgets as $widget) {
                $blockId = 'widget-' . $widget->id;
                $categorySlug = $category->slug;
                $parentCategory = null;
                
                // Eğer alt kategori ise, parent bilgisini de ekle
                if ($category->parent_id) {
                    $parentCategory = \Modules\WidgetManagement\App\Models\WidgetCategory::find($category->parent_id);
                    if ($parentCategory) {
                        $categorySlug = $parentCategory->slug . '-' . $categorySlug;
                        
                        // Eğer üst kategorinin de parenti varsa (3. seviye kategori)
                        if ($parentCategory->parent_id) {
                            $grandParentCategory = \Modules\WidgetManagement\App\Models\WidgetCategory::find($parentCategory->parent_id);
                            if ($grandParentCategory) {
                                $categorySlug = $grandParentCategory->slug . '-' . $categorySlug;
                            }
                        }
                    }
                }
                
                $blocks[] = [
                    'id' => $blockId,
                    'label' => $widget->name,
                    'category' => $categorySlug,
                    'icon' => $category->icon ?? 'fa fa-puzzle-piece',
                    'content' => $widget->content_html ?? '<div class="widget-placeholder">' . $widget->name . '</div>',
                    'widget_id' => $widget->id,
                    'description' => $widget->description,
                    'thumbnail' => $widget->thumbnail,
                    'type' => $widget->type ?? 'widget',
                    'has_items' => $widget->has_items,
                    'category_name' => $category->title,
                    'parent_category_name' => $parentCategory ? $parentCategory->title : null,
                    'is_widget' => true
                ];
            }
        } catch (\Exception $e) {
            Log::error("Kategori widgetları eklenirken hata: " . $e->getMessage(), [
                'category' => $category->title,
                'type_filter' => $typeFilter
            ]);
        }
    }
    
    /**
     * Tenant widget'larını tipine göre sıralayarak yükle (aktif kullanılan widgetlar)
     *
     * @return array
     */
    protected function loadTenantWidgets(): array
    {
        $blocks = [];
        
        try {
            // WidgetManagement modülünün yüklü olup olmadığını kontrol et
            if (!class_exists('Modules\WidgetManagement\App\Models\TenantWidget')) {
                return [];
            }
            
            // Özel category_id oluştur
            $activeCategoryId = 'active-widgets';
            
            // Önce dynamic tipindeki widget'ları al
            $dynamicWidgets = $this->loadTenantWidgetsByType('dynamic');
            $blocks = array_merge($blocks, $dynamicWidgets);
            
            // Sonra static tipindeki widget'ları al
            $staticWidgets = $this->loadTenantWidgetsByType('static');
            $blocks = array_merge($blocks, $staticWidgets);
            
            Log::info('Tenant widget blokları tipine göre yüklendi:', ['count' => count($blocks)]);
        } catch (\Exception $e) {
            Log::error('Tenant widget blokları yüklenirken hata oluştu: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
        
        return $blocks;
    }
    
    /**
     * Belirli tipteki tenant widget'larını yükle
     * 
     * @param string $type Widget tipi (dynamic, static)
     * @return array
     */
    protected function loadTenantWidgetsByType($type = 'dynamic'): array
    {
        $blocks = [];
        $activeCategoryId = 'active-widgets';
        
        try {
            // Aktif tenant widget'larını getir
            $tenantWidgets = \Modules\WidgetManagement\App\Models\TenantWidget::where('is_active', true)
                ->orderBy('order')
                ->get();
                
            // Widget'ların orijinal tiplerini almak için widget ID'lerini topla
            $widgetIds = $tenantWidgets->pluck('widget_id')->filter()->toArray();
            $widgetsMap = [];
            
            if (!empty($widgetIds)) {
                $widgets = \Modules\WidgetManagement\App\Models\Widget::whereIn('id', $widgetIds)->get();
                foreach ($widgets as $widget) {
                    $widgetsMap[$widget->id] = $widget;
                }
            }
            
            // Filtreleme ve blok oluşturma
            foreach ($tenantWidgets as $tenantWidget) {
                $blockId = 'tenant-widget-' . $tenantWidget->id;
                $isTargetType = false;
                
                // Widget bilgilerini getir
                $widget = isset($widgetsMap[$tenantWidget->widget_id]) ? $widgetsMap[$tenantWidget->widget_id] : null;
                $widgetName = 'Widget #' . $tenantWidget->id;
                $content = '<div class="widget-placeholder">Aktif Widget #' . $tenantWidget->id . '</div>';
                $widgetType = 'static'; // varsayılan tip
                
                if ($widget) {
                    $widgetName = $widget->name;
                    $content = $widget->content_html ?? $content;
                    $widgetType = $widget->has_items ? 'dynamic' : 'static';
                }
                
                // Özel widget kontrolü
                if ($tenantWidget->is_custom) {
                    $content = $tenantWidget->custom_html ?? $content;
                }
                
                // Widget başlığını ayarlardan alabilir miyiz?
                if (isset($tenantWidget->settings['title'])) {
                    $widgetName = $tenantWidget->settings['title'];
                }
                
                // Belirtilen tipe uyan widget'ları filtrele
                if ($widgetType === $type) {
                    // Bloğu ekle
                    $blocks[] = [
                        'id' => $blockId,
                        'label' => $widgetName . ' (' . ($type === 'dynamic' ? 'Dinamik' : 'Statik') . ')',
                        'category' => $activeCategoryId,
                        'icon' => 'fa fa-star',
                        'content' => $content,
                        'tenant_widget_id' => $tenantWidget->id,
                        'widget_id' => $tenantWidget->widget_id,
                        'type' => $widgetType,
                        'is_tenant_widget' => true,
                        'is_active' => true
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("Tenant widget blokları '{$type}' tipine göre yüklenirken hata oluştu: " . $e->getMessage());
        }
        
        return $blocks;
    }
    
    /**
     * Kategoriye göre blokları al
     *
     * @param string $category
     * @return array
     */
    public function getBlocksByCategory(string $category): array
    {
        $blocks = $this->getAllBlocks();
        
        return array_filter($blocks, function ($block) use ($category) {
            return $block['category'] === $category;
        });
    }
    
    /**
     * Bloğu HTML olarak render et
     *
     * @param string $blockId
     * @return string|null
     */
    public function renderBlock(string $blockId): ?string
    {
        $blocks = $this->getAllBlocks();
        
        foreach ($blocks as $block) {
            if ($block['id'] === $blockId) {
                // Eğer widget ise
                if (isset($block['is_widget']) && $block['is_widget'] && 
                    isset($block['widget_id']) && class_exists('Modules\WidgetManagement\App\Models\Widget')) {
                    
                    $widget = \Modules\WidgetManagement\App\Models\Widget::find($block['widget_id']);
                    if ($widget) {
                        return $widget->content_html ?? '';
                    }
                }
                
                // Eğer tenant widget ise
                if (isset($block['is_tenant_widget']) && $block['is_tenant_widget'] && 
                    isset($block['tenant_widget_id']) && class_exists('Modules\WidgetManagement\App\Models\TenantWidget')) {
                    
                    $tenantWidget = \Modules\WidgetManagement\App\Models\TenantWidget::find($block['tenant_widget_id']);
                    if ($tenantWidget) {
                        if ($tenantWidget->is_custom) {
                            return $tenantWidget->custom_html ?? '';
                        } elseif ($tenantWidget->widget_id) {
                            $widget = \Modules\WidgetManagement\App\Models\Widget::find($tenantWidget->widget_id);
                            if ($widget) {
                                return $widget->content_html ?? '';
                            }
                        }
                    }
                }
                
                // Normal blok içeriği
                return $block['content'] ?? '';
            }
        }
        
        return null;
    }
}