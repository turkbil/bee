<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class StudioThemeService
{
    protected $cachePrefix = 'studio_theme_';
    protected $cacheDuration = 60;
    
    public function getAllThemes(): array
    {
        $cacheKey = $this->cachePrefix . 'all_themes';
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            if (!class_exists('Modules\ThemeManagement\App\Models\Theme')) {
                return [
                    'simple' => [
                        'id' => 0,
                        'name' => 'Simple Tema',
                        'folder_name' => 'simple',
                        'description' => 'Varsayılan basit tema',
                        'thumbnail' => '/img/themes/simple.jpg'
                    ]
                ];
            }
            
            $themes = \Modules\ThemeManagement\App\Models\Theme::where('is_active', true)->get();
            
            $result = [];
            foreach ($themes as $theme) {
                $result[$theme->folder_name] = [
                    'id' => $theme->id,
                    'name' => $theme->name,
                    'folder_name' => $theme->folder_name,
                    'description' => $theme->description,
                    'thumbnail' => $theme->thumbnail ? url($theme->thumbnail) : url('/img/themes/default.jpg')
                ];
            }
            
            if (!isset($result['simple'])) {
                $result['simple'] = [
                    'id' => 0,
                    'name' => 'Simple Tema',
                    'folder_name' => 'simple',
                    'description' => 'Varsayılan basit tema',
                    'thumbnail' => '/img/themes/simple.jpg'
                ];
            }
            
            return $result;
        });
    }
    
    public function getDefaultTheme(): ?array
    {
        $cacheKey = $this->cachePrefix . 'default_theme_' . (function_exists('tenant_id') ? tenant_id() : 'default');
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $allThemes = $this->getAllThemes();
            
            if (empty($allThemes)) {
                return null;
            }
            
            $setting = config('studio.themes.default', 'simple');
            
            return $allThemes[$setting] ?? reset($allThemes);
        });
    }
    
    public function getHeaderFooterTemplates(string $themeName = 'simple'): array
    {
        $cacheKey = $this->cachePrefix . 'templates_' . $themeName;
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($themeName) {
            $results = [
                'header' => [],
                'footer' => []
            ];
            
            $themeBasePath = resource_path('themes/' . $themeName);
            
            if (File::isDirectory($themeBasePath)) {
                $headerPath = $themeBasePath . '/headers';
                if (File::isDirectory($headerPath)) {
                    foreach (File::files($headerPath) as $file) {
                        if (str_ends_with($file->getFilename(), '.blade.php') || str_ends_with($file->getFilename(), '.php')) {
                            $name = str_replace(['.blade.php', '.php'], '', $file->getFilename());
                            $results['header'][$name] = [
                                'name' => ucfirst($name),
                                'file' => $name
                            ];
                        }
                    }
                }
                
                $footerPath = $themeBasePath . '/footers';
                if (File::isDirectory($footerPath)) {
                    foreach (File::files($footerPath) as $file) {
                        if (str_ends_with($file->getFilename(), '.blade.php') || str_ends_with($file->getFilename(), '.php')) {
                            $name = str_replace(['.blade.php', '.php'], '', $file->getFilename());
                            $results['footer'][$name] = [
                                'name' => ucfirst($name),
                                'file' => $name
                            ];
                        }
                    }
                }
            }
            
            return $results;
        });
    }
    
    public function clearCache(?string $themeKey = null): void
    {
        if ($themeKey) {
            Cache::forget($this->cachePrefix . $themeKey);
        } else {
            $keys = Cache::get($this->cachePrefix . 'keys', []);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            Cache::forget($this->cachePrefix . 'keys');
        }
    }
}