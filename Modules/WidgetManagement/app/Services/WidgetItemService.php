<?php

namespace Modules\WidgetManagement\app\Services;

use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;
use Illuminate\Support\Str;

class WidgetItemService
{
    /**
     * Widget Service
     */
    protected $widgetService;
    
    /**
     * Constructor
     */
    public function __construct(WidgetService $widgetService)
    {
        $this->widgetService = $widgetService;
    }
    
    /**
     * Widget için öğeleri al
     */
    public function getItemsForWidget($tenantWidgetId)
    {
        return WidgetItem::where('tenant_widget_id', $tenantWidgetId)
            ->orderBy('order')
            ->get();
    }
    
    /**
     * Widget öğesi ekle
     */
    public function addItem($tenantWidgetId, $content)
    {
        $tenantWidget = TenantWidget::findOrFail($tenantWidgetId);
        
        // Otomatik unique_id ekle
        if (!isset($content['unique_id'])) {
            $content['unique_id'] = (string) Str::uuid();
        }
        
        // Title yok ise ekle
        if (!isset($content['title'])) {
            $content['title'] = 'Yeni İçerik ' . date('Y-m-d H:i:s');
        }
        
        // is_active yok ise ekle ve varsayılan aktif
        if (!isset($content['is_active'])) {
            $content['is_active'] = true;
        }
        
        $maxOrder = WidgetItem::where('tenant_widget_id', $tenantWidgetId)
            ->max('order') ?? 0;
            
        $item = WidgetItem::create([
            'tenant_widget_id' => $tenantWidgetId,
            'content' => $content,
            'order' => $maxOrder + 1
        ]);
        
        // Widget item oluşturma log'u
        if (function_exists('log_activity')) {
            log_activity($item, 'oluşturuldu');
        }
        
        // Widget önbelleğini temizle
        if (function_exists('tenant') && tenant()) {
            $this->widgetService->clearWidgetCache(tenant()->id, $tenantWidgetId);
        } else {
            $this->widgetService->clearWidgetCache(null, $tenantWidgetId);
        }
        
        return $item;
    }
    
    /**
     * Widget öğesi güncelle
     */
    public function updateItem($itemId, $content)
    {
        $item = WidgetItem::findOrFail($itemId);
        
        // Otomatik unique_id ekle
        if (!isset($content['unique_id'])) {
            $content['unique_id'] = (string) Str::uuid();
        }
        
        // Title yok ise mevcut title'ı devam ettir
        if (!isset($content['title']) && isset($item->content['title'])) {
            $content['title'] = $item->content['title'];
        }
        
        // is_active yok ise mevcut aktiflik durumunu devam ettir
        if (!isset($content['is_active']) && isset($item->content['is_active'])) {
            $content['is_active'] = $item->content['is_active'];
        }
        
        $item->update(['content' => $content]);
        
        // Widget item güncelleme log'u
        if (function_exists('log_activity')) {
            log_activity($item, 'güncellendi');
        }
        
        // Widget önbelleğini temizle
        if (function_exists('tenant') && tenant()) {
            $this->widgetService->clearWidgetCache(tenant()->id, $item->tenant_widget_id);
        } else {
            $this->widgetService->clearWidgetCache(null, $item->tenant_widget_id);
        }
        
        return $item;
    }
    
    /**
     * Widget öğesi sil
     */
    public function deleteItem($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $tenantWidgetId = $item->tenant_widget_id;
        
        // Widget item silme log'u
        if (function_exists('log_activity')) {
            log_activity($item, 'silindi');
        }
        
        $result = $item->delete();
        
        // Widget önbelleğini temizle
        if (function_exists('tenant') && tenant()) {
            $this->widgetService->clearWidgetCache(tenant()->id, $tenantWidgetId);
        } else {
            $this->widgetService->clearWidgetCache(null, $tenantWidgetId);
        }
        
        return $result;
    }
    
    /**
     * Widget öğelerini sırala
     */
    public function reorderItems($tenantWidgetId, $itemIds)
    {
        // İtemId'lerin array olduğundan emin olalım
        if (!is_array($itemIds)) {
            $itemIds = (array)$itemIds;
        }
        
        foreach ($itemIds as $index => $itemId) {
            // Cast to integer
            $itemId = (int)$itemId;
            $order = $index + 1;
            
            // Öğe sırasını güncelle
            WidgetItem::where('id', $itemId)
                ->where('tenant_widget_id', $tenantWidgetId)
                ->update(['order' => $order]);
        }
        
        // Widget item sıralama log'u (tek seferlik)
        if (function_exists('log_activity')) {
            // TenantWidget bulup log ekle
            $tenantWidget = TenantWidget::find($tenantWidgetId);
            if ($tenantWidget) {
                activity()
                    ->performedOn($tenantWidget)
                    ->withProperties(['item_count' => count($itemIds)])
                    ->log('widget itemları sıralandı');
            }
        }
        
        // Widget önbelleğini temizle
        if (function_exists('tenant') && tenant()) {
            $this->widgetService->clearWidgetCache(tenant()->id, $tenantWidgetId);
        } else {
            $this->widgetService->clearWidgetCache(null, $tenantWidgetId);
        }
        
        return true;
    }
}