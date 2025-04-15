<?php

namespace Modules\Studio\App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\HtmlString renderCss()
 * @method static \Illuminate\Support\HtmlString renderJs()
 * @method static string uniqueId(string $prefix = 'studio')
 * @method static string escape(string $content)
 * @method static string editorUrl(string $module, int $id)
 * @method static string themeUrl(string $themeName, string $path)
 * @method static \Illuminate\Support\HtmlString renderWidget(int $widgetId)
 * 
 * @see \Modules\Studio\App\Support\StudioHelper
 */
class Studio extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'studio';
    }
}