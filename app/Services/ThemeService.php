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
            // Önce modül içindeki tema klasörünü kontrol et
            $moduleThemePath = "{$module}::themes.{$themeName}.{$view}";
            
            if (View::exists($moduleThemePath)) {
                return $moduleThemePath;
            }
        }
        
        // Temadaki genel görünüm
        $themePath = "themes.{$themeName}.{$view}";
        if (View::exists($themePath)) {
            return $themePath;
        }
        
        // Modül varsayılan görünümü
        if ($module) {
            return "{$module}::{$view}";
        }
        
        // Son çare olarak doğrudan view'i döndür
        return $view;
    }
}