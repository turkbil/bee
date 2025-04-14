<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\Studio\App\Models\StudioSetting;

class StudioManagerService
{
    protected $assetService;
    protected $themeService;
    protected $widgetService;
    protected $parserService;
    
    /**
     * Servisi başlat
     */
    public function __construct()
    {
        // Servis örneklerini geciktirilmiş olarak yükle
        $this->assetService = app('studio.assets');
        $this->themeService = app('studio.theme');
        $this->widgetService = app('studio.widget');
        $this->parserService = app('studio.parser');
    }
    
    /**
     * Editor için gerekli tüm verileri hazırla
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @return array
     */
    public function prepareEditorData(string $module, int $moduleId): array
    {
        try {
            // Tema, widget ve ayarlar verilerini hazırla
            $themes = $this->themeService->getAllThemes();
            $widgets = $this->widgetService->getAllWidgets();
            $settings = $this->getModuleSettings($module, $moduleId);
            
            // Aktif tema ve temaya ait şablonlar
            $activeTheme = $settings['theme'] ?? config('studio.themes.default');
            $templates = $this->themeService->getTemplatesForTheme($activeTheme);
            
            return [
                'themes' => $themes,
                'widgets' => $widgets,
                'settings' => $settings,
                'templates' => $templates,
                'editor_config' => $this->getEditorConfig(),
            ];
        } catch (\Exception $e) {
            Log::error('Studio verilerini hazırlarken hata: ' . $e->getMessage(), [
                'module' => $module,
                'moduleId' => $moduleId,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Hata durumunda boş veri döndür
            return [
                'themes' => [],
                'widgets' => [],
                'settings' => [],
                'templates' => [],
                'editor_config' => $this->getEditorConfig(),
            ];
        }
    }
    
    /**
     * Modül için Studio ayarlarını getir
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @return array
     */
    public function getModuleSettings(string $module, int $moduleId): array
    {
        // Önbellek anahtarını oluştur
        $cacheEnabled = config('studio.cache.enable', true);
        $cacheKey = $this->getCacheKey("settings_{$module}_{$moduleId}");
        
        if ($cacheEnabled) {
            return Cache::remember($cacheKey, now()->addMinutes(config('studio.cache.ttl')), function () use ($module, $moduleId) {
                return $this->loadModuleSettings($module, $moduleId);
            });
        }
        
        return $this->loadModuleSettings($module, $moduleId);
    }
    
    /**
     * Modül ayarlarını veritabanından yükle
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @return array
     */
    protected function loadModuleSettings(string $module, int $moduleId): array
    {
        try {
            $settings = StudioSetting::where('module', $module)
                ->where('module_id', $moduleId)
                ->first();
            
            if ($settings) {
                return [
                    'theme' => $settings->theme,
                    'header_template' => $settings->header_template,
                    'footer_template' => $settings->footer_template,
                    'settings' => $settings->settings ?? [],
                ];
            }
            
            // Eğer ayar yoksa varsayılan ayarları döndür
            return [
                'theme' => config('studio.themes.default'),
                'header_template' => null,
                'footer_template' => null,
                'settings' => [],
            ];
        } catch (\Exception $e) {
            Log::error('Modül ayarları yüklenirken hata: ' . $e->getMessage());
            
            // Hata durumunda varsayılan ayarları döndür
            return [
                'theme' => config('studio.themes.default'),
                'header_template' => null,
                'footer_template' => null,
                'settings' => [],
            ];
        }
    }
    
    /**
     * Modül ayarlarını kaydet
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @param array $data Kaydedilecek veri
     * @return bool
     */
    public function saveModuleSettings(string $module, int $moduleId, array $data): bool
    {
        try {
            $settings = StudioSetting::updateOrCreate(
                ['module' => $module, 'module_id' => $moduleId],
                [
                    'theme' => $data['theme'] ?? config('studio.themes.default'),
                    'header_template' => $data['header_template'] ?? null,
                    'footer_template' => $data['footer_template'] ?? null,
                    'settings' => $data['settings'] ?? [],
                ]
            );
            
            // Önbelleği temizle
            $this->clearModuleCache($module, $moduleId);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Modül ayarları kaydedilirken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Editor ayarlarını döndür
     *
     * @return array
     */
    public function getEditorConfig(): array
    {
        return config('studio.editor');
    }
    
    /**
     * Önbelleği temizle
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @return bool
     */
    public function clearModuleCache(string $module, int $moduleId): bool
    {
        try {
            $cacheKey = $this->getCacheKey("settings_{$module}_{$moduleId}");
            Cache::forget($cacheKey);
            
            // Widget ve tema önbelleklerini de temizle
            $this->widgetService->clearCache();
            $this->themeService->clearCache();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Önbellek temizlenirken hata: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Önbellek anahtarı oluştur
     *
     * @param string $key Anahtar
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        $prefix = config('studio.cache.prefix', 'studio_');
        $tenantId = 'central'; // Varsayılan değer
        
        try {
            // Fonksiyon varsa ve tenant varsa
            if (function_exists('tenant') && tenant()) {
                $tenantId = tenant()->getTenantKey();
            }
        } catch (\Throwable $e) {
            // Hata durumunda varsayılan tenant kullan
            Log::warning('Tenant ID alınırken hata: ' . $e->getMessage());
        }
        
        return "{$prefix}{$tenantId}_{$key}";
    }
}