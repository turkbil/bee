<?php

declare(strict_types=1);

namespace Modules\AI\app\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Modules\AI\app\Exceptions\ChatWidgetException;

/**
 * Chat Widget Service V2
 * 
 * Modular ve reusable chat widget sistemi
 * Multiple placement, customizable appearance, context-aware responses
 * 
 * Features:
 * - Reusable widget component system
 * - Multiple placement locations
 * - Customizable appearance and themes  
 * - Context-aware AI responses
 * - Performance optimized rendering
 * - Configuration management
 * 
 * @package Modules\AI\app\Services
 * @author AI V2 System
 * @version 2.0.0
 */
readonly class ChatWidgetService
{
    /**
     * Widget cache TTL (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Supported widget placements
     */
    private const SUPPORTED_PLACEMENTS = [
        'bottom-right' => 'Bottom Right Corner',
        'bottom-left' => 'Bottom Left Corner', 
        'top-right' => 'Top Right Corner',
        'top-left' => 'Top Left Corner',
        'center' => 'Center of Screen',
        'sidebar' => 'Sidebar Widget',
        'header' => 'Header Widget',
        'footer' => 'Footer Widget',
        'inline' => 'Inline Content Widget'
    ];

    /**
     * Widget size options
     */
    private const SIZE_OPTIONS = [
        'compact' => ['width' => '320px', 'height' => '400px'],
        'standard' => ['width' => '380px', 'height' => '500px'],
        'large' => ['width' => '450px', 'height' => '600px'],
        'fullscreen' => ['width' => '100vw', 'height' => '100vh']
    ];

    /**
     * Widget themes
     */
    private const WIDGET_THEMES = [
        'modern' => 'Modern Clean Design',
        'minimal' => 'Minimal Simple',
        'colorful' => 'Colorful Vibrant',
        'dark' => 'Dark Mode',
        'glassmorphism' => 'Glass Effect',
        'neumorphism' => 'Neomorphic Design'
    ];

    public function __construct(
        private WidgetConfigBuilder $configBuilder,
        private WidgetRenderer $renderer,
        private DatabaseLearningService $learningService
    ) {}

    /**
     * Widget render et
     */
    public function renderWidget(array $config = []): string
    {
        try {
            Log::info('[Chat Widget V2] Rendering widget', ['config' => $config]);

            // Default config ile merge
            $finalConfig = $this->mergeWithDefaults($config);

            // Config validation
            $this->validateConfig($finalConfig);

            // Cache key oluştur
            $cacheKey = $this->generateCacheKey($finalConfig);

            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($finalConfig) {
                // Context-aware AI setup
                $aiContext = $this->prepareAIContext($finalConfig);

                // Widget render
                $widgetHtml = $this->renderer->render($finalConfig, $aiContext);

                Log::info('[Chat Widget V2] Widget rendered successfully', [
                    'placement' => $finalConfig['placement'],
                    'theme' => $finalConfig['theme'],
                    'size' => $finalConfig['size']
                ]);

                return $widgetHtml;
            });

        } catch (Exception $e) {
            Log::error('[Chat Widget V2] Widget rendering failed', [
                'error' => $e->getMessage(),
                'config' => $config
            ]);

            throw ChatWidgetException::renderingFailed($e->getMessage());
        }
    }

    /**
     * Multiple widget render (farklı konumlar için)
     */
    public function renderMultipleWidgets(array $widgets): array
    {
        $renderedWidgets = [];

        foreach ($widgets as $widgetId => $config) {
            try {
                $renderedWidgets[$widgetId] = [
                    'html' => $this->renderWidget($config),
                    'placement' => $config['placement'] ?? 'bottom-right',
                    'config' => $config
                ];
            } catch (Exception $e) {
                Log::error('[Chat Widget V2] Multiple widget rendering failed', [
                    'widget_id' => $widgetId,
                    'error' => $e->getMessage()
                ]);

                // Continue with other widgets
                $renderedWidgets[$widgetId] = [
                    'html' => $this->renderErrorWidget($e->getMessage()),
                    'placement' => $config['placement'] ?? 'bottom-right',
                    'config' => $config,
                    'error' => true
                ];
            }
        }

        return $renderedWidgets;
    }

    /**
     * Widget config builder
     */
    public function buildConfig(array $options = []): array
    {
        return $this->configBuilder->build($options);
    }

    /**
     * Available placements getir
     */
    public function getAvailablePlacements(): array
    {
        return self::SUPPORTED_PLACEMENTS;
    }

    /**
     * Available themes getir
     */
    public function getAvailableThemes(): array
    {
        return self::WIDGET_THEMES;
    }

    /**
     * Available sizes getir
     */
    public function getAvailableSizes(): array
    {
        return self::SIZE_OPTIONS;
    }

    /**
     * Widget konfigürasyonlarını kaydet
     */
    public function saveWidgetConfig(string $widgetId, array $config): bool
    {
        try {
            $configPath = storage_path("app/ai-widgets/{$widgetId}.json");
            
            // Directory oluştur
            $directory = dirname($configPath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Config kaydet
            File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));

            // Cache temizle
            $this->clearWidgetCache($widgetId);

            Log::info('[Chat Widget V2] Widget config saved', [
                'widget_id' => $widgetId,
                'config_path' => $configPath
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('[Chat Widget V2] Widget config save failed', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Widget konfigürasyonunu yükle
     */
    public function loadWidgetConfig(string $widgetId): ?array
    {
        try {
            $configPath = storage_path("app/ai-widgets/{$widgetId}.json");

            if (!File::exists($configPath)) {
                return null;
            }

            $config = json_decode(File::get($configPath), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('[Chat Widget V2] Invalid widget config JSON', [
                    'widget_id' => $widgetId,
                    'json_error' => json_last_error_msg()
                ]);
                
                return null;
            }

            return $config;

        } catch (Exception $e) {
            Log::error('[Chat Widget V2] Widget config load failed', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Tüm widget'ları listele
     */
    public function listWidgets(): array
    {
        try {
            $widgetsPath = storage_path('app/ai-widgets');
            
            if (!File::exists($widgetsPath)) {
                return [];
            }

            $widgetFiles = File::files($widgetsPath);
            $widgets = [];

            foreach ($widgetFiles as $file) {
                if ($file->getExtension() === 'json') {
                    $widgetId = $file->getFilenameWithoutExtension();
                    $config = $this->loadWidgetConfig($widgetId);
                    
                    if ($config) {
                        $widgets[$widgetId] = [
                            'id' => $widgetId,
                            'name' => $config['name'] ?? "Widget {$widgetId}",
                            'placement' => $config['placement'] ?? 'bottom-right',
                            'theme' => $config['theme'] ?? 'modern',
                            'enabled' => $config['enabled'] ?? true,
                            'created_at' => $file->getCTime(),
                            'updated_at' => $file->getMTime()
                        ];
                    }
                }
            }

            return $widgets;

        } catch (Exception $e) {
            Log::error('[Chat Widget V2] Widget listing failed', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Widget sil
     */
    public function deleteWidget(string $widgetId): bool
    {
        try {
            $configPath = storage_path("app/ai-widgets/{$widgetId}.json");

            if (File::exists($configPath)) {
                File::delete($configPath);
                $this->clearWidgetCache($widgetId);

                Log::info('[Chat Widget V2] Widget deleted', [
                    'widget_id' => $widgetId
                ]);

                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error('[Chat Widget V2] Widget deletion failed', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Widget preview render
     */
    public function renderPreview(array $config): string
    {
        try {
            // Preview mode ekle
            $config['preview_mode'] = true;
            $config['ai_enabled'] = false; // Preview'da AI çalıştırma

            return $this->renderer->render($config, []);

        } catch (Exception $e) {
            Log::error('[Chat Widget V2] Preview rendering failed', [
                'error' => $e->getMessage()
            ]);

            return $this->renderErrorWidget('Preview could not be generated');
        }
    }

    /**
     * Widget JavaScript dosyası oluştur
     */
    public function generateWidgetJS(array $config): string
    {
        $widgetId = $config['id'] ?? 'default';
        $apiEndpoints = [
            'chat' => route('api.ai.chat'),
            'context' => route('api.ai.context') // Database Learning Context
        ];

        $jsConfig = json_encode([
            'id' => $widgetId,
            'placement' => $config['placement'] ?? 'bottom-right',
            'theme' => $config['theme'] ?? 'modern',
            'size' => $config['size'] ?? 'standard',
            'api_endpoints' => $apiEndpoints,
            'rate_limiting' => [
                'guest_limit' => 60,
                'user_limit' => 200
            ],
            'features' => [
                'context_awareness' => $config['context_aware'] ?? true,
                'response_templates' => $config['use_templates'] ?? true,
                'database_learning' => $config['database_learning'] ?? true
            ]
        ]);

        return View::make('ai::widgets.widget-js', [
            'config' => $jsConfig,
            'widget_id' => $widgetId
        ])->render();
    }

    /**
     * Default config ile merge
     */
    private function mergeWithDefaults(array $config): array
    {
        $defaults = [
            'placement' => 'bottom-right',
            'theme' => 'modern',
            'size' => 'standard',
            'enabled' => true,
            'ai_enabled' => true,
            'context_aware' => true,
            'use_templates' => true,
            'database_learning' => true,
            'rate_limiting' => true,
            'guest_access' => true,
            'show_typing' => true,
            'show_avatars' => true,
            'welcome_message' => 'Merhaba! Size nasıl yardımcı olabilirim?'
        ];

        return array_merge($defaults, $config);
    }

    /**
     * Config validation
     */
    private function validateConfig(array $config): void
    {
        // Required fields
        $required = ['placement', 'theme', 'size'];
        
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw new \InvalidArgumentException("Required field missing: {$field}");
            }
        }

        // Validate placement
        if (!array_key_exists($config['placement'], self::SUPPORTED_PLACEMENTS)) {
            throw new \InvalidArgumentException("Invalid placement: {$config['placement']}");
        }

        // Validate theme
        if (!array_key_exists($config['theme'], self::WIDGET_THEMES)) {
            throw new \InvalidArgumentException("Invalid theme: {$config['theme']}");
        }

        // Validate size
        if (!array_key_exists($config['size'], self::SIZE_OPTIONS)) {
            throw new \InvalidArgumentException("Invalid size: {$config['size']}");
        }
    }

    /**
     * AI context hazırla
     */
    private function prepareAIContext(array $config): array
    {
        if (!($config['context_aware'] ?? true)) {
            return [];
        }

        try {
            // Database Learning context'i al
            $contextType = $config['context_type'] ?? 'general';
            $aiContext = $this->learningService->getAIOptimizedContext($contextType);

            return [
                'system_context' => $aiContext,
                'widget_context' => [
                    'placement' => $config['placement'],
                    'theme' => $config['theme'],
                    'user_type' => 'widget_user'
                ],
                'features_enabled' => [
                    'templates' => $config['use_templates'] ?? true,
                    'learning' => $config['database_learning'] ?? true
                ]
            ];

        } catch (Exception $e) {
            Log::warning('[Chat Widget V2] Context preparation failed', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Cache key oluştur
     */
    private function generateCacheKey(array $config): string
    {
        $keyData = [
            'placement' => $config['placement'],
            'theme' => $config['theme'],
            'size' => $config['size'],
            'context_aware' => $config['context_aware'] ?? true
        ];

        return 'chat_widget_' . md5(json_encode($keyData));
    }

    /**
     * Widget cache temizle
     */
    private function clearWidgetCache(string $widgetId): void
    {
        // Pattern-based cache clearing
        $patterns = [
            "chat_widget_{$widgetId}_*",
            "chat_widget_*_{$widgetId}",
        ];

        foreach ($patterns as $pattern) {
            Cache::flush(); // Simplified for now
        }
    }

    /**
     * Error widget render
     */
    private function renderErrorWidget(string $error): string
    {
        return View::make('ai::widgets.error-widget', [
            'error' => $error
        ])->render();
    }
}