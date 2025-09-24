<?php

namespace App\Services;

class SecurityValidationService
{
    /**
     * Zararlı CSS pattern'leri
     */
    private static $dangerousCssPatterns = [
        '/javascript\s*:/i',                    // javascript: protokol
        '/data\s*:\s*text\/html/i',            // data:text/html
        '/expression\s*\(/i',                  // CSS expression (IE)
        '/binding\s*:/i',                      // -moz-binding
        '/behavior\s*:/i',                     // behavior (IE)
        '/import\s+["\']javascript:/i',        // @import javascript:
        '/url\s*\(\s*["\']?\s*javascript:/i',  // url(javascript:)
        '/url\s*\(\s*["\']?\s*data\s*:\s*text\/html/i', // url(data:text/html)
        '/vbscript\s*:/i',                     // vbscript: protokol
        '/@import.*javascript/i',              // @import ile javascript
        '/expression\s*\([^)]*\)/i',          // expression() fonksiyonu
    ];

    /**
     * Zararlı JS pattern'leri
     */
    private static $dangerousJsPatterns = [
        '/eval\s*\(/i',                        // eval() fonksiyonu
        '/Function\s*\(/i',                    // Function() constructor
        '/setTimeout\s*\(\s*["\'][^"\']*["\'].*\)/i', // setTimeout string kod
        '/setInterval\s*\(\s*["\'][^"\']*["\'].*\)/i', // setInterval string kod
        '/document\.write/i',                  // document.write
        '/document\.writeln/i',                // document.writeln
        '/innerHTML\s*=/i',                    // innerHTML assignment
        '/outerHTML\s*=/i',                    // outerHTML assignment
        '/insertAdjacentHTML/i',               // insertAdjacentHTML
        '/execScript/i',                       // execScript (IE)
        '/createContextualFragment/i',         // createContextualFragment
        '/javascript\s*:/i',                   // javascript: protokol
        '/vbscript\s*:/i',                     // vbscript: protokol
        '/data\s*:\s*text\/html/i',           // data:text/html
        '/window\s*\[\s*["\'][^"\']*["\']\s*\]/i', // window["eval"] vs.
        '/this\s*\[\s*["\'][^"\']*["\']\s*\]/i',   // this["eval"] vs.
        '/globalThis/i',                       // globalThis erişimi
        '/constructor\s*\.\s*constructor/i',   // constructor.constructor
        '/__proto__/i',                        // __proto__ manipulation
        '/prototype\s*\[/i',                   // prototype pollution
        '/XMLHttpRequest/i',                   // XHR
        '/fetch\s*\(/i',                       // fetch API
        '/WebSocket/i',                        // WebSocket
        '/import\s*\(/i',                      // dynamic import
        '/require\s*\(/i',                     // require (Node.js)
        '/process\s*\./i',                     // process object
        '/Buffer\s*\./i',                      // Buffer object
        '/fs\s*\./i',                          // file system
        '/child_process/i',                    // child_process
        '/cluster/i',                          // cluster module
        '/crypto\s*\./i',                      // crypto module (sensitive)
        '/os\s*\./i',                          // os module
        '/path\s*\./i',                        // path module
        '/url\s*\./i',                         // url module
        '/querystring\s*\./i',                 // querystring module
        '/util\s*\./i',                        // util module
        '/events\s*\./i',                      // events module
        '/stream\s*\./i',                      // stream module
        '/net\s*\./i',                         // net module
        '/http\s*\./i',                        // http module
        '/https\s*\./i',                       // https module
    ];

    /**
     * Zararlı HTML pattern'leri
     */
    private static $dangerousHtmlPatterns = [
        '/<script[^>]*>/i',                    // script tag
        '/<\/script>/i',                       // script end tag
        '/<iframe[^>]*>/i',                    // iframe tag
        '/<object[^>]*>/i',                    // object tag
        '/<embed[^>]*>/i',                     // embed tag
        '/<applet[^>]*>/i',                    // applet tag
        '/<form[^>]*>/i',                      // form tag
        '/<input[^>]*>/i',                     // input tag
        '/<meta[^>]*http-equiv/i',             // meta refresh
        '/<link[^>]*href[^>]*javascript:/i',   // link javascript
        '/on\w+\s*=/i',                        // event handlers (onclick, onload, etc.)
        '/javascript\s*:/i',                   // javascript: protokol
        '/vbscript\s*:/i',                     // vbscript: protokol
        '/data\s*:\s*text\/html/i',           // data:text/html
        '/<svg[^>]*onload/i',                  // SVG onload
        '/<img[^>]*on\w+/i',                   // img event handlers
        '/<style[^>]*>/i',                     // style tag (inline CSS)
        '/<\/style>/i',                        // style end tag
    ];

