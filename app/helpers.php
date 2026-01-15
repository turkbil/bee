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

if (!function_exists('browser_supports_webp')) {
    /**
     * Browser WebP Format Desteği Kontrolü
     *
     * HTTP Accept header'ını kontrol ederek tarayıcının
     * WebP formatını destekleyip desteklemediğini belirler.
     *
     * Eski cihazlar (Safari 12 ve altı, IE11, eski Android)
     * WebP desteklemediği için JPG fallback gerekir.
     *
     * @return bool WebP destekleniyor mu?
     *
     * Örnek Kullanım:
     * $format = browser_supports_webp() ? 'webp' : 'jpg';
     */
    function browser_supports_webp(): bool
    {
        // CLI'da çalışıyorsa (cron, queue vb.) WebP varsay
        if (app()->runningInConsole()) {
            return true;
        }

        // Accept header kontrolü
        $accept = request()->header('Accept', '');

        // image/webp Accept header'da varsa destekliyor
        if (str_contains($accept, 'image/webp')) {
            return true;
        }

        // User-Agent bazlı kontrol (Accept header yoksa)
        $userAgent = request()->userAgent() ?? '';

        // Chrome 32+, Firefox 65+, Edge 18+, Opera 19+ WebP destekler
        // Safari 14+ (macOS Big Sur) WebP destekler
        // iOS 14+ WebP destekler

        // Eski Safari kontrolü (WebP desteklemiyor)
        if (preg_match('/Safari\/[\d.]+/i', $userAgent) && !preg_match('/Chrome|Chromium|CriOS/i', $userAgent)) {
            // Safari versiyonu kontrolü
            if (preg_match('/Version\/([\d.]+)/i', $userAgent, $matches)) {
                $version = (float) $matches[1];
                // Safari 14 ve üzeri WebP destekler
                if ($version < 14) {
                    return false;
                }
            }
            // iOS Safari kontrolü
            if (preg_match('/OS ([\d_]+)/i', $userAgent, $matches)) {
                $version = (float) str_replace('_', '.', $matches[1]);
                // iOS 14 ve üzeri WebP destekler
                if ($version < 14) {
                    return false;
                }
            }
        }

        // IE hiç WebP desteklemez
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return false;
        }

        // Eski Android kontrolü (4.x ve altı)
        if (preg_match('/Android ([\d.]+)/i', $userAgent, $matches)) {
            $version = (float) $matches[1];
            if ($version < 5) {
                return false;
            }
        }

        // Varsayılan olarak WebP destekle (modern tarayıcılar)
        return true;
    }
}

