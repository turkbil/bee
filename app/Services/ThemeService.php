<?php

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
        
        // Modül içerisindeki tema view'ı
        if ($module) {
            $moduleView = "{$module}-themes.{$themeName}.{$view}";
            if (View::exists($moduleView)) {
                return $moduleView;
            }
        }
        
        // Genel tema view'ı
        $themeView = "themes.{$themeName}.{$view}";
        if (View::exists($themeView)) {
            return $themeView;
        }
        
        // Modül içerisindeki varsayılan view
        if ($module) {
            $defaultModuleView = "{$module}::{$view}";
            if (View::exists($defaultModuleView)) {
                return $defaultModuleView;
            }
        }
        
        // Debug için
        \Log::warning("View not found: module={$module}, view={$view}, theme={$themeName}");
        
        // Fallback olarak tema içindeki view'e döneriz
        return $themeView;
    }
}