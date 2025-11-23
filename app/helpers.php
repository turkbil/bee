<?php

/**
 * Laravel 12 Compatibility Helpers
 *
 * Laravel 12'de kaldırılan helper fonksiyonlar
 */

if (!function_exists('array_last')) {
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    function array_last($array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? value($default) : end($array);
        }

        return Illuminate\Support\Arr::last($array, $callback, $default);
    }
}

/**
 * ======================================================================
 * Thumbmaker Helper Functions
 * ======================================================================
 */

if (!function_exists('thumb')) {
    /**
     * Universal Thumbmaker Helper
     *
     * Anında görsel boyutlandırma, format dönüştürme
     *
     * @param  string|object  $src  Görsel URL veya Media objesi
     * @param  int|null  $width  Genişlik
     * @param  int|null  $height  Yükseklik
     * @param  array  $options  Ek parametreler [quality, alignment, scale, format, cache]
     * @return string  Thumbmaker URL
     *
     * Örnek Kullanım:
     * thumb($media, 400, 300)
     * thumb($media, 400, 300, ['quality' => 90, 'alignment' => 'c'])
     * thumb('https://example.com/image.jpg', 800, null, ['format' => 'webp'])
     */
    function thumb($src, ?int $width = null, ?int $height = null, array $options = []): string
    {
        // Media objesi ise URL'e çevir
        if (is_object($src) && method_exists($src, 'getUrl')) {
            $src = $src->getUrl();
        }

        // Eğer src zaten thumbmaker URL'i ise direkt döndür
        if (str_contains($src, '/thumbmaker?')) {
            return $src;
        }

        // Parametreleri hazırla
        $params = array_filter([
            'src' => $src,
            'w' => $width,
            'h' => $height,
            'q' => $options['quality'] ?? null,
            'a' => $options['alignment'] ?? null,
            's' => $options['scale'] ?? null,
            'f' => $options['format'] ?? 'webp',
            'c' => $options['cache'] ?? 1,
        ]);

        return route('thumbmaker', $params);
    }
}

if (!function_exists('thumb_url')) {
    /**
     * Thumbmaker URL Builder (Alternatif isim)
     *
     * @param  string  $src
     * @param  int|null  $width
     * @param  int|null  $height
     * @param  int  $quality
     * @return string
     */
    function thumb_url(string $src, ?int $width = null, ?int $height = null, int $quality = 85): string
    {
        return thumb($src, $width, $height, ['quality' => $quality]);
    }
}

/**
 * ======================================================================
 * Blog Image Processing Helper
 * ======================================================================
 */

if (!function_exists('process_blog_images')) {
    /**
     * Blog Body İçeriğindeki Görselleri Optimize Et
     *
     * 1. Tüm img tag'lerine loading="lazy" ekler
     * 2. Storage URL'lerini Thumbmaker URL'sine çevirir
     * 3. WebP formatına optimize eder
     *
     * @param  string  $html  Blog body HTML içeriği
     * @param  int  $defaultWidth  Varsayılan genişlik (800px)
     * @param  int  $defaultHeight  Varsayılan yükseklik (600px)
     * @param  int  $quality  Görsel kalitesi (85)
     * @return string  Optimize edilmiş HTML
     *
     * Örnek Kullanım:
     * $optimizedHtml = process_blog_images($bodyHtml);
     */
    function process_blog_images(string $html, int $defaultWidth = 800, int $defaultHeight = 600, int $quality = 85): string
    {
        if (empty($html)) {
            return $html;
        }

        // DOMDocument ile HTML parse et
        $dom = new DOMDocument('1.0', 'UTF-8');

        // UTF-8 encoding sorunu çözümü + HTML5 hatalarını bastır
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        // Tüm img tag'lerini bul
        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            // 1️⃣ Lazy loading ekle (eğer yoksa)
            if (!$img->hasAttribute('loading')) {
                $img->setAttribute('loading', 'lazy');
            }

            // 2️⃣ Src attribute al
            $src = $img->getAttribute('src');

            if (empty($src)) {
                continue;
            }

            // Eğer zaten Thumbmaker URL'i ise skip et
            if (str_contains($src, '/thumbmaker?')) {
                continue;
            }

            // Storage URL kontrolü (storage/ içeren URL'ler)
            if (str_contains($src, '/storage/')) {
                // Width/height attribute'larını oku (varsa)
                $width = $img->hasAttribute('width') ? (int) $img->getAttribute('width') : $defaultWidth;
                $height = $img->hasAttribute('height') ? (int) $img->getAttribute('height') : $defaultHeight;

                // Thumbmaker URL oluştur
                $thumbUrl = thumb($src, $width, $height, [
                    'quality' => $quality,
                    'format' => 'webp',
                    'scale' => 1, // Fill mode (kare crop)
                    'alignment' => 'c', // Center
                ]);

                // Src'yi güncelle
                $img->setAttribute('src', $thumbUrl);

                // Width/height attribute'larını koru (SEO için önemli)
                if (!$img->hasAttribute('width')) {
                    $img->setAttribute('width', (string) $width);
                }
                if (!$img->hasAttribute('height')) {
                    $img->setAttribute('height', (string) $height);
                }
            }
        }

        // HTML'i geri çevir (TÜM <?xml encoding> tag'lerini kaldır)
        $output = $dom->saveHTML();
        $output = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $output);

        return $output;
    }
}

