<?php

namespace Modules\Studio\Listeners;

use Modules\Studio\Events\StudioContentSaved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class StudioContentSavedListener
{
    /**
     * Olayı işle
     *
     * @param StudioContentSaved $event
     * @return void
     */
    public function handle(StudioContentSaved $event): void
    {
        // İçerik kaydedildiğinde önbellekleri temizle
        $this->clearCaches($event->module, $event->id);
        
        // Log
        Log::info('Studio içeriği kaydedildi', [
            'module' => $event->module,
            'id' => $event->id,
            'data' => $event->data,
            'user' => auth()->user()->email ?? 'Bilinmeyen',
            'timestamp' => $event->timestamp
        ]);
    }
    
    /**
     * İlgili önbellekleri temizle
     *
     * @param string $module
     * @param int $id
     * @return void
     */
    protected function clearCaches(string $module, int $id): void
    {
        // Tenant ID
        $tenantId = 'central';
        
        // Tenant fonksiyonu varsa ve tenant nesnesi varsa tenant ID'yi al
        if (function_exists('tenant') && tenant()) {
            $tenantId = tenant()->getTenantKey();
        }
        
        $prefix = config('studio.cache.prefix', 'studio_');
        
        // Modüle özel önbellekleri temizle
        $cacheKey = "{$prefix}{$tenantId}_settings_{$module}_{$id}";
        Cache::forget($cacheKey);
        
        // Modül türüne göre ek önbellekleri temizle
        switch ($module) {
            case 'page':
                // Sayfa önbelleklerini temizle
                if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
                    \Spatie\ResponseCache\Facades\ResponseCache::forget("/pages/{$id}");
                }
                break;
        }
    }
}