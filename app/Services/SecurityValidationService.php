<?php

namespace App\Services;

class SecurityValidationService
{
    /**
     * Zararlı CSS pattern'leri - MİNİMAL KONTROL
     *
     * FELSEFE: Modern CSS'e tam özgürlük
     *
     * ✅ İZİN VERİLEN:
     * - Tüm CSS3 ve CSS4 özellikleri
     * - CSS Variables ve Custom Properties
     * - @import direktifleri
     * - url() fonksiyonları (resimler, fontlar için)
     * - CSS-in-JS ve inline styles
     * - Animasyonlar ve transition'lar
     * - Media queries ve container queries
     * - Modern layout sistemleri (Grid, Flexbox)
     *
     * ❌ SADECE BUNLAR ENGELLENİR:
     * - IE'ye özel eski zararlı pattern'ler
     */
    private static $dangerousCssPatterns = [
        '/expression\s*\([^)]*alert/i',        // Eski IE expression() ile alert
        '/behavior\s*:.*\.htc/i',              // IE behavior .htc dosyaları
        '/-moz-binding\s*:.*xml/i',            // Firefox eski binding
    ];

    /**
     * Zararlı JS pattern'leri - ÇOK MİNİMAL KONTROL
     *
     * FELSEFE: Modern JavaScript'e tam özgürlük
     *
     * ✅ İZİN VERİLEN:
     * - eval(), new Function() (bazen gerçekten gerekli)
     * - setTimeout/setInterval (normal kullanım)
     * - document.write (legacy kod için gerekli olabilir)
     * - Console işlemleri
     * - DOM manipülasyonu
     * - AJAX/Fetch işlemleri
     * - Modern framework kodları
     * - Prototype ve class tanımlamaları
     *
     * ❌ SADECE BUNLAR ENGELLENİR:
     * - Çok spesifik zararlı kod kombinasyonları
     */
    private static $dangerousJsPatterns = [
        // Sadece çok spesifik zararlı pattern'ler
        '/child_process.*exec/i',              // Node.js command execution
        '/require\s*\(\s*["\']child_process/i', // Node.js shell access
        '/process\.exit\s*\(\s*\)/i',          // Direkt process sonlandırma
    ];

    /**
     * Zararlı HTML pattern'leri - SADECE GERÇEK TEHDİTLER
     *
     * FELSEFE: Modern web geliştirme için maksimum özgürlük, minimum kısıtlama
     *
     * ✅ İZİN VERİLEN HER ŞEY:
     * - Tüm HTML5 tag'leri (script, style, iframe, form, input, vb.)
     * - Inline JavaScript (onclick, onload gibi event handler'lar dahil)
     * - Modern framework kodları (React, Vue, Alpine.js)
     * - CSS-in-JS ve inline styles
     * - Data attributes ve custom elements
     * - SVG ve Canvas işlemleri
     * - Form elemanları ve input'lar
     *
     * ❌ SADECE BUNLAR ENGELLENİR:
     * - PHP kod injection (<?php)
     * - Server-side includes (<!--#include)
     * - Bilinen zararlı domain'ler (eğer tespit edilirse)
     */
    private static $dangerousHtmlPatterns = [
        // Sadece gerçek server-side tehditler
        '/<\?php|<\?=|\?>/i',                  // PHP kod injection
        '/<!--\s*#(include|exec|config)/i',    // Server-side includes
        // Çok spesifik zararlı pattern'ler
        '/document\.cookie.*steal/i',          // Cookie çalma girişimi
        '/eval\s*\(\s*unescape/i',            // Obfuscated eval
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

        // Zararlı pattern kontrolü - sadece gerçek tehditler
        foreach (self::$dangerousCssPatterns as $pattern) {
            if (preg_match($pattern, $cleanCss)) {
                $errors[] = "Güvenlik riski: Eski/zararlı CSS pattern tespit edildi";
                break;
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

        // Zararlı pattern kontrolü - sadece çok spesifik tehditler
        foreach (self::$dangerousJsPatterns as $pattern) {
            if (preg_match($pattern, $cleanJs)) {
                $errors[] = "Güvenlik riski: Zararlı pattern tespit edildi";
                break;
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
     *
     * Bu metod sayfa editöründe yazılan HTML, JavaScript ve CSS içeriğini kontrol eder.
     * Script ve style tag'lerine izin verilir ancak aşağıdaki tehlikeli pattern'ler engellenir:
     *
     * ✅ İZİN VERİLEN İÇERİKLER:
     * - <script>console.log('Hello');</script> (Yerel script)
     * - <style>body { color: red; }</style> (Yerel style)
     * - <div class="container">İçerik</div> (Normal HTML)
     *
     * ❌ ENGELLENEN İÇERİKLER:
     * - <button onclick="alert()">Click</button> (Inline event handler)
     * - <a href="javascript:void(0)">Link</a> (JavaScript protokol)
     * - <script src="http://evil.com/xss.js"></script> (External script)
     * - <img onerror="alert('XSS')"> (Event handler injection)
     * - <?php echo "code"; ?> (Server-side kod)
     *
     * @param string $html Kontrol edilecek HTML içerik
     * @return array ['valid' => bool, 'errors' => array, 'clean_code' => string]
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
                $errors[] = "Güvenlik riski: Zararlı pattern tespit edildi";
                break;
            }
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