/**
 * ======================================================================
 * Blog AI Cron System Helper Functions
 * ======================================================================
 */

if (!function_exists('getTenantSetting')) {
    /**
     * Tenant-aware setting value getter
     *
     * Gets setting value for current tenant from SettingValue table
     *
     * @param  string  $key  Setting key (örn: 'blog_ai_daily_count')
     * @param  mixed  $default  Default value if not found
     * @return mixed  Setting value
     *
     * Örnek Kullanım:
     * $dailyCount = getTenantSetting('blog_ai_daily_count', 4);
     * $autoPublish = getTenantSetting('blog_ai_auto_publish', true);
     */
    function getTenantSetting(string $key, $default = null)
    {
        try {
            // Central DB'den Setting'i bul
            $setting = \Modules\SettingManagement\App\Models\Setting::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            // Tenant DB'den value'yu çek
            if (function_exists('tenant') && tenant()) {
                $settingValue = \Modules\SettingManagement\App\Models\SettingValue::on('tenant')
                    ->where('setting_id', $setting->id)
                    ->first();

                if ($settingValue && $settingValue->value !== null) {
                    return $settingValue->value;
                }
            }

            // Default value (setting default veya parameter default)
            return $setting->default_value ?? $default;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('⚠️ getTenantSetting failed', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return $default;
        }
    }
}

if (!function_exists('getBlogDailyCount')) {
    /**
     * Get blog daily count from tenant setting (option value → integer)
     *
     * Setting 'blog_ai_daily_count' option value'larını gerçek sayıya çevirir:
     * - option1 → 1 blog/gün
     * - option2 → 2 blog/gün
     * - option3 → 3 blog/gün
     * - option4 → 4 blog/gün (B2B Optimal)
     * - option5 → 5 blog/gün
     * - option6 → 6 blog/gün
     * - option7 → 8 blog/gün (SEO Maximum)
     *
     * @return int Günlük blog sayısı (varsayılan: 4)
     */
    function getBlogDailyCount(): int
    {
        $optionValue = getTenantSetting('blog_ai_daily_count', 'option4');

        // Option value → Integer mapping
        $mapping = [
            'option1' => 1,
            'option2' => 2,
            'option3' => 3,
            'option4' => 4,
            'option5' => 5,
            'option6' => 6,
            'option7' => 8,
        ];

        return $mapping[$optionValue] ?? 4; // Fallback: 4 blog/gün
    }
}

if (!function_exists('sanitize_blade_content')) {
    /**
     * Blog İçeriğinden Tehlikeli Blade Direktiflerini Temizle
     *
     * AI tarafından üretilen blog içeriklerinde yanlışlıkla
     * Blade layout direktifleri olabilir. Bu direktifler
     * Blade::render() ile işlenirken hata oluşturur.
     *
     * Temizlenen direktifler:
     * - @section, @endsection, @show
     * - @yield, @parent
     * - @extends, @include, @component, @slot
     * - @push, @endpush, @prepend, @endprepend
     * - @stack, @once, @endonce
     * - @verbatim, @endverbatim
     * - @inject, @env, @production
     *
     * @param  string  $content  Blog body içeriği
     * @return string  Sanitize edilmiş içerik
     *
     * Örnek Kullanım:
     * $safeContent = sanitize_blade_content($parsedBody);
     * Blade::render($safeContent);
     */
    function sanitize_blade_content(string $content): string
    {
        if (empty($content)) {
            return $content;
        }

        // Tehlikeli Blade layout direktifleri (parametre ile)
        $dangerousDirectives = [
            // Section & Layout
            '/@section\s*\([^)]*\)/s',
            '/@endsection/',
            '/@show/',
            '/@yield\s*\([^)]*\)/s',
            '/@parent/',
            '/@extends\s*\([^)]*\)/s',

            // Include & Component
            '/@include\s*\([^)]*\)/s',
            '/@includeIf\s*\([^)]*\)/s',
            '/@includeWhen\s*\([^)]*\)/s',
            '/@includeUnless\s*\([^)]*\)/s',
            '/@includeFirst\s*\([^)]*\)/s',
            '/@component\s*\([^)]*\)/s',
            '/@endcomponent/',
            '/@slot\s*\([^)]*\)/s',
            '/@endslot/',

            // Stack & Push
            '/@push\s*\([^)]*\)/s',
            '/@endpush/',
            '/@prepend\s*\([^)]*\)/s',
            '/@endprepend/',
            '/@stack\s*\([^)]*\)/s',

            // Once
            '/@once/',
            '/@endonce/',

            // Verbatim (bu da tehlikeli olabilir)
            '/@verbatim/',
            '/@endverbatim/',

            // Inject & Environment
            '/@inject\s*\([^)]*\)/s',
            '/@env\s*\([^)]*\)/s',
            '/@endenv/',
            '/@production/',
            '/@endproduction/',

            // Fragment
            '/@fragment\s*\([^)]*\)/s',
            '/@endfragment/',
        ];

        // Tüm tehlikeli direktifleri kaldır
        foreach ($dangerousDirectives as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }

        // Boş satırları temizle (opsiyonel)
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);

        return $content;
    }
}

