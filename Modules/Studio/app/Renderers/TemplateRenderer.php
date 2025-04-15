<?php

namespace Modules\Studio\App\Renderers;

use Modules\Studio\App\Contracts\RendererInterface;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class TemplateRenderer implements RendererInterface
{
    /**
     * İçeriği render et
     *
     * @param string $content
     * @param array $params
     * @return string
     */
    public function render(string $content, array $params = []): string
    {
        // İçeriği Blade şablonu olarak derle
        return $this->compile($content, $params);
    }
    
    /**
     * Şablonu render et
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderTemplate(string $template, array $params = []): string
    {
        // Tema öneki eklenmiş mi kontrol et
        if (!str_contains($template, '::')) {
            // Eğer / içeriyorsa tema şablonu olarak varsay
            if (str_contains($template, '/')) {
                list($theme, $path) = explode('/', $template, 2);
                $template = 'themes.' . $theme . '.' . $path;
            } else {
                $template = 'studio::templates.' . $template;
            }
        }
        
        if (View::exists($template)) {
            return View::make($template, $params)->render();
        }
        
        return '<!-- Template not found: ' . $template . ' -->';
    }
    
    /**
     * Şablonu derle
     *
     * @param string $template
     * @param array $params
     * @return string
     */
    public function compile(string $template, array $params = []): string
    {
        // Blade şablonunu derle
        return Blade::render($template, $params);
    }
    
    /**
     * Tema şablonunu render et
     *
     * @param string $theme
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderThemeTemplate(string $theme, string $template, array $params = []): string
    {
        $viewName = 'themes.' . $theme . '.' . $template;
        
        if (View::exists($viewName)) {
            return View::make($viewName, $params)->render();
        }
        
        return '<!-- Theme template not found: ' . $viewName . ' -->';
    }
    
    /**
     * Şablonda değişiklik yap ve render et
     *
     * @param string $template
     * @param array $replacements
     * @param array $params
     * @return string
     */
    public function renderWithReplacements(string $template, array $replacements, array $params = []): string
    {
        // Önce şablonu render et
        $content = $this->renderTemplate($template, $params);
        
        // Değiştirme işlemleri
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        return $content;
    }
}