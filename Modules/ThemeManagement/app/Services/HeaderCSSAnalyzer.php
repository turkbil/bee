<?php

declare(strict_types=1);

namespace Modules\ThemeManagement\app\Services;

use Illuminate\Support\Facades\Log;

/**
 * Header CSS Analyzer Service
 *
 * Mevcut sayfanın header CSS'ini analiz ederek AI'ya entegre eder.
 * Bu sayede AI ürettiği içerik mevcut sayfa tasarımına uyumlu olur.
 */
class HeaderCSSAnalyzer
{
    /**
     * Mevcut sayfa CSS'ini analiz et
     */
    public function analyzeCurrentPageCSS(?string $currentPageUrl = null): array
    {
        try {
            Log::info('🎨 Header CSS analizi başlıyor...', ['url' => $currentPageUrl]);

            // Tema dosyalarından CSS analizi
            $themeCSS = $this->analyzeThemeCSS();

            // Layout dosyalarından CSS analizi
            $layoutCSS = $this->analyzeLayoutCSS();

            // Component dosyalarından CSS analizi
            $componentCSS = $this->analyzeComponentCSS();

            // Tüm analizi birleştir
            $analysis = [
                'colors' => $this->extractColors($themeCSS, $layoutCSS, $componentCSS),
                'typography' => $this->extractTypography($themeCSS, $layoutCSS, $componentCSS),
                'spacing' => $this->extractSpacing($themeCSS, $layoutCSS, $componentCSS),
                'components' => $this->extractComponents($themeCSS, $layoutCSS, $componentCSS),
                'layout_patterns' => $this->extractLayoutPatterns($themeCSS, $layoutCSS, $componentCSS),
                'animations' => $this->extractAnimations($themeCSS, $layoutCSS, $componentCSS),
                'brand_elements' => $this->extractBrandElements($themeCSS, $layoutCSS, $componentCSS)
            ];

            Log::info('✅ Header CSS analizi tamamlandı', [
                'colors_found' => count($analysis['colors']),
                'typography_patterns' => count($analysis['typography']),
                'spacing_patterns' => count($analysis['spacing']),
                'components_found' => count($analysis['components'])
            ]);

            return $analysis;

        } catch (\Exception $e) {
            Log::error('❌ Header CSS analiz hatası: ' . $e->getMessage());

            return [
                'colors' => [],
                'typography' => [],
                'spacing' => [],
                'components' => [],
                'layout_patterns' => [],
                'animations' => [],
                'brand_elements' => []
            ];
        }
    }

    /**
     * Tema CSS dosyalarını analiz et
     */
    private function analyzeThemeCSS(): string
    {
        $themeCSS = '';

        // Ana tema CSS dosyalarını bul
        $cssFiles = [
            storage_path('app/tenant' . tenant('id') . '/public/assets/css/app.css'),
            storage_path('app/tenant' . tenant('id') . '/public/css/app.css'),
            storage_path('app/tenant' . tenant('id') . '/public/css/style.css'),
            storage_path('app/tenant' . tenant('id') . '/public/assets/css/theme.css'),
            storage_path('app/tenant' . tenant('id') . '/public/css/custom.css')
        ];

        foreach ($cssFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $themeCSS .= "\n" . $content;
                Log::info('📄 CSS dosyası okundu: ' . basename($file), ['size' => strlen($content)]);
            }
        }

