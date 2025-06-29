<?php

namespace App\Repositories;

use App\Contracts\ThemeRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\ThemeManagement\App\Models\Theme;

class ThemeRepository implements ThemeRepositoryInterface
{
    /**
     * Cache TTL dakika cinsinden
     */
    protected const CACHE_TTL = 60; // 1 saat
    
    /**
     * Aktif temayı getirir
     */
    public function getActiveTheme(): ?object
    {
        $tenant = tenant();
        
        if ($tenant) {
            return $this->getThemeForTenant($tenant->id);
        }
        
        return $this->getDefaultTheme();
    }
    
    /**
     * Tenant için temayı getirir
     */
    public function getThemeForTenant(string $tenantId): ?object
    {
        $cacheKey = "theme:tenant_{$tenantId}";
        $cacheTags = ["tenant_{$tenantId}:theme"];
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addMinutes(self::CACHE_TTL), function () use ($tenantId) {
            $tenant = tenant();
            if (!$tenant) {
                return null;
            }
            
            $theme = Theme::on('mysql')
                ->where('name', $tenant->theme)
                ->where('is_active', true)
                ->first();
            
            if (!$theme) {
                // Fallback: default tema
                $theme = $this->getDefaultTheme();
            }
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Tenant tema yüklendi", [
                    'tenant_id' => $tenantId,
                    'theme_name' => $theme ? $theme->name : 'none',
                    'cache_key' => $cacheKey
                ]);
            }
            
            return $theme;
        });
    }
    
    /**
     * Default temayı getirir
     */
    public function getDefaultTheme(): ?object
    {
        $cacheKey = 'theme:default';
        $cacheTags = ['central:theme'];
        
        return Cache::tags($cacheTags)->remember($cacheKey, now()->addHours(24), function () {
            $theme = Theme::on('mysql')
                ->where('is_default', true)
                ->where('is_active', true)
                ->first();
            
            if (!$theme) {
                // Son çare: herhangi bir aktif tema
                $theme = Theme::on('mysql')
                    ->where('is_active', true)
                    ->first();
                
                if (!$theme) {
                    // Hiç tema yoksa emergency fallback tema oluştur
                    $theme = $this->createEmergencyTheme();
                }
            }
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Default tema yüklendi", [
                    'theme_name' => $theme ? $theme->name : 'none',
                    'cache_key' => $cacheKey
                ]);
            }
            
            return $theme;
        });
    }
    
    /**
     * Tema cache'ini temizler
     */
    public function clearThemeCache(?string $tenantId = null): void
    {
        if ($tenantId) {
            // Belirli tenant'ın tema cache'ini temizle
            Cache::tags(["tenant_{$tenantId}:theme"])->flush();
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Tenant tema cache'i temizlendi", ['tenant_id' => $tenantId]);
            }
        } else {
            // Tüm tema cache'lerini temizle
            Cache::tags(['central:theme'])->flush();
            
            // Tüm tenant tema cache'lerini de temizle
            $redis = Cache::getRedis();
            $pattern = '*:theme:*';
            $keys = $redis->keys($pattern);
            
            if (!empty($keys)) {
                $redis->del($keys);
            }
            
            if (app()->environment(['local', 'staging'])) {
                Log::debug("Tüm tema cache'leri temizlendi");
            }
        }
    }
    
    /**
     * Tema varsa true döner
     */
    public function themeExists(string $themeName): bool
    {
        return Theme::on('mysql')
            ->where('name', $themeName)
            ->where('is_active', true)
            ->exists();
    }
    
    /**
     * Emergency fallback tema oluştur
     */
    protected function createEmergencyTheme(): object
    {
        if (app()->environment(['local', 'staging'])) {
            Log::warning("Emergency tema oluşturuluyor - hiç aktif tema bulunamadı");
        }
        
        // Runtime'da basit tema objesi oluştur (DB'ye kaydetme)
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