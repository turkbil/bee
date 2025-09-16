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
            'rtl_support' => false
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