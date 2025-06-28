<?php
// app/Services/ThemeService.php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\ThemeManagement\App\Models\Theme;

class ThemeService
{
    protected $activeTheme;
    protected static $staticThemeCache = null;

    public function __construct()
    {
        $this->setActiveTheme();
    }

    protected function setActiveTheme()
    {
        try {
            // Cache key oluştur
            $cacheKey = 'theme_service_active_theme';
            
            // Tenant varsa cache key'e tenant id ekle
            if (function_exists('tenant') && $t = tenant()) {
                $cacheKey .= '_tenant_' . $t->id;
                
                // Tenant-specific theme cache'den kontrol et
                $this->activeTheme = Cache::remember($cacheKey, now()->addHours(24), function() use ($t) {
                    return Theme::on('mysql')->where('name', $t->theme)
                                  ->where('is_active', true)
                                  ->first();
                });
                
                if ($this->activeTheme) {
                    return;
                }
            }
            
            // STATIC CACHE + REDIS CACHE - İKİLİ KORUMA
            if (self::$staticThemeCache !== null) {
                $this->activeTheme = self::$staticThemeCache;
                return;
            }
            
            $globalCacheKey = 'theme_service_default_theme_v2';
            $this->activeTheme = Cache::remember($globalCacheKey, now()->addDays(7), function() {
                \Log::info('🎨 THEME CACHE MISS - Database sorgusu yapılıyor');
                return Theme::on('mysql')->where('is_default', true)
                    ->where('is_active', true)
                    ->first();
            });
            
            // Static cache'e de kaydet
            self::$staticThemeCache = $this->activeTheme;
            
            // Cache'de yoksa fallback kullan
            if (!$this->activeTheme) {
                $this->activeTheme = new Theme([
                    'name' => 'blank',
                    'folder_name' => 'blank'
                ]);
            }
            
        } catch (\Exception $e) {
            // Hata durumunda fallback theme kullan
            Log::info('ThemeService: Using fallback theme due to error: ' . $e->getMessage());
            $this->activeTheme = new Theme([
                'name' => 'blank',
                'folder_name' => 'blank'
            ]);
        }
    }

    public function getActiveTheme()
    {
        return $this->activeTheme;
    }

    public function getThemeViewPath($view, $module = null)
    {
        $themeName = $this->activeTheme->folder_name;
        
        // 1. Modül içerisindeki tema view'ı - YENİ YAPI
        if ($module) {
            $moduleThemeView = "{$module}::front.themes.{$themeName}.{$view}";
            if (View::exists($moduleThemeView)) {
    
                return $moduleThemeView;
            }
        }
        
        // 2. Ana tema içerisindeki view - YENİ YAPI
        $mainThemeView = "themes.{$themeName}.{$view}";
        if (View::exists($mainThemeView)) {
            Log::debug("Using main theme view: {$mainThemeView}");
            return $mainThemeView;
        }
        
        // 3. Modül içindeki fallback view - YENİ YAPI
        if ($module) {
            $defaultModuleView = "{$module}::front.{$view}";
            if (View::exists($defaultModuleView)) {
                Log::debug("Using default module view: {$defaultModuleView}");
                return $defaultModuleView;
            }
        }
        
        // Tüm alternatifleri kontrol et ve logla
        Log::error("View not found for any path. Module: {$module}, View: {$view}, Theme: {$themeName}");
        throw new \Exception("View [{$view}] not found in any location.");
    }

    /**
     * Theme cache'ini temizle
     */
    public function clearThemeCache()
    {
        Cache::forget('theme_service_default_theme');
        
        // Tenant cache'lerini de temizle
        if (function_exists('tenant') && $t = tenant()) {
            Cache::forget('theme_service_active_theme_tenant_' . $t->id);
        }
    }

    /**
     * Tüm theme cache'lerini temizle
     */
    public static function clearAllThemeCache()
    {
        Cache::forget('theme_service_default_theme');
        
        // Tenant cache pattern'ini temizle (Redis için)
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $keys = Cache::getRedis()->keys('theme_service_active_theme_tenant_*');
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        }
    }
}