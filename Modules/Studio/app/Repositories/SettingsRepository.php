<?php

namespace Modules\Studio\App\Repositories;

use Modules\Studio\App\Models\StudioSettings;
use Illuminate\Support\Facades\Cache;

class SettingsRepository
{
    /**
     * Ayarları al
     *
     * @param string $module
     * @param int $moduleId
     * @return array
     */
    public function getSettings(string $module, int $moduleId): array
    {
        $cacheKey = 'studio_settings_' . $module . '_' . $moduleId;
        $cacheTtl = config('studio.cache.ttl', 3600);
        
        return Cache::remember($cacheKey, $cacheTtl, function() use ($module, $moduleId) {
            $settings = StudioSettings::where('module', $module)
                ->where('module_id', $moduleId)
                ->first();
                
            return $settings ? $settings->settings : [];
        });
    }
    
    /**
     * Ayar değerini al
     *
     * @param string $module
     * @param int $moduleId
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $module, int $moduleId, string $key, $default = null)
    {
        $settings = $this->getSettings($module, $moduleId);
        return $settings[$key] ?? $default;
    }
    
    /**
     * Ayar değerini kaydet
     *
     * @param string $module
     * @param int $moduleId
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function saveSetting(string $module, int $moduleId, string $key, $value): bool
    {
        try {
            $settings = StudioSettings::findOrCreateFor($module, $moduleId);
            $settings->setSetting($key, $value);
            $result = $settings->save();
            
            // Önbelleği temizle
            $cacheKey = 'studio_settings_' . $module . '_' . $moduleId;
            Cache::forget($cacheKey);
            
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ayar kaydedilirken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tema ayarlarını kaydet
     *
     * @param string $module
     * @param int $moduleId
     * @param string $theme
     * @param string|null $headerTemplate
     * @param string|null $footerTemplate
     * @return bool
     */
    public function saveThemeSettings(
        string $module, 
        int $moduleId, 
        string $theme,
        ?string $headerTemplate = null,
        ?string $footerTemplate = null
    ): bool
    {
        try {
            $settings = StudioSettings::findOrCreateFor($module, $moduleId);
            $settings->theme = $theme;
            
            if ($headerTemplate !== null) {
                $settings->header_template = $headerTemplate;
            }
            
            if ($footerTemplate !== null) {
                $settings->footer_template = $footerTemplate;
            }
            
            $result = $settings->save();
            
            // Önbelleği temizle
            $cacheKey = 'studio_settings_' . $module . '_' . $moduleId;
            Cache::forget($cacheKey);
            
            return $result;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Tema ayarları kaydedilirken hata: ' . $e->getMessage());
            return false;
        }
    }
}