<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\ThemeManagement\App\Models\Theme;

class ThemeService
{
    protected ?object $activeTheme = null;
    
    public function __construct()
    {
        // Basit constructor - dependency olmadan çalışsın
    }
    
    /**
     * Aktif temayı getirir
     */
    public function getActiveTheme(): ?object
    {
        if ($this->activeTheme === null) {
            $this->activeTheme = $this->loadActiveTheme();
        }
        
        return $this->activeTheme;
    }
    
    /**
     * Aktif temayı yükle - ThemeRepository ile uyumlu cache keys
     */
    protected function loadActiveTheme(): ?object
    {
        try {
            // Repository ile aynı cache key'leri kullan (duplication önleme)
            // Redis cache kullan (file cache yerine)
            $cache = Cache::store('redis');

            if (function_exists('tenant') && $t = tenant()) {
                // Tenant tema cache - Repository ile uyumlu
                $cacheKey = "theme:tenant_{$t->id}";

                $theme = $cache->remember($cacheKey, now()->addHours(1), function() use ($t) {
                    return Theme::on('mysql')->where('name', $t->theme)
                                  ->where('is_active', true)
                                  ->first();
                });

                if ($theme) {
                    return $theme;
                }
            }

            // Default tema cache - Repository ile uyumlu
            $cacheKey = 'theme:default';

            return $cache->remember($cacheKey, now()->addHours(24), function() {
                // Default tema ara
                $theme = Theme::on('mysql')
                    ->where('is_default', true)
                    ->where('is_active', true)
                    ->first();
                
                if (!$theme) {
                    // Herhangi bir aktif tema ara
                    $theme = Theme::on('mysql')
                        ->where('is_active', true)
                        ->first();
                }
                
                if (!$theme) {
                    // Emergency fallback tema oluştur
                    return (object) [
                        'id' => 0,
                        'name' => 'emergency',
                        'display_name' => 'Emergency Theme',
                        'is_active' => true,
                        'is_default' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                
                return $theme;
            });
            
        } catch (\Exception $e) {
            Log::error('ThemeService error: ' . $e->getMessage());
            
            // Emergency fallback
            return (object) [
                'id' => 0,
                'name' => 'emergency',
                'display_name' => 'Emergency Theme',
                'is_active' => true,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
    }
    
    /**
     * Tema view path'lerini ayarlar
     */
    public function setupThemeViews(): void
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            return;
        }
        
        // Tema view path'ini ayarla
        $themePath = resource_path("views/themes/{$theme->name}");
        
        if (is_dir($themePath)) {
            View::addLocation($themePath);
        }
    }
    
    /**
     * Tema asset URL'ini getirir
     */
    public function getThemeAssetUrl(string $asset): string
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            return asset($asset);
        }
        
        return asset("themes/{$theme->name}/{$asset}");
    }
    
    /**
     * Tema bilgilerini getirir
     */
    public function getThemeInfo(): array
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            return [
                'name' => 'emergency',
                'display_name' => 'Emergency Theme',
                'is_active' => true
            ];
        }
        
        return [
            'name' => $theme->name,
            'display_name' => $theme->display_name ?? $theme->name,
            'is_active' => $theme->is_active ?? true,
            'is_default' => $theme->is_default ?? false
        ];
    }
    
    /**
     * Tema cache'ini temizler - Repository ile uyumlu
     */
    public function clearThemeCache(?string $tenantId = null): void
    {
        $this->activeTheme = null; // Instance cache'i temizle

        if ($tenantId) {
            // Belirli tenant'ın tema cache'ini temizle
            Cache::forget("theme:tenant_{$tenantId}");
        } else {
            // Default tema cache'ini temizle
            Cache::forget('theme:default');

            // Tüm tenant tema cache'lerini de temizle
            try {
                $redis = Cache::getRedis();
                $pattern = config('database.redis.options.prefix') . ':theme:tenant_*';
                $keys = $redis->keys($pattern);

                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } catch (\Exception $e) {
                // Redis hatası - sessizce devam et
                \Log::debug('Theme cache clear failed: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Tema view path'ini getirir (modül desteği ile)
     */
    public function getThemeViewPath(string $view, string $module = null): string
    {
        $theme = $this->getActiveTheme();
        
        if (!$theme) {
            // Theme yoksa modül view'ını kullan
            return $module ? "{$module}::front.{$view}" : $view;
        }
        
        $themeName = $theme->name;
        
        if ($module) {
            // Önce modül içindeki tema view'ını kontrol et
            // Modules/{Module}/resources/views/themes/{theme}/{view}.blade.php
            $moduleThemeViewPath = "{$module}::themes.{$themeName}.{$view}";
            
            if (view()->exists($moduleThemeViewPath)) {
                return $moduleThemeViewPath;
            }
            
            // Tema yoksa modül default view'ını kullan
            // Modules/{Module}/resources/views/front/{view}.blade.php
            return "{$module}::front.{$view}";
        }
        
        // Genel tema view'ı (layout için)
        $themeViewPath = "themes.{$themeName}.{$view}";
        
        if (view()->exists($themeViewPath)) {
            return $themeViewPath;
        }
        
        // Fallback
        return $view;
    }
    
    /**
     * Tema değiştirildiğinde çağrılır
     */
    public function refreshTheme(): void
    {
        $this->activeTheme = null;
        $this->setupThemeViews();
    }
}