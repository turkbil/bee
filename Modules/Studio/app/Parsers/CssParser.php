<?php

namespace Modules\Studio\App\Parsers;

class CssParser
{
    /**
     * CSS içeriğini temizler ve düzeltmeler yapar
     *
     * @param string|null $cssString
     * @return string
     */
    public function parseAndFixCss(?string $cssString): string
    {
        if (!$cssString || !is_string($cssString)) {
            return '';
        }
        
        // CSS içeriğini temizle
        return trim($cssString);
    }
    
    /**
     * CSS içeriğini küçült
     *
     * @param string $css
     * @return string
     */
    public function minifyCss(string $css): string
    {
        // Yorum satırlarını kaldır
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Boşlukları ve yeni satırları kaldır
        $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
        
        // CSS kurallarındaki boşlukları azalt
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*({|}|\[|\]|=|~|\+|>|\||;|:|,)\s*/', '$1', $css);
        
        return trim($css);
    }
    
    /**
     * CSS içeriğini optimize et
     *
     * @param string $css
     * @return string
     */
    public function optimizeCss(string $css): string
    {
        // Şimdilik sadece minify yap
        return $this->minifyCss($css);
    }
}