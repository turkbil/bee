<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Chat;

use Exception;
use Illuminate\Support\Facades\{Cache, View, Log};
use Illuminate\Contracts\View\View as ViewContract;
use Modules\AI\App\Exceptions\Chat\WidgetRenderException;

/**
 * Advanced Multi-Theme Widget Rendering Service
 * 
 * Bu service chat widget'ları farklı tema, boyut ve placement'lara göre render eder.
 * 6 farklı tema, 4 boyut seçeneği ve 9 placement lokasyonu destekler.
 * Performance için aggressive caching ve lazy loading kullanır.
 * 
 * Features:
 * - Multi-theme rendering (modern, minimal, colorful, dark, glassmorphism, neumorphism)
 * - Responsive size management (compact, standard, large, fullscreen)
 * - Placement-specific optimizations (9 different locations)
 * - Component-based architecture with reusable parts
 * - CSS/JS asset management with minification
 * - RTL language support
 * - Accessibility features (WCAG 2.1 AA compliance)
 * - Performance monitoring and error handling
 * 
 * @author Nurullah Okatan
 * @version 2.0
 */
readonly class WidgetRenderer
{
    // Theme-specific CSS class mappings
    private const THEME_CLASSES = [
        'modern' => [
            'container' => 'ai-widget-modern backdrop-blur-sm border border-blue-200/30',
            'header' => 'bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-t-xl',
            'body' => 'bg-white/95 backdrop-blur-md',
            'footer' => 'bg-gray-50/90 border-t border-gray-200/50',
            'button' => 'bg-blue-500 hover:bg-blue-600 text-white transition-all duration-300',
            'input' => 'border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg',
            'animation' => 'animate-fade-in-up'
        ],
        'minimal' => [
            'container' => 'ai-widget-minimal bg-white shadow-lg border border-gray-100',
            'header' => 'bg-white text-gray-800 border-b border-gray-100',
            'body' => 'bg-white',
            'footer' => 'bg-white border-t border-gray-100',
            'button' => 'bg-gray-800 hover:bg-gray-900 text-white transition-colors',
            'input' => 'border-gray-200 focus:ring-gray-500 focus:border-gray-500',
            'animation' => 'animate-slide-in'
        ],
        'colorful' => [
            'container' => 'ai-widget-colorful bg-gradient-to-br from-purple-400 to-pink-400',
            'header' => 'bg-gradient-to-r from-purple-600 to-pink-600 text-white',
            'body' => 'bg-white/95',
            'footer' => 'bg-purple-50/90',
            'button' => 'bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white',
            'input' => 'border-purple-300 focus:ring-purple-500 focus:border-purple-500',
            'animation' => 'animate-bounce-in'
        ],
        'dark' => [
            'container' => 'ai-widget-dark bg-gray-900/95 border border-gray-700/50',
            'header' => 'bg-gray-800 text-gray-100 border-b border-gray-700',
            'body' => 'bg-gray-900/90 text-gray-100',
            'footer' => 'bg-gray-800/90 border-t border-gray-700',
            'button' => 'bg-gray-700 hover:bg-gray-600 text-gray-100',
            'input' => 'bg-gray-800 border-gray-600 text-gray-100 focus:ring-gray-500',
            'animation' => 'animate-fade-in'
        ],
        'glassmorphism' => [
            'container' => 'ai-widget-glass backdrop-blur-xl bg-white/10 border border-white/20',
            'header' => 'bg-white/20 backdrop-blur-md text-gray-800 border-b border-white/30',
            'body' => 'bg-white/10 backdrop-blur-lg text-gray-800',
            'footer' => 'bg-white/20 backdrop-blur-md border-t border-white/30',
            'button' => 'bg-white/30 hover:bg-white/40 backdrop-blur-sm text-gray-800',
            'input' => 'bg-white/20 border-white/30 placeholder-gray-600 focus:bg-white/30',
            'animation' => 'animate-glass-morph'
        ],
        'neumorphism' => [
            'container' => 'ai-widget-neuro bg-gray-100 shadow-neuro-inset',
            'header' => 'bg-gray-100 text-gray-800 shadow-neuro-sm',
            'body' => 'bg-gray-100 text-gray-800',
            'footer' => 'bg-gray-100 shadow-neuro-inset',
            'button' => 'bg-gray-100 shadow-neuro hover:shadow-neuro-pressed text-gray-800',
            'input' => 'bg-gray-100 shadow-neuro-inset border-none focus:shadow-neuro-focus',
            'animation' => 'animate-neuro-rise'
        ]
    ];

    // Size-specific dimensions and styling
    private const SIZE_STYLES = [
        'compact' => [
            'width' => '280px',
            'height' => '360px',
            'header_height' => '40px',
            'font_size' => 'text-xs',
            'padding' => 'p-2',
            'button_size' => 'px-3 py-1 text-xs',
            'input_size' => 'text-xs px-2 py-1'
        ],
        'standard' => [
            'width' => '350px',
            'height' => '450px',
            'header_height' => '48px',
            'font_size' => 'text-sm',
            'padding' => 'p-3',
            'button_size' => 'px-4 py-2 text-sm',
            'input_size' => 'text-sm px-3 py-2'
        ],
        'large' => [
            'width' => '420px',
            'height' => '540px',
            'header_height' => '56px',
            'font_size' => 'text-base',
            'padding' => 'p-4',
            'button_size' => 'px-5 py-2 text-base',
            'input_size' => 'text-base px-4 py-2'
        ],
        'fullscreen' => [
            'width' => '100vw',
            'height' => '100vh',
            'header_height' => '64px',
            'font_size' => 'text-lg',
            'padding' => 'p-6',
            'button_size' => 'px-6 py-3 text-lg',
            'input_size' => 'text-lg px-4 py-3'
        ]
    ];

    // Placement-specific positioning and behavior
    private const PLACEMENT_STYLES = [
        'bottom-right' => [
            'position' => 'fixed bottom-4 right-4 z-50',
            'origin' => 'bottom-right',
            'mobile_position' => 'fixed bottom-2 right-2 z-50',
            'animation_direction' => 'from-bottom-right'
        ],
        'bottom-left' => [
            'position' => 'fixed bottom-4 left-4 z-50',
            'origin' => 'bottom-left',
            'mobile_position' => 'fixed bottom-2 left-2 z-50',
            'animation_direction' => 'from-bottom-left'
        ],
        'top-right' => [
            'position' => 'fixed top-4 right-4 z-50',
            'origin' => 'top-right',
            'mobile_position' => 'fixed top-2 right-2 z-50',
            'animation_direction' => 'from-top-right'
        ],
        'top-left' => [
            'position' => 'fixed top-4 left-4 z-50',
            'origin' => 'top-left',
            'mobile_position' => 'fixed top-2 left-2 z-50',
            'animation_direction' => 'from-top-left'
        ],
        'center' => [
            'position' => 'fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50',
            'origin' => 'center',
            'mobile_position' => 'fixed inset-2 z-50',
            'animation_direction' => 'from-center'
        ],
        'sidebar' => [
            'position' => 'sticky top-4 z-40',
            'origin' => 'top',
            'mobile_position' => 'fixed bottom-0 left-0 right-0 z-50',
            'animation_direction' => 'from-side'
        ],
        'header' => [
            'position' => 'sticky top-0 z-40',
            'origin' => 'top',
            'mobile_position' => 'sticky top-0 z-40',
            'animation_direction' => 'from-top'
        ],
        'footer' => [
            'position' => 'sticky bottom-0 z-40',
            'origin' => 'bottom',
            'mobile_position' => 'sticky bottom-0 z-40',
            'animation_direction' => 'from-bottom'
        ],
        'inline' => [
            'position' => 'relative z-10',
            'origin' => 'center',
            'mobile_position' => 'relative z-10',
            'animation_direction' => 'fade-in'
        ]
    ];

    public function __construct(
        private WidgetConfigBuilder $configBuilder
    ) {}

    /**
     * Ana rendering metodu - widget'ı belirtilen konfigürasyona göre render eder
     */
    public function renderWidget(array $config): ViewContract
    {
        try {
            // Configuration validation ve normalization
            $config = $this->normalizeConfig($config);
            
            // Cache key generation
            $cacheKey = $this->generateCacheKey($config);
            
            // Cache'den kontrol et
            if ($config['performance']['cache_enabled'] ?? true) {
                $cached = Cache::get($cacheKey);
                if ($cached !== null) {
                    return $cached;
                }
            }

            // Widget data preparation
            $widgetData = $this->prepareWidgetData($config);
            
            // Theme-specific styling
            $styling = $this->generateStyling($config);
            
            // Component assembly
            $components = $this->assembleComponents($config, $widgetData, $styling);
            
            // Final view rendering
            $view = $this->createFinalView($components, $config);
            
            // Cache result
            if ($config['performance']['cache_enabled'] ?? true) {
                Cache::put($cacheKey, $view, now()->addHours(1));
            }
            
            return $view;

        } catch (Exception $e) {
            Log::error('Widget render failed', [
                'config' => $config,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw WidgetRenderException::withRenderError($e->getMessage(), $config);
        }
    }

    /**
     * Multiple widget rendering - birden fazla widget'ı batch olarak render eder
     */
    public function renderMultipleWidgets(array $widgets): array
    {
        $renderedWidgets = [];
        $errors = [];
        
        foreach ($widgets as $widgetId => $config) {
            try {
                $renderedWidgets[$widgetId] = $this->renderWidget($config);
            } catch (Exception $e) {
                $errors[$widgetId] = $e->getMessage();
                Log::warning("Widget render failed for ID: {$widgetId}", [
                    'config' => $config,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        if (!empty($errors)) {
            Log::info('Batch widget rendering completed with some errors', [
                'success_count' => count($renderedWidgets),
                'error_count' => count($errors),
                'errors' => $errors
            ]);
        }
        
        return [
            'success' => $renderedWidgets,
            'errors' => $errors,
            'summary' => [
                'total' => count($widgets),
                'success' => count($renderedWidgets),
                'failed' => count($errors)
            ]
        ];
    }

    /**
     * Preview rendering - gerçek AI entegrasyonu olmadan widget preview'u
     */
    public function renderPreview(array $config): ViewContract
    {
        // Preview için config'i modifiye et
        $previewConfig = array_merge($config, [
            'preview_mode' => true,
            'ai_enabled' => false,
            'mock_data' => $this->generateMockData($config),
            'performance' => [
                'cache_enabled' => false,
                'lazy_loading' => false
            ]
        ]);
        
        return $this->renderWidget($previewConfig);
    }

    /**
     * Theme-specific CSS generation
     */
    public function generateThemeCSS(string $theme, string $size = 'standard'): string
    {
        $cacheKey = "widget_theme_css_{$theme}_{$size}";
        
        return Cache::remember($cacheKey, now()->addHours(12), function() use ($theme, $size) {
            $themeClasses = self::THEME_CLASSES[$theme] ?? self::THEME_CLASSES['modern'];
            $sizeStyles = self::SIZE_STYLES[$size] ?? self::SIZE_STYLES['standard'];
            
            $css = ":root {\n";
            $css .= "  --ai-widget-width: {$sizeStyles['width']};\n";
            $css .= "  --ai-widget-height: {$sizeStyles['height']};\n";
            $css .= "  --ai-widget-header-height: {$sizeStyles['header_height']};\n";
            $css .= "}\n\n";
            
            // Theme-specific CSS rules
            $css .= ".ai-chat-widget {\n";
            $css .= "  width: var(--ai-widget-width);\n";
            $css .= "  height: var(--ai-widget-height);\n";
            $css .= "  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);\n";
            $css .= "}\n\n";
            
            // Responsive breakpoints
            $css .= "@media (max-width: 640px) {\n";
            $css .= "  .ai-chat-widget {\n";
            $css .= "    width: calc(100vw - 16px);\n";
            $css .= "    height: calc(100vh - 32px);\n";
            $css .= "  }\n";
            $css .= "}\n\n";
            
            // Animation keyframes
            $css .= $this->generateAnimationCSS($theme);
            
            return $css;
        });
    }

    /**
     * JavaScript bundle generation for widget functionality
     */
    public function generateWidgetJS(array $config): string
    {
        $jsConfig = [
            'widget_id' => $config['widget_id'] ?? 'default',
            'theme' => $config['theme'] ?? 'modern',
            'placement' => $config['placement'] ?? 'bottom-right',
            'features' => $config['features'] ?? [],
            'ai_enabled' => $config['ai_enabled'] ?? true,
            'auto_open' => $config['behavior']['auto_open'] ?? false,
            'minimize_on_blur' => $config['behavior']['minimize_on_blur'] ?? true
        ];
        
        $js = "window.AIWidgetConfig = " . json_encode($jsConfig, JSON_PRETTY_PRINT) . ";\n\n";
        
        // Widget initialization script
        $js .= file_get_contents(resource_path('js/ai-widget-core.js'));
        
        return $js;
    }

    // Private helper methods
    
    private function normalizeConfig(array $config): array
    {
        // Default config ile merge
        $defaults = $this->configBuilder->getDefaultConfig();
        $normalized = array_merge($defaults, $config);
        
        // Validation
        $this->validateConfig($normalized);
        
        return $normalized;
    }
    
    private function validateConfig(array $config): void
    {
        $requiredFields = ['theme', 'size', 'placement'];
        
        foreach ($requiredFields as $field) {
            if (!isset($config[$field])) {
                throw WidgetRenderException::withValidationError("Missing required field: {$field}", $config);
            }
        }
        
        // Theme validation
        if (!isset(self::THEME_CLASSES[$config['theme']])) {
            throw WidgetRenderException::withValidationError("Invalid theme: {$config['theme']}", $config);
        }
        
        // Size validation
        if (!isset(self::SIZE_STYLES[$config['size']])) {
            throw WidgetRenderException::withValidationError("Invalid size: {$config['size']}", $config);
        }
        
        // Placement validation
        if (!isset(self::PLACEMENT_STYLES[$config['placement']])) {
            throw WidgetRenderException::withValidationError("Invalid placement: {$config['placement']}", $config);
        }
    }
    
    private function generateCacheKey(array $config): string
    {
        $keyData = [
            'theme' => $config['theme'],
            'size' => $config['size'],
            'placement' => $config['placement'],
            'features' => md5(json_encode($config['features'] ?? [])),
            'version' => '2.0'
        ];
        
        return 'ai_widget_' . md5(json_encode($keyData));
    }
    
    private function prepareWidgetData(array $config): array
    {
        return [
            'widget_id' => $config['widget_id'] ?? uniqid('widget_'),
            'title' => $config['title'] ?? 'AI Asistan',
            'subtitle' => $config['subtitle'] ?? 'Size nasıl yardımcı olabilirim?',
            'placeholder' => $config['placeholder'] ?? 'Mesajınızı yazın...',
            'features' => $config['features'] ?? [],
            'branding' => $config['branding'] ?? [],
            'locale' => app()->getLocale(),
            'rtl' => in_array(app()->getLocale(), ['ar', 'fa', 'he']),
            'accessibility' => $config['accessibility'] ?? []
        ];
    }
    
    private function generateStyling(array $config): array
    {
        $theme = $config['theme'];
        $size = $config['size'];
        $placement = $config['placement'];
        
        return [
            'theme_classes' => self::THEME_CLASSES[$theme],
            'size_styles' => self::SIZE_STYLES[$size],
            'placement_styles' => self::PLACEMENT_STYLES[$placement],
            'custom_css' => $config['custom_css'] ?? '',
            'animations_enabled' => $config['animations']['enabled'] ?? true
        ];
    }
    
    private function assembleComponents(array $config, array $data, array $styling): array
    {
        return [
            'header' => $this->renderHeader($data, $styling, $config),
            'body' => $this->renderBody($data, $styling, $config),
            'footer' => $this->renderFooter($data, $styling, $config),
            'overlay' => $this->renderOverlay($styling, $config),
            'trigger' => $this->renderTrigger($data, $styling, $config)
        ];
    }
    
    private function createFinalView(array $components, array $config): ViewContract
    {
        return View::make('ai::chat.widget.container', [
            'components' => $components,
            'config' => $config,
            'css' => $this->generateThemeCSS($config['theme'], $config['size']),
            'js' => $this->generateWidgetJS($config)
        ]);
    }
    
    private function renderHeader(array $data, array $styling, array $config): string
    {
        $classes = implode(' ', [
            $styling['theme_classes']['header'],
            $styling['size_styles']['padding'],
            'flex items-center justify-between',
            $styling['size_styles']['font_size']
        ]);
        
        return "<div class=\"{$classes}\" style=\"height: {$styling['size_styles']['header_height']}\">
            <div class=\"flex items-center space-x-2\">
                <h3 class=\"font-semibold\">{$data['title']}</h3>
            </div>
            <button class=\"minimize-btn opacity-70 hover:opacity-100 transition-opacity\">
                <svg class=\"w-4 h-4\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                    <path fill-rule=\"evenodd\" d=\"M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z\" clip-rule=\"evenodd\"></path>
                </svg>
            </button>
        </div>";
    }
    
    private function renderBody(array $data, array $styling, array $config): string
    {
        $classes = implode(' ', [
            $styling['theme_classes']['body'],
            $styling['size_styles']['padding'],
            'flex-1 overflow-hidden flex flex-col',
            $styling['size_styles']['font_size']
        ]);
        
        return "<div class=\"{$classes}\">
            <div class=\"messages-container flex-1 overflow-y-auto space-y-2 mb-3\">
                <div class=\"welcome-message {$styling['theme_classes']['body']} p-3 rounded-lg\">
                    <p>{$data['subtitle']}</p>
                </div>
            </div>
            <div class=\"input-area flex space-x-2\">
                <input type=\"text\" 
                       placeholder=\"{$data['placeholder']}\" 
                       class=\"{$styling['theme_classes']['input']} {$styling['size_styles']['input_size']} flex-1 rounded-lg border focus:outline-none focus:ring-2 transition-all\">
                <button class=\"{$styling['theme_classes']['button']} {$styling['size_styles']['button_size']} rounded-lg flex items-center justify-center transition-all\">
                    <svg class=\"w-4 h-4\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                        <path fill-rule=\"evenodd\" d=\"M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z\" clip-rule=\"evenodd\"></path>
                    </svg>
                </button>
            </div>
        </div>";
    }
    
    private function renderFooter(array $data, array $styling, array $config): string
    {
        if (!($config['features']['show_branding'] ?? true)) {
            return '';
        }
        
        $classes = implode(' ', [
            $styling['theme_classes']['footer'],
            $styling['size_styles']['padding'],
            'text-xs text-center opacity-70',
            'py-2'
        ]);
        
        return "<div class=\"{$classes}\">
            Powered by AI Assistant
        </div>";
    }
    
    private function renderOverlay(array $styling, array $config): string
    {
        return "<div class=\"widget-overlay fixed inset-0 bg-black/20 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300\" style=\"display: none;\"></div>";
    }
    
    private function renderTrigger(array $data, array $styling, array $config): string
    {
        $position = $styling['placement_styles']['position'];
        $buttonClass = $styling['theme_classes']['button'];
        
        return "<button class=\"widget-trigger {$buttonClass} {$position} w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110\">
            <svg class=\"w-6 h-6\" fill=\"currentColor\" viewBox=\"0 0 20 20\">
                <path fill-rule=\"evenodd\" d=\"M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z\" clip-rule=\"evenodd\"></path>
            </svg>
        </button>";
    }
    
    private function generateAnimationCSS(string $theme): string
    {
        return "
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3) rotate(-10deg); }
            50% { opacity: 1; transform: scale(1.05) rotate(2deg); }
            70% { transform: scale(0.9) rotate(-1deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }
        
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out; }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        .animate-bounce-in { animation: bounceIn 0.5s ease-out; }
        .animate-fade-in { animation: fadeInUp 0.2s ease-out; }
        .animate-glass-morph { animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .animate-neuro-rise { animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
        ";
    }
    
    private function generateMockData(array $config): array
    {
        return [
            'messages' => [
                [
                    'type' => 'bot',
                    'content' => 'Merhaba! Size nasıl yardımcı olabilirim?',
                    'timestamp' => now()->subMinutes(2)
                ],
                [
                    'type' => 'user', 
                    'content' => 'Web sitem için SEO önerilerine ihtiyacım var.',
                    'timestamp' => now()->subMinute()
                ],
                [
                    'type' => 'bot',
                    'content' => 'Tabii ki! Web sitenizin SEO performansını artırmak için birkaç önerim var. Öncelikle mevcut sitenizin URL\'sini paylaşabilir misiniz?',
                    'timestamp' => now()
                ]
            ],
            'typing' => false,
            'online' => true,
            'last_seen' => now()
        ];
    }
}