<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class StudioThemeService
{
    /**
     * Tüm temaları getir
     *
     * @return array
     */
    public function getAllThemes(): array
    {
        // Önbellekleme parametreleri
        $cacheEnabled = config('studio.cache.enable', true);
        $cacheTtl = config('studio.cache.ttl', 60 * 24);
        $cacheKey = $this->getCacheKey('all_themes');
        
        // Önbellekten al veya yükle
        if ($cacheEnabled) {
            return Cache::remember($cacheKey, now()->addMinutes($cacheTtl), function () {
                return $this->loadThemes();
            });
        }
        
        return $this->loadThemes();
    }
    
    /**
     * Temaları yükle
     *
     * @return array
     */
    protected function loadThemes(): array
    {
        try {
            // ThemeManagement modülü aktif mi kontrol et
            if (class_exists('Modules\ThemeManagement\App\Models\Theme')) {
                return $this->loadThemesFromDatabase();
            }
            
            // Dosya sisteminden temaları yükle
            return $this->loadThemesFromFileSystem();
        } catch (\Exception $e) {
            Log::error('Temalar yüklenirken hata: ' . $e->getMessage());
            return $this->getDefaultThemes();
        }
    }
    
    /**
     * Veritabanından temaları yükle
     *
     * @return array
     */
    protected function loadThemesFromDatabase(): array
    {
        try {
            return \Modules\ThemeManagement\App\Models\Theme::where('is_active', true)
                ->get()
                ->map(function ($theme) {
                    return [
                        'id' => $theme->id,
                        'name' => $theme->name,
                        'title' => $theme->title,
                        'description' => $theme->description,
                        'folder_name' => $theme->folder_name,
                        'is_default' => $theme->is_default,
                        'screenshot' => $theme->screenshot ? asset('storage/' . $theme->screenshot) : null,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Veritabanından temalar yüklenirken hata: ' . $e->getMessage());
            return $this->getDefaultThemes();
        }
    }
    
    /**
     * Dosya sisteminden temaları yükle
     *
     * @return array
     */
    protected function loadThemesFromFileSystem(): array
    {
        try {
            $themes = [];
            $themesPath = resource_path('views/themes');
            
            if (!File::isDirectory($themesPath)) {
                return $this->getDefaultThemes();
            }
            
            $directories = File::directories($themesPath);
            
            foreach ($directories as $directory) {
                $folderName = basename($directory);
                $configFile = $directory . '/theme.json';
                
                if (File::exists($configFile)) {
                    $config = json_decode(File::get($configFile), true);
                    
                    $themes[] = [
                        'id' => $config['id'] ?? count($themes) + 1,
                        'name' => $config['name'] ?? $folderName,
                        'title' => $config['title'] ?? ucfirst($folderName),
                        'description' => $config['description'] ?? '',
                        'folder_name' => $folderName,
                        'is_default' => $config['is_default'] ?? false,
                        'screenshot' => $config['screenshot'] ? asset('themes/' . $folderName . '/' . $config['screenshot']) : null,
                    ];
                } else {
                    // Basit tema yapılandırması
                    $themes[] = [
                        'id' => count($themes) + 1,
                        'name' => $folderName,
                        'title' => ucfirst($folderName),
                        'description' => ucfirst($folderName) . ' teması',
                        'folder_name' => $folderName,
                        'is_default' => $folderName === 'default',
                        'screenshot' => null,
                    ];
                }
            }
            
            // En az bir tema yoksa, varsayılan temaları ekle
            if (empty($themes)) {
                return $this->getDefaultThemes();
            }
            
            return $themes;
        } catch (\Exception $e) {
            Log::error('Dosya sisteminden temalar yüklenirken hata: ' . $e->getMessage());
            return $this->getDefaultThemes();
        }
    }
    
    /**
     * Varsayılan temaları döndür
     *
     * @return array
     */
    protected function getDefaultThemes(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'default',
                'title' => 'Varsayılan Tema',
                'description' => 'Varsayılan tema',
                'folder_name' => 'default',
                'is_default' => true,
                'screenshot' => null,
            ],
            [
                'id' => 2,
                'name' => 'bootstrap',
                'title' => 'Bootstrap Tema',
                'description' => 'Bootstrap 5 teması',
                'folder_name' => 'bootstrap',
                'is_default' => false,
                'screenshot' => null,
            ],
        ];
    }
    
    /**
     * Varsayılan temayı getir
     *
     * @return array|null
     */
    public function getDefaultTheme(): ?array
    {
        $themes = $this->getAllThemes();
        
        // Varsayılan olarak işaretlenmiş temayı bul
        foreach ($themes as $theme) {
            if ($theme['is_default']) {
                return $theme;
            }
        }
        
        // Varsayılan tema yoksa ilk temayı döndür
        return $themes[0] ?? null;
    }
    
    /**
     * Tema için şablonları getir
     *
     * @param string $themeName Tema adı
     * @return array
     */
    public function getTemplatesForTheme(string $themeName): array
    {
        try {
            // Şablon klasörlerini kontrol et
            $headersPath = resource_path('views/themes/' . $themeName . '/headers');
            $footersPath = resource_path('views/themes/' . $themeName . '/footers');
            $sectionsPath = resource_path('views/themes/' . $themeName . '/sections');
            
            $templates = [
                'headers' => $this->getTemplatesFromDirectory($headersPath, $themeName, 'headers'),
                'footers' => $this->getTemplatesFromDirectory($footersPath, $themeName, 'footers'),
                'sections' => $this->getTemplatesFromDirectory($sectionsPath, $themeName, 'sections'),
            ];
            
            return $templates;
        } catch (\Exception $e) {
            Log::error('Tema şablonları yüklenirken hata: ' . $e->getMessage());
            
            return [
                'headers' => [],
                'footers' => [],
                'sections' => [],
            ];
        }
    }
    
    /**
     * Dizinden şablonları yükle
     *
     * @param string $directory Dizin yolu
     * @param string $themeName Tema adı
     * @param string $type Şablon tipi
     * @return array
     */
    protected function getTemplatesFromDirectory(string $directory, string $themeName, string $type): array
    {
        $templates = [];
        
        if (File::isDirectory($directory)) {
            $files = File::files($directory);
            
            foreach ($files as $file) {
                if (File::extension($file) === 'php' || File::extension($file) === 'blade.php') {
                    $name = str_replace(['.blade.php', '.php'], '', $file->getFilename());
                    
                    $templates[] = [
                        'name' => $name,
                        'title' => ucfirst(str_replace(['-', '_'], ' ', $name)),
                        'path' => 'themes.' . $themeName . '.' . $type . '.' . $name,
                    ];
                }
            }
        }
        
        return $templates;
    }
    
    /**
     * Temayı değiştir
     *
     * @param string $module Modül adı
     * @param int $moduleId İçerik ID
     * @param string $theme Tema adı
     * @return bool
     */
    public function changeTheme(string $module, int $moduleId, string $theme): bool
    {
        try {
            $managerService = app('studio.manager');
            
            $settings = $managerService->getModuleSettings($module, $moduleId);
            $settings['theme'] = $theme;
            
            // Temaya ait şablonları kontrol et
            $templates = $this->getTemplatesForTheme($theme);
            if (!empty($templates['headers'])) {
                $settings['header_template'] = $templates['headers'][0]['path'] ?? null;
            }
            
            if (!empty($templates['footers'])) {
                $settings['footer_template'] = $templates['footers'][0]['path'] ?? null;
            }
            
            return $managerService->saveModuleSettings($module, $moduleId, $settings);
        } catch (\Exception $e) {
            Log::error('Tema değiştirilirken hata: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Önbelleği temizle
     *
     * @return void
     */
    public function clearCache(): void
    {
        $cacheKey = $this->getCacheKey('all_themes');
        Cache::forget($cacheKey);
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
        
        // tenant() fonksiyonu var mı kontrol et
        if (function_exists('tenant')) {
            // tenant() null değilse getTenantKey() çağır
            $tenant = tenant();
            $tenantId = $tenant ? $tenant->getTenantKey() : 'central';
        } else {
            $tenantId = 'central';
        }
        
        return "{$prefix}{$tenantId}_{$key}";
    }
}