if (!function_exists('calculateActiveHours')) {
    /**
     * Calculate active hours for blog AI cron based on daily count
     *
     * Returns array of hours (0-23) when blog generation should run
     *
     * @param  int  $dailyCount  Number of blogs per day (1-8)
     * @return array  Active hours (örn: [0, 6, 12, 18])
     *
     * Schedule Mapping:
     * - 1 blog/day: [0] - Gece yarısı
     * - 2 blog/day: [0, 12] - Her 12 saatte
     * - 3 blog/day: [0, 8, 16] - Her 8 saatte
     * - 4 blog/day: [0, 6, 12, 18] - Her 6 saatte (B2B Optimal)
     * - 5 blog/day: [0, 5, 10, 15, 20] - Her 5 saatte
     * - 6 blog/day: [0, 4, 8, 12, 16, 20] - Her 4 saatte
     * - 8 blog/day: [0, 3, 6, 9, 12, 15, 18, 21] - Her 3 saatte (SEO Maximum)
     *
     * Örnek Kullanım:
     * $hours = calculateActiveHours(4); // [0, 6, 12, 18]
     * in_array(date('H'), $hours); // Check if current hour is active
     */
    function calculateActiveHours(int $dailyCount): array
    {
        $schedules = [
            1 => [0],
            2 => [0, 12],
            3 => [0, 8, 16],
            4 => [0, 6, 12, 18], // B2B Optimal
            5 => [0, 5, 10, 15, 20],
            6 => [0, 4, 8, 12, 16, 20],
            8 => [0, 3, 6, 9, 12, 15, 18, 21], // SEO Maximum
        ];

        return $schedules[$dailyCount] ?? [0]; // Fallback: Günde 1 (gece yarısı)
    }
}
