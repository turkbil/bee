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
        
        // Modül için özel tema görünümü
        if ($module) {
            $modulePath = "modules/{$module}/resources/themes/{$themeName}/{$view}";
            
            if (View::exists($modulePath)) {
                return $modulePath;
            }
        }
        
        // Temadaki genel görünüm
        $themePath = "resources.themes.{$themeName}.{$view}";
        if (View::exists($themePath)) {
            return $themePath;
        }
        
        // Modül varsayılan görünümü
        return $module ? "{$module}::$view" : $view;
    }
}