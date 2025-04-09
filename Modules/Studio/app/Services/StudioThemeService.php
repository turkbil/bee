<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Cache;
use Modules\ThemeManagement\App\Models\Theme;

class StudioThemeService
{
    /**
     * Tüm temaları al
     *
     * @return array
     */
    public function getAllThemes()
    {
        // Temaları önbellekten al ya da veritabanından çek
        $cacheKey = 'studio_themes';
        return Cache::remember($cacheKey, now()->addHours(1), function () {
            if (!class_exists('Modules\ThemeManagement\App\Models\Theme')) {
                return [];
            }
            
            return Theme::where('is_active', true)
                ->get()
                ->map(function ($theme) {
                    return [
                        'id' => $theme->theme_id,
                        'name' => $theme->name,
                        'title' => $theme->title,
                        'description' => $theme->description,
                        'folder_name' => $theme->folder_name,
                        'is_default' => $theme->is_default,
                    ];
                })
                ->toArray();
        });
    }
    
    /**
     * Varsayılan temayı al
     *
     * @return array|null
     */
    public function getDefaultTheme()
    {
        $themes = $this->getAllThemes();
        foreach ($themes as $theme) {
            if ($theme['is_default']) {
                return $theme;
            }
        }
        
        return $themes[0] ?? null;
    }
    
    /**
     * Tema başlık ve footer şablonlarını al
     *
     * @param string $themeName
     * @return array
     */
    public function getHeaderFooterTemplates($themeName = null)
    {
        if (!$themeName) {
            $defaultTheme = $this->getDefaultTheme();
            $themeName = $defaultTheme ? $defaultTheme['folder_name'] : 'blank';
        }
        
        // Şablonları al
        $headerPath = resource_path('views/themes/' . $themeName . '/headers');
        $footerPath = resource_path('views/themes/' . $themeName . '/footers');
        
        $headers = [];
        $footers = [];
        
        if (is_dir($headerPath)) {
            $headerFiles = scandir($headerPath);
            foreach ($headerFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'blade.php') {
                    $name = str_replace('.blade.php', '', $file);
                    $headers[] = [
                        'name' => $name,
                        'path' => 'themes.' . $themeName . '.headers.' . $name
                    ];
                }
            }
        }
        
        if (is_dir($footerPath)) {
            $footerFiles = scandir($footerPath);
            foreach ($footerFiles as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'blade.php') {
                    $name = str_replace('.blade.php', '', $file);
                    $footers[] = [
                        'name' => $name,
                        'path' => 'themes.' . $themeName . '.footers.' . $name
                    ];
                }
            }
        }
        
        return [
            'headers' => $headers,
            'footers' => $footers
        ];
    }
}