<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishThemeAssets extends Command
{
    protected $signature = 'theme:publish {theme? : Tema adı (varsayılan: tüm temalar)}';
    protected $description = 'Tema varlıklarını public dizinine kopyalar';

    public function handle()
    {
        $theme = $this->argument('theme');
        
        if ($theme) {
            $this->publishTheme($theme);
        } else {
            $this->publishAllThemes();
        }
        
        $this->info('Tema varlıkları başarıyla yayınlandı!');
        
        return Command::SUCCESS;
    }
    
    private function publishTheme($theme)
    {
        $source = resource_path("views/themes/{$theme}/assets");
        $destination = public_path("themes/{$theme}/assets");
        
        if (!File::isDirectory($source)) {
            $this->error("{$theme} teması için assets dizini bulunamadı!");
            return;
        }
        
        File::ensureDirectoryExists($destination);
        File::copyDirectory($source, $destination);
        
        $this->info("{$theme} teması varlıkları yayınlandı.");
    }
    
    private function publishAllThemes()
    {
        $themesPath = resource_path('views/themes');
        $themes = File::directories($themesPath);
        
        foreach ($themes as $themePath) {
            $theme = basename($themePath);
            $this->publishTheme($theme);
        }
        
        // Modül temalarını da yayınla
        $modulesPath = base_path('Modules');
        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);
            
            foreach ($modules as $modulePath) {
                $module = basename($modulePath);
                $moduleThemesPath = "{$modulePath}/resources/views/front/themes";
                
                if (File::isDirectory($moduleThemesPath)) {
                    $moduleThemes = File::directories($moduleThemesPath);
                    
                    foreach ($moduleThemes as $moduleThemePath) {
                        $moduleTheme = basename($moduleThemePath);
                        $source = "{$moduleThemePath}/assets";
                        $destination = public_path("themes/{$module}-{$moduleTheme}/assets");
                        
                        if (File::isDirectory($source)) {
                            File::ensureDirectoryExists($destination);
                            File::copyDirectory($source, $destination);
                            $this->info("{$module}-{$moduleTheme} modül teması varlıkları yayınlandı.");
                        }
                    }
                }
            }
        }
    }
}