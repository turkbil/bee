<?php

namespace Modules\WidgetManagement\app\Services;

use Modules\WidgetManagement\app\Models\TenantWidget;
use Modules\WidgetManagement\app\Models\WidgetItem;

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
        
        $maxOrder = WidgetItem::where('tenant_widget_id', $tenantWidgetId)
            ->max('order') ?? 0;
            
        $item = WidgetItem::create([
            'tenant_widget_id' => $tenantWidgetId,
            'content' => $content,
            'order' => $maxOrder + 1
        ]);
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache(tenant()->id, $tenantWidgetId);
        
        return $item;
    }
    
    /**
     * Widget öğesi güncelle
     */
    public function updateItem($itemId, $content)
    {
        $item = WidgetItem::findOrFail($itemId);
        $item->update(['content' => $content]);
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache(tenant()->id, $item->tenant_widget_id);
        
        return $item;
    }
    
    /**
     * Widget öğesi sil
     */
    public function deleteItem($itemId)
    {
        $item = WidgetItem::findOrFail($itemId);
        $tenantWidgetId = $item->tenant_widget_id;
        
        $result = $item->delete();
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache(tenant()->id, $tenantWidgetId);
        
        return $result;
    }
    
    /**
     * Widget öğelerini sırala
     */
    public function reorderItems($tenantWidgetId, $itemIds)
    {
        foreach ($itemIds as $index => $itemId) {
            WidgetItem::where('id', $itemId)
                ->where('tenant_widget_id', $tenantWidgetId)
                ->update(['order' => $index + 1]);
        }
        
        // Widget önbelleğini temizle
        $this->widgetService->clearWidgetCache(tenant()->id, $tenantWidgetId);
        
        return true;
    }
}