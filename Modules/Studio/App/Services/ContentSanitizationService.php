<?php

namespace Modules\Studio\App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Log;
use Exception;

class ContentSanitizationService
{
    protected $htmlPurifier;
    protected $config;

    public function __construct()
    {
        $this->initializeHtmlPurifier();
    }

    /**
     * HTMLPurifier'ı başlat
     */
    private function initializeHtmlPurifier()
    {
        $this->config = HTMLPurifier_Config::createDefault();
        
        // Güvenli HTML taglerini belirle
        $this->config->set('HTML.Allowed', 
            'div[class|id|style],p[class|id|style],span[class|id|style],h1[class|id|style],h2[class|id|style],h3[class|id|style],h4[class|id|style],h5[class|id|style],h6[class|id|style],' .
            'img[src|alt|class|id|style|width|height],a[href|class|id|style|title|target],' .
            'ul[class|id|style],ol[class|id|style],li[class|id|style],' .
            'table[class|id|style],tr[class|id|style],td[class|id|style],th[class|id|style],thead,tbody,tfoot,' .
            'strong,b,em,i,u,br,hr,' .
            'blockquote[class|id|style],code,pre[class|id|style],' .
            'button[class|id|style|type|onclick],input[type|class|id|style|name|value|placeholder],' .
            'form[action|method|class|id|style],label[for|class|id|style],select[name|class|id|style],option[value],' .
            'textarea[name|class|id|style|rows|cols|placeholder],' .
            'section[class|id|style],article[class|id|style],aside[class|id|style],header[class|id|style],footer[class|id|style],nav[class|id|style],' .
            'video[src|controls|width|height|class|id|style],audio[src|controls|class|id|style]'
        );
        
        // CSS property'lerini belirle
        $this->config->set('CSS.AllowedProperties', 
            'color,background,background-color,background-image,background-repeat,background-position,background-size,' .
            'font-family,font-size,font-weight,font-style,text-align,text-decoration,line-height,' .
            'margin,margin-top,margin-right,margin-bottom,margin-left,' .
            'padding,padding-top,padding-right,padding-bottom,padding-left,' .
            'border,border-top,border-right,border-bottom,border-left,border-radius,border-color,border-width,border-style,' .
            'width,height,max-width,max-height,min-width,min-height,' .
            'display,position,top,right,bottom,left,float,clear,overflow,z-index,' .
            'opacity,visibility,cursor,box-shadow,text-shadow,' .
            'flex,flex-direction,justify-content,align-items,align-self,flex-wrap,' .
            'grid,grid-template-columns,grid-template-rows,grid-gap,grid-column,grid-row,' .
            'transform,transition,animation'
        );

        // URI scheme'larını belirle
        $this->config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
            'data' => true // Base64 image'lar için
        ]);

        // Cache dizini
        $this->config->set('Cache.SerializerPath', storage_path('framework/cache/htmlpurifier'));
        
        $this->htmlPurifier = new HTMLPurifier($this->config);
    }

    /**
     * HTML içeriği sanitize et
     */
    public function sanitizeHtml(string $content): string
    {
        try {
            if (empty($content)) {
                return '';
            }

            // HTMLPurifier ile temizle
            $sanitized = $this->htmlPurifier->purify($content);
            
            Log::debug('HTML sanitized', [
                'original_length' => strlen($content),
                'sanitized_length' => strlen($sanitized),
                'removed_content' => strlen($content) - strlen($sanitized)
            ]);

            return $sanitized;
        } catch (Exception $e) {
            Log::error('HTML sanitization failed', [
                'error' => $e->getMessage(),
                'content_length' => strlen($content)
            ]);
            
            // Fallback: Sadece temel HTML escape
            return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * CSS içeriği sanitize et
     */
    public function sanitizeCss(string $css): string
    {
        try {
            if (empty($css)) {
                return '';
            }

            // Tehlikeli CSS pattern'larını kaldır
            $dangerousPatterns = [
                '/javascript:/i',
                '/vbscript:/i',
                '/data:(?!image)/i', // image dışındaki data: URL'leri
                '/expression\s*\(/i',
                '/behavior\s*:/i',
                '/-moz-binding/i',
                '/@import/i',
                '/url\s*\(\s*[\'"]?\s*javascript:/i'
            ];

            $sanitized = $css;
            foreach ($dangerousPatterns as $pattern) {
                $sanitized = preg_replace($pattern, '', $sanitized);
            }

            // Sadece güvenli CSS property'lerini tut
            $sanitized = $this->filterCssProperties($sanitized);

            Log::debug('CSS sanitized', [
                'original_length' => strlen($css),
                'sanitized_length' => strlen($sanitized),
                'removed_content' => strlen($css) - strlen($sanitized)
            ]);

            return $sanitized;
        } catch (Exception $e) {
            Log::error('CSS sanitization failed', [
                'error' => $e->getMessage(),
                'css_length' => strlen($css)
            ]);
            
            return '';
        }
    }

    /**
     * JavaScript içeriği sanitize et (çok kısıtlayıcı)
     */
    public function sanitizeJs(string $js): string
    {
        try {
            if (empty($js)) {
                return '';
            }

            // Widget JavaScript'leri için çok kısıtlayıcı yaklaşım
            // Sadece basit jQuery/DOM manipulation'a izin ver
            
            $dangerousPatterns = [
                '/eval\s*\(/i',
                '/Function\s*\(/i',
                '/setTimeout\s*\(/i',
                '/setInterval\s*\(/i',
                '/document\.write/i',
                '/innerHTML\s*=/i',
                '/outerHTML\s*=/i',
                '/location\s*=/i',
                '/window\./i',
                '/document\.cookie/i',
                '/localStorage/i',
                '/sessionStorage/i',
                '/XMLHttpRequest/i',
                '/fetch\s*\(/i',
                '/import\s*\(/i',
                '/require\s*\(/i',
                '/ajax/i'
            ];

            $sanitized = $js;
            foreach ($dangerousPatterns as $pattern) {
                $sanitized = preg_replace($pattern, '/* REMOVED */', $sanitized);
            }

            // Eğer çok fazla şey kaldırıldıysa, JS'yi tamamen temizle
            $removedPercentage = (strlen($js) - strlen($sanitized)) / strlen($js) * 100;
            if ($removedPercentage > 30) {
                Log::warning('JavaScript heavily sanitized, removing entirely', [
                    'removed_percentage' => $removedPercentage,
                    'original_length' => strlen($js)
                ]);
                return '/* JavaScript removed due to security concerns */';
            }

            Log::debug('JavaScript sanitized', [
                'original_length' => strlen($js),
                'sanitized_length' => strlen($sanitized),
                'removed_percentage' => $removedPercentage
            ]);

            return $sanitized;
        } catch (Exception $e) {
            Log::error('JavaScript sanitization failed', [
                'error' => $e->getMessage(),
                'js_length' => strlen($js)
            ]);
            
            return '/* JavaScript removed due to error */';
        }
    }

    /**
     * CSS property'lerini filtrele
     */
    private function filterCssProperties(string $css): string
    {
        // Allowed CSS properties
        $allowedProperties = [
            'color', 'background', 'background-color', 'background-image', 'background-repeat', 'background-position', 'background-size',
            'font-family', 'font-size', 'font-weight', 'font-style', 'text-align', 'text-decoration', 'line-height',
            'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
            'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
            'border', 'border-top', 'border-right', 'border-bottom', 'border-left', 'border-radius', 'border-color', 'border-width', 'border-style',
            'width', 'height', 'max-width', 'max-height', 'min-width', 'min-height',
            'display', 'position', 'top', 'right', 'bottom', 'left', 'float', 'clear', 'overflow', 'z-index',
            'opacity', 'visibility', 'cursor', 'box-shadow', 'text-shadow',
            'flex', 'flex-direction', 'justify-content', 'align-items', 'align-self', 'flex-wrap',
            'grid', 'grid-template-columns', 'grid-template-rows', 'grid-gap', 'grid-column', 'grid-row',
            'transform', 'transition', 'animation'
        ];

        // CSS kurallarını parse et ve filtrele
        // Bu basit bir implementasyon, daha gelişmiş CSS parser kullanılabilir
        return $css; // Şimdilik basit versiyonu dönüyor
    }

    /**
     * Widget içeriklerini toplu sanitize et
     */
    public function sanitizeWidgetContent(array $content): array
    {
        $sanitized = [];

        if (isset($content['html'])) {
            $sanitized['html'] = $this->sanitizeHtml($content['html']);
        }

        if (isset($content['css'])) {
            $sanitized['css'] = $this->sanitizeCss($content['css']);
        }

        if (isset($content['js'])) {
            $sanitized['js'] = $this->sanitizeJs($content['js']);
        }

        return $sanitized;
    }

    /**
     * Test için güvensiz içerik tespiti
     */
    public function detectUnsafeContent(string $content, string $type = 'html'): array
    {
        $threats = [];

        switch ($type) {
            case 'html':
                if (preg_match('/<script/i', $content)) $threats[] = 'script_tag';
                if (preg_match('/javascript:/i', $content)) $threats[] = 'javascript_url';
                if (preg_match('/onload|onclick|onerror/i', $content)) $threats[] = 'event_handler';
                if (preg_match('/<iframe/i', $content)) $threats[] = 'iframe_tag';
                break;

            case 'css':
                if (preg_match('/javascript:/i', $content)) $threats[] = 'javascript_url';
                if (preg_match('/expression\s*\(/i', $content)) $threats[] = 'css_expression';
                if (preg_match('/@import/i', $content)) $threats[] = 'css_import';
                break;

            case 'js':
                if (preg_match('/eval\s*\(/i', $content)) $threats[] = 'eval_function';
                if (preg_match('/document\.write/i', $content)) $threats[] = 'document_write';
                if (preg_match('/location\s*=/i', $content)) $threats[] = 'location_redirect';
                break;
        }

        return $threats;
    }
}