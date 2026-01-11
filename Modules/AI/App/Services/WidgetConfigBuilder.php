<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Widget Config Builder V2
 * 
 * Chat widget konfigürasyonlarını oluşturan ve yöneten sistem
 * Advanced configuration options ve validation sağlar
 * 
 * Features:
 * - Dynamic config generation
 * - Advanced validation rules
 * - Theme-specific configurations
 * - Placement-aware settings
 * - Performance optimization configs
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
readonly class WidgetConfigBuilder
{
    /**
     * Default configuration template
     */
    private const DEFAULT_CONFIG = [
        'name' => 'AI Chat Widget',
        'enabled' => true,
        'placement' => 'bottom-right',
        'theme' => 'modern',
        'size' => 'standard',
        'ai_enabled' => true,
        'context_aware' => true,
        'use_templates' => true,
        'database_learning' => true,
        'rate_limiting' => true,
        'guest_access' => true,
        'show_typing' => true,
        'show_avatars' => true,
        'animation_enabled' => true,
        'sound_enabled' => false,
        'auto_open' => false,
        'minimize_enabled' => true,
        'welcome_message' => 'Merhaba! Size nasıl yardımcı olabilirim?',
        'placeholder_text' => 'Mesajınızı yazın...',
        'send_button_text' => 'Gönder'
    ];

    /**
     * Theme-specific configurations
     */
    private const THEME_CONFIGS = [
        'modern' => [
            'primary_color' => '#3b82f6',
            'secondary_color' => '#1e40af', 
            'background_color' => '#ffffff',
            'text_color' => '#1f2937',
            'border_radius' => '12px',
            'shadow' => '0 20px 25px -5px rgba(0, 0, 0, 0.1)',
            'font_family' => "'Inter', sans-serif",
            'animation_duration' => '300ms'
        ],
        'minimal' => [
            'primary_color' => '#000000',
            'secondary_color' => '#4b5563',
            'background_color' => '#ffffff',
            'text_color' => '#000000',
            'border_radius' => '4px',
            'shadow' => '0 1px 3px rgba(0, 0, 0, 0.1)',
            'font_family' => "'Helvetica Neue', sans-serif",
            'animation_duration' => '200ms'
        ],
        'colorful' => [
            'primary_color' => '#8b5cf6',
            'secondary_color' => '#7c3aed',
            'background_color' => '#fef3ff',
            'text_color' => '#581c87',
            'border_radius' => '16px',
            'shadow' => '0 25px 50px -12px rgba(139, 92, 246, 0.25)',
            'font_family' => "'Poppins', sans-serif",
            'animation_duration' => '400ms'
        ],
        'dark' => [
            'primary_color' => '#10b981',
            'secondary_color' => '#059669',
            'background_color' => '#1f2937',
            'text_color' => '#f9fafb',
            'border_radius' => '8px',
            'shadow' => '0 20px 25px -5px rgba(0, 0, 0, 0.4)',
            'font_family' => "'JetBrains Mono', monospace",
            'animation_duration' => '250ms'
        ],
        'glassmorphism' => [
            'primary_color' => '#06b6d4',
            'secondary_color' => '#0891b2',
            'background_color' => 'rgba(255, 255, 255, 0.1)',
            'text_color' => '#ffffff',
            'border_radius' => '20px',
            'shadow' => '0 8px 32px 0 rgba(31, 38, 135, 0.37)',
            'backdrop_filter' => 'blur(4px)',
            'border' => '1px solid rgba(255, 255, 255, 0.18)',
            'font_family' => "'SF Pro Display', sans-serif",
            'animation_duration' => '350ms'
        ],
        'neumorphism' => [
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'background_color' => '#e0e5ec',
            'text_color' => '#4a5568',
            'border_radius' => '20px',
            'shadow' => '9px 9px 16px #a3b1c6, -9px -9px 16px #ffffff',
            'inner_shadow' => 'inset 6px 6px 10px #c7d3e1, inset -6px -6px 10px #f9ffff',
            'font_family' => "'Nunito', sans-serif",
            'animation_duration' => '300ms'
        ]
    ];

    /**
     * Placement-specific configurations
     */
    private const PLACEMENT_CONFIGS = [
        'bottom-right' => [
            'position' => 'fixed',
            'bottom' => '20px',
            'right' => '20px',
            'z_index' => 1000,
            'transform_origin' => 'bottom right',
            'animation_enter' => 'slideInBottomRight',
            'animation_exit' => 'slideOutBottomRight'
        ],
        'bottom-left' => [
            'position' => 'fixed',
            'bottom' => '20px',
            'left' => '20px',
            'z_index' => 1000,
            'transform_origin' => 'bottom left',
            'animation_enter' => 'slideInBottomLeft',
            'animation_exit' => 'slideOutBottomLeft'
        ],
        'top-right' => [
            'position' => 'fixed',
            'top' => '20px',
            'right' => '20px',
            'z_index' => 1000,
            'transform_origin' => 'top right',
            'animation_enter' => 'slideInTopRight',
            'animation_exit' => 'slideOutTopRight'
        ],
        'top-left' => [
            'position' => 'fixed',
            'top' => '20px',
            'left' => '20px',
            'z_index' => 1000,
            'transform_origin' => 'top left',
            'animation_enter' => 'slideInTopLeft',
            'animation_exit' => 'slideOutTopLeft'
        ],
        'center' => [
            'position' => 'fixed',
            'top' => '50%',
            'left' => '50%',
            'transform' => 'translate(-50%, -50%)',
            'z_index' => 1001,
            'transform_origin' => 'center',
            'animation_enter' => 'zoomIn',
            'animation_exit' => 'zoomOut'
        ],
        'sidebar' => [
            'position' => 'relative',
            'width' => '100%',
            'height' => 'auto',
            'transform_origin' => 'left',
            'animation_enter' => 'slideInLeft',
            'animation_exit' => 'slideOutLeft'
        ],
        'header' => [
            'position' => 'relative',
            'width' => '100%',
            'height' => 'auto',
            'transform_origin' => 'top',
            'animation_enter' => 'slideInDown',
            'animation_exit' => 'slideOutUp'
        ],
        'footer' => [
            'position' => 'relative',
            'width' => '100%',
            'height' => 'auto',
            'transform_origin' => 'bottom',
            'animation_enter' => 'slideInUp',
            'animation_exit' => 'slideOutDown'
        ],
        'inline' => [
            'position' => 'relative',
            'width' => '100%',
            'height' => 'auto',
            'display' => 'block',
            'transform_origin' => 'center',
            'animation_enter' => 'fadeIn',
            'animation_exit' => 'fadeOut'
        ]
    ];

    /**
     * Size-based configurations
     */
    private const SIZE_CONFIGS = [
        'compact' => [
            'width' => '320px',
            'height' => '400px',
            'min_width' => '280px',
            'min_height' => '350px',
            'max_height' => '450px',
            'font_size' => '14px',
            'padding' => '12px',
            'avatar_size' => '32px',
            'input_height' => '40px',
            'button_size' => '36px'
        ],
        'standard' => [
            'width' => '380px',
            'height' => '500px',
            'min_width' => '320px',
            'min_height' => '400px',
            'max_height' => '600px',
            'font_size' => '15px',
            'padding' => '16px',
            'avatar_size' => '36px',
            'input_height' => '48px',
            'button_size' => '44px'
        ],
        'large' => [
            'width' => '450px',
            'height' => '600px',
            'min_width' => '400px',
            'min_height' => '500px',
            'max_height' => '700px',
            'font_size' => '16px',
            'padding' => '20px',
            'avatar_size' => '42px',
            'input_height' => '52px',
            'button_size' => '48px'
        ],
        'fullscreen' => [
            'width' => '100vw',
            'height' => '100vh',
            'min_width' => '100vw',
            'min_height' => '100vh',
            'max_height' => '100vh',
            'font_size' => '16px',
            'padding' => '24px',
            'avatar_size' => '48px',
            'input_height' => '56px',
            'button_size' => '52px'
        ]
    ];

    /**
     * Feature-based configurations
     */
    private const FEATURE_CONFIGS = [
        'ai_enabled' => [
            'requires' => ['context_aware', 'use_templates'],
            'api_endpoints' => ['chat', 'context'],
            'cache_ttl' => 3600
        ],
        'context_aware' => [
            'requires' => ['database_learning'],
            'context_types' => ['general', 'seo', 'blog', 'code', 'analysis']
        ],
        'database_learning' => [
            'cache_ttl' => 86400,
            'context_refresh_interval' => 3600
        ],
        'rate_limiting' => [
            'guest_limit' => 60,
            'user_limit' => 200,
            'window' => 3600
        ],
        'guest_access' => [
            'requires' => ['rate_limiting'],
            'restrictions' => ['limited_features', 'no_history']
        ]
    ];

    public function __construct()
    {
    }

    /**
     * Config oluştur
     */
    public function build(array $options = []): array
    {
        try {
            Log::info('[Widget Config Builder V2] Building configuration', ['options' => $options]);

            // Base config ile başla
            $config = self::DEFAULT_CONFIG;

            // User options ile merge
            $config = array_merge($config, $options);

            // Theme-specific config ekle
            $config = $this->applyThemeConfig($config);

            // Placement-specific config ekle
            $config = $this->applyPlacementConfig($config);

            // Size-specific config ekle
            $config = $this->applySizeConfig($config);

            // Feature-based config ekle
            $config = $this->applyFeatureConfig($config);

            // Responsive settings ekle
            $config = $this->applyResponsiveConfig($config);

            // Performance optimizations ekle
            $config = $this->applyPerformanceConfig($config);

            // Validation
            $this->validateConfig($config);

            // Meta bilgiler ekle
            $config['meta'] = [
                'generated_at' => now()->toISOString(),
                'version' => '2.0.0',
                'config_hash' => md5(json_encode($config))
            ];

            Log::info('[Widget Config Builder V2] Configuration built successfully', [
                'theme' => $config['theme'],
                'placement' => $config['placement'],
                'size' => $config['size']
            ]);

            return $config;

        } catch (Exception $e) {
            Log::error('[Widget Config Builder V2] Configuration build failed', [
                'error' => $e->getMessage(),
                'options' => $options
            ]);

            // Fallback config döndür
            return $this->getFallbackConfig();
        }
    }

    /**
     * Config template'leri getir
     */
    public function getTemplates(): array
    {
        return [
            'minimal_chat' => [
                'name' => 'Minimal Chat',
                'theme' => 'minimal',
                'placement' => 'bottom-right',
                'size' => 'compact',
                'ai_enabled' => true,
                'context_aware' => false,
                'animation_enabled' => false
            ],
            'full_featured' => [
                'name' => 'Full Featured Chat',
                'theme' => 'modern',
                'placement' => 'bottom-right',
                'size' => 'standard',
                'ai_enabled' => true,
                'context_aware' => true,
                'database_learning' => true,
                'use_templates' => true
            ],
            'sidebar_support' => [
                'name' => 'Sidebar Support',
                'theme' => 'colorful',
                'placement' => 'sidebar',
                'size' => 'large',
                'ai_enabled' => true,
                'context_aware' => true,
                'auto_open' => true
            ],
            'dark_mode' => [
                'name' => 'Dark Mode Chat',
                'theme' => 'dark',
                'placement' => 'bottom-left',
                'size' => 'standard',
                'ai_enabled' => true,
                'context_aware' => true,
                'sound_enabled' => true
            ],
            'glass_effect' => [
                'name' => 'Glass Effect',
                'theme' => 'glassmorphism',
                'placement' => 'center',
                'size' => 'large',
                'ai_enabled' => true,
                'context_aware' => true,
                'animation_enabled' => true
            ]
        ];
    }

    /**
     * Theme config uygula
     */
    private function applyThemeConfig(array $config): array
    {
        $theme = $config['theme'] ?? 'modern';
        
        if (isset(self::THEME_CONFIGS[$theme])) {
            $config['theme_config'] = self::THEME_CONFIGS[$theme];
        } else {
            $config['theme_config'] = self::THEME_CONFIGS['modern'];
        }

        return $config;
    }

    /**
     * Placement config uygula
     */
    private function applyPlacementConfig(array $config): array
    {
        $placement = $config['placement'] ?? 'bottom-right';
        
        if (isset(self::PLACEMENT_CONFIGS[$placement])) {
            $config['placement_config'] = self::PLACEMENT_CONFIGS[$placement];
        } else {
            $config['placement_config'] = self::PLACEMENT_CONFIGS['bottom-right'];
        }

        return $config;
    }

    /**
     * Size config uygula
     */
    private function applySizeConfig(array $config): array
    {
        $size = $config['size'] ?? 'standard';
        
        if (isset(self::SIZE_CONFIGS[$size])) {
            $config['size_config'] = self::SIZE_CONFIGS[$size];
        } else {
            $config['size_config'] = self::SIZE_CONFIGS['standard'];
        }

        return $config;
    }

    /**
     * Feature config uygula
     */
    private function applyFeatureConfig(array $config): array
    {
        $config['feature_config'] = [];

        foreach (self::FEATURE_CONFIGS as $feature => $featureConfig) {
            if ($config[$feature] ?? false) {
                $config['feature_config'][$feature] = $featureConfig;
                
                // Required features'ları aktif et
                if (isset($featureConfig['requires'])) {
                    foreach ($featureConfig['requires'] as $required) {
                        $config[$required] = true;
                    }
                }
            }
        }

        return $config;
    }

    /**
     * Responsive config uygula
     */
    private function applyResponsiveConfig(array $config): array
    {
        $config['responsive'] = [
            'mobile' => [
                'max_width' => '768px',
                'size_override' => 'compact',
                'placement_override' => 'bottom-right',
                'fullscreen_threshold' => '480px'
            ],
            'tablet' => [
                'max_width' => '1024px',
                'size_override' => 'standard'
            ],
            'desktop' => [
                'min_width' => '1025px'
            ]
        ];

        return $config;
    }

    /**
     * Performance config uygula
     */
    private function applyPerformanceConfig(array $config): array
    {
        $config['performance'] = [
            'lazy_load' => true,
            'cache_enabled' => true,
            'preload_assets' => false,
            'debounce_typing' => 300,
            'max_message_history' => 50,
            'image_compression' => true,
            'minimize_requests' => true
        ];

        return $config;
    }

    /**
     * Config validation
     */
    private function validateConfig(array $config): void
    {
        $required = ['theme', 'placement', 'size', 'ai_enabled'];
        
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw new \InvalidArgumentException("Required configuration field missing: {$field}");
            }
        }

        // Theme validation
        if (!array_key_exists($config['theme'], self::THEME_CONFIGS)) {
            throw new \InvalidArgumentException("Invalid theme: {$config['theme']}");
        }

        // Placement validation
        if (!array_key_exists($config['placement'], self::PLACEMENT_CONFIGS)) {
            throw new \InvalidArgumentException("Invalid placement: {$config['placement']}");
        }

        // Size validation
        if (!array_key_exists($config['size'], self::SIZE_CONFIGS)) {
            throw new \InvalidArgumentException("Invalid size: {$config['size']}");
        }

        // Feature dependencies validation
        if ($config['context_aware'] && !$config['ai_enabled']) {
            throw new \InvalidArgumentException("context_aware requires ai_enabled");
        }

        if ($config['database_learning'] && !$config['context_aware']) {
            throw new \InvalidArgumentException("database_learning requires context_aware");
        }
    }

    /**
     * Fallback config
     */
    private function getFallbackConfig(): array
    {
        return array_merge(self::DEFAULT_CONFIG, [
            'theme_config' => self::THEME_CONFIGS['modern'],
            'placement_config' => self::PLACEMENT_CONFIGS['bottom-right'],
            'size_config' => self::SIZE_CONFIGS['standard'],
            'meta' => [
                'generated_at' => now()->toISOString(),
                'version' => '2.0.0',
                'fallback' => true
            ]
        ]);
    }
}