        // Public klasöründeki diğer CSS dosyalarını tara
        $publicPath = storage_path('app/tenant' . tenant('id') . '/public');
        if (is_dir($publicPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicPath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'css') {
                    $content = file_get_contents($file->getPathname());
                    $themeCSS .= "\n" . $content;
                    Log::info('📄 Ek CSS dosyası okundu: ' . $file->getFilename(), ['size' => strlen($content)]);
                }
            }
        }

        return $themeCSS;
    }

    /**
     * Layout dosyalarını analiz et
     */
    private function analyzeLayoutCSS(): string
    {
        $layoutCSS = '';

        // Layout Blade dosyalarından CSS çıkar
        $layoutFiles = [
            resource_path('views/layouts/app.blade.php'),
            resource_path('views/layouts/master.blade.php'),
            resource_path('views/layouts/frontend.blade.php'),
            resource_path('views/layouts/admin.blade.php')
        ];

        foreach ($layoutFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                // <style> tagları içindeki CSS'i çıkar
                preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $content, $matches);
                foreach ($matches[1] as $css) {
                    $layoutCSS .= "\n" . $css;
                }

                // Tailwind class'larını çıkar
                preg_match_all('/class=["\']([^"\']+)["\']/', $content, $classMatches);
                foreach ($classMatches[1] as $classes) {
                    $layoutCSS .= "\n/* Layout Classes: $classes */";
                }

                Log::info('📄 Layout dosyası analiz edildi: ' . basename($file));
            }
        }

        return $layoutCSS;
    }

    /**
     * Component dosyalarını analiz et
     */
    private function analyzeComponentCSS(): string
    {
        $componentCSS = '';

        // Livewire component'lerini analiz et
        $componentDirs = [
            app_path('Http/Livewire'),
            base_path('Modules/*/resources/views/livewire'),
            resource_path('views/components'),
            resource_path('views/livewire')
        ];

        foreach ($componentDirs as $dir) {
            if (is_dir($dir)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dir)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'php') {
                        $content = file_get_contents($file->getPathname());

                        // Blade template içindeki CSS'i çıkar
                        preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $content, $matches);
                        foreach ($matches[1] as $css) {
                            $componentCSS .= "\n" . $css;
                        }

                        // Tailwind class'larını çıkar
                        preg_match_all('/class=["\']([^"\']+)["\']/', $content, $classMatches);
                        foreach ($classMatches[1] as $classes) {
                            $componentCSS .= "\n/* Component Classes: $classes */";
                        }
                    }
                }
            }
        }

        return $componentCSS;
    }

    /**
     * Renk paletini çıkar
     */
    private function extractColors(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $colors = [];

        // Hex renklerini çıkar
        preg_match_all('/#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})/', $allCSS, $hexMatches);
        foreach ($hexMatches[0] as $hex) {
            $colors['hex'][] = $hex;
        }

        // RGB renklerini çıkar
        preg_match_all('/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/', $allCSS, $rgbMatches);
        for ($i = 0; $i < count($rgbMatches[0]); $i++) {
            $colors['rgb'][] = "rgb({$rgbMatches[1][$i]}, {$rgbMatches[2][$i]}, {$rgbMatches[3][$i]})";
        }

        // CSS custom properties (variables) çıkar
        preg_match_all('/--[\w-]+:\s*([^;]+);/', $allCSS, $varMatches);
        foreach ($varMatches[0] as $variable) {
            $colors['variables'][] = trim($variable);
        }

        // Tailwind renk class'larını çıkar
        preg_match_all('/(text|bg|border)-([\w-]+)-([\d]+)/', $allCSS, $tailwindMatches);
        for ($i = 0; $i < count($tailwindMatches[0]); $i++) {
            $colors['tailwind'][] = $tailwindMatches[0][$i];
        }

        return array_map('array_unique', $colors);
    }

    /**
     * Typography sistemini çıkar
     */
    private function extractTypography(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $typography = [];

        // Font family'leri çıkar
        preg_match_all('/font-family:\s*([^;]+);/', $allCSS, $fontMatches);
        foreach ($fontMatches[1] as $font) {
            $typography['fonts'][] = trim($font, '"\'');
        }

        // Font size'ları çıkar
        preg_match_all('/font-size:\s*([^;]+);/', $allCSS, $sizeMatches);
        foreach ($sizeMatches[1] as $size) {
            $typography['sizes'][] = trim($size);
        }

        // Tailwind typography class'larını çıkar
        preg_match_all('/(text)-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl|8xl|9xl)/', $allCSS, $tailwindTextMatches);
        foreach ($tailwindTextMatches[0] as $textClass) {
            $typography['tailwind_sizes'][] = $textClass;
        }

        // Font weight'leri çıkar
        preg_match_all('/(font)-(thin|extralight|light|normal|medium|semibold|bold|extrabold|black)/', $allCSS, $weightMatches);
        foreach ($weightMatches[0] as $weight) {
            $typography['weights'][] = $weight;
        }

        return array_map('array_unique', $typography);
    }

    /**
     * Spacing paternlerini çıkar
     */
    private function extractSpacing(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $spacing = [];

        // Tailwind spacing class'larını çıkar
        preg_match_all('/(p|m|px|py|pl|pr|pt|pb|mx|my|ml|mr|mt|mb)-([\d]+)/', $allCSS, $spacingMatches);
        foreach ($spacingMatches[0] as $spacingClass) {
            $spacing['tailwind'][] = $spacingClass;
        }

        // Gap class'larını çıkar
        preg_match_all('/gap-([\d]+)/', $allCSS, $gapMatches);
        foreach ($gapMatches[0] as $gap) {
            $spacing['gaps'][] = $gap;
        }

        // Space-y ve space-x class'larını çıkar
        preg_match_all('/space-(x|y)-([\d]+)/', $allCSS, $spaceMatches);
        foreach ($spaceMatches[0] as $space) {
            $spacing['space'][] = $space;
        }

        return array_map('array_unique', $spacing);
    }

    /**
     * Component paternlerini çıkar
     */
    private function extractComponents(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $components = [];

        // Button pattern'lerini çıkar
        preg_match_all('/\.btn[^{]*\{[^}]+\}/', $allCSS, $btnMatches);
        foreach ($btnMatches[0] as $btn) {
            $components['buttons'][] = $btn;
        }

        // Card pattern'lerini çıkar
        preg_match_all('/\.card[^{]*\{[^}]+\}/', $allCSS, $cardMatches);
        foreach ($cardMatches[0] as $card) {
            $components['cards'][] = $card;
        }

        // Tailwind component class'larını çıkar
        preg_match_all('/(rounded|shadow|border)-([\w-]+)/', $allCSS, $componentMatches);
        foreach ($componentMatches[0] as $component) {
            $components['tailwind'][] = $component;
        }

        return array_map('array_unique', $components);
    }

    /**
     * Layout paternlerini çıkar
     */
    private function extractLayoutPatterns(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $patterns = [];

        // Grid pattern'lerini çıkar
        preg_match_all('/grid-(cols|rows)-([\d]+)/', $allCSS, $gridMatches);
        foreach ($gridMatches[0] as $grid) {
            $patterns['grid'][] = $grid;
        }

        // Flex pattern'lerini çıkar
        preg_match_all('/(flex|items|justify)-([\w-]+)/', $allCSS, $flexMatches);
        foreach ($flexMatches[0] as $flex) {
            $patterns['flex'][] = $flex;
        }

        // Container pattern'lerini çıkar
        preg_match_all('/container[^{]*\{[^}]+\}/', $allCSS, $containerMatches);
        foreach ($containerMatches[0] as $container) {
            $patterns['containers'][] = $container;
        }

        return array_map('array_unique', $patterns);
    }

    /**
     * Animation paternlerini çıkar
     */
    private function extractAnimations(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $animations = [];

        // Transition class'larını çıkar
        preg_match_all('/transition-([\w-]+)/', $allCSS, $transitionMatches);
        foreach ($transitionMatches[0] as $transition) {
            $animations['transitions'][] = $transition;
        }

        // Animation class'larını çıkar
        preg_match_all('/animate-([\w-]+)/', $allCSS, $animateMatches);
        foreach ($animateMatches[0] as $animate) {
            $animations['animations'][] = $animate;
        }

        // Hover state'leri çıkar
        preg_match_all('/hover:([\w-]+)/', $allCSS, $hoverMatches);
        foreach ($hoverMatches[0] as $hover) {
            $animations['hover'][] = $hover;
        }

        return array_map('array_unique', $animations);
    }

    /**
     * Brand elementlerini çıkar
     */
    private function extractBrandElements(string $themeCSS, string $layoutCSS, string $componentCSS): array
    {
        $allCSS = $themeCSS . $layoutCSS . $componentCSS;
        $brand = [];

        // Logo ve brand class'larını çıkar
        preg_match_all('/\.(logo|brand|header|navbar)[^{]*\{[^}]+\}/', $allCSS, $brandMatches);
        foreach ($brandMatches[0] as $brandElement) {
            $brand['elements'][] = $brandElement;
        }

        // Primary/Secondary renk tanımlarını çıkar
        preg_match_all('/(primary|secondary|accent)-([\w-]+)/', $allCSS, $colorMatches);
        foreach ($colorMatches[0] as $color) {
            $brand['colors'][] = $color;
        }

        return array_map('array_unique', $brand);
    }

    /**
     * CSS analiz sonuçlarını AI prompt'a dönüştür
     */
    public function buildCSSContextForAI(array $cssAnalysis): string
    {
        $context = "MEVCUT SAYFA CSS ANALİZİ:\n\n";

        // Renk sistemi
        if (!empty($cssAnalysis['colors'])) {
            $context .= "RENK PALETİ:\n";

            if (!empty($cssAnalysis['colors']['hex'])) {
                $hexColors = array_slice($cssAnalysis['colors']['hex'], 0, 10);
                $context .= "- Hex Renkler: " . implode(', ', $hexColors) . "\n";
            }

            if (!empty($cssAnalysis['colors']['tailwind'])) {
                $tailwindColors = array_slice($cssAnalysis['colors']['tailwind'], 0, 15);
                $context .= "- Tailwind Renkler: " . implode(', ', $tailwindColors) . "\n";
            }

            if (!empty($cssAnalysis['colors']['variables'])) {
                $variables = array_slice($cssAnalysis['colors']['variables'], 0, 10);
                $context .= "- CSS Variables: " . implode(', ', $variables) . "\n";
            }

            $context .= "\n";
        }

        // Typography sistemi
        if (!empty($cssAnalysis['typography'])) {
            $context .= "TYPOGRAPHİ SİSTEMİ:\n";

            if (!empty($cssAnalysis['typography']['fonts'])) {
                $fonts = array_slice($cssAnalysis['typography']['fonts'], 0, 5);
                $context .= "- Font Families: " . implode(', ', $fonts) . "\n";
            }

            if (!empty($cssAnalysis['typography']['tailwind_sizes'])) {
                $sizes = array_slice($cssAnalysis['typography']['tailwind_sizes'], 0, 10);
                $context .= "- Tailwind Sizes: " . implode(', ', $sizes) . "\n";
            }

            if (!empty($cssAnalysis['typography']['weights'])) {
                $weights = array_slice($cssAnalysis['typography']['weights'], 0, 8);
                $context .= "- Font Weights: " . implode(', ', $weights) . "\n";
            }

            $context .= "\n";
        }

        // Spacing sistemi
        if (!empty($cssAnalysis['spacing'])) {
            $context .= "SPACING SİSTEMİ:\n";

            if (!empty($cssAnalysis['spacing']['tailwind'])) {
                $spacing = array_slice($cssAnalysis['spacing']['tailwind'], 0, 15);
                $context .= "- Mevcut Spacing: " . implode(', ', $spacing) . "\n";
            }

            if (!empty($cssAnalysis['spacing']['gaps'])) {
                $gaps = array_slice($cssAnalysis['spacing']['gaps'], 0, 8);
                $context .= "- Gap Classes: " . implode(', ', $gaps) . "\n";
            }

            $context .= "\n";
        }

        // Component paternleri
        if (!empty($cssAnalysis['components'])) {
            $context .= "COMPONENT PATTERN'LERİ:\n";

            if (!empty($cssAnalysis['components']['tailwind'])) {
                $components = array_slice($cssAnalysis['components']['tailwind'], 0, 15);
                $context .= "- Tailwind Components: " . implode(', ', $components) . "\n";
            }

            $context .= "\n";
        }

        // Layout paternleri
        if (!empty($cssAnalysis['layout_patterns'])) {
            $context .= "LAYOUT PATTERN'LERİ:\n";

            if (!empty($cssAnalysis['layout_patterns']['grid'])) {
                $grids = array_slice($cssAnalysis['layout_patterns']['grid'], 0, 8);
                $context .= "- Grid Patterns: " . implode(', ', $grids) . "\n";
            }

            if (!empty($cssAnalysis['layout_patterns']['flex'])) {
                $flex = array_slice($cssAnalysis['layout_patterns']['flex'], 0, 10);
                $context .= "- Flex Patterns: " . implode(', ', $flex) . "\n";
            }

            $context .= "\n";
        }

        // Animation sistemi
        if (!empty($cssAnalysis['animations'])) {
            $context .= "ANİMASYON SİSTEMİ:\n";

            if (!empty($cssAnalysis['animations']['transitions'])) {
                $transitions = array_slice($cssAnalysis['animations']['transitions'], 0, 8);
                $context .= "- Transitions: " . implode(', ', $transitions) . "\n";
            }

            if (!empty($cssAnalysis['animations']['hover'])) {
                $hover = array_slice($cssAnalysis['animations']['hover'], 0, 10);
                $context .= "- Hover Effects: " . implode(', ', $hover) . "\n";
            }

            $context .= "\n";
        }

        $context .= "ÖNEMLİ TALİMAT: Yukarıdaki mevcut CSS pattern'lerini ve class'ları MUTLAKA kullan!\n";
        $context .= "Mevcut tasarım diline uygun, tutarlı bir içerik üret.\n";
        $context .= "Yeni renkler icat etme, mevcut renk paletini kullan.\n";
        $context .= "Mevcut spacing ve typography scale'ini takip et.\n\n";

        return $context;
    }
}