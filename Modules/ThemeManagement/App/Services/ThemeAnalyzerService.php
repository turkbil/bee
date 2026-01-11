<?php

declare(strict_types=1);

namespace Modules\ThemeManagement\app\Services;

use Modules\ThemeManagement\app\Models\Theme;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\Tenant;

/**
 * Theme Analyzer Service
 *
 * Aktif tema dosyalarını analiz eder, renk paletini çıkarır,
 * CSS/JS dosyalarını tarar ve tasarım kurallarını tespit eder.
 *
 * AI Content Builder için tema uyumu sağlar.
 */
class ThemeAnalyzerService
{
    private const CACHE_TTL = 900; // 15 dakika
    private const COLOR_REGEX = '/#(?:[0-9a-fA-F]{3}){1,2}|rgb\([^)]+\)|rgba\([^)]+\)|var\(--[^)]+\)/';
    private const FONT_REGEX = '/font-family:\s*([^;]+);/i';
    private const SPACING_REGEX = '/(padding|margin):\s*([^;]+);/i';

    /**
     * Tenant'ın aktif temasını analiz et
     */
    public function analyzeTheme(int $tenantId): array
    {
        $cacheKey = "theme_analysis_{$tenantId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tenantId) {
            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                throw new \Exception('Tenant bulunamadı');
            }

            $theme = Theme::find($tenant->theme_id);
            if (!$theme) {
                throw new \Exception('Tema bulunamadı');
            }

