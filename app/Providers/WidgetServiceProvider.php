<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Modules\WidgetManagement\App\Support\ShortcodeParser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Modules\WidgetManagement\app\Services\WidgetService;

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
        
        // Widget çözümleyici servisini singleton olarak kaydet
        $this->app->singleton('widget.resolver', function ($app) {
            return $this;
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
        
        // Widget CSS ve JS içeriğini render etme direktifleri
        Blade::directive('widgetstyles', function () {
            return "<?php echo \\Modules\\WidgetManagement\\app\\Services\\WidgetService::getStylesOutput(); ?>";
        });
        
        Blade::directive('widgetscripts', function () {
            return "<?php echo \\Modules\\WidgetManagement\\app\\Services\\WidgetService::getScriptsOutput(); ?>";
        });
        
        // Özel direktif: Widget içeriğini render etme
        Blade::directive('renderwidgets', function ($expression) {
            return '<?php echo app("widget.resolver")->resolveWidgetContent(' . $expression . '); ?>';
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
        
        // Widget render işlemi için global bir yardımcı görünüm verisi ekleyelim
        View::composer('themes.blank.layouts.app', function ($view) {
            $view->with('widgetResolver', $this);
        });
    }
    
    /**
     * Frontend widget çözümleyicisi - widget placeholder'larını doğrudan HTML içerikle değiştirir
     * 
     * @param string $content HTML içerik
     * @return string İşlenmiş HTML içerik
     */
    public function resolveWidgetContent($content)
    {
        // Widget embed pattern - data-tenant-widget-id'yi bul ve widget içeriğini ekle
        $pattern = '/<div[^>]*data-tenant-widget-id="(\d+)"[^>]*>.*?<div[^>]*id="widget-content-\d+"[^>]*>.*?<\/div><\/div>/s';
        
        return preg_replace_callback($pattern, function($matches) {
            $widgetId = $matches[1];
            
            // Widget içeriğini direkt olarak render et
            if (function_exists('widget_by_id')) {
                return widget_by_id($widgetId);
            }
            
            return "<!-- Widget ID: $widgetId (Widget helper fonksiyonu bulunamadı) -->";
        }, $content);
    }
}