    /**
     * CSS kod güvenlik kontrolü
     */
    public static function validateCss(string $css): array
    {
        $errors = [];
        $cleanCss = trim($css);

        if (empty($cleanCss)) {
            return ['valid' => true, 'errors' => [], 'clean_code' => ''];
        }

        // Zararlı pattern kontrolü
        foreach (self::$dangerousCssPatterns as $pattern) {
            if (preg_match($pattern, $cleanCss)) {
                $errors[] = "Güvenlik riski: Zararlı CSS pattern tespit edildi";
                break;
            }
        }

        // URL kontrolü - sadece güvenli protokollere izin ver
        if (preg_match_all('/url\s*\(\s*["\']?([^)]+)["\']?\s*\)/i', $cleanCss, $matches)) {
            foreach ($matches[1] as $url) {
                $url = trim($url, '"\'');
                if (!self::isValidCssUrl($url)) {
                    $errors[] = "Güvenlik riski: Güvenli olmayan URL - " . substr($url, 0, 50);
                }
            }
        }

        // @import kontrolü
        if (preg_match_all('/@import\s+["\']?([^"\';\s]+)["\']?/i', $cleanCss, $matches)) {
            foreach ($matches[1] as $importUrl) {
                if (!self::isValidCssUrl($importUrl)) {
                    $errors[] = "Güvenlik riski: Güvenli olmayan @import URL - " . substr($importUrl, 0, 50);
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'clean_code' => $cleanCss
        ];
    }

    /**
     * JavaScript kod güvenlik kontrolü
     */
    public static function validateJs(string $js): array
    {
        $errors = [];
        $cleanJs = trim($js);

        if (empty($cleanJs)) {
            return ['valid' => true, 'errors' => [], 'clean_code' => ''];
        }

        // Zararlı pattern kontrolü
        foreach (self::$dangerousJsPatterns as $pattern) {
            if (preg_match($pattern, $cleanJs)) {
                $errors[] = "Güvenlik riski: Zararlı JavaScript pattern tespit edildi";
                break;
            }
        }

        // Base64 decode kontrolü (gizlenmiş kod)
        if (preg_match('/atob\s*\(/i', $cleanJs) || preg_match('/btoa\s*\(/i', $cleanJs)) {
            $errors[] = "Güvenlik riski: Base64 encode/decode fonksiyonu tespit edildi";
        }

        // String concatenation ile gizlenmiş eval kontrolü
        if (preg_match('/\[\s*["\'][^"\']*["\']\s*\+\s*["\'][^"\']*["\']\s*\]/i', $cleanJs)) {
            if (preg_match('/ev.*al|function|constructor/i', $cleanJs)) {
                $errors[] = "Güvenlik riski: Potansiyel gizlenmiş kod çalıştırma";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'clean_code' => $cleanJs
        ];
    }

    /**
     * HTML içerik güvenlik kontrolü
     */
    public static function validateHtml(string $html): array
    {
        $errors = [];
        $cleanHtml = trim($html);

        if (empty($cleanHtml)) {
            return ['valid' => true, 'errors' => [], 'clean_code' => ''];
        }

        // Zararlı pattern kontrolü
        foreach (self::$dangerousHtmlPatterns as $pattern) {
            if (preg_match($pattern, $cleanHtml)) {
                $errors[] = "Güvenlik riski: Zararlı HTML pattern tespit edildi";
                break;
            }
        }

        // PHP kod kontrolü
        if (preg_match('/<\?php|<\?=|\?>/i', $cleanHtml)) {
            $errors[] = "Güvenlik riski: PHP kod tespit edildi";
        }

        // Server-side include kontrolü
        if (preg_match('/<!--\s*#(include|exec|config|set)/i', $cleanHtml)) {
            $errors[] = "Güvenlik riski: Server-side include tespit edildi";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'clean_code' => $cleanHtml
        ];
    }

    /**
     * CSS URL güvenlik kontrolü
     */
    private static function isValidCssUrl(string $url): bool
    {
        // Boş URL güvenli
        if (empty(trim($url))) {
            return true;
        }

        // Güvenli protokoller
        $safeProtocols = ['http:', 'https:', '//', '/', './'];

        foreach ($safeProtocols as $protocol) {
            if (str_starts_with(strtolower($url), $protocol)) {
                return true;
            }
        }

        // Relatif path (protokol yok)
        if (!preg_match('/^[a-z]+:/i', $url)) {
            return true;
        }

        return false;
    }

    /**
     * Tüm kod türleri için toplu validation
     */
    public static function validateCode(string $code, string $type): array
    {
        return match($type) {
            'css' => self::validateCss($code),
            'js', 'javascript' => self::validateJs($code),
            'html' => self::validateHtml($code),
            default => ['valid' => false, 'errors' => ['Desteklenmeyen kod türü'], 'clean_code' => '']
        };
    }
}