            return [
                'theme_info' => $this->getThemeInfo($theme),
                'color_palette' => $this->extractColorPalette($theme),
                'typography' => $this->extractTypography($theme),
                'spacing' => $this->extractSpacing($theme),
                'components' => $this->extractComponents($theme),
                'framework' => $this->detectFramework($theme),
                'custom_css' => $this->extractCustomCSS($theme)
            ];
        });
    }

    /**
     * Tema temel bilgilerini al
     */
    private function getThemeInfo(Theme $theme): array
    {
        return [
            'id' => $theme->theme_id,
            'name' => $theme->theme_name,
            'version' => $theme->version ?? '1.0.0',
            'framework' => $theme->framework ?? 'tailwind',
            'has_dark_mode' => $this->hasDarkMode($theme),
            'responsive' => true,
            'rtl_support' => false,
            'js_patterns' => $this->extractJavaScriptPatterns($theme),
            'css_methodology' => $this->detectCSSMethodology($theme)
        ];
    }

    /**
     * Renk paletini çıkar
     */
    private function extractColorPalette(Theme $theme): array
    {
        $colors = [];
        $cssFiles = $this->getThemeCSSFiles($theme);

        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // CSS değişkenlerini bul
                preg_match_all('/--([^:]+):\s*([^;]+);/', $content, $cssVars);
                foreach ($cssVars[1] as $index => $varName) {
                    if (Str::contains($varName, ['color', 'bg', 'text', 'primary', 'secondary'])) {
                        $colors['variables'][$varName] = $cssVars[2][$index];
                    }
                }

                // Direkt renkleri bul
                preg_match_all(self::COLOR_REGEX, $content, $matches);
                $colors['direct'] = array_unique($matches[0]);
            }
        }

        // Tailwind renkleri
        $colors['tailwind'] = $this->extractTailwindColors($theme);

        // Ana renkleri belirle
        $colors['primary'] = $this->detectPrimaryColor($colors);
        $colors['secondary'] = $this->detectSecondaryColor($colors);
        $colors['accent'] = $this->detectAccentColor($colors);

        return $colors;
    }

    /**
     * Typography kurallarını çıkar
     */
    private function extractTypography(Theme $theme): array
    {
        $typography = [
            'fonts' => [],
            'sizes' => [],
            'weights' => [],
            'line_heights' => []
        ];

        $cssFiles = $this->getThemeCSSFiles($theme);

        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // Font families
                preg_match_all(self::FONT_REGEX, $content, $fonts);
                $typography['fonts'] = array_unique(array_map('trim', $fonts[1] ?? []));

                // Font sizes
                preg_match_all('/font-size:\s*([^;]+);/i', $content, $sizes);
                $typography['sizes'] = array_unique($sizes[1] ?? []);

                // Font weights
                preg_match_all('/font-weight:\s*([^;]+);/i', $content, $weights);
                $typography['weights'] = array_unique($weights[1] ?? []);

                // Line heights
                preg_match_all('/line-height:\s*([^;]+);/i', $content, $lineHeights);
                $typography['line_heights'] = array_unique($lineHeights[1] ?? []);
            }
        }

        // Tailwind typography
        if ($this->isTailwindTheme($theme)) {
            $typography['tailwind_classes'] = $this->extractTailwindTypography($theme);
        }

        return $typography;
    }

    /**
     * Spacing kurallarını çıkar
     */
    private function extractSpacing(Theme $theme): array
    {
        $spacing = [
            'padding' => [],
            'margin' => [],
            'gap' => [],
            'container_width' => '1280px',
            'section_spacing' => '4rem'
        ];

        $cssFiles = $this->getThemeCSSFiles($theme);

        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // Padding değerleri
                preg_match_all('/padding:\s*([^;]+);/i', $content, $paddings);
                $spacing['padding'] = array_unique($paddings[1] ?? []);

                // Margin değerleri
                preg_match_all('/margin:\s*([^;]+);/i', $content, $margins);
                $spacing['margin'] = array_unique($margins[1] ?? []);

                // Gap değerleri
                preg_match_all('/gap:\s*([^;]+);/i', $content, $gaps);
                $spacing['gap'] = array_unique($gaps[1] ?? []);

                // Container width
                if (preg_match('/\.container\s*{[^}]*max-width:\s*([^;]+);/i', $content, $container)) {
                    $spacing['container_width'] = trim($container[1]);
                }
            }
        }

        return $spacing;
    }

    /**
     * Kullanılan componentleri tespit et
     */
    private function extractComponents(Theme $theme): array
    {
        $components = [
            'buttons' => [],
            'cards' => [],
            'forms' => [],
            'navigation' => [],
            'modals' => [],
            'alerts' => []
        ];

        $viewFiles = $this->getThemeViewFiles($theme);

        foreach ($viewFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // Button patterns
                if (preg_match_all('/<button[^>]*class="([^"]*)"/', $content, $buttons)) {
                    $components['buttons'] = array_merge($components['buttons'], $buttons[1]);
                }

                // Card patterns
                if (preg_match_all('/class="[^"]*card[^"]*"/', $content, $cards)) {
                    $components['cards'][] = 'found';
                }

                // Form patterns
                if (preg_match_all('/<form[^>]*>/', $content, $forms)) {
                    $components['forms'][] = 'found';
                }

                // Alpine.js components
                if (Str::contains($content, ['x-data', 'x-show', 'x-model'])) {
                    $components['alpine'] = true;
                }

                // Livewire components
                if (Str::contains($content, ['wire:', '@livewire'])) {
                    $components['livewire'] = true;
                }
            }
        }

        // Unique classes
        $components['buttons'] = array_unique($components['buttons']);

        return $components;
    }

    /**
     * Framework'ü tespit et
     */
    private function detectFramework(Theme $theme): string
    {
        $viewFiles = $this->getThemeViewFiles($theme);
        $cssFiles = $this->getThemeCSSFiles($theme);

        // Tailwind kontrolü
        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);
                if (Str::contains($content, ['@tailwind', 'tailwindcss'])) {
                    return 'tailwind';
                }
            }
        }

        // Bootstrap kontrolü
        foreach ($viewFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);
                if (Str::contains($content, ['col-md-', 'btn-primary', 'container-fluid'])) {
                    return 'bootstrap';
                }
            }
        }

        return 'custom';
    }

    /**
     * Custom CSS kurallarını çıkar
     */
    private function extractCustomCSS(Theme $theme): array
    {
        $customCSS = [];
        $cssFiles = $this->getThemeCSSFiles($theme);

        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // :root değişkenleri
                if (preg_match('/:root\s*{([^}]+)}/s', $content, $rootVars)) {
                    $customCSS['root_vars'] = $rootVars[1];
                }

                // Custom animations
                preg_match_all('/@keyframes\s+([^\s{]+)/', $content, $animations);
                $customCSS['animations'] = $animations[1] ?? [];

                // Custom transitions
                preg_match_all('/transition:\s*([^;]+);/i', $content, $transitions);
                $customCSS['transitions'] = array_unique($transitions[1] ?? []);
            }
        }

        return $customCSS;
    }

    /**
     * Dark mode desteği kontrolü
     */
    private function hasDarkMode(Theme $theme): bool
    {
        $cssFiles = $this->getThemeCSSFiles($theme);

        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);
                if (Str::contains($content, ['@media (prefers-color-scheme: dark)', '.dark', '[data-theme="dark"]'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Tailwind tema mı kontrolü
     */
    private function isTailwindTheme(Theme $theme): bool
    {
        return $this->detectFramework($theme) === 'tailwind';
    }

    /**
     * Tailwind renklerini çıkar
     */
    private function extractTailwindColors(Theme $theme): array
    {
        if (!$this->isTailwindTheme($theme)) {
            return [];
        }

        $configFile = base_path('tailwind.config.js');
        if (!File::exists($configFile)) {
            return [];
        }

        // Tailwind config'den renkleri parse et
        // Bu basit bir implementasyon, gerekirse genişletilebilir
        return [
            'primary' => 'blue',
            'secondary' => 'gray',
            'success' => 'green',
            'danger' => 'red',
            'warning' => 'yellow',
            'info' => 'cyan'
        ];
    }

    /**
     * Tailwind typography sınıflarını çıkar
     */
    private function extractTailwindTypography(Theme $theme): array
    {
        return [
            'headings' => ['text-4xl', 'text-3xl', 'text-2xl', 'text-xl', 'text-lg'],
            'body' => ['text-base', 'text-sm', 'text-xs'],
            'weights' => ['font-light', 'font-normal', 'font-medium', 'font-semibold', 'font-bold'],
            'colors' => ['text-gray-900', 'text-gray-700', 'text-gray-500']
        ];
    }

    /**
     * Ana rengi tespit et
     */
    private function detectPrimaryColor(array $colors): string
    {
        // CSS değişkenlerinden primary rengi bul
        if (isset($colors['variables'])) {
            foreach ($colors['variables'] as $name => $value) {
                if (Str::contains($name, 'primary')) {
                    return $value;
                }
            }
        }

        // Tailwind'den al
        if (isset($colors['tailwind']['primary'])) {
            return $colors['tailwind']['primary'];
        }

        return '#3B82F6'; // Varsayılan mavi
    }

    /**
     * İkincil rengi tespit et
     */
    private function detectSecondaryColor(array $colors): string
    {
        if (isset($colors['variables'])) {
            foreach ($colors['variables'] as $name => $value) {
                if (Str::contains($name, 'secondary')) {
                    return $value;
                }
            }
        }

        return '#6B7280'; // Varsayılan gri
    }

    /**
     * Vurgu rengini tespit et
     */
    private function detectAccentColor(array $colors): string
    {
        if (isset($colors['variables'])) {
            foreach ($colors['variables'] as $name => $value) {
                if (Str::contains($name, 'accent')) {
                    return $value;
                }
            }
        }

        return '#10B981'; // Varsayılan yeşil
    }

    /**
     * Tema CSS dosyalarını al
     */
    private function getThemeCSSFiles(Theme $theme): array
    {
        $files = [];

        // Public CSS
        $publicPath = public_path('css');
        if (File::exists($publicPath)) {
            $files = array_merge($files, File::glob($publicPath . '/*.css'));
        }

        // Theme specific CSS
        $themePath = resource_path("views/themes/{$theme->theme_name}/assets/css");
        if (File::exists($themePath)) {
            $files = array_merge($files, File::glob($themePath . '/*.css'));
        }

        // Compiled CSS
        $files[] = public_path('build/assets/app.css');

        return $files;
    }

    /**
     * Tema view dosyalarını al
     */
    private function getThemeViewFiles(Theme $theme): array
    {
        $files = [];

        // Theme views
        $themePath = resource_path("views/themes/{$theme->theme_name}");
        if (File::exists($themePath)) {
            $files = File::glob($themePath . '/**/*.blade.php');
        }

        // Layout files
        $layoutPath = resource_path('views/layouts');
        if (File::exists($layoutPath)) {
            $files = array_merge($files, File::glob($layoutPath . '/*.blade.php'));
        }

        return $files;
    }

    /**
     * Tema analizini önizleme formatında döndür
     */
    public function getThemePreview(int $tenantId): array
    {
        $analysis = $this->analyzeTheme($tenantId);

        return [
            'theme_name' => $analysis['theme_info']['name'],
            'framework' => $analysis['framework'],
            'primary_color' => $analysis['color_palette']['primary'],
            'secondary_color' => $analysis['color_palette']['secondary'],
            'font_family' => $analysis['typography']['fonts'][0] ?? 'system-ui',
            'has_dark_mode' => $analysis['theme_info']['has_dark_mode'],
            'components_available' => array_keys(array_filter($analysis['components'])),
            'quick_summary' => $this->generateQuickSummary($analysis)
        ];
    }

    /**
     * JavaScript pattern'lerini çıkar
     */
    private function extractJavaScriptPatterns(Theme $theme): array
    {
        $patterns = [
            'animations' => [],
            'interactions' => [],
            'libraries' => [],
            'custom_functions' => [],
            'event_handlers' => []
        ];

        $jsFiles = $this->getThemeJSFiles($theme);

        foreach ($jsFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // Animation patterns
                if (preg_match_all('/(animate|transition|transform)\([^\)]*\)/i', $content, $animations)) {
                    $patterns['animations'] = array_merge($patterns['animations'], $animations[0]);
                }

                // Event handlers
                if (preg_match_all('/\.(on|addEventListener)\([^\)]*\)/i', $content, $events)) {
                    $patterns['event_handlers'] = array_merge($patterns['event_handlers'], $events[0]);
                }

                // Library usage
                if (Str::contains($content, 'Alpine')) $patterns['libraries'][] = 'Alpine.js';
                if (Str::contains($content, 'jQuery')) $patterns['libraries'][] = 'jQuery';
                if (Str::contains($content, 'gsap')) $patterns['libraries'][] = 'GSAP';
                if (Str::contains($content, 'AOS')) $patterns['libraries'][] = 'AOS';
                if (Str::contains($content, 'Swiper')) $patterns['libraries'][] = 'Swiper';

                // Custom functions
                if (preg_match_all('/function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/i', $content, $functions)) {
                    $patterns['custom_functions'] = array_merge($patterns['custom_functions'], $functions[1]);
                }

                // Modern JS patterns
                if (Str::contains($content, '=>')) $patterns['modern_js'] = true;
                if (Str::contains($content, 'const ')) $patterns['es6'] = true;
                if (Str::contains($content, 'async ')) $patterns['async_await'] = true;
            }
        }

        // Unique values
        $patterns['animations'] = array_unique($patterns['animations']);
        $patterns['event_handlers'] = array_unique($patterns['event_handlers']);
        $patterns['libraries'] = array_unique($patterns['libraries']);
        $patterns['custom_functions'] = array_unique($patterns['custom_functions']);

        return $patterns;
    }

    /**
     * CSS metodolojisini tespit et
     */
    private function detectCSSMethodology(Theme $theme): array
    {
        $methodology = [
            'bem' => false,
            'atomic' => false,
            'utility_first' => false,
            'component_based' => false,
            'naming_convention' => 'unknown'
        ];

        $cssFiles = $this->getThemeCSSFiles($theme);

        foreach ($cssFiles as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // BEM methodology
                if (preg_match('/\.[a-z-]+(__[a-z-]+)?(--[a-z-]+)?/i', $content)) {
                    $methodology['bem'] = true;
                    $methodology['naming_convention'] = 'bem';
                }

                // Utility classes (Tailwind style)
                if (preg_match('/(p|m|w|h|bg|text|flex|grid)-\d+/', $content)) {
                    $methodology['utility_first'] = true;
                    $methodology['atomic'] = true;
                }

                // Component-based
                if (preg_match('/\.(card|button|modal|nav|header|footer)\s*{/', $content)) {
                    $methodology['component_based'] = true;
                }
            }
        }

        return $methodology;
    }

    /**
     * Tema JS dosyalarını al
     */
    private function getThemeJSFiles(Theme $theme): array
    {
        $files = [];

        // Public JS
        $publicPath = public_path('js');
        if (File::exists($publicPath)) {
            $files = array_merge($files, File::glob($publicPath . '/*.js'));
        }

        // Theme specific JS
        $themePath = resource_path("views/themes/{$theme->theme_name}/assets/js");
        if (File::exists($themePath)) {
            $files = array_merge($files, File::glob($themePath . '/*.js'));
        }

        // Compiled JS
        $files[] = public_path('build/assets/app.js');

        return $files;
    }

    /**
     * Tema dosyalarından component pattern'lerini çıkar
     */
    public function extractComponentPatterns(Theme $theme): array
    {
        $patterns = [
            'button_styles' => [],
            'card_structures' => [],
            'form_layouts' => [],
            'navigation_types' => [],
            'content_containers' => [],
            'common_classes' => []
        ];

        $files = array_merge(
            $this->getThemeViewFiles($theme),
            $this->getThemeCSSFiles($theme)
        );

        foreach ($files as $file) {
            if (File::exists($file)) {
                $content = File::get($file);

                // Button patterns
                if (preg_match_all('/<button[^>]*class="([^"]*)"/i', $content, $buttons)) {
                    $patterns['button_styles'] = array_merge($patterns['button_styles'], $buttons[1]);
                }

                // Card patterns
                if (preg_match_all('/class="[^"]*card[^"]*"/i', $content, $cards)) {
                    $patterns['card_structures'] = array_merge($patterns['card_structures'], $cards[0]);
                }

                // Form patterns
                if (preg_match_all('/<form[^>]*class="([^"]*)"/i', $content, $forms)) {
                    $patterns['form_layouts'] = array_merge($patterns['form_layouts'], $forms[1]);
                }

                // Container patterns
                if (preg_match_all('/class="[^"]*container[^"]*"/i', $content, $containers)) {
                    $patterns['content_containers'] = array_merge($patterns['content_containers'], $containers[0]);
                }

                // Common utility classes
                if (preg_match_all('/class="([^"]*)"/i', $content, $allClasses)) {
                    foreach ($allClasses[1] as $classString) {
                        $classes = explode(' ', $classString);
                        $patterns['common_classes'] = array_merge($patterns['common_classes'], $classes);
                    }
                }
            }
        }

        // Unique and filter
        foreach ($patterns as $key => $value) {
            if (is_array($value)) {
                $patterns[$key] = array_unique(array_filter($value));
            }
        }

        return $patterns;
    }

    /**
     * Tema için AI context oluştur
     */
    public function buildAIContext(int $tenantId): array
    {
        $analysis = $this->analyzeTheme($tenantId);
        $tenant = Tenant::find($tenantId);
        $theme = Theme::find($tenant->theme_id);
        $componentPatterns = $this->extractComponentPatterns($theme);

        return [
            'theme_info' => $analysis['theme_info'],
            'color_system' => [
                'primary' => $analysis['color_palette']['primary'],
                'secondary' => $analysis['color_palette']['secondary'],
                'accent' => $analysis['color_palette']['accent'] ?? '#10B981',
                'variables' => $analysis['color_palette']['variables'] ?? [],
                'tailwind_colors' => $analysis['color_palette']['tailwind'] ?? []
            ],
            'typography_system' => [
                'fonts' => $analysis['typography']['fonts'],
                'size_scale' => $analysis['typography']['sizes'],
                'weight_scale' => $analysis['typography']['weights'],
                'line_heights' => $analysis['typography']['line_heights']
            ],
            'spacing_system' => [
                'padding_scale' => $analysis['spacing']['padding'],
                'margin_scale' => $analysis['spacing']['margin'],
                'container_width' => $analysis['spacing']['container_width'],
                'section_spacing' => $analysis['spacing']['section_spacing']
            ],
            'component_patterns' => $componentPatterns,
            'javascript_patterns' => $analysis['theme_info']['js_patterns'],
            'css_methodology' => $analysis['theme_info']['css_methodology'],
            'framework_specifics' => [
                'type' => $analysis['framework'],
                'dark_mode' => $analysis['theme_info']['has_dark_mode'],
                'responsive' => $analysis['theme_info']['responsive']
            ],
            'custom_features' => [
                'animations' => $analysis['custom_css']['animations'] ?? [],
                'transitions' => $analysis['custom_css']['transitions'] ?? [],
                'root_variables' => $analysis['custom_css']['root_vars'] ?? ''
            ]
        ];
    }

    /**
     * Hızlı özet oluştur
     */
    private function generateQuickSummary(array $analysis): string
    {
        $framework = ucfirst($analysis['framework']);
        $darkMode = $analysis['theme_info']['has_dark_mode'] ? 'Dark mode destekli' : 'Light mode';
        $components = count(array_filter($analysis['components']));

        return "{$framework} tabanlı, {$darkMode}, {$components} hazır component";
    }
}