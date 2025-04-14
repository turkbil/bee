<?php

namespace Modules\Studio\Listeners;

use Modules\Studio\Events\StudioWidgetUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class StudioWidgetUpdatedListener
{
    /**
     * Olayı işle
     *
     * @param StudioWidgetUpdated $event
     * @return void
     */
    public function handle(StudioWidgetUpdated $event): void
    {
        // Widget önbelleğini temizle
        $this->clearWidgetCache($event->widgetId);
        
        // Log
        Log::info('Studio widget güncellendi', [
            'widget_id' => $event->widgetId,
            'changes' => $event->changes,
            'user' => auth()->user()->email ?? 'Bilinmeyen',
            'timestamp' => $event->timestamp
        ]);
    }
    
    /**
     * Widget önbelleğini temizle
     *
     * @param int $widgetId
     * @return void
     */
    protected function clearWidgetCache(int $widgetId): void
    {
        // Tenant ID
        $tenantId = function_exists('tenant') ? tenant()->getTenantKey() : 'central';
        $prefix = config('studio.cache.prefix', 'studio_');
        
        // Widget önbelleğini temizle
        $widgetCacheKey = "{$prefix}{$tenantId}_widget_{$widgetId}";
        Cache::forget($widgetCacheKey);
        
        // Tüm widget önbelleğini temizle
        $allWidgetsCacheKey = "{$prefix}{$tenantId}_all_widgets";
        Cache::forget($allWidgetsCacheKey);
        
        // Caches widgets as blocks önbelleğini temizle
        $widgetsAsBlocksCacheKey = "{$prefix}{$tenantId}_widgets_as_blocks";
        Cache::forget($widgetsAsBlocksCacheKey);
    }
}