if (!function_exists('get_optimal_image_format')) {
    /**
     * Optimal Görsel Formatını Belirle
     *
     * Browser desteğine göre en uygun formatı döndürür.
     * WebP desteklenmiyorsa JPG kullanır.
     *
     * @param string|null $preferred Tercih edilen format (null = auto)
     * @return string Format (webp, jpg)
     *
     * Örnek Kullanım:
     * $format = get_optimal_image_format(); // 'webp' veya 'jpg'
     * $format = get_optimal_image_format('webp'); // zorla webp
     */
    function get_optimal_image_format(?string $preferred = null): string
    {
        // Explicit format belirtildiyse onu kullan
        if ($preferred !== null && $preferred !== 'auto') {
            return $preferred;
        }

        // Browser desteğine göre seç
        return browser_supports_webp() ? 'webp' : 'jpg';
    }
}

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

        // Format belirleme: auto = browser desteğine göre
        $requestedFormat = $options['format'] ?? 'auto';
        $finalFormat = get_optimal_image_format($requestedFormat);

        // Parametreleri hazırla
        $params = array_filter([
            'src' => $src,
            'w' => $width,
            'h' => $height,
            'q' => $options['quality'] ?? null,
            'a' => $options['alignment'] ?? null,
            's' => $options['scale'] ?? null,
            'f' => $finalFormat,
            'c' => $options['cache'] ?? 1,
        ]);

        // Tenant-aware URL oluştur
        if (app()->runningInConsole()) {
            // CLI'da tenant domain'ini kullan
            $tenant = tenant();
            if ($tenant && $tenant->domains->isNotEmpty()) {
                $domain = $tenant->domains->first()->domain;
                $baseUrl = 'https://' . $domain;
            } else {
                $baseUrl = config('app.url');
            }
        } else {
            // Web request'te mevcut domain'i kullan
            $baseUrl = request()->getSchemeAndHttpHost();
        }
        return $baseUrl . '/thumbmaker?' . http_build_query($params);
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

        // Tüm img tag'lerini bul (array'e dönüştür - live NodeList sorunu için)
        $images = iterator_to_array($dom->getElementsByTagName('img'));

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
                // Orijinal src'yi sakla (lightbox için)
                $originalSrc = $src;

                // Width/height attribute'larını oku (varsa)
                $width = $img->hasAttribute('width') ? (int) $img->getAttribute('width') : $defaultWidth;
                $height = $img->hasAttribute('height') ? (int) $img->getAttribute('height') : $defaultHeight;

                // Thumbmaker URL oluştur (küçük versiyon - thumbnail)
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

                // 3️⃣ Lightbox için data attribute ekle (JavaScript ile wrapper eklenecek)
                $img->setAttribute('data-glightbox-src', $originalSrc);
                $img->setAttribute('data-glightbox-gallery', 'blog-content');

                if ($img->hasAttribute('alt') && !empty($img->getAttribute('alt'))) {
                    $img->setAttribute('data-glightbox-title', $img->getAttribute('alt'));
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
     * - option8 → 12 blog/gün (Agresif SEO)
     * - option9 → 24 blog/gün (Maximum Growth)
     *
     * @return int Günlük blog sayısı (varsayılan: 4)
     */
    function getBlogDailyCount(): int
    {
        $optionValue = getTenantSetting('blog_ai_daily_count', 'option4');

        // 🔧 FIX: Direkt sayı kontrolü (backward compatibility)
        // Eğer direkt sayı verilmişse (eski kayıtlar), onu kullan
        if (is_numeric($optionValue)) {
            return (int) $optionValue;
        }

        // Option value → Integer mapping
        $mapping = [
            'option1' => 1,   // 1 blog/gün (günde 1)
            'option2' => 2,   // 2 blog/gün (12 saatte 1)
            'option3' => 3,   // 3 blog/gün (8 saatte 1)
            'option4' => 4,   // 4 blog/gün (6 saatte 1) - DEFAULT
            'option5' => 5,   // 5 blog/gün (5 saatte 1)
            'option6' => 6,   // 6 blog/gün (4 saatte 1)
            'option7' => 7,   // 7 blog/gün (haftada her gün farklı)
            'option8' => 8,   // 8 blog/gün (3 saatte 1)
            'option9' => 12,  // 12 blog/gün (2 saatte 1)
            'option10' => 24, // 24 blog/gün (her saat)
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
     * @param  int  $dailyCount  Number of blogs per day (1-24)
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
     * - 12 blog/day: [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22] - Her 2 saatte (Agresif SEO)
     * - 24 blog/day: [0-23] Her saat (Maximum Growth)
     *
     * Örnek Kullanım:
     * $hours = calculateActiveHours(4); // [0, 6, 12, 18]
     * in_array(date('H'), $hours); // Check if current hour is active
     */
    function calculateActiveHours(int $dailyCount): array
    {
        $schedules = [
            1 => [0],                                                           // 1 blog/gün - Gece yarısı
            2 => [0, 12],                                                        // 2 blog/gün - Her 12 saatte
            3 => [0, 8, 16],                                                     // 3 blog/gün - Her 8 saatte
            4 => [0, 6, 12, 18],                                                 // 4 blog/gün - Her 6 saatte (B2B Optimal)
            5 => [0, 5, 10, 15, 20],                                             // 5 blog/gün - Her 5 saatte
            6 => [0, 4, 8, 12, 16, 20],                                          // 6 blog/gün - Her 4 saatte
            7 => [0, 3, 6, 9, 12, 15, 18],                                       // 7 blog/gün - Dengeli dağılım
            8 => [0, 3, 6, 9, 12, 15, 18, 21],                                   // 8 blog/gün - Her 3 saatte (SEO Strong)
            12 => [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22],                  // 12 blog/gün - Her 2 saatte (Agresif SEO)
            24 => range(0, 23),                                                  // 24 blog/gün - Her saat (Maximum Growth)
        ];

        return $schedules[$dailyCount] ?? [0]; // Fallback: Günde 1 (gece yarısı)
    }
}

if (!function_exists('clear_all_caches')) {
    /**
     * Universal Tenant-Aware Cache Clear Function
     *
     * Sadece mevcut tenant'ın cache'lerini temizler (tenant-aware).
     * Admin paneldeki "Cache Temizle" butonu ile aynı mantık.
     *
     * Kullanım:
     * clear_all_caches(); // Mevcut tenant cache'lerini temizle
     * clear_all_caches('theme_change'); // Context ile temizle (log için)
     * clear_all_caches('manual', true); // Tüm sistemi temizle (dikkatli kullan!)
     *
     * @param string $context Log için açıklama (opsiyonel)
     * @param bool $global Tüm sistemi mi temizle? (default: false = sadece tenant)
     * @return bool İşlem başarılı mı
     */
    function clear_all_caches(string $context = 'manual', bool $global = false): bool
    {
        try {
            $tenantId = function_exists('tenant') && tenant() ? tenant('id') : null;

            // 1. View cache (tenant-specific views zaten ayrı)
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            // 2. Response cache - Tenant-aware tag ile temizle
            if (config('responsecache.enabled')) {
                if ($tenantId && !$global) {
                    // Sadece bu tenant'ın response cache'ini temizle
                    $cacheTag = "tenant_{$tenantId}_response_cache";
                    try {
                        \Illuminate\Support\Facades\Cache::tags([$cacheTag])->flush();
                    } catch (\Exception $e) {
                        // Tag desteklenmiyorsa genel temizle
                        \Illuminate\Support\Facades\Artisan::call('responsecache:clear');
                    }
                } else {
                    // Global temizleme
                    \Illuminate\Support\Facades\Artisan::call('responsecache:clear');
                }
            }

            // 3. Application cache - Tenant-aware
            if ($tenantId && !$global) {
                // Tenant-specific cache key'lerini temizle
                $tenantCachePrefix = "tenant_{$tenantId}_";

                // Redis tag-based flush (eğer destekleniyorsa)
                try {
                    \Illuminate\Support\Facades\Cache::tags([$tenantCachePrefix])->flush();
                } catch (\Exception $e) {
                    // Tag desteklenmiyorsa forget ile temizle
                    // Ana cache key'lerini temizle
                    $cacheKeys = [
                        "theme_{$tenantId}",
                        "settings_{$tenantId}",
                        "menu_{$tenantId}",
                        "translations_{$tenantId}",
                    ];
                    foreach ($cacheKeys as $key) {
                        \Illuminate\Support\Facades\Cache::forget($key);
                    }
                }
            } else {
                // Global flush (dikkatli!)
                \Illuminate\Support\Facades\Cache::flush();
            }

            // 4. OPcache reset (HTTP üzerinden) - Her zaman gerekli
            try {
                $domain = $tenantId ? request()->getHost() : 'ixtif.com';
                @file_get_contents("https://{$domain}/opcache-reset.php", false, stream_context_create([
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
                    'http' => ['timeout' => 3]
                ]));
            } catch (\Exception $e) {
                // Sessizce devam et
            }

            \Illuminate\Support\Facades\Log::info('✅ Cache Cleared', [
                'context' => $context,
                'tenant_id' => $tenantId ?? 'central',
                'global' => $global,
            ]);

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Cache Clear Failed', [
                'context' => $context,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

/**
 * ======================================================================
 * Tenant CSS Helper
 * ======================================================================
 */

if (!function_exists('tenant_css')) {
    /**
     * Tenant'a ozel CSS dosyasinin URL'ini dondur
     *
     * Tenant ID'ye gore ilgili CSS dosyasini yukler.
     * Dosya yoksa fallback olarak app.css kullanilir.
     *
     * @param  string|null  $fallback  Fallback CSS dosyasi (varsayilan: app.css)
     * @return string  CSS dosyasi URL'i
     *
     * Ornek Kullanim:
     * <link rel="stylesheet" href="{{ tenant_css() }}">
     */
    function tenant_css(?string $fallback = 'css/app.css'): string
    {
        // Tenant varsa tenant-specific CSS kontrol et
        if (function_exists('tenant') && $t = tenant()) {
            $tenantCss = "css/tenant-{$t->id}.css";
            $tenantCssPath = public_path($tenantCss);

            // Tenant CSS dosyasi varsa onu kullan
            if (file_exists($tenantCssPath)) {
                // Cache busting icin timestamp ekle
                $timestamp = filemtime($tenantCssPath);
                return asset($tenantCss) . '?v=' . $timestamp;
            }
        }

        // Fallback CSS
        $fallbackPath = public_path($fallback);
        $timestamp = file_exists($fallbackPath) ? filemtime($fallbackPath) : time();

        return asset($fallback) . '?v=' . $timestamp;
    }
}

if (!function_exists('tenant_css_exists')) {
    /**
     * Tenant'a ozel CSS dosyasinin var olup olmadigini kontrol et
     *
     * @return bool
     */
    function tenant_css_exists(): bool
    {
        if (function_exists('tenant') && $t = tenant()) {
            return file_exists(public_path("css/tenant-{$t->id}.css"));
        }

        return false;
    }
}

if (!function_exists('tenant_trans')) {
    /**
     * Get tenant-specific translation
     * Falls back to default translation if tenant translation not found
     *
     * @param string $key Translation key (e.g., 'player.queue_title')
     * @param array $replace Replacement parameters
     * @param string|null $locale Locale (defaults to current app locale)
     * @return string
     */
    function tenant_trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $tenantId = tenant('id');
        $locale = $locale ?? app()->getLocale();

        // If no tenant, use default translation
        if (!$tenantId) {
            return __($key, $replace, $locale);
        }

        // Parse key to get file and key parts
        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) {
            return __($key, $replace, $locale);
        }

        [$file, $translationKey] = $parts;

        // Build tenant-specific lang path
        $tenantLangPath = base_path("lang/tenant/{$tenantId}/{$locale}/{$file}.php");

        // Check if tenant translation file exists
        if (file_exists($tenantLangPath)) {
            $translations = include $tenantLangPath;

            if (isset($translations[$translationKey])) {
                $translation = $translations[$translationKey];

                // Handle replacements
                foreach ($replace as $search => $value) {
                    $translation = str_replace(':' . $search, $value, $translation);
                }

                return $translation;
            }
        }

        // Fallback to default translation
        return __($key, $replace, $locale);
    }
}

if (!function_exists('tenant_trans_choice')) {
    /**
     * Get tenant-specific pluralized translation
     *
     * @param string $key Translation key
     * @param int $number Count for pluralization
     * @param array $replace Replacement parameters
     * @param string|null $locale Locale
     * @return string
     */
    function tenant_trans_choice(string $key, int $number, array $replace = [], ?string $locale = null): string
    {
        $replace['count'] = $number;
        return tenant_trans($key, $replace, $locale);
    }
}

if (!function_exists('tenant_lang')) {
    /**
     * Get all translations for a tenant lang file (useful for JavaScript)
     *
     * @param string $file Lang file name (e.g., 'player')
     * @param string|null $locale Locale
     * @return array
     */
    function tenant_lang(string $file, ?string $locale = null): array
    {
        $tenantId = tenant('id');
        $locale = $locale ?? app()->getLocale();

        if (!$tenantId) {
            return [];
        }

        $tenantLangPath = base_path("lang/tenant/{$tenantId}/{$locale}/{$file}.php");

        if (file_exists($tenantLangPath)) {
            return include $tenantLangPath;
        }

        return [];
    }
}

/**
 * ======================================================================
 * Media Collection Helper (2025)
 * ======================================================================
 */

if (!function_exists('getFirstMediaWithFallback')) {
    /**
     * Get first media from model with multi-collection fallback
     *
     * Priority: hero > featured_image > gallery > product_images > images > default
     *
     * @param  mixed  $model  Model with HasMedia trait
     * @param  string|null  $preferredCollection  Preferred collection (default: hero)
     * @return \Spatie\MediaLibrary\MediaCollections\Models\Media|null
     *
     * Usage:
     * getFirstMediaWithFallback($product)
     * getFirstMediaWithFallback($blog, 'featured_image')
     */
    function getFirstMediaWithFallback($model, ?string $preferredCollection = 'hero')
    {
        if (!$model || !method_exists($model, 'getFirstMedia')) {
            return null;
        }

        // Tenant context kontrolü - database seçili değilse null dön
        try {
            if (function_exists('tenant') && tenant() === null) {
                // Central context'te tenant model'e media query yapılamaz
                return null;
            }
        } catch (\Exception $e) {
            // Tenant check başarısız olursa devam et
        }

        // Fallback chain
        $collections = [
            $preferredCollection,
            'hero',
            'featured_image',
            'gallery',
            'product_images',
            'images',
            'default',
        ];

        // Remove duplicates
        $collections = array_unique($collections);

        try {
            foreach ($collections as $collection) {
                if ($model->hasMedia($collection)) {
                    return $model->getFirstMedia($collection);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Database seçili değilse (tenant context yok) sessizce null dön
            if (str_contains($e->getMessage(), 'No database selected')) {
                return null;
            }
            throw $e;
        }

        return null;
    }
}
