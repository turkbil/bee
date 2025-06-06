<?php
// app/Services/ThemeService.php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Modules\ThemeManagement\App\Models\Theme;

class ThemeService
{
    protected $activeTheme;

    public function __construct()
    {
        $this->setActiveTheme();
    }

    protected function setActiveTheme()
    {
        // Tenant-specific theme if available
        if (function_exists('tenant') && $t = tenant()) {
            $theme = Theme::where('name', $t->theme)
                          ->where('is_active', true)
                          ->first();
            if ($theme) {
                $this->activeTheme = $theme;
                return;
            }
        }
        // Global default theme
        $this->activeTheme = Theme::where('is_default', true)
            ->where('is_active', true)
            ->first() ?? new Theme([
                'name' => 'blank',
                'folder_name' => 'blank'
            ]);
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
}