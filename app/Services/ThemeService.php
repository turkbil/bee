<?php
// app/Services/ThemeService.php

namespace App\Services;

use Illuminate\Support\Facades\View;
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
        // Aktif temayı veritabanından al, yoksa blank'i kullan
        $this->activeTheme = Theme::where('is_default', true)
            ->where('is_active', true)
            ->first();

        if (!$this->activeTheme) {
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
        
        // 1. Modül içerisindeki tema view'ı
        if ($module) {
            $moduleThemeView = "{$module}-themes.{$themeName}.{$view}";
            if (View::exists($moduleThemeView)) {
                \Log::debug("Using module theme view: {$moduleThemeView}");
                return $moduleThemeView;
            }
        }
        
        // 2. Ana tema içerisindeki view (resources/themes)
        $mainThemeView = "themes.{$themeName}.{$view}";
        if (View::exists($mainThemeView)) {
            \Log::debug("Using main theme view: {$mainThemeView}");
            return $mainThemeView;
        }
        
        // 3. Modül içindeki varsayılan view
        if ($module) {
            $defaultModuleView = "{$module}::{$view}";
            if (View::exists($defaultModuleView)) {
                \Log::debug("Using default module view: {$defaultModuleView}");
                return $defaultModuleView;
            }
        }
        
        // Tüm alternatifleri kontrol et ve logla
        \Log::error("View not found for any path. Module: {$module}, View: {$view}, Theme: {$themeName}");
        throw new \Exception("View [{$view}] not found in any location.");
    }
}