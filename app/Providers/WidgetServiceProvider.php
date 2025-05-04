<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Modules\WidgetManagement\App\Support\ShortcodeParser;
use Illuminate\Support\Facades\File;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('shortcode.parser', function ($app) {
            return new ShortcodeParser();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Blade Direktiflerini Kaydet
        $this->registerBladeDirectives();
        
        // Yardımcı Fonksiyonları Yükle
        $this->loadHelpers();
        
        // Composer Macro - İçerikteki kısa kodları işle
        $this->registerViewComposers();
    }
    
    /**
     * Blade direktiflerini kaydet
     */
    protected function registerBladeDirectives(): void
    {
        // @widget(id) direktifi - ID ile widget render etme
        Blade::directive('widget', function ($expression) {
            return "<?php echo widget_by_id($expression); ?>";
        });
        
        // @widgetblock(slug, [params]) direktifi - Slug ile widget render etme
        Blade::directive('widgetblock', function ($expression) {
            return "<?php echo widget_by_slug($expression); ?>";
        });
        
        // @widgets(position) direktifi - Pozisyona göre tüm widget'ları render etme
        Blade::directive('widgets', function ($expression) {
            return "<?php echo widgets_by_position($expression); ?>";
        });
        
        // @parsewidgets(content) direktifi - İçerikteki widget kısa kodlarını işleme
        Blade::directive('parsewidgets', function ($expression) {
            return "<?php echo parse_widget_shortcodes($expression); ?>";
        });
        
        // @module(id) direktifi - Module widget render etme
        Blade::directive('module', function ($expression) {
            return "<?php echo module_widget_by_id($expression); ?>";
        });
        
        // @file(id) direktifi - File widget render etme  
        Blade::directive('file', function ($expression) {
            return "<?php echo widget_file_by_id($expression); ?>";
        });
    }
    
    /**
     * Yardımcı fonksiyonları yükle
     */
    protected function loadHelpers(): void
    {
        // WidgetManagement modülü mevcut mu kontrol et
        $helperPath = base_path('Modules/WidgetManagement/app/Helpers/WidgetHelper.php');
        
        if (File::exists($helperPath)) {
            require_once $helperPath;
        }
    }
    
    /**
     * View composer kaydet - içerikteki kısa kodları otomatik işleme
     */
    protected function registerViewComposers(): void
    {
        // Belirli görünümlerde içerikteki widget shortcode'ları otomatik işle
        view()->composer(['page::*', 'content::*', 'blog::*'], function ($view) {
            $html = $view->getData()['content'] ?? null;
            
            // İçerik değişkeni varsa ve string ise, içindeki shortcode'ları işle
            if (is_string($html)) {
                $parsedContent = parse_widget_shortcodes($html);
                $view->with('content', $parsedContent);
            }
        });
    }
}