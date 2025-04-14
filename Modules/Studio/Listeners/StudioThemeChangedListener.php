<?php

namespace Modules\Studio\Listeners;

use Modules\Studio\Events\StudioThemeChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class StudioThemeChangedListener
{
    /**
     * Olayı işle
     *
     * @param StudioThemeChanged $event
     * @return void
     */
    public function handle(StudioThemeChanged $event): void
    {
        // Tema değiştiğinde önbellekleri temizle
        $this->clearCaches($event->module, $event->id, $event->theme);
        
        // Log
        Log::info('Studio teması değiştirildi', [
            'module' => $event->module,
            'id' => $event->id,
            'theme' => $event->theme,
            'user' => auth()->user()->email ?? 'Bilinmeyen',
            'timestamp' => $event->timestamp
        ]);
    }
    
    /**
     * İlgili önbellekleri temizle
     *
     * @param string $module
     * @param int $id
     * @param string $theme
     * @return void
     */
    protected function clearCaches(string $module, int $id, string $theme): void
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
        
        // Tema önbelleğini temizle
        $themeCacheKey = "{$prefix}{$tenantId}_theme_{$theme}";
        Cache::forget($themeCacheKey);
        
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