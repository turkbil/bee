<?php

namespace Modules\Studio\App\Support;

use Illuminate\Support\HtmlString;

class StudioHelper
{
    /**
     * CSS dosyalarını render et
     *
     * @return HtmlString
     */
    public static function renderCss(): HtmlString
    {
        return new HtmlString(app('studio.asset')->renderCss());
    }
    
    /**
     * JS dosyalarını render et
     *
     * @return HtmlString
     */
    public static function renderJs(): HtmlString
    {
        return new HtmlString(app('studio.asset')->renderJs());
    }
    
    /**
     * Benzersiz ID oluştur
     *
     * @param string $prefix
     * @return string
     */
    public static function uniqueId(string $prefix = 'studio'): string
    {
        return $prefix . '-' . substr(md5(uniqid(mt_rand(), true)), 0, 8);
    }
    
    /**
     * HTML escape
     *
     * @param string $content
     * @return string
     */
    public static function escape(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Studio editör URL'si oluştur
     *
     * @param string $module
     * @param int $id
     * @return string
     */
    public static function editorUrl(string $module, int $id): string
    {
        return route('admin.studio.editor', ['module' => $module, 'id' => $id]);
    }
    
    /**
     * Tema URL'si oluştur
     *
     * @param string $themeName
     * @param string $path
     * @return string
     */
    public static function themeUrl(string $themeName, string $path): string
    {
        return asset('themes/' . $themeName . '/' . ltrim($path, '/'));
    }
    
    /**
     * Widget içeriğini render et
     *
     * @param int $widgetId
     * @return HtmlString
     */
    public static function renderWidget(int $widgetId): HtmlString
    {
        $widgetService = app('studio.widget');
        $content = $widgetService->getWidgetContent($widgetId);
        
        if (!$content) {
            return new HtmlString('<!-- Widget not found: ' . $widgetId . ' -->');
        }
        
        $html = $content['html'] ?? '';
        $css = '';
        
        if (isset($content['css']) && !empty($content['css'])) {
            $css = '<style>' . $content['css'] . '</style>';
        }
        
        return new HtmlString($css . $html